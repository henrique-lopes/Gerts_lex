<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CaseDocument;
use App\Models\CaseModel;

class CaseDocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cases = CaseModel::all();
        
        if ($cases->isEmpty()) {
            return;
        }

        $documentTypes = [
            'Petição Inicial',
            'Procuração',
            'Documentos Pessoais',
            'Comprovante de Residência',
            'Contrato',
            'Certidão',
            'Laudo Pericial',
            'Sentença',
            'Recurso',
            'Manifestação',
            'Ata de Audiência',
            'Despacho',
            'Decisão Interlocutória'
        ];

        foreach ($cases as $case) {
            // Criar 3-8 documentos por caso
            $numDocuments = rand(3, 8);
            
            for ($i = 0; $i < $numDocuments; $i++) {
                $documentType = $documentTypes[array_rand($documentTypes)];
                
                CaseDocument::create([
                    'case_id' => $case->id,
                    'name' => $documentType . ' - ' . $case->case_number,
                    'description' => 'Documento do tipo ' . strtolower($documentType) . ' referente ao caso ' . $case->title,
                    'file_path' => 'documents/case_' . $case->id . '/' . strtolower(str_replace(' ', '_', $documentType)) . '_' . $i . '.pdf',
                    'file_size' => rand(50000, 2000000), // Entre 50KB e 2MB
                    'mime_type' => 'application/pdf',
                    'uploaded_by' => 'Sistema',
                    'uploaded_at' => now()->subDays(rand(1, 30)),
                ]);
            }
        }
    }
}

