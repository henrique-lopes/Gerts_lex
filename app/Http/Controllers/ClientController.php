<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $clients = Client::paginate(10);
        return view("tenant.clients.index", compact("clients"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("tenant.clients.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "name" => "required|string|max:255",
            "email" => "nullable|string|email|max:255",
            "phone" => "nullable|string|max:255",
            "cpf_cnpj" => "nullable|string|max:255",
            "address" => "nullable|string",
        ]);

        Client::create([
            "tenant_id" => auth()->user()->tenant_id, // Assuming tenant_id is available via authenticated user
            "name" => $request->name,
            "email" => $request->email,
            "phone" => $request->phone,
            "cpf_cnpj" => $request->cpf_cnpj,
            "address" => $request->address,
        ]);

        return redirect()->route("tenant.clients.index")
            ->with("success", "Cliente criado com sucesso!");
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        return view("tenant.clients.show", compact("client"));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
    {
        return view("tenant.clients.edit", compact("client"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client)
    {
        $request->validate([
            "name" => "required|string|max:255",
            "email" => "nullable|string|email|max:255",
            "phone" => "nullable|string|max:255",
            "cpf_cnpj" => "nullable|string|max:255",
            "address" => "nullable|string",
        ]);

        $client->update([
            "name" => $request->name,
            "email" => $request->email,
            "phone" => $request->phone,
            "cpf_cnpj" => $request->cpf_cnpj,
            "address" => $request->address,
        ]);

        return redirect()->route("tenant.clients.index")
            ->with("success", "Cliente atualizado com sucesso!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        $client->delete();

        return redirect()->route("tenant.clients.index")
            ->with("success", "Cliente removido com sucesso!");
    }
}