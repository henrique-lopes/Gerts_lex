<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Fee;
use App\Models\CaseModel;
use App\Models\Lawyer;

class FeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lawyers = Lawyer::all();
        $cases = CaseModel::all();

        if ($lawyers->isEmpty() || $cases->isEmpty()) {
            $this->command->warn("Advogados ou casos não encontrados. Pulando a criação de honorários.");
            return;
        }

        $feeTypes = ['Honorários Contratuais', 'Honorários de Êxito', 'Honorários Sucumbenciais', 'Taxa Administrativa', 'Custas Processuais'];
        $statuses = ['pendente', 'pago', 'vencido'];
        
        // Criar 10-20 honorários
        $numFees = rand(10, 20);
        
        for ($i = 0; $i < $numFees; $i++) {
            $amount = rand(500, 10000);
            $status = $statuses[array_rand($statuses)];
            
            Fee::create([
                'lawyer_id' => $lawyers->random()->id,
                'case_id' => $cases->random()->id,
                'description' => $feeTypes[array_rand($feeTypes)],
                'amount' => $amount,
                'due_date' => now()->addDays(rand(-30, 60)),
                'paid_date' => $status === 'pago' ? now()->subDays(rand(1, 30)) : null,
                'status' => $status,
                'notes' => 'Honorário gerado automaticamente pelo seeder.',
            ]);
        }
    }
}
