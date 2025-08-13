<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Primeiro, criar o usuário administrador
        $this->call(AdminUserSeeder::class);
        
        // Depois, criar os tenants
        $this->call(TenantSeeder::class);
        
        // Depois, para cada tenant, criar os dados específicos
        $tenants = \App\Models\Tenant::all();
        
        foreach ($tenants as $tenant) {
            // Executar no contexto do tenant
            $tenant->makeCurrent();
            
            // Criar advogados
            $this->call(LawyerSeeder::class);
            
            // Criar clientes
            $this->call(ClientSeeder::class);
            
            // Criar casos
            $this->call(CaseModelSeeder::class);
            
            // Criar honorários
            $this->call(FeeSeeder::class);
            
            // Criar compromissos
            $this->call(AppointmentSeeder::class);
            
            // Criar prazos
            $this->call(DeadlineSeeder::class);
            
            // Criar documentos
            $this->call(CaseDocumentSeeder::class);
        }
    }
}

