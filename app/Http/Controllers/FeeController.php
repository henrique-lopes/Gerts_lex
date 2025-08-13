<?php

namespace App\Http\Controllers;

use App\Models\Fee;
use App\Models\CaseModel;
use Illuminate\Http\Request;

class FeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $fees = Fee::with("case")->paginate(10);
        return view("tenant.fees.index", compact("fees"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $cases = CaseModel::all();
        return view("tenant.fees.create", compact("cases"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "case_id" => "required|exists:cases,id",
            "amount" => "required|numeric|min:0",
            "type" => "required|string|in:fixed,percentage",
            "status" => "required|string|in:pending,paid,overdue",
            "due_date" => "nullable|date",
            "paid_date" => "nullable|date",
        ]);

        Fee::create($request->all());

        return redirect()->route("tenant.fees.index")
            ->with("success", "Honorário criado com sucesso!");
    }

    /**
     * Display the specified resource.
     */
    public function show(Fee $fee)
    {
        return view("tenant.fees.show", compact("fee"));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Fee $fee)
    {
        $cases = CaseModel::all();
        return view("tenant.fees.edit", compact("fee", "cases"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Fee $fee)
    {
        $request->validate([
            "case_id" => "required|exists:cases,id",
            "amount" => "required|numeric|min:0",
            "type" => "required|string|in:fixed,percentage",
            "status" => "required|string|in:pending,paid,overdue",
            "due_date" => "nullable|date",
            "paid_date" => "nullable|date",
        ]);

        $fee->update($request->all());

        return redirect()->route("tenant.fees.index")
            ->with("success", "Honorário atualizado com sucesso!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Fee $fee)
    {
        $fee->delete();

        return redirect()->route("tenant.fees.index")
            ->with("success", "Honorário removido com sucesso!");
    }
}