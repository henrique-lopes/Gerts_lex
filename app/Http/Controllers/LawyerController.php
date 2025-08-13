<?php

namespace App\Http\Controllers;

use App\Models\Lawyer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class LawyerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lawyers = Lawyer::with("user")->paginate(10);
        $tenant = \Spatie\Multitenancy\Models\Tenant::current();
        return view("tenant.lawyers.index-simple", compact("lawyers", "tenant"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("tenant.lawyers.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "name" => "required|string|max:255",
            "email" => "required|string|email|max:255|unique:users",
            "password" => "required|string|min:8|confirmed",
            "oab_number" => "nullable|string|max:255",
            "oab_state" => "nullable|string|max:255",
            "specialties" => "nullable|string|max:255",
        ]);

        $user = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => Hash::make($request->password),
        ]);

        $user->lawyer()->create([
            "oab_number" => $request->oab_number,
            "oab_state" => $request->oab_state,
            "specialties" => $request->specialties,
        ]);

        return redirect()->route("tenant.lawyers.index")
            ->with("success", "Advogado criado com sucesso!");
    }

    /**
     * Display the specified resource.
     */
    public function show(Lawyer $lawyer)
    {
        return view("tenant.lawyers.show", compact("lawyer"));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Lawyer $lawyer)
    {
        return view("tenant.lawyers.edit", compact("lawyer"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Lawyer $lawyer)
    {
        $request->validate([
            "name" => "required|string|max:255",
            "email" => ["required", "string", "email", "max:255", Rule::unique("users")->ignore($lawyer->user_id)],
            "password" => "nullable|string|min:8|confirmed",
            "oab_number" => "nullable|string|max:255",
            "oab_state" => "nullable|string|max:255",
            "specialties" => "nullable|string|max:255",
        ]);

        $lawyer->user->update([
            "name" => $request->name,
            "email" => $request->email,
            "password" => $request->password ? Hash::make($request->password) : $lawyer->user->password,
        ]);

        $lawyer->update([
            "oab_number" => $request->oab_number,
            "oab_state" => $request->oab_state,
            "specialties" => $request->specialties,
        ]);

        return redirect()->route("tenant.lawyers.index")
            ->with("success", "Advogado atualizado com sucesso!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lawyer $lawyer)
    {
        $lawyer->user->delete(); // This will also delete the lawyer due to cascade delete

        return redirect()->route("tenant.lawyers.index")
            ->with("success", "Advogado removido com sucesso!");
    }
}