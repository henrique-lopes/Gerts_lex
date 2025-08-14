<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class TenantAuthController extends Controller
{
    /**
     * Exibe o formulário de login
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Processa o login do usuário
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ], [
            'email.required' => 'O campo email é obrigatório.',
            'email.email' => 'Por favor, insira um email válido.',
            'password.required' => 'O campo senha é obrigatório.',
            'password.min' => 'A senha deve ter pelo menos 6 caracteres.',
        ]);

        // Busca o usuário pelo email
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['As credenciais fornecidas não conferem com nossos registros.'],
            ]);
        }

        // Verifica se o usuário tem um tenant associado
        $tenant = $this->getUserTenant($user);
        
        if (!$tenant) {
            throw ValidationException::withMessages([
                'email' => ['Usuário não está associado a nenhum escritório.'],
            ]);
        }

        // Verifica se o tenant está ativo
        if (!$tenant->active) {
            // Redireciona para área de pagamentos se tenant inativo
            Session::put('blocked_tenant', $tenant->id);
            Session::put('blocked_user', $user->id);
            
            return redirect()->route('payment.dashboard')
                ->with('warning', 'Sua conta está suspensa. Regularize seu pagamento para continuar.');
        }

        // Verifica status de pagamento
        $paymentStatus = $this->checkPaymentStatus($tenant);
        
        if ($paymentStatus === 'blocked') {
            Session::put('blocked_tenant', $tenant->id);
            Session::put('blocked_user', $user->id);
            
            return redirect()->route('payment.dashboard')
                ->with('error', 'Pagamento em atraso. Regularize para acessar o sistema.');
        }

        if ($paymentStatus === 'trial_expired') {
            Session::put('blocked_tenant', $tenant->id);
            Session::put('blocked_user', $user->id);
            
            return redirect()->route('payment.dashboard')
                ->with('warning', 'Período de teste expirado. Escolha um plano para continuar.');
        }

        // Login bem-sucedido
        Auth::login($user, $request->boolean('remember'));
        
        // Armazena informações do tenant na sessão
        Session::put('current_tenant', $tenant->id);
        Session::put('tenant_name', $tenant->name);
        Session::put('tenant_domain', $tenant->domain);
        Session::put('subscription_status', $tenant->subscription_status ?? 'active');
        Session::put('subscription_plan', $tenant->subscription_plan ?? 'basic');

        $request->session()->regenerate();

        // Redireciona baseado no status de pagamento
        if ($paymentStatus === 'trial') {
            return redirect()->intended(route('dashboard'))
                ->with('info', 'Você está no período de teste. Considere assinar um plano.');
        }

        return redirect()->intended(route('dashboard'))
            ->with('success', 'Login realizado com sucesso!');
    }

    /**
     * Realiza logout do usuário
     */
    public function logout(Request $request)
    {
        $tenantName = Session::get('tenant_name', 'Escritório');
        
        Auth::logout();
        
        // Limpa todas as informações da sessão
        Session::flush();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', "Logout realizado com sucesso. Até logo, {$tenantName}!");
    }

    /**
     * Busca o tenant associado ao usuário
     */
    private function getUserTenant(User $user)
    {
        // Se o usuário tem tenant_id diretamente
        if ($user->tenant_id) {
            return Tenant::find($user->tenant_id);
        }

        // Busca por domínio no email (ex: user@escritorio.com)
        $emailDomain = explode('@', $user->email)[1] ?? null;
        if ($emailDomain) {
            $tenant = Tenant::where('domain', $emailDomain)->first();
            if ($tenant) {
                return $tenant;
            }
        }

        // Busca por associação através de tabela pivot (se existir)
        // Implementar conforme necessário

        return null;
    }

    /**
     * Verifica o status de pagamento do tenant
     */
    private function checkPaymentStatus(Tenant $tenant)
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
     * Middleware para verificar autenticação e status do tenant
     */
    public function checkTenantAccess(Request $request, $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $tenantId = Session::get('current_tenant');
        if (!$tenantId) {
            Auth::logout();
            return redirect()->route('login')
                ->with('error', 'Sessão inválida. Faça login novamente.');
        }

        $tenant = Tenant::find($tenantId);
        if (!$tenant || !$tenant->active) {
            Auth::logout();
            Session::flush();
            return redirect()->route('login')
                ->with('error', 'Conta suspensa. Entre em contato com o suporte.');
        }

        // Verifica status de pagamento em tempo real
        $paymentStatus = $this->checkPaymentStatus($tenant);
        if (in_array($paymentStatus, ['blocked', 'trial_expired'])) {
            Auth::logout();
            Session::put('blocked_tenant', $tenant->id);
            return redirect()->route('payment.dashboard')
                ->with('error', 'Acesso bloqueado. Regularize seu pagamento.');
        }

        return $next($request);
    }

    /**
     * Obtém informações do tenant atual
     */
    public function getCurrentTenant()
    {
        $tenantId = Session::get('current_tenant');
        if (!$tenantId) {
            return null;
        }

        return Tenant::find($tenantId);
    }

    /**
     * Atualiza status de pagamento do tenant
     */
    public function updatePaymentStatus(Request $request)
    {
        $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'status' => 'required|in:active,trial,past_due,canceled,suspended',
            'plan' => 'nullable|in:basic,premium',
        ]);

        $tenant = Tenant::findOrFail($request->tenant_id);
        
        $tenant->update([
            'subscription_status' => $request->status,
            'subscription_plan' => $request->plan,
            'updated_at' => now(),
        ]);

        // Se ativando, remove da sessão de bloqueio
        if ($request->status === 'active') {
            Session::forget(['blocked_tenant', 'blocked_user']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Status de pagamento atualizado com sucesso.',
            'tenant' => $tenant->fresh(),
        ]);
    }

    /**
     * Verifica se usuário pode acessar área administrativa
     */
    public function canAccessAdmin(User $user = null)
    {
        $user = $user ?? Auth::user();
        
        if (!$user) {
            return false;
        }

        // Verifica se é super admin
        if ($user->email === 'admin@gertslex.com' || $user->hasRole('super_admin')) {
            return true;
        }

        // Verifica se é admin do tenant
        $tenant = $this->getCurrentTenant();
        if ($tenant && $user->hasRole('admin') && $user->tenant_id === $tenant->id) {
            return true;
        }

        return false;
    }

    /**
     * Dashboard de informações da sessão (para debug)
     */
    public function sessionInfo()
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Não autenticado'], 401);
        }

        return response()->json([
            'user' => Auth::user(),
            'tenant' => $this->getCurrentTenant(),
            'session' => [
                'current_tenant' => Session::get('current_tenant'),
                'tenant_name' => Session::get('tenant_name'),
                'tenant_domain' => Session::get('tenant_domain'),
                'subscription_status' => Session::get('subscription_status'),
                'subscription_plan' => Session::get('subscription_plan'),
            ],
            'payment_status' => $this->getCurrentTenant() ? 
                $this->checkPaymentStatus($this->getCurrentTenant()) : null,
        ]);
    }
}
