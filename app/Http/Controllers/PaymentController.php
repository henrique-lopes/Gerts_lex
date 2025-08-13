<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaymentController extends Controller
{
    /**
     * Dashboard de pagamentos para escritórios bloqueados
     */
    public function dashboard()
    {
        $tenantId = Session::get('blocked_tenant');
        $userId = Session::get('blocked_user');

        // Modo de teste - simula tenant bloqueado
        if (!$tenantId && request()->has('test')) {
            $tenant = Tenant::first();
            $user = User::first();
            
            if ($tenant && $user) {
                $tenantId = $tenant->id;
                $userId = $user->id;
                Session::put('blocked_tenant', $tenantId);
                Session::put('blocked_user', $userId);
            }
        }

        if (!$tenantId) {
            return redirect()->route('login')
                ->with('error', 'Sessão inválida. Faça login novamente.');
        }

        $tenant = Tenant::find($tenantId);
        $user = User::find($userId);

        if (!$tenant || !$user) {
            return redirect()->route('login')
                ->with('error', 'Dados não encontrados. Entre em contato com o suporte.');
        }

        // Dados do escritório
        $tenantData = [
            'id' => $tenant->id,
            'name' => $tenant->name,
            'domain' => $tenant->domain,
            'email' => $tenant->email,
            'phone' => $tenant->phone,
            'subscription_status' => $tenant->subscription_status ?? 'trial',
            'subscription_plan' => $tenant->subscription_plan ?? 'basic',
            'trial_ends_at' => $tenant->trial_ends_at,
            'last_payment_at' => $tenant->last_payment_at,
            'next_billing_date' => $tenant->next_billing_date,
        ];

        // Histórico de pagamentos (simulado)
        $paymentHistory = $this->getPaymentHistory($tenant);

        // Planos disponíveis
        $plans = $this->getAvailablePlans();

        // Status de pagamento atual
        $paymentStatus = $this->getPaymentStatus($tenant);

        // Dados para o dashboard
        $dashboardData = [
            'tenant' => $tenantData,
            'user' => $user,
            'payment_status' => $paymentStatus,
            'payment_history' => $paymentHistory,
            'plans' => $plans,
            'statistics' => $this->getPaymentStatistics($tenant),
        ];

        return view('payment.dashboard', compact('dashboardData'));
    }

    /**
     * Processa pagamento
     */
    public function process(Request $request)
    {
        $request->validate([
            'plan' => 'required|in:basic,premium',
            'payment_method' => 'required|in:credit_card,debit_card,pix',
            'tenant_id' => 'required|exists:tenants,id',
        ]);

        $tenant = Tenant::findOrFail($request->tenant_id);
        $plan = $request->plan;
        $paymentMethod = $request->payment_method;

        // Simula processamento de pagamento
        $paymentResult = $this->processPayment($tenant, $plan, $paymentMethod, $request);

        if ($paymentResult['success']) {
            // Atualiza status do tenant
            $tenant->update([
                'subscription_status' => 'active',
                'subscription_plan' => $plan,
                'last_payment_at' => now(),
                'next_billing_date' => now()->addMonth(),
                'trial_ends_at' => null,
                'active' => true,
            ]);

            // Remove da sessão de bloqueio
            Session::forget(['blocked_tenant', 'blocked_user']);

            // Registra pagamento no histórico
            $this->recordPayment($tenant, $plan, $paymentMethod, $paymentResult);

            return response()->json([
                'success' => true,
                'message' => 'Pagamento processado com sucesso!',
                'redirect_url' => route('login'),
                'payment_id' => $paymentResult['payment_id'],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $paymentResult['message'],
            'error_code' => $paymentResult['error_code'] ?? null,
        ], 400);
    }

    /**
     * Gera QR Code PIX
     */
    public function generatePixQrCode(Request $request)
    {
        $request->validate([
            'plan' => 'required|in:basic,premium',
            'tenant_id' => 'required|exists:tenants,id',
        ]);

        $tenant = Tenant::findOrFail($request->tenant_id);
        $plan = $request->plan;
        $amount = $this->getPlanPrice($plan);

        // Gera dados do PIX
        $pixData = $this->generatePixData($tenant, $amount, $plan);

        return response()->json([
            'success' => true,
            'pix_data' => $pixData,
            'qr_code' => $pixData['qr_code'],
            'copy_paste' => $pixData['copy_paste'],
            'amount' => $amount,
            'expires_at' => now()->addMinutes(30)->toISOString(),
        ]);
    }

    /**
     * Webhook para confirmação de pagamento PIX
     */
    public function pixWebhook(Request $request)
    {
        // Valida webhook (implementar assinatura)
        $paymentId = $request->input('payment_id');
        $status = $request->input('status');
        $tenantId = $request->input('tenant_id');

        if ($status === 'approved') {
            $tenant = Tenant::find($tenantId);
            if ($tenant) {
                $tenant->update([
                    'subscription_status' => 'active',
                    'last_payment_at' => now(),
                    'next_billing_date' => now()->addMonth(),
                    'active' => true,
                ]);

                // Registra pagamento
                $this->recordPayment($tenant, $tenant->subscription_plan, 'pix', [
                    'payment_id' => $paymentId,
                    'amount' => $this->getPlanPrice($tenant->subscription_plan),
                    'status' => 'approved',
                ]);
            }
        }

        return response()->json(['success' => true]);
    }

    /**
     * Obtém histórico de pagamentos
     */
    private function getPaymentHistory(Tenant $tenant)
    {
        // Simula histórico de pagamentos
        return [
            [
                'id' => 'pay_001',
                'date' => '2024-12-13',
                'amount' => 149.90,
                'plan' => 'basic',
                'method' => 'credit_card',
                'status' => 'approved',
                'description' => 'Plano Básico - Dezembro 2024',
            ],
            [
                'id' => 'pay_002',
                'date' => '2024-11-13',
                'amount' => 149.90,
                'plan' => 'basic',
                'method' => 'pix',
                'status' => 'approved',
                'description' => 'Plano Básico - Novembro 2024',
            ],
            [
                'id' => 'pay_003',
                'date' => '2024-10-13',
                'amount' => 149.90,
                'plan' => 'basic',
                'method' => 'credit_card',
                'status' => 'failed',
                'description' => 'Plano Básico - Outubro 2024',
            ],
        ];
    }

    /**
     * Obtém planos disponíveis
     */
    private function getAvailablePlans()
    {
        return [
            'basic' => [
                'name' => 'Plano Básico',
                'price' => 149.90,
                'features' => [
                    'Até 3 advogados',
                    'Gestão de clientes ilimitada',
                    'Gestão de casos básica',
                    'Agenda simples',
                    'Documentos básicos',
                    'Suporte por email',
                ],
                'popular' => false,
            ],
            'premium' => [
                'name' => 'Plano Premium',
                'price' => 299.90,
                'features' => [
                    'Advogados ilimitados',
                    'Gestão completa de clientes',
                    'Gestão avançada de casos',
                    'Agenda com notificações',
                    'Documentos avançados',
                    'Relatórios e analytics',
                    'Integrações',
                    'Suporte prioritário',
                ],
                'popular' => true,
            ],
        ];
    }

    /**
     * Obtém status de pagamento
     */
    private function getPaymentStatus(Tenant $tenant)
    {
        $status = $tenant->subscription_status ?? 'trial';
        $trialEndsAt = $tenant->trial_ends_at;
        $nextBilling = $tenant->next_billing_date;

        $statusData = [
            'current_status' => $status,
            'status_label' => $this->getStatusLabel($status),
            'status_color' => $this->getStatusColor($status),
            'trial_ends_at' => $trialEndsAt,
            'next_billing_date' => $nextBilling,
            'days_until_billing' => $nextBilling ? now()->diffInDays($nextBilling, false) : null,
            'is_trial' => $status === 'trial',
            'is_active' => $status === 'active',
            'is_blocked' => in_array($status, ['past_due', 'canceled', 'suspended']),
        ];

        return $statusData;
    }

    /**
     * Obtém estatísticas de pagamento
     */
    private function getPaymentStatistics(Tenant $tenant)
    {
        return [
            'total_paid' => 449.70, // Simulado
            'payments_count' => 3,
            'average_payment' => 149.90,
            'last_payment_date' => '2024-12-13',
            'subscription_since' => '2024-10-13',
            'months_subscribed' => 3,
        ];
    }

    /**
     * Processa pagamento (simulado)
     */
    private function processPayment(Tenant $tenant, string $plan, string $method, Request $request)
    {
        $amount = $this->getPlanPrice($plan);

        // Simula processamento baseado no método
        switch ($method) {
            case 'credit_card':
                return $this->processCreditCard($request, $amount);
            case 'debit_card':
                return $this->processDebitCard($request, $amount);
            case 'pix':
                return $this->processPix($tenant, $amount, $plan);
            default:
                return ['success' => false, 'message' => 'Método de pagamento inválido'];
        }
    }

    /**
     * Processa cartão de crédito
     */
    private function processCreditCard(Request $request, float $amount)
    {
        // Simula processamento
        $success = rand(1, 10) > 2; // 80% de sucesso

        if ($success) {
            return [
                'success' => true,
                'payment_id' => 'cc_' . uniqid(),
                'amount' => $amount,
                'method' => 'credit_card',
                'status' => 'approved',
            ];
        }

        return [
            'success' => false,
            'message' => 'Cartão recusado. Verifique os dados ou tente outro cartão.',
            'error_code' => 'card_declined',
        ];
    }

    /**
     * Processa cartão de débito
     */
    private function processDebitCard(Request $request, float $amount)
    {
        // Simula processamento
        $success = rand(1, 10) > 3; // 70% de sucesso

        if ($success) {
            return [
                'success' => true,
                'payment_id' => 'db_' . uniqid(),
                'amount' => $amount,
                'method' => 'debit_card',
                'status' => 'approved',
            ];
        }

        return [
            'success' => false,
            'message' => 'Saldo insuficiente ou cartão inválido.',
            'error_code' => 'insufficient_funds',
        ];
    }

    /**
     * Processa PIX
     */
    private function processPix(Tenant $tenant, float $amount, string $plan)
    {
        // PIX sempre gera QR Code para pagamento
        return [
            'success' => true,
            'payment_id' => 'pix_' . uniqid(),
            'amount' => $amount,
            'method' => 'pix',
            'status' => 'pending',
            'requires_qr_code' => true,
        ];
    }

    /**
     * Gera dados do PIX
     */
    private function generatePixData(Tenant $tenant, float $amount, string $plan)
    {
        $pixKey = 'contato@gertslex.com'; // Chave PIX do Gert's Lex
        $description = "Gert's Lex - {$plan} - {$tenant->name}";
        
        // Simula geração de QR Code PIX
        $qrCodeData = base64_encode("PIX|{$pixKey}|{$amount}|{$description}");
        
        return [
            'pix_key' => $pixKey,
            'amount' => $amount,
            'description' => $description,
            'qr_code' => "data:image/png;base64,{$qrCodeData}",
            'copy_paste' => "00020126580014BR.GOV.BCB.PIX0136{$pixKey}520400005303986540{$amount}5802BR5913Gerts Lex6009SAO PAULO62070503***6304",
            'expires_at' => now()->addMinutes(30),
        ];
    }

    /**
     * Registra pagamento no histórico
     */
    private function recordPayment(Tenant $tenant, string $plan, string $method, array $paymentData)
    {
        // Implementar registro em tabela de pagamentos
        DB::table('payment_history')->insert([
            'tenant_id' => $tenant->id,
            'payment_id' => $paymentData['payment_id'],
            'amount' => $paymentData['amount'],
            'plan' => $plan,
            'method' => $method,
            'status' => $paymentData['status'],
            'processed_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Obtém preço do plano
     */
    private function getPlanPrice(string $plan): float
    {
        $prices = [
            'basic' => 149.90,
            'premium' => 299.90,
        ];

        return $prices[$plan] ?? 149.90;
    }

    /**
     * Obtém label do status
     */
    private function getStatusLabel(string $status): string
    {
        $labels = [
            'active' => 'Ativo',
            'trial' => 'Período de Teste',
            'past_due' => 'Pagamento em Atraso',
            'canceled' => 'Cancelado',
            'suspended' => 'Suspenso',
        ];

        return $labels[$status] ?? 'Desconhecido';
    }

    /**
     * Obtém cor do status
     */
    private function getStatusColor(string $status): string
    {
        $colors = [
            'active' => 'green',
            'trial' => 'blue',
            'past_due' => 'yellow',
            'canceled' => 'red',
            'suspended' => 'red',
        ];

        return $colors[$status] ?? 'gray';
    }
}
