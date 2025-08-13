<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Lawyer;
use App\Models\User;

class LawyerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $specialties = ['Direito Civil', 'Direito Criminal', 'Direito Trabalhista', 'Direito Empresarial', 'Direito Tributário', 'Direito Previdenciário'];
        $states = ['SP', 'RJ', 'MG', 'RS', 'PR', 'SC', 'BA', 'GO', 'PE', 'CE'];
        
        // Criar 3-5 advogados
        $numLawyers = rand(3, 5);
        
        for ($i = 0; $i < $numLawyers; $i++) {
            // Criar usuário para o advogado
            $user = User::create([
                'name' => 'Dr. ' . ['João', 'Maria', 'Pedro', 'Ana', 'Carlos', 'Fernanda', 'Ricardo', 'Juliana'][array_rand(['João', 'Maria', 'Pedro', 'Ana', 'Carlos', 'Fernanda', 'Ricardo', 'Juliana'])] . ' ' . ['Silva', 'Santos', 'Oliveira', 'Souza', 'Costa', 'Pereira'][array_rand(['Silva', 'Santos', 'Oliveira', 'Souza', 'Costa', 'Pereira'])],
                'email' => 'advogado' . ($i + 1) . '@' . uniqid() . '.com',
                'password' => bcrypt('password123'),
                'email_verified_at' => now(),
            ]);

            // Criar advogado
            Lawyer::create([
                'user_id' => $user->id,
                'oab_number' => rand(100000, 999999),
                'oab_state' => $states[array_rand($states)],
                'specialties' => $specialties[array_rand($specialties)],
            ]);
        }
    }
}
