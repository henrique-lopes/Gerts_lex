<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\TenantInvitationMail;

class TenantInvitationController extends Controller
{
    /**
     * Show the form for inviting a new user.
     */
    public function create()
    {
        return view("tenant.invitations.create");
    }

    /**
     * Store a newly created invitation in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "email" => "required|string|email|max:255|unique:users",
        ]);

        $token = Str::random(32);

        // Store invitation (e.g., in a dedicated invitations table or cache)
        // For simplicity, we\'ll just send the email here.
        // In a real app, you\'d save the token, invited_email, tenant_id, expiry_date
        // and then verify it when the user registers.

        Mail::to($request->email)->send(new TenantInvitationMail($token, $request->email));

        return redirect()->route("tenant.users.index")
            ->with("success", "Convite enviado para " . $request->email . " com sucesso!");
    }

    /**
     * Show the registration form for an invited user.
     */
    public function showRegistrationForm(Request $request)
    {
        // In a real app, you\'d verify the token and tenant here.
        // For simplicity, we\'ll just pass the email.
        return view("auth.register-invited", ["email" => $request->query("email")]);
    }
}