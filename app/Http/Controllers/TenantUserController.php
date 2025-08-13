<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class TenantUserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index()
    {
        $users = User::paginate(10);
        return view("tenant.users.index", compact("users"));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        return view("tenant.users.create");
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "name" => "required|string|max:255",
            "email" => "required|string|email|max:255|unique:users",
            "password" => "required|string|min:8|confirmed",
        ]);

        User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => Hash::make($request->password),
        ]);

        return redirect()->route("tenant.users.index")
            ->with("success", "Usuário criado com sucesso!");
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        return view("tenant.users.show", compact("user"));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        return view("tenant.users.edit", compact("user"));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            "name" => "required|string|max:255",
            "email" => ["required", "string", "email", "max:255", Rule::unique("users")->ignore($user->id)],
            "password" => "nullable|string|min:8|confirmed",
        ]);

        $user->update([
            "name" => $request->name,
            "email" => $request->email,
            "password" => $request->password ? Hash::make($request->password) : $user->password,
        ]);

        return redirect()->route("tenant.users.index")
            ->with("success", "Usuário atualizado com sucesso!");
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route("tenant.users.index")
            ->with("success", "Usuário removido com sucesso!");
    }
}