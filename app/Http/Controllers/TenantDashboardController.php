<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Multitenancy\Models\Tenant;
use Laravel\Cashier\Cashier;

class TenantDashboardController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware("auth");
    }

    /**
     * Show the tenant dashboard
     */
    public function index()
    {
        $tenant = Tenant::current();
<<<<<<< HEAD
        
=======

>>>>>>> 75d276e55e8071ac5037fb79472c91094dcb9e2e
        if (!$tenant) {
            abort(404, "Tenant não encontrado");
        }

        // Get some basic stats for the dashboard
        $stats = [
            "users_count" => \App\Models\User::count(),
            "lawyers_count" => \App\Models\Lawyer::count(),
            "clients_count" => \App\Models\Client::count(),
            "cases_count" => \App\Models\CaseModel::count(),
            "fees_count" => \App\Models\Fee::count(),
            "appointments_count" => \App\Models\Appointment::count(),
            "deadlines_count" => \App\Models\Deadline::count(),
            "documents_count" => \App\Models\CaseDocument::count(),
<<<<<<< HEAD
            "trial_days_left" => $tenant->trial_ends_at ? 
=======
            "trial_days_left" => $tenant->trial_ends_at ?
>>>>>>> 75d276e55e8071ac5037fb79472c91094dcb9e2e
                now()->diffInDays($tenant->trial_ends_at, false) : null,
            "subscription_status" => $tenant->subscription_status ?? 'trial',
            "subscription_plan" => $tenant->subscription_plan ?? 'basic',
        ];

        return view("tenant.dashboard-simple", compact("tenant", "stats"));
    }

    /**
     * Show tenant settings
     */
    public function settings()
    {
        $tenant = Tenant::current();
<<<<<<< HEAD
        
=======

>>>>>>> 75d276e55e8071ac5037fb79472c91094dcb9e2e
        if (!$tenant) {
            abort(404, "Tenant não encontrado");
        }

        return view("tenant.settings", compact("tenant"));
    }

    /**
     * Update tenant settings
     */
    public function updateSettings(Request $request)
    {
        $tenant = Tenant::current();
<<<<<<< HEAD
        
=======

>>>>>>> 75d276e55e8071ac5037fb79472c91094dcb9e2e
        if (!$tenant) {
            abort(404, "Tenant não encontrado");
        }

        $request->validate([
            "name" => "required|string|max:255",
            "settings" => "nullable|array",
        ]);

        $tenant->update([
            "name" => $request->name,
            "settings" => $request->settings ?? [],
        ]);

        return redirect()->route("tenant.settings")
            ->with("success", "Configurações atualizadas com sucesso!");
    }

    /**
     * Show subscription information
     */
    public function subscription()
    {
        $tenant = Tenant::current();
<<<<<<< HEAD
        
=======

>>>>>>> 75d276e55e8071ac5037fb79472c91094dcb9e2e
        if (!$tenant) {
            abort(404, "Tenant não encontrado");
        }

        // Define your Stripe product prices here
        $plans = [
            "price_1Pj2g2LdJ2g2g2g2g2g2g2g2" => [
                "name" => "Básico",
                "description" => "Até 5 usuários, Suporte por email, 10GB de armazenamento",
                "price" => "R$ 29,90/mês",
            ],
            "price_1Pj2g3LdJ2g2g2g2g2g2g2g3" => [
                "name" => "Profissional",
                "description" => "Até 25 usuários, Suporte prioritário, 100GB de armazenamento, Relatórios avançados",
                "price" => "R$ 59,90/mês",
            ],
            "price_1Pj2g4LdJ2g2g2g2g2g2g2g4" => [
                "name" => "Empresarial",
                "description" => "Usuários ilimitados, Suporte 24/7, Armazenamento ilimitado, API personalizada",
                "price" => "R$ 129,90/mês",
            ],
        ];

        return view("tenant.subscription", compact("tenant", "plans"));
    }

    public function subscribe(Request $request)
    {
        $tenant = Tenant::current();

        if (!$tenant) {
            abort(404, "Tenant não encontrado");
        }

        $priceId = $request->input("price_id");

        return $tenant->newSubscription("default", $priceId)->checkout([
            "success_url" => route("tenant.subscription"),
            "cancel_url" => route("tenant.subscription"),
        ]);
    }

    public function cancelSubscription()
    {
        $tenant = Tenant::current();

        if (!$tenant) {
            abort(404, "Tenant não encontrado");
        }

        $tenant->subscription("default")->cancel();

        return redirect()->route("tenant.subscription")
            ->with("success", "Sua assinatura foi cancelada.");
    }

    public function resumeSubscription()
    {
        $tenant = Tenant::current();

        if (!$tenant) {
            abort(404, "Tenant não encontrado");
        }

        $tenant->subscription("default")->resume();

        return redirect()->route("tenant.subscription")
            ->with("success", "Sua assinatura foi reativada.");
    }

    public function updatePaymentMethod(Request $request)
    {
        $tenant = Tenant::current();

        if (!$tenant) {
            abort(404, "Tenant não encontrado");
        }

        $tenant->updateDefaultPaymentMethod($request->payment_method);

        return redirect()->route("tenant.subscription")
            ->with("success", "Método de pagamento atualizado com sucesso.");
    }
}
<<<<<<< HEAD

=======
>>>>>>> 75d276e55e8071ac5037fb79472c91094dcb9e2e
