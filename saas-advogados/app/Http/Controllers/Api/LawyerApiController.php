<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lawyer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Spatie\Multitenancy\Models\Tenant;

class LawyerApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        // Create cache key based on request parameters and tenant
        $cacheKey = 'lawyers_' . 
                   (Tenant::current()->id ?? 'default') . '_' . 
                   md5(serialize($request->all()));
        
        // Try to get from cache first
        $result = Cache::remember($cacheKey, 300, function() use ($request) { // 5 minutes cache
            $query = Lawyer::with('user');
            
            // Search functionality
            if ($request->has('search')) {
                $search = $request->get('search');
                $query->whereHas('user', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })->orWhere('oab_number', 'like', "%{$search}%")
                  ->orWhere('specialties', 'like', "%{$search}%");
            }
            
            // Filter by state
            if ($request->has('state')) {
                $query->where('state', $request->get('state'));
            }
            
            // Pagination
            $perPage = $request->get('per_page', 15);
            return $query->paginate($perPage);
        });
        
        return response()->json([
            'success' => true,
            'data' => $result->items(),
            'pagination' => [
                'current_page' => $result->currentPage(),
                'last_page' => $result->lastPage(),
                'per_page' => $result->perPage(),
                'total' => $result->total(),
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'oab_number' => 'required|string|max:20|unique:lawyers',
            'state' => 'required|string|size:2',
            'specialties' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Create user first
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'email_verified_at' => now(),
            ]);

            // Create lawyer
            $lawyer = Lawyer::create([
                'user_id' => $user->id,
                'tenant_id' => Tenant::current()->id,
                'oab_number' => $request->oab_number,
                'state' => $request->state,
                'specialties' => $request->specialties,
                'phone' => $request->phone,
            ]);

            $lawyer->load('user');

            // Clear cache for this tenant
            $this->clearLawyersCache();

            return response()->json([
                'success' => true,
                'message' => 'Lawyer created successfully',
                'data' => $lawyer
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create lawyer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $lawyer = Lawyer::with('user')->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $lawyer
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lawyer not found'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $lawyer = Lawyer::findOrFail($id);
            
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $lawyer->user_id,
                'password' => 'sometimes|nullable|string|min:8',
                'oab_number' => 'sometimes|required|string|max:20|unique:lawyers,oab_number,' . $id,
                'state' => 'sometimes|required|string|size:2',
                'specialties' => 'nullable|string',
                'phone' => 'nullable|string|max:20',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Update user if needed
            if ($request->has('name') || $request->has('email') || $request->has('password')) {
                $userUpdate = [];
                if ($request->has('name')) $userUpdate['name'] = $request->name;
                if ($request->has('email')) $userUpdate['email'] = $request->email;
                if ($request->has('password')) $userUpdate['password'] = Hash::make($request->password);
                
                $lawyer->user->update($userUpdate);
            }

            // Update lawyer
            $lawyerUpdate = $request->only(['oab_number', 'state', 'specialties', 'phone']);
            $lawyer->update($lawyerUpdate);

            $lawyer->load('user');

            // Clear cache for this tenant
            $this->clearLawyersCache();

            return response()->json([
                'success' => true,
                'message' => 'Lawyer updated successfully',
                'data' => $lawyer
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update lawyer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $lawyer = Lawyer::findOrFail($id);
            
            // Delete associated user
            $lawyer->user->delete();
            
            // Delete lawyer
            $lawyer->delete();

            // Clear cache for this tenant
            $this->clearLawyersCache();

            return response()->json([
                'success' => true,
                'message' => 'Lawyer deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete lawyer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear lawyers cache for current tenant
     */
    private function clearLawyersCache(): void
    {
        $tenantId = Tenant::current()->id ?? 'default';
        $pattern = "lawyers_{$tenantId}_*";
        
        // Clear all cached lawyer queries for this tenant
        Cache::flush(); // Simple approach - in production, use more specific cache tagging
    }
}
