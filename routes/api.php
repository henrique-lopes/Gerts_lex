<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LawyerApiController;
use App\Http\Controllers\Api\ClientApiController;
use App\Http\Controllers\Api\CaseApiController;
use App\Http\Controllers\Api\FeeApiController;
use App\Http\Controllers\Api\AppointmentApiController;
use App\Http\Controllers\Api\DeadlineApiController;
use App\Http\Controllers\Api\CaseDocumentApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Public API endpoints for testing (remove in production)
Route::get('test/lawyers', [LawyerApiController::class, 'index']);
Route::get('test/clients', [ClientApiController::class, 'index']);
Route::get('test/send-email', function () {
    $tenant = \Spatie\Multitenancy\Models\Tenant::current();
    
    if (!$tenant) {
        return response()->json(['error' => 'Tenant not found'], 404);
    }
    
    // Dispatch email job
    \App\Jobs\SendEmailJob::dispatch(
        $tenant->id,
        'test@example.com',
        'Test Email from ' . $tenant->name,
        'This is a test email sent via queue for tenant: ' . $tenant->name
    );
    
    return response()->json([
        'success' => true,
        'message' => 'Email job dispatched successfully',
        'tenant' => $tenant->name
    ]);
});

Route::get('test/stats', function () {
    $tenant = \Spatie\Multitenancy\Models\Tenant::current();
    
    if (!$tenant) {
        return response()->json(['error' => 'Tenant not found'], 404);
    }
    
    $stats = [
        'users_count' => \App\Models\User::count(),
        'lawyers_count' => \App\Models\Lawyer::count(),
        'clients_count' => \App\Models\Client::count(),
        'cases_count' => \App\Models\CaseModel::count(),
        'fees_count' => \App\Models\Fee::count(),
        'appointments_count' => \App\Models\Appointment::count(),
        'deadlines_count' => \App\Models\Deadline::count(),
        'documents_count' => \App\Models\CaseDocument::count(),
        'tenant_info' => [
            'name' => $tenant->name,
            'domain' => $tenant->domain,
            'subscription_status' => $tenant->subscription_status ?? 'trial',
            'subscription_plan' => $tenant->subscription_plan ?? 'basic',
        ]
    ];
    
    return response()->json([
        'success' => true,
        'data' => $stats
    ]);
});

// API routes with authentication, tenant middleware and rate limiting
Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    
    // Lawyers API
    Route::apiResource('lawyers', LawyerApiController::class);
    
    // Clients API
    Route::apiResource('clients', ClientApiController::class);
    
    // Cases API
    Route::apiResource('cases', CaseApiController::class);
    
    // Fees API
    Route::apiResource('fees', FeeApiController::class);
    
    // Appointments API
    Route::apiResource('appointments', AppointmentApiController::class);
    
    // Deadlines API
    Route::apiResource('deadlines', DeadlineApiController::class);
    
    // Case Documents API
    Route::apiResource('case-documents', CaseDocumentApiController::class);
    
    // Additional endpoints
    Route::get('dashboard/stats', function () {
        $tenant = \Spatie\Multitenancy\Models\Tenant::current();
        
        if (!$tenant) {
            return response()->json(['error' => 'Tenant not found'], 404);
        }
        
        $stats = [
            'users_count' => \App\Models\User::count(),
            'lawyers_count' => \App\Models\Lawyer::count(),
            'clients_count' => \App\Models\Client::count(),
            'cases_count' => \App\Models\CaseModel::count(),
            'fees_count' => \App\Models\Fee::count(),
            'appointments_count' => \App\Models\Appointment::count(),
            'deadlines_count' => \App\Models\Deadline::count(),
            'documents_count' => \App\Models\CaseDocument::count(),
            'tenant_info' => [
                'name' => $tenant->name,
                'domain' => $tenant->domain,
                'subscription_status' => $tenant->subscription_status ?? 'trial',
                'subscription_plan' => $tenant->subscription_plan ?? 'basic',
            ]
        ];
        
        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    });
    
    // Search endpoint
    Route::get('search', function (Request $request) {
        $query = $request->get('q');
        $type = $request->get('type', 'all');
        
        if (!$query) {
            return response()->json([
                'success' => false,
                'message' => 'Query parameter is required'
            ], 422);
        }
        
        $results = [];
        
        if ($type === 'all' || $type === 'lawyers') {
            $lawyers = \App\Models\Lawyer::with('user')
                ->whereHas('user', function($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('email', 'like', "%{$query}%");
                })
                ->orWhere('oab_number', 'like', "%{$query}%")
                ->limit(5)
                ->get();
            
            $results['lawyers'] = $lawyers;
        }
        
        if ($type === 'all' || $type === 'clients') {
            $clients = \App\Models\Client::where('name', 'like', "%{$query}%")
                ->orWhere('email', 'like', "%{$query}%")
                ->orWhere('document', 'like', "%{$query}%")
                ->limit(5)
                ->get();
            
            $results['clients'] = $clients;
        }
        
        if ($type === 'all' || $type === 'cases') {
            $cases = \App\Models\CaseModel::where('case_number', 'like', "%{$query}%")
                ->orWhere('title', 'like', "%{$query}%")
                ->orWhere('court', 'like', "%{$query}%")
                ->limit(5)
                ->get();
            
            $results['cases'] = $cases;
        }
        
        return response()->json([
            'success' => true,
            'data' => $results
        ]);
    });
});
