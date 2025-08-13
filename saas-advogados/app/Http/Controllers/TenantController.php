<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TenantController extends Controller
{
    /**
     * Display a listing of tenants (for admin)
     */
    public function index()
    {
        $tenants = Tenant::paginate(15);
        return view('admin.tenants.index', compact('tenants'));
    }

    /**
     * Show the form for creating a new tenant
     */
    public function create()
    {
        return view('admin.tenants.create');
    }

    /**
     * Store a newly created tenant
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'domain' => 'required|string|max:255|unique:tenants,domain|alpha_dash',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|unique:users,email',
            'admin_password' => 'required|string|min:8|confirmed',
            'subscription_plan' => 'nullable|string',
        ]);

        // Create tenant
        $tenant = Tenant::create([
            'name' => $request->name,
            'domain' => $request->domain,
            'database' => 'tenant_' . $request->domain,
            'subscription_status' => 'trial',
            'subscription_plan' => $request->subscription_plan,
            'trial_ends_at' => Carbon::now()->addDays(14), // 14-day trial
        ]);

        // Make tenant current to create admin user in tenant context
        $tenant->makeCurrent();

        // Create admin user for the tenant
        User::create([
            'name' => $request->admin_name,
            'email' => $request->admin_email,
            'password' => Hash::make($request->admin_password),
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admin.tenants.index')
            ->with('success', 'Tenant criado com sucesso!');
    }

    /**
     * Display the specified tenant
     */
    public function show(Tenant $tenant)
    {
        return view('admin.tenants.show', compact('tenant'));
    }

    /**
     * Show the form for editing the specified tenant
     */
    public function edit(Tenant $tenant)
    {
        return view('admin.tenants.edit', compact('tenant'));
    }

    /**
     * Update the specified tenant
     */
    public function update(Request $request, Tenant $tenant)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'domain' => 'required|string|max:255|alpha_dash|unique:tenants,domain,' . $tenant->id,
            'subscription_status' => 'required|in:trial,active,suspended,cancelled',
            'subscription_plan' => 'nullable|string',
            'trial_ends_at' => 'nullable|date',
        ]);

        $tenant->update($request->only([
            'name', 'domain', 'subscription_status', 
            'subscription_plan', 'trial_ends_at'
        ]));

        return redirect()->route('admin.tenants.index')
            ->with('success', 'Tenant atualizado com sucesso!');
    }

    /**
     * Remove the specified tenant
     */
    public function destroy(Tenant $tenant)
    {
        $tenant->delete();

        return redirect()->route('admin.tenants.index')
            ->with('success', 'Tenant removido com sucesso!');
    }

    /**
     * Suspend a tenant
     */
    public function suspend(Tenant $tenant)
    {
        $tenant->update(['subscription_status' => 'suspended']);

        return redirect()->back()
            ->with('success', 'Tenant suspenso com sucesso!');
    }

    /**
     * Activate a tenant
     */
    public function activate(Tenant $tenant)
    {
        $tenant->update(['subscription_status' => 'active']);

        return redirect()->back()
            ->with('success', 'Tenant ativado com sucesso!');
    }
}
