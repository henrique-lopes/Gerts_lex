<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant;

class TenantDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenants = Tenant::all();
        
        foreach ($tenants as $tenant) {
            $this->command->info("Populando dados para o tenant: {$tenant->name}");
            
            // Executar no contexto do tenant
            $tenant->makeCurrent();
            
            try {
                // Definir tenant_id globalmente para os seeders
                config(['app.current_tenant_id' => $tenant->id]);
                
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
                
                $this->command->info("Dados criados com sucesso para o tenant: {$tenant->name}");
                
            } catch (\Exception $e) {
                $this->command->error("Erro ao criar dados para o tenant {$tenant->name}: " . $e->getMessage());
            }
            
            // Finalizar contexto do tenant
            $tenant->forgetCurrent();
        }
    }
}

