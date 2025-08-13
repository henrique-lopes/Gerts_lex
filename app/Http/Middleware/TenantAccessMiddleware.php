<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class TenantAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verifica se o usuário está autenticado
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Você precisa estar logado para acessar esta área.');
        }

        // Obtém o tenant da sessão
        $tenantId = Session::get('current_tenant');
        
        if (!$tenantId) {
            Auth::logout();
            Session::flush();
            return redirect()->route('login')
                ->with('error', 'Sessão inválida. Faça login novamente.');
        }

        // Verifica se o tenant existe e está ativo
        $tenant = Tenant::find($tenantId);
        
        if (!$tenant) {
            Auth::logout();
            Session::flush();
            return redirect()->route('login')
                ->with('error', 'Escritório não encontrado. Entre em contato com o suporte.');
        }

        if (!$tenant->active) {
            Auth::logout();
            Session::flush();
            return redirect()->route('login')
                ->with('error', 'Conta suspensa. Entre em contato com o suporte.');
        }

        // Verifica status de pagamento em tempo real
        $paymentStatus = $this->checkPaymentStatus($tenant);
        
        // Se o pagamento está bloqueado, redireciona para área de pagamentos
        if (in_array($paymentStatus, ['blocked', 'trial_expired'])) {
            Auth::logout();
            Session::put('blocked_tenant', $tenant->id);
            Session::put('blocked_user', Auth::id());
            
            $message = $paymentStatus === 'blocked' 
                ? 'Pagamento em atraso. Regularize para acessar o sistema.'
                : 'Período de teste expirado. Escolha um plano para continuar.';
                
            return redirect()->route('payment.dashboard')
                ->with('error', $message);
        }

        // Atualiza informações do tenant na sessão se necessário
        if (Session::get('tenant_name') !== $tenant->name) {
            Session::put('tenant_name', $tenant->name);
            Session::put('tenant_domain', $tenant->domain);
            Session::put('subscription_status', $tenant->subscription_status ?? 'active');
            Session::put('subscription_plan', $tenant->subscription_plan ?? 'basic');
        }

        // Adiciona informações do tenant ao request para uso nos controllers
        $request->merge([
            'current_tenant' => $tenant,
            'payment_status' => $paymentStatus,
        ]);

        return $next($request);
    }

    /**
     * Verifica o status de pagamento do tenant
     */
    private function checkPaymentStatus(Tenant $tenant): string
    {
        $subscriptionStatus = $tenant->subscription_status ?? 'trial';
        $trialEndsAt = $tenant->trial_ends_at;

        switch ($subscriptionStatus) {
            case 'active':
                return 'active';
                
            case 'trial':
                if ($trialEndsAt && now()->isAfter($trialEndsAt)) {
                    return 'trial_expired';
                }
                return 'trial';
                
            case 'past_due':
                // Período de carência de 7 dias
                $gracePeriod = now()->subDays(7);
                if ($tenant->updated_at && $tenant->updated_at->isBefore($gracePeriod)) {
                    return 'blocked';
                }
                return 'past_due';
                
            case 'canceled':
            case 'suspended':
                return 'blocked';
                
            default:
                return 'trial';
        }
    }

    /**
     * Middleware específico para área administrativa
     */
    public function handleAdmin(Request $request, Closure $next): Response
    {
        // Primeiro verifica acesso básico do tenant
        $response = $this->handle($request, function($req) { return $req; });
        
        if ($response instanceof \Illuminate\Http\RedirectResponse) {
            return $response;
        }

        // Verifica se o usuário tem permissão administrativa
        $user = Auth::user();
        $tenant = $request->get('current_tenant');

        // Super admin sempre tem acesso
        if ($user->email === 'admin@gertslex.com') {
            return $next($request);
        }

        // Verifica se é admin do tenant atual
        if ($user->hasRole && $user->hasRole('admin') && $user->tenant_id === $tenant->id) {
            return $next($request);
        }

        // Verifica se é proprietário do tenant
        if (isset($tenant->owner_id) && $tenant->owner_id === $user->id) {
            return $next($request);
        }

        return redirect()->route('tenant.dashboard')
            ->with('error', 'Você não tem permissão para acessar a área administrativa.');
    }

    /**
     * Middleware para verificar plano premium
     */
    public function handlePremium(Request $request, Closure $next): Response
    {
        // Primeiro verifica acesso básico do tenant
        $response = $this->handle($request, function($req) { return $req; });
        
        if ($response instanceof \Illuminate\Http\RedirectResponse) {
            return $response;
        }

        $tenant = $request->get('current_tenant');
        $subscriptionPlan = $tenant->subscription_plan ?? 'basic';

        if ($subscriptionPlan !== 'premium') {
            return redirect()->route('tenant.dashboard')
                ->with('warning', 'Esta funcionalidade está disponível apenas no plano Premium. Faça upgrade para acessar.');
        }

        return $next($request);
    }

    /**
     * Middleware para verificar período de teste
     */
    public function handleTrial(Request $request, Closure $next): Response
    {
        // Primeiro verifica acesso básico do tenant
        $response = $this->handle($request, function($req) { return $req; });
        
        if ($response instanceof \Illuminate\Http\RedirectResponse) {
            return $response;
        }

        $paymentStatus = $request->get('payment_status');
        
        if ($paymentStatus === 'trial') {
            // Adiciona aviso sobre período de teste
            Session::flash('trial_warning', 'Você está no período de teste. Algumas funcionalidades podem ser limitadas.');
        }

        return $next($request);
    }
}
