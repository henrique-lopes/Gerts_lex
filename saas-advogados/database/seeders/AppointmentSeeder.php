<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Appointment;
use App\Models\Lawyer;
use App\Models\CaseModel;

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lawyers = Lawyer::all();
        $cases = CaseModel::all();

        if ($lawyers->isEmpty() || $cases->isEmpty()) {
            $this->command->warn("Advogados ou casos não encontrados. Pulando a criação de compromissos.");
            return;
        }

        $appointmentTypes = ['Audiência', 'Reunião com Cliente', 'Perícia', 'Depoimento', 'Mediação', 'Conciliação', 'Consulta', 'Diligência'];
        $locations = ['Fórum Central', 'Escritório', 'Tribunal de Justiça', 'Cartório', 'Delegacia', 'Cliente - Empresa', 'Videoconferência'];
        
        // Criar 8-15 compromissos
        $numAppointments = rand(8, 15);
        
        for ($i = 0; $i < $numAppointments; $i++) {
            $appointmentDate = now()->addDays(rand(-30, 60));
            $appointmentTime = rand(8, 18) . ':' . ['00', '30'][rand(0, 1)];
            
            Appointment::create([
                'lawyer_id' => $lawyers->random()->id,
                'case_id' => $cases->random()->id,
                'title' => $appointmentTypes[array_rand($appointmentTypes)],
                'description' => 'Compromisso relacionado ao caso. ' . $appointmentTypes[array_rand($appointmentTypes)] . ' agendada.',
                'appointment_date' => $appointmentDate->format('Y-m-d'),
                'appointment_time' => $appointmentTime,
                'location' => $locations[array_rand($locations)],
                'status' => ['agendado', 'realizado', 'cancelado'][array_rand(['agendado', 'realizado', 'cancelado'])],
                'notes' => 'Compromisso criado automaticamente pelo seeder.',
            ]);
        }
    }
}
