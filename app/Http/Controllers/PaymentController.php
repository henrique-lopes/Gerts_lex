<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\User;
use App\Services\PaymentGatewayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

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
        if (request()->has('test')) {
            $tenant = new Tenant([
                'id' => 1,
                'name' => 'Empresa Demo',
                'domain' => 'demo',
                'email' => 'admin@demo.com',
                'phone' => '(11) 99999-9999',
                'subscription_status' => 'trial',
                'subscription_plan' => 'basic',
                'trial_ends_at' => now()->addDays(7),
                'next_billing_date' => now()->addMonth()
            ]);
        } else {
            if (!$tenantId) {
                return redirect()->route('login')->with('error', 'Acesso não autorizado.');
            }
            $tenant = Tenant::find($tenantId);
            if (!$tenant) {
                return redirect()->route('login')->with('error', 'Escritório não encontrado.');
            }
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
        $paymentHistory = $this->getPaymentHistory($tenant);

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
     * Processar pagamento genérico
     */
    public function process(Request $request)
    {
        $request->validate([
            'plan' => 'required|in:basic,premium',
            'payment_method' => 'required|in:credit_card,debit_card,pix',
        ]);

        $plan = $request->plan;
        $paymentMethod = $request->payment_method;

        // Redireciona para o método específico
        switch ($paymentMethod) {
            case 'pix':
                return $this->processPixPayment($request);
            case 'credit_card':
                return $this->processCreditCardPayment($request);
            case 'debit_card':
                return $this->processDebitCardPayment($request);
            default:
                return back()->with('error', 'Método de pagamento inválido.');
        }
    }

    /**
     * Processar pagamento PIX
     */
    public function processPixPayment(Request $request)
    {
        $request->validate([
            'plan' => 'required|in:basic,premium',
        ]);

        $plan = $request->plan;
        $planConfig = config("payment.plans.{$plan}");
        
        if (!$planConfig) {
            return back()->with('error', 'Plano não encontrado.');
        }

        try {
            $result = $this->paymentGateway->processPixPayment([
                'amount' => $planConfig['price'],
                'plan' => $plan,
                'tenant_id' => Session::get('blocked_tenant', 1)
            ]);

            if ($result['success']) {
                return redirect()->route('payment.success')
                    ->with('success', 'Pagamento PIX processado com sucesso!')
                    ->with('payment_data', $result['data']);
            } else {
                return redirect()->route('payment.failure')
                    ->with('error', $result['message']);
            }
        } catch (\Exception $e) {
            Log::error('PIX Payment Error', [
                'message' => $e->getMessage(),
                'plan' => $plan
            ]);

            return redirect()->route('payment.failure')
                ->with('error', 'Erro ao processar pagamento PIX.');
        }
    }

    /**
     * Processar pagamento com cartão de crédito
     */
    public function processCreditCardPayment(Request $request)
    {
        $request->validate([
            'plan' => 'required|in:basic,premium',
            'card_number' => 'required|string',
            'card_holder' => 'required|string',
            'card_expiry' => 'required|string',
            'card_cvv' => 'required|string',
            'installments' => 'required|integer|min:1|max:12'
        ]);

        $plan = $request->plan;
        $planConfig = config("payment.plans.{$plan}");
        
        if (!$planConfig) {
            return back()->with('error', 'Plano não encontrado.');
        }

        try {
            $result = $this->paymentGateway->processCreditCardPayment([
                'amount' => $planConfig['price'],
                'plan' => $plan,
                'tenant_id' => Session::get('blocked_tenant', 1),
                'card_data' => [
                    'number' => $request->card_number,
                    'holder' => $request->card_holder,
                    'expiry' => $request->card_expiry,
                    'cvv' => $request->card_cvv
                ],
                'installments' => $request->installments
            ]);

            if ($result['success']) {
                return redirect()->route('payment.success')
                    ->with('success', 'Pagamento com cartão de crédito processado com sucesso!')
                    ->with('payment_data', $result['data']);
            } else {
                return redirect()->route('payment.failure')
                    ->with('error', $result['message']);
            }
        } catch (\Exception $e) {
            Log::error('Credit Card Payment Error', [
                'message' => $e->getMessage(),
                'plan' => $plan
            ]);

            return redirect()->route('payment.failure')
                ->with('error', 'Erro ao processar pagamento com cartão de crédito.');
        }
    }

    /**
     * Processar pagamento com cartão de débito
     */
    public function processDebitCardPayment(Request $request)
    {
        $request->validate([
            'plan' => 'required|in:basic,premium',
            'card_number' => 'required|string',
            'card_holder' => 'required|string',
            'card_expiry' => 'required|string',
            'card_cvv' => 'required|string'
        ]);

        $plan = $request->plan;
        $planConfig = config("payment.plans.{$plan}");
        
        if (!$planConfig) {
            return back()->with('error', 'Plano não encontrado.');
        }

        try {
            $result = $this->paymentGateway->processDebitCardPayment([
                'amount' => $planConfig['price'],
                'plan' => $plan,
                'tenant_id' => Session::get('blocked_tenant', 1),
                'card_data' => [
                    'number' => $request->card_number,
                    'holder' => $request->card_holder,
                    'expiry' => $request->card_expiry,
                    'cvv' => $request->card_cvv
                ]
            ]);

            if ($result['success']) {
                return redirect()->route('payment.success')
                    ->with('success', 'Pagamento com cartão de débito processado com sucesso!')
                    ->with('payment_data', $result['data']);
            } else {
                return redirect()->route('payment.failure')
                    ->with('error', $result['message']);
            }
        } catch (\Exception $e) {
            Log::error('Debit Card Payment Error', [
                'message' => $e->getMessage(),
                'plan' => $plan
            ]);

            return redirect()->route('payment.failure')
                ->with('error', 'Erro ao processar pagamento com cartão de débito.');
        }
    }

    /**
     * Página de sucesso do pagamento
     */
    public function success(Request $request)
    {
        return redirect()->route('payment.dashboard')
            ->with('success', 'Pagamento processado com sucesso! Seu plano foi ativado.');
    }

    /**
     * Página de falha do pagamento
     */
    public function failure(Request $request)
    {
        return redirect()->route('payment.dashboard')
            ->with('error', 'Falha no pagamento. Tente novamente.');
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
     * Webhook para confirmação de pagamentos
     */
    public function webhook(Request $request)
    {
        try {
            $paymentId = $request->input('payment_id');
            $status = $request->input('status');
            $tenantId = $request->input('tenant_id');

            if ($status === 'approved') {
                $tenant = Tenant::find($tenantId);
                if ($tenant) {
                    $tenant->update([
                        'subscription_status' => 'active',
                        'last_payment_at' => now(),
                        'next_billing_date' => now()->addMonth()
                    ]);

                    Log::info('Payment Webhook Processed', [
                        'payment_id' => $paymentId,
                        'tenant_id' => $tenantId,
                        'status' => $status
                    ]);
                }
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Webhook Error', [
                'message' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * Obter label do status de pagamento
     */
    private function getStatusLabel($status)
    {
        $labels = [
            'trial' => 'Período de Teste',
            'active' => 'Ativo',
            'past_due' => 'Pagamento em Atraso',
            'suspended' => 'Suspenso',
            'canceled' => 'Cancelado'
        ];

        return $labels[$status] ?? 'Status Desconhecido';
    }

    /**
     * Obter histórico de pagamentos simulado
     */
    private function getPaymentHistory(Tenant $tenant)
    {
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
}

