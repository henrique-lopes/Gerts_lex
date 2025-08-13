<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lawyer;
use App\Models\CaseModel;
use App\Models\Appointment;
use App\Models\Fee;
use App\Models\Deadline;
use Carbon\Carbon;

class LawyerDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $lawyer = Lawyer::where("user_id", $user->id)->first();

        if (!$lawyer) {
            abort(403, "Você não está associado a um advogado neste tenant.");
        }

        // Casos atrelados ao advogado
        $cases = CaseModel::where("lawyer_id", $lawyer->id)->get();

        // Próximos compromissos
        $upcomingAppointments = Appointment::where("lawyer_id", $lawyer->id)
                                        ->where("start_time", ">=", Carbon::now())
                                        ->orderBy("start_time")
                                        ->limit(5)
                                        ->get();

        // Prazos próximos
        $upcomingDeadlines = Deadline::where("lawyer_id", $lawyer->id) // Assuming deadline can be linked to a lawyer
                                    ->where("due_date", ">=", Carbon::now())
                                    ->where("status", "pending")
                                    ->orderBy("due_date")
                                    ->limit(5)
                                    ->get();

        // Total de honorários recebidos (exemplo simples)
        $totalFeesReceived = Fee::where("lawyer_id", $lawyer->id)
                                ->where("status", "paid")
                                ->sum("amount");

        // Montante total de honorários (exemplo simples)
        $totalFeesAmount = Fee::where("lawyer_id", $lawyer->id)
                             ->sum("amount");

        return view("tenant.lawyer_dashboard", compact("lawyer", "cases", "upcomingAppointments", "upcomingDeadlines", "totalFeesReceived", "totalFeesAmount"));
    }
}
