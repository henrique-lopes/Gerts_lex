<?php

namespace App\Http\Controllers;

use App\Models\Deadline;
use App\Models\CaseModel;
use Illuminate\Http\Request;

class DeadlineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $deadlines = Deadline::with("case")->paginate(10);
        return view("tenant.deadlines.index", compact("deadlines"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $cases = CaseModel::all();
        return view("tenant.deadlines.create", compact("cases"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "title" => "required|string|max:255",
            "description" => "nullable|string",
            "due_date" => "required|date",
            "status" => "required|string|in:pending,completed,overdue",
            "case_id" => "nullable|exists:cases,id",
        ]);

        Deadline::create([
            "tenant_id" => auth()->user()->tenant_id,
            "title" => $request->title,
            "description" => $request->description,
            "due_date" => $request->due_date,
            "status" => $request->status,
            "case_id" => $request->case_id,
        ]);

        return redirect()->route("tenant.deadlines.index")
            ->with("success", "Prazo criado com sucesso!");
    }

    /**
     * Display the specified resource.
     */
    public function show(Deadline $deadline)
    {
        return view("tenant.deadlines.show", compact("deadline"));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Deadline $deadline)
    {
        $cases = CaseModel::all();
        return view("tenant.deadlines.edit", compact("deadline", "cases"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Deadline $deadline)
    {
        $request->validate([
            "title" => "required|string|max:255",
            "description" => "nullable|string",
            "due_date" => "required|date",
            "status" => "required|string|in:pending,completed,overdue",
            "case_id" => "nullable|exists:cases,id",
        ]);

        $deadline->update([
            "title" => $request->title,
            "description" => $request->description,
            "due_date" => $request->due_date,
            "status" => $request->status,
            "case_id" => $request->case_id,
        ]);

        return redirect()->route("tenant.deadlines.index")
            ->with("success", "Prazo atualizado com sucesso!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Deadline $deadline)
    {
        $deadline->delete();

        return redirect()->route("tenant.deadlines.index")
            ->with("success", "Prazo removido com sucesso!");
    }
}