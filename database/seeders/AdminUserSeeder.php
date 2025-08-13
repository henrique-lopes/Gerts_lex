<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Tenant;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Criar tenant principal para o administrador
        $adminTenant = Tenant::firstOrCreate(
            ['domain' => 'admin'],
            [
                'name' => 'Administração Gert\'s Lex',
                'domain' => 'admin',
                'email' => 'henrique-lopes@outlook.com.br',
                'phone' => '(11) 99999-9999',
                'subscription_status' => 'active',
                'subscription_plan' => 'premium',
                'trial_ends_at' => null,
                'last_payment_at' => now(),
                'next_billing_date' => now()->addYear(),
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        // Criar usuário administrador
        $adminUser = User::firstOrCreate(
            ['email' => 'henrique-lopes@outlook.com.br'],
            [
                'name' => 'Henrique Lopes',
                'email' => 'henrique-lopes@outlook.com.br',
                'email_verified_at' => now(),
                'password' => Hash::make('admin123'),
                'tenant_id' => $adminTenant->id,
                'role' => 'super_admin',
                'is_admin' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        // Criar tenant de demonstração
        $demoTenant = Tenant::firstOrCreate(
            ['domain' => 'demo'],
            [
                'name' => 'Escritório Demo',
                'domain' => 'demo',
                'email' => 'demo@gertslex.com',
                'phone' => '(11) 88888-8888',
                'subscription_status' => 'trial',
                'subscription_plan' => 'basic',
                'trial_ends_at' => now()->addDays(30),
                'last_payment_at' => null,
                'next_billing_date' => now()->addMonth(),
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        // Criar usuário demo
        $demoUser = User::firstOrCreate(
            ['email' => 'admin@demo.com'],
            [
                'name' => 'Administrador Demo',
                'email' => 'admin@demo.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'tenant_id' => $demoTenant->id,
                'role' => 'admin',
                'is_admin' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        $this->command->info('✅ Usuário administrador criado com sucesso!');
        $this->command->info('📧 Email: henrique-lopes@outlook.com.br');
        $this->command->info('🔑 Senha: admin123');
        $this->command->info('🏢 Tenant: admin (ID: ' . $adminTenant->id . ')');
        $this->command->info('');
        $this->command->info('✅ Usuário demo criado com sucesso!');
        $this->command->info('📧 Email: admin@demo.com');
        $this->command->info('🔑 Senha: password');
        $this->command->info('🏢 Tenant: demo (ID: ' . $demoTenant->id . ')');
    }
}
