<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Client;
use App\Models\Lawyer;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lawyers = Lawyer::all();

        if ($lawyers->isEmpty()) {
            $this->command->warn("Nenhum advogado encontrado. Pulando a criação de clientes.");
            return;
        }

        // Obter o tenant atual
        $tenantId = config('app.current_tenant_id');
        if (!$tenantId) {
            $this->command->warn("Nenhum tenant ativo. Pulando a criação de clientes.");
            return;
        }

        $names = ['João Silva', 'Maria Santos', 'Pedro Oliveira', 'Ana Costa', 'Carlos Pereira', 'Fernanda Lima', 'Ricardo Souza', 'Juliana Alves'];
        $companies = ['Tech Solutions Ltda', 'Inovação Digital S.A.', 'Consultoria Empresarial', 'Comércio e Serviços', 'Indústria Nacional'];
        
        // Criar 5-8 clientes
        $numClients = rand(5, 8);
        
        for ($i = 0; $i < $numClients; $i++) {
            $isCompany = rand(0, 1);
            
            Client::create([
                'tenant_id' => $tenantId,
                'name' => $isCompany ? $companies[array_rand($companies)] : $names[array_rand($names)],
                'email' => 'cliente' . ($i + 1) . '@email.com',
                'phone' => '(11) ' . rand(90000, 99999) . '-' . rand(1000, 9999),
                'cpf_cnpj' => $isCompany ? 
                    sprintf('%02d.%03d.%03d/%04d-%02d', rand(10, 99), rand(100, 999), rand(100, 999), rand(1000, 9999), rand(10, 99)) :
                    sprintf('%03d.%03d.%03d-%02d', rand(100, 999), rand(100, 999), rand(100, 999), rand(10, 99)),
                'address' => 'Rua ' . ['das Flores', 'dos Pinheiros', 'da Liberdade', 'do Comércio'][array_rand(['das Flores', 'dos Pinheiros', 'da Liberdade', 'do Comércio'])] . ', ' . rand(100, 999) . ' - ' . ['São Paulo', 'Rio de Janeiro', 'Belo Horizonte', 'Porto Alegre'][array_rand(['São Paulo', 'Rio de Janeiro', 'Belo Horizonte', 'Porto Alegre'])] . ' - ' . ['SP', 'RJ', 'MG', 'RS'][array_rand(['SP', 'RJ', 'MG', 'RS'])] . ' - ' . rand(10000, 99999) . '-' . rand(100, 999),
            ]);
        }
    }
}
