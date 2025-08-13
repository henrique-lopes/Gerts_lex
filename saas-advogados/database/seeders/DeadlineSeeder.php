<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Deadline;
use App\Models\CaseModel;
use App\Models\Lawyer;

class DeadlineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cases = CaseModel::all();
        $lawyers = Lawyer::all();
        
        if ($cases->isEmpty() || $lawyers->isEmpty()) {
            return;
        }

        $deadlineTypes = [
            'Contestação',
            'Recurso',
            'Manifestação',
            'Petição Inicial',
            'Audiência',
            'Perícia',
            'Alegações Finais',
            'Embargos',
            'Apelação',
            'Cumprimento de Sentença'
        ];

        foreach ($cases as $case) {
            // Criar 2-4 prazos por caso
            $numDeadlines = rand(2, 4);
            
            for ($i = 0; $i < $numDeadlines; $i++) {
                $dueDate = now()->addDays(rand(1, 90));
                $isCompleted = rand(0, 100) < 30; // 30% chance de estar completo
                
                Deadline::create([
                    'case_id' => $case->id,
                    'lawyer_id' => $lawyers->random()->id,
                    'title' => $deadlineTypes[array_rand($deadlineTypes)],
                    'description' => 'Prazo para ' . strtolower($deadlineTypes[array_rand($deadlineTypes)]) . ' no processo ' . $case->case_number,
                    'due_date' => $dueDate,
                    'is_completed' => $isCompleted,
                    'completed_at' => $isCompleted ? $dueDate->subDays(rand(1, 5)) : null,
                    'priority' => ['baixa', 'média', 'alta'][rand(0, 2)],
                ]);
            }
        }
    }
}

