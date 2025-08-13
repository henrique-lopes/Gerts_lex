<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create example tenants
        $tenants = [
            [
                'name' => 'Empresa Demo',
                'domain' => 'demo',
                'database' => 'tenant_demo',
                'subscription_status' => 'trial',
                'subscription_plan' => null,
                'trial_ends_at' => Carbon::now()->addDays(14),
                'admin_name' => 'Admin Demo',
                'admin_email' => 'admin@demo.com',
                'admin_password' => 'password123',
            ],
            [
                'name' => 'Tech Solutions',
                'domain' => 'techsolutions',
                'database' => 'tenant_techsolutions',
                'subscription_status' => 'active',
                'subscription_plan' => 'pro',
                'trial_ends_at' => null,
                'admin_name' => 'João Silva',
                'admin_email' => 'joao@techsolutions.com',
                'admin_password' => 'password123',
            ],
            [
                'name' => 'Startup Inovadora',
                'domain' => 'startup',
                'database' => 'tenant_startup',
                'subscription_status' => 'trial',
                'subscription_plan' => null,
                'trial_ends_at' => Carbon::now()->addDays(7),
                'admin_name' => 'Maria Santos',
                'admin_email' => 'maria@startup.com',
                'admin_password' => 'password123',
            ],
        ];

        foreach ($tenants as $tenantData) {
            // Extract admin data
            $adminData = [
                'name' => $tenantData['admin_name'],
                'email' => $tenantData['admin_email'],
                'password' => $tenantData['admin_password'],
            ];
            
            // Remove admin data from tenant data
            unset($tenantData['admin_name'], $tenantData['admin_email'], $tenantData['admin_password']);

            // Create tenant
            $tenant = Tenant::create($tenantData);

            // Make tenant current to create admin user in tenant context
            $tenant->makeCurrent();

            // Create admin user for the tenant
            User::create([
                'name' => $adminData['name'],
                'email' => $adminData['email'],
                'password' => Hash::make($adminData['password']),
                'email_verified_at' => now(),
            ]);

            // Create additional users for some tenants
            if ($tenant->domain === 'techsolutions') {
                User::create([
                    'name' => 'Pedro Oliveira',
                    'email' => 'pedro@techsolutions.com',
                    'password' => Hash::make('password123'),
                    'email_verified_at' => now(),
                ]);

                User::create([
                    'name' => 'Ana Costa',
                    'email' => 'ana@techsolutions.com',
                    'password' => Hash::make('password123'),
                    'email_verified_at' => now(),
                ]);
            }

            $this->command->info("Tenant '{$tenant->name}' criado com sucesso!");
        }
    }
}
