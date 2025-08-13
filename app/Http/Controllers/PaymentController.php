<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\User;
use App\Services\PaymentGatewayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $paymentGateway;

    public function __construct(PaymentGatewayService $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }

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
            'email' => $tenant->email ?? 'Não informado',
            'phone' => $tenant->phone ?? 'Não informado',
            'subscription_plan' => $tenant->subscription_plan ?? 'basic'
        ];

        // Status de pagamento
        $paymentStatus = [
            'status_label' => $this->getStatusLabel($tenant->subscription_status ?? 'trial'),
            'is_trial' => ($tenant->subscription_status ?? 'trial') === 'trial',
            'is_blocked' => in_array($tenant->subscription_status ?? 'trial', ['past_due', 'suspended', 'canceled']),
            'next_billing_date' => $tenant->next_billing_date ?? null
        ];

        // Histórico de pagamentos
        $paymentHistory = $this->getPaymentHistory($tenantId);

        // Planos disponíveis
        $plans = config('payment.plans');

        // Dados consolidados para a view
        $dashboardData = [
            'tenant' => $tenantData,
            'payment_status' => $paymentStatus,
            'payment_history' => $paymentHistory,
            'plans' => $plans
        ];

        return view('payment.dashboard', compact('dashboardData'));
    }

    /**
     * Processa o pagamento
     */
    public function process(Request $request)
    {
        $request->validate([
            'plan' => 'required|in:basic,premium',
            'payment_method' => 'required|in:credit_card,debit_card,pix'
        ]);

        $tenantId = Session::get('blocked_tenant');
        $userId = Session::get('blocked_user');

        if (!$tenantId || !$userId) {
            return response()->json([
                'success' => false,
                'error' => 'Sessão inválida'
            ], 400);
        }

        $tenant = Tenant::find($tenantId);
        $user = User::find($userId);

        if (!$tenant || !$user) {
            return response()->json([
                'success' => false,
                'error' => 'Dados não encontrados'
            ], 404);
        }

        try {
            DB::beginTransaction();

            // Dados do plano selecionado
            $planConfig = config("payment.plans.{$request->plan}");
            
            if (!$planConfig) {
                return response()->json([
                    'success' => false,
                    'error' => 'Plano inválido'
                ], 400);
            }

            // Dados do pagamento
            $paymentData = [
                'title' => $planConfig['name'],
                'description' => "Assinatura mensal - {$planConfig['name']} - {$tenant->name}",
                'amount' => $planConfig['price'],
                'method' => $request->payment_method,
                'payer' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'document_type' => 'CPF',
                    'document_number' => ''
                ],
                'external_reference' => "GLEX_{$tenantId}_{$request->plan}_" . time(),
                'success_url' => route('payment.success'),
                'failure_url' => route('payment.failure'),
                'pending_url' => route('payment.pending'),
                'webhook_url' => route('payment.webhook')
            ];

            // Processa o pagamento baseado no método
            $result = $this->processPaymentByMethod($request->payment_method, $paymentData, $request);

            if ($result['success']) {
                // Registra o pagamento no histórico
                $this->recordPaymentHistory([
                    'tenant_id' => $tenantId,
                    'user_id' => $userId,
                    'plan' => $request->plan,
                    'amount' => $planConfig['price'],
                    'method' => $request->payment_method,
                    'payment_id' => $result['payment_id'],
                    'status' => $result['status'] ?? 'pending',
                    'external_reference' => $paymentData['external_reference']
                ]);

                // Se aprovado imediatamente, atualiza o tenant
                if (isset($result['status']) && $result['status'] === 'approved') {
                    $this->updateTenantSubscription($tenant, $request->plan);
                    
                    // Remove da sessão de bloqueio
                    Session::forget(['blocked_tenant', 'blocked_user']);
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'payment_id' => $result['payment_id'],
                    'status' => $result['status'] ?? 'pending',
                    'qr_code' => $result['qr_code'] ?? null,
                    'qr_code_base64' => $result['qr_code_base64'] ?? null,
                    'redirect_url' => $result['redirect_url'] ?? null,
                    'message' => $this->getPaymentMessage($result['status'] ?? 'pending', $request->payment_method)
                ]);
            }

            DB::rollback();

            return response()->json([
                'success' => false,
                'error' => $result['error'] ?? 'Erro ao processar pagamento'
            ], 400);

        } catch (\Exception $e) {
            DB::rollback();
            
            Log::error('Payment Processing Error', [
                'tenant_id' => $tenantId,
                'plan' => $request->plan,
                'method' => $request->payment_method,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Erro interno. Tente novamente.'
            ], 500);
        }
    }

    /**
     * Processa pagamento baseado no método
     */
    private function processPaymentByMethod(string $method, array $paymentData, Request $request)
    {
        $gateway = config('payment.default_gateway', 'simulation');

        if ($gateway === 'simulation' || config('payment.simulation.enabled', true)) {
            return $this->paymentGateway->simulatePayment(array_merge($paymentData, ['method' => $method]));
        }

        switch ($method) {
            case 'pix':
                return $this->paymentGateway->processPixPayment($paymentData);
                
            case 'credit_card':
                return $this->processCardPaymentWithValidation($paymentData, $request, 'credit');
                
            case 'debit_card':
                return $this->processCardPaymentWithValidation($paymentData, $request, 'debit');
                
            default:
                return [
                    'success' => false,
                    'error' => 'Método de pagamento não suportado'
                ];
        }
    }

    /**
     * Processa pagamento com cartão com validação
     */
    private function processCardPaymentWithValidation(array $paymentData, Request $request, string $cardType)
    {
        // Validação específica para cartão
        $request->validate([
            'card_number' => 'required|string|min:13|max:19',
            'card_holder' => 'required|string|min:3',
            'expiry_month' => 'required|integer|min:1|max:12',
            'expiry_year' => 'required|integer|min:' . date('Y'),
            'cvv' => 'required|string|size:3',
            'installments' => $cardType === 'credit' ? 'required|integer|min:1|max:12' : 'nullable'
        ]);

        // Adiciona dados do cartão ao paymentData
        $paymentData['card_token'] = 'sim_' . $cardType . '_token_' . time(); // Em produção, usar token real
        $paymentData['installments'] = $request->installments ?? 1;

        return $this->paymentGateway->processCardPayment($paymentData, $cardType);
    }

    /**
     * API específica para pagamento PIX
     */
    public function processPixPayment(Request $request)
    {
        try {
            $request->validate([
                'plan' => 'required|in:basic,premium',
                'tenant_id' => 'required|integer'
            ]);

            $plan = config('payment.plans.' . $request->plan);
            $tenant = \DB::table('tenants')->where('id', $request->tenant_id)->first();

            if (!$tenant) {
                return response()->json([
                    'success' => false,
                    'error' => 'Escritório não encontrado'
                ], 404);
            }

            $paymentData = [
                'amount' => $plan['price'],
                'description' => "Assinatura {$plan['name']} - {$tenant->name}",
                'payer' => [
                    'email' => $tenant->email,
                    'name' => $tenant->name
                ],
                'external_reference' => "tenant_{$tenant->id}_" . time(),
                'webhook_url' => route('payment.webhook')
            ];

            $result = $this->paymentGateway->processPixPayment($paymentData);

            if ($result['success']) {
                // Registrar no histórico
                $this->recordPaymentHistory([
                    'tenant_id' => $tenant->id,
                    'payment_id' => $result['payment_id'],
                    'amount' => $plan['price'],
                    'method' => 'pix',
                    'status' => $result['status'],
                    'plan' => $request->plan,
                    'external_reference' => $paymentData['external_reference']
                ]);

                return response()->json([
                    'success' => true,
                    'payment_id' => $result['payment_id'],
                    'qr_code' => $result['qr_code'] ?? null,
                    'qr_code_url' => $result['qr_code_url'] ?? null,
                    'pix_key' => $result['pix_key'] ?? null,
                    'expires_at' => $result['expires_at'] ?? null,
                    'simulated' => $result['simulated'] ?? false
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => $result['error'] ?? 'Erro ao processar pagamento PIX'
            ], 400);

        } catch (\Exception $e) {
            Log::error('PIX Payment Error', [
                'message' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Erro interno do servidor'
            ], 500);
        }
    }

    /**
     * API específica para pagamento com cartão de crédito
     */
    public function processCreditCardPayment(Request $request)
    {
        try {
            $request->validate([
                'plan' => 'required|in:basic,premium',
                'tenant_id' => 'required|integer',
                'card_number' => 'required|string',
                'card_holder' => 'required|string',
                'expiry_month' => 'required|integer|min:1|max:12',
                'expiry_year' => 'required|integer|min:' . date('Y'),
                'cvv' => 'required|string|size:3',
                'installments' => 'required|integer|min:1|max:12'
            ]);

            $plan = config('payment.plans.' . $request->plan);
            $tenant = \DB::table('tenants')->where('id', $request->tenant_id)->first();

            if (!$tenant) {
                return response()->json([
                    'success' => false,
                    'error' => 'Escritório não encontrado'
                ], 404);
            }

            $paymentData = [
                'amount' => $plan['price'],
                'description' => "Assinatura {$plan['name']} - {$tenant->name}",
                'payer' => [
                    'email' => $tenant->email,
                    'name' => $tenant->name
                ],
                'card_token' => 'sim_credit_token_' . time(), // Em produção, usar token real
                'installments' => $request->installments,
                'external_reference' => "tenant_{$tenant->id}_" . time(),
                'webhook_url' => route('payment.webhook')
            ];

            $result = $this->paymentGateway->processCardPayment($paymentData, 'credit');

            if ($result['success'] || $result['status'] === 'approved') {
                // Registrar no histórico
                $this->recordPaymentHistory([
                    'tenant_id' => $tenant->id,
                    'payment_id' => $result['payment_id'],
                    'amount' => $plan['price'],
                    'method' => 'credit_card',
                    'status' => $result['status'],
                    'plan' => $request->plan,
                    'installments' => $request->installments,
                    'external_reference' => $paymentData['external_reference']
                ]);

                // Se aprovado, atualizar tenant
                if ($result['status'] === 'approved') {
                    $this->updateTenantSubscriptionById($tenant->id, $request->plan);
                }

                return response()->json([
                    'success' => true,
                    'payment_id' => $result['payment_id'],
                    'status' => $result['status'],
                    'authorization_code' => $result['authorization_code'] ?? null,
                    'installments' => $request->installments,
                    'simulated' => $result['simulated'] ?? false
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => $this->getPaymentErrorMessage($result['status_detail'] ?? 'unknown'),
                'status_detail' => $result['status_detail'] ?? null
            ], 400);

        } catch (\Exception $e) {
            Log::error('Credit Card Payment Error', [
                'message' => $e->getMessage(),
                'request' => $request->except(['card_number', 'cvv']) // Não logar dados sensíveis
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Erro interno do servidor'
            ], 500);
        }
    }

    /**
     * API específica para pagamento com cartão de débito
     */
    public function processDebitCardPayment(Request $request)
    {
        try {
            $request->validate([
                'plan' => 'required|in:basic,premium',
                'tenant_id' => 'required|integer',
                'card_number' => 'required|string',
                'card_holder' => 'required|string',
                'expiry_month' => 'required|integer|min:1|max:12',
                'expiry_year' => 'required|integer|min:' . date('Y'),
                'cvv' => 'required|string|size:3'
            ]);

            $plan = config('payment.plans.' . $request->plan);
            $tenant = \DB::table('tenants')->where('id', $request->tenant_id)->first();

            if (!$tenant) {
                return response()->json([
                    'success' => false,
                    'error' => 'Escritório não encontrado'
                ], 404);
            }

            $paymentData = [
                'amount' => $plan['price'],
                'description' => "Assinatura {$plan['name']} - {$tenant->name}",
                'payer' => [
                    'email' => $tenant->email,
                    'name' => $tenant->name
                ],
                'card_token' => 'sim_debit_token_' . time(), // Em produção, usar token real
                'external_reference' => "tenant_{$tenant->id}_" . time(),
                'webhook_url' => route('payment.webhook')
            ];

            $result = $this->paymentGateway->processCardPayment($paymentData, 'debit');

            if ($result['success'] || $result['status'] === 'approved') {
                // Registrar no histórico
                $this->recordPaymentHistory([
                    'tenant_id' => $tenant->id,
                    'payment_id' => $result['payment_id'],
                    'amount' => $plan['price'],
                    'method' => 'debit_card',
                    'status' => $result['status'],
                    'plan' => $request->plan,
                    'external_reference' => $paymentData['external_reference']
                ]);

                // Se aprovado, atualizar tenant
                if ($result['status'] === 'approved') {
                    $this->updateTenantSubscriptionById($tenant->id, $request->plan);
                }

                return response()->json([
                    'success' => true,
                    'payment_id' => $result['payment_id'],
                    'status' => $result['status'],
                    'authorization_code' => $result['authorization_code'] ?? null,
                    'simulated' => $result['simulated'] ?? false
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => $this->getPaymentErrorMessage($result['status_detail'] ?? 'unknown'),
                'status_detail' => $result['status_detail'] ?? null
            ], 400);

        } catch (\Exception $e) {
            Log::error('Debit Card Payment Error', [
                'message' => $e->getMessage(),
                'request' => $request->except(['card_number', 'cvv']) // Não logar dados sensíveis
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Erro interno do servidor'
            ], 500);
        }
    }

    /**
     * Atualiza assinatura do tenant por ID
     */
    private function updateTenantSubscriptionById(int $tenantId, string $plan)
    {
        \DB::table('tenants')
            ->where('id', $tenantId)
            ->update([
                'subscription_status' => 'active',
                'subscription_plan' => $plan,
                'last_payment_date' => now(),
                'next_billing_date' => now()->addMonth(),
                'updated_at' => now()
            ]);

        Log::info('Tenant Subscription Updated', [
            'tenant_id' => $tenantId,
            'plan' => $plan
        ]);
    }

    /**
     * Converte códigos de erro em mensagens amigáveis
     */
    private function getPaymentErrorMessage(string $statusDetail)
    {
        $messages = [
            'cc_rejected_insufficient_amount' => 'Saldo insuficiente no cartão',
            'cc_rejected_bad_filled_security_code' => 'Código de segurança inválido',
            'cc_rejected_bad_filled_date' => 'Data de validade inválida',
            'cc_rejected_high_risk' => 'Transação rejeitada por segurança',
            'cc_rejected_blacklist' => 'Cartão bloqueado',
            'cc_rejected_card_disabled' => 'Cartão desabilitado',
            'cc_rejected_call_for_authorize' => 'Entre em contato com o banco',
            'cc_rejected_card_error' => 'Erro no cartão',
            'cc_rejected_duplicated_payment' => 'Pagamento duplicado',
            'cc_rejected_invalid_installments' => 'Número de parcelas inválido'
        ];

        return $messages[$statusDetail] ?? 'Erro no processamento do pagamento';
    }

    /**
     * Webhook para receber notificações de pagamento
     */
    public function webhook(Request $request)
    {
        try {
            Log::info('Payment Webhook Received', $request->all());

            $webhookData = $request->all();
            $result = $this->paymentGateway->processWebhook($webhookData);

            if ($result['success']) {
                $paymentData = $result['payment_data'];
                
                // Atualiza o status do pagamento no histórico
                $this->updatePaymentStatus($paymentData['payment_id'], $paymentData['status']);

                // Se aprovado, atualiza a assinatura do tenant
                if ($paymentData['status'] === 'approved') {
                    $this->processApprovedPayment($paymentData['payment_id']);
                }

                return response()->json(['status' => 'ok']);
            }

            return response()->json(['error' => 'Webhook processing failed'], 400);

        } catch (\Exception $e) {
            Log::error('Webhook Error', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return response()->json(['error' => 'Internal error'], 500);
        }
    }

    /**
     * Página de sucesso do pagamento
     */
    public function success(Request $request)
    {
        $paymentId = $request->get('payment_id');
        $externalReference = $request->get('external_reference');

        if ($paymentId) {
            // Consulta o status do pagamento
            $result = $this->paymentGateway->getPaymentStatus($paymentId);
            
            if ($result['success'] && $result['status'] === 'approved') {
                $this->processApprovedPayment($paymentId);
                
                return redirect()->route('tenant.dashboard')
                    ->with('success', 'Pagamento aprovado! Sua assinatura foi ativada.');
            }
        }

        return redirect()->route('payment.dashboard')
            ->with('info', 'Aguardando confirmação do pagamento...');
    }

    /**
     * Página de falha do pagamento
     */
    public function failure(Request $request)
    {
        return redirect()->route('payment.dashboard')
            ->with('error', 'Pagamento não foi aprovado. Tente novamente ou escolha outro método.');
    }

    /**
     * Página de pagamento pendente
     */
    public function pending(Request $request)
    {
        return redirect()->route('payment.dashboard')
            ->with('info', 'Pagamento pendente. Aguarde a confirmação.');
    }

    /**
     * Obtém histórico de pagamentos
     */
    private function getPaymentHistory(int $tenantId)
    {
        // Simula histórico para demonstração
        return [
            [
                'description' => 'Plano Básico - Dezembro 2024',
                'date' => '2024-12-13',
                'amount' => 149.90,
                'status' => 'approved',
                'status_class' => 'success'
            ],
            [
                'description' => 'Plano Básico - Novembro 2024',
                'date' => '2024-11-13',
                'amount' => 149.90,
                'status' => 'approved',
                'status_class' => 'success'
            ],
            [
                'description' => 'Plano Básico - Outubro 2024',
                'date' => '2024-10-13',
                'amount' => 149.90,
                'status' => 'rejected',
                'status_class' => 'danger'
            ]
        ];
    }

    /**
     * Registra pagamento no histórico
     */
    private function recordPaymentHistory(array $data)
    {
        // Aqui você salvaria no banco de dados
        // Por enquanto, apenas loga
        Log::info('Payment Recorded', $data);
    }

    /**
     * Atualiza status do pagamento
     */
    private function updatePaymentStatus(string $paymentId, string $status)
    {
        // Aqui você atualizaria o status no banco
        Log::info('Payment Status Updated', [
            'payment_id' => $paymentId,
            'status' => $status
        ]);
    }

    /**
     * Processa pagamento aprovado
     */
    private function processApprovedPayment(string $paymentId)
    {
        // Busca o pagamento no histórico e atualiza o tenant
        // Por enquanto, simula a aprovação
        Log::info('Payment Approved', ['payment_id' => $paymentId]);
    }

    /**
     * Atualiza assinatura do tenant
     */
    private function updateTenantSubscription(Tenant $tenant, string $plan)
    {
        $tenant->update([
            'subscription_plan' => $plan,
            'subscription_status' => 'active',
            'trial_ends_at' => null,
            'next_billing_date' => now()->addMonth()
        ]);

        Log::info('Tenant Subscription Updated', [
            'tenant_id' => $tenant->id,
            'plan' => $plan
        ]);
    }

    /**
     * Obtém label do status
     */
    private function getStatusLabel(string $status)
    {
        $labels = [
            'trial' => 'Período de Teste',
            'active' => 'Ativo',
            'past_due' => 'Pagamento em Atraso',
            'canceled' => 'Cancelado',
            'suspended' => 'Suspenso'
        ];

        return $labels[$status] ?? 'Desconhecido';
    }

    /**
     * Obtém mensagem do pagamento
     */
    private function getPaymentMessage(string $status, string $method)
    {
        if ($method === 'pix') {
            return $status === 'pending' 
                ? 'PIX gerado com sucesso! Escaneie o QR Code ou copie o código para pagar.'
                : 'Pagamento PIX processado.';
        }

        $messages = [
            'approved' => 'Pagamento aprovado com sucesso!',
            'pending' => 'Pagamento em processamento. Aguarde a confirmação.',
            'rejected' => 'Pagamento rejeitado. Verifique os dados e tente novamente.'
        ];

        return $messages[$status] ?? 'Status do pagamento: ' . $status;
    }
}

