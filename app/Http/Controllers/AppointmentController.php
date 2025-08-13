<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\CaseModel;
use App\Models\Lawyer;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $appointments = Appointment::with(["case", "lawyer"])->paginate(10);
        return view("tenant.appointments.index", compact("appointments"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $cases = CaseModel::all();
        $lawyers = Lawyer::with("user")->get();
        return view("tenant.appointments.create", compact("cases", "lawyers"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "title" => "required|string|max:255",
            "description" => "nullable|string",
            "start_time" => "required|date",
            "end_time" => "required|date|after:start_time",
            "type" => "required|string|max:255",
            "case_id" => "nullable|exists:cases,id",
            "lawyer_id" => "nullable|exists:lawyers,id",
        ]);

        Appointment::create([
            "tenant_id" => auth()->user()->tenant_id,
            "title" => $request->title,
            "description" => $request->description,
            "start_time" => $request->start_time,
            "end_time" => $request->end_time,
            "type" => $request->type,
            "case_id" => $request->case_id,
            "lawyer_id" => $request->lawyer_id,
        ]);

        return redirect()->route("tenant.appointments.index")
            ->with("success", "Compromisso criado com sucesso!");
    }

    /**
     * Display the specified resource.
     */
    public function show(Appointment $appointment)
    {
        return view("tenant.appointments.show", compact("appointment"));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Appointment $appointment)
    {
        $cases = CaseModel::all();
        $lawyers = Lawyer::with("user")->get();
        return view("tenant.appointments.edit", compact("appointment", "cases", "lawyers"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Appointment $appointment)
    {
        $request->validate([
            "title" => "required|string|max:255",
            "description" => "nullable|string",
            "start_time" => "required|date",
            "end_time" => "required|date|after:start_time",
            "type" => "required|string|max:255",
            "case_id" => "nullable|exists:cases,id",
            "lawyer_id" => "nullable|exists:lawyers,id",
        ]);

        $appointment->update([
            "title" => $request->title,
            "description" => $request->description,
            "start_time" => $request->start_time,
            "end_time" => $request->end_time,
            "type" => $request->type,
            "case_id" => $request->case_id,
            "lawyer_id" => $request->lawyer_id,
        ]);

        return redirect()->route("tenant.appointments.index")
            ->with("success", "Compromisso atualizado com sucesso!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Appointment $appointment)
    {
        $appointment->delete();

        return redirect()->route("tenant.appointments.index")
            ->with("success", "Compromisso removido com sucesso!");
    }
}