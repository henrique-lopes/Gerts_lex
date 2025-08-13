<?php

namespace App\Http\Controllers;

use App\Models\CaseModel;
use App\Models\Client;
use App\Models\Lawyer;
use Illuminate\Http\Request;

class CaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cases = CaseModel::with(["client", "lawyer"])->paginate(10);
        return view("tenant.cases.index", compact("cases"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clients = Client::all();
        $lawyers = Lawyer::with("user")->get();
        return view("tenant.cases.create", compact("clients", "lawyers"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "client_id" => "required|exists:clients,id",
            "lawyer_id" => "nullable|exists:lawyers,id",
            "case_number" => "nullable|string|max:255",
            "case_type" => "required|string|max:255",
            "court" => "nullable|string|max:255",
            "status" => "required|string|max:255",
            "description" => "nullable|string",
        ]);

        CaseModel::create([
            "tenant_id" => auth()->user()->tenant_id,
            "client_id" => $request->client_id,
            "lawyer_id" => $request->lawyer_id,
            "case_number" => $request->case_number,
            "case_type" => $request->case_type,
            "court" => $request->court,
            "status" => $request->status,
            "description" => $request->description,
        ]);

        return redirect()->route("tenant.cases.index")
            ->with("success", "Caso criado com sucesso!");
    }

    /**
     * Display the specified resource.
     */
    public function show(CaseModel $case)
    {
        return view("tenant.cases.show", compact("case"));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CaseModel $case)
    {
        $clients = Client::all();
        $lawyers = Lawyer::with("user")->get();
        return view("tenant.cases.edit", compact("case", "clients", "lawyers"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CaseModel $case)
    {
        $request->validate([
            "client_id" => "required|exists:clients,id",
            "lawyer_id" => "nullable|exists:lawyers,id",
            "case_number" => "nullable|string|max:255",
            "case_type" => "required|string|max:255",
            "court" => "nullable|string|max:255",
            "status" => "required|string|max:255",
            "description" => "nullable|string",
        ]);

        $case->update([
            "client_id" => $request->client_id,
            "lawyer_id" => $request->lawyer_id,
            "case_number" => $request->case_number,
            "case_type" => $request->case_type,
            "court" => $request->court,
            "status" => $request->status,
            "description" => $request->description,
        ]);

        return redirect()->route("tenant.cases.index")
            ->with("success", "Caso atualizado com sucesso!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CaseModel $case)
    {
        $case->delete();

        return redirect()->route("tenant.cases.index")
            ->with("success", "Caso removido com sucesso!");
    }
}