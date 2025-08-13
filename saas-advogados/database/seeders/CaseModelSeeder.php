<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CaseModel;
use App\Models\Client;
use App\Models\Lawyer;

class CaseModelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lawyers = Lawyer::all();
        $clients = Client::all();

        if ($lawyers->isEmpty() || $clients->isEmpty()) {
            $this->command->warn("Advogados ou clientes não encontrados. Pulando a criação de casos.");
            return;
        }

        $caseTypes = ['Ação de Cobrança', 'Ação Trabalhista', 'Divórcio', 'Inventário', 'Ação Penal', 'Ação Civil', 'Execução Fiscal', 'Mandado de Segurança'];
        $statuses = ['ativo', 'arquivado', 'suspenso'];
        
        // Criar 8-12 casos
        $numCases = rand(8, 12);
        
        for ($i = 0; $i < $numCases; $i++) {
            $caseNumber = rand(1000000, 9999999) . '-' . rand(10, 99) . '.' . date('Y') . '.8.26.' . rand(1000, 9999);
            
            CaseModel::create([
                'lawyer_id' => $lawyers->random()->id,
                'client_id' => $clients->random()->id,
                'case_type' => $caseTypes[array_rand($caseTypes)],
                'description' => 'Caso de ' . strtolower($caseTypes[array_rand($caseTypes)]) . ' movido em favor do cliente.',
                'case_number' => $caseNumber,
                'court' => rand(1, 10) . 'ª Vara Cível de São Paulo',
                'status' => $statuses[array_rand($statuses)],
            ]);
        }
    }
}
