<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class PaymentGatewayService
{
    private $accessToken;
    private $baseUrl;
    private $isProduction;

    public function __construct()
    {
        $this->accessToken = config('payment.mercadopago.access_token');
        $this->isProduction = config('payment.mercadopago.production', false);
        $this->baseUrl = $this->isProduction 
            ? 'https://api.mercadopago.com' 
            : 'https://api.mercadopago.com'; // Mesmo endpoint, diferencia pelo token
    }

    /**
     * Cria uma preferência de pagamento no Mercado Pago
     */
    public function createPaymentPreference(array $paymentData)
    {
        try {
            $preference = [
                'items' => [
                    [
                        'title' => $paymentData['title'],
                        'description' => $paymentData['description'],
                        'quantity' => 1,
                        'currency_id' => 'BRL',
                        'unit_price' => (float) $paymentData['amount']
                    ]
                ],
                'payer' => [
                    'name' => $paymentData['payer']['name'],
                    'email' => $paymentData['payer']['email'],
                    'identification' => [
                        'type' => $paymentData['payer']['document_type'] ?? 'CPF',
                        'number' => $paymentData['payer']['document_number'] ?? ''
                    ]
                ],
                'payment_methods' => [
                    'excluded_payment_methods' => [],
                    'excluded_payment_types' => [],
                    'installments' => 12
                ],
                'back_urls' => [
                    'success' => $paymentData['success_url'],
                    'failure' => $paymentData['failure_url'],
                    'pending' => $paymentData['pending_url']
                ],
                'auto_return' => 'approved',
                'external_reference' => $paymentData['external_reference'],
                'notification_url' => $paymentData['webhook_url'],
                'expires' => true,
                'expiration_date_from' => now()->toISOString(),
                'expiration_date_to' => now()->addHours(24)->toISOString()
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl . '/checkout/preferences', $preference);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            Log::error('Mercado Pago API Error', [
                'status' => $response->status(),
                'response' => $response->json()
            ]);

            return [
                'success' => false,
                'error' => 'Erro ao criar preferência de pagamento',
                'details' => $response->json()
            ];

        } catch (Exception $e) {
            Log::error('Payment Gateway Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => 'Erro interno do gateway de pagamento'
            ];
        }
    }

    /**
     * Cria um pagamento PIX
     */
    public function createPixPayment(array $paymentData)
    {
        try {
            $payment = [
                'transaction_amount' => (float) $paymentData['amount'],
                'description' => $paymentData['description'],
                'payment_method_id' => 'pix',
                'payer' => [
                    'email' => $paymentData['payer']['email'],
                    'first_name' => $paymentData['payer']['name'],
                    'identification' => [
                        'type' => $paymentData['payer']['document_type'] ?? 'CPF',
                        'number' => $paymentData['payer']['document_number'] ?? ''
                    ]
                ],
                'external_reference' => $paymentData['external_reference'],
                'notification_url' => $paymentData['webhook_url']
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json',
                'X-Idempotency-Key' => uniqid()
            ])->post($this->baseUrl . '/v1/payments', $payment);

            if ($response->successful()) {
                $data = $response->json();
                
                return [
                    'success' => true,
                    'payment_id' => $data['id'],
                    'status' => $data['status'],
                    'qr_code' => $data['point_of_interaction']['transaction_data']['qr_code'] ?? null,
                    'qr_code_base64' => $data['point_of_interaction']['transaction_data']['qr_code_base64'] ?? null,
                    'ticket_url' => $data['point_of_interaction']['transaction_data']['ticket_url'] ?? null,
                    'expires_at' => $data['date_of_expiration'] ?? null
                ];
            }

            Log::error('PIX Payment Error', [
                'status' => $response->status(),
                'response' => $response->json()
            ]);

            return [
                'success' => false,
                'error' => 'Erro ao criar pagamento PIX',
                'details' => $response->json()
            ];

        } catch (Exception $e) {
            Log::error('PIX Payment Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => 'Erro interno ao processar PIX'
            ];
        }
    }

    /**
     * Consulta status de um pagamento
     */
    public function getPaymentStatus(string $paymentId)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken
            ])->get($this->baseUrl . '/v1/payments/' . $paymentId);

            if ($response->successful()) {
                $data = $response->json();
                
                return [
                    'success' => true,
                    'payment_id' => $data['id'],
                    'status' => $data['status'],
                    'status_detail' => $data['status_detail'],
                    'amount' => $data['transaction_amount'],
                    'date_created' => $data['date_created'],
                    'date_approved' => $data['date_approved'] ?? null
                ];
            }

            return [
                'success' => false,
                'error' => 'Pagamento não encontrado'
            ];

        } catch (Exception $e) {
            Log::error('Payment Status Exception', [
                'payment_id' => $paymentId,
                'message' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'Erro ao consultar status do pagamento'
            ];
        }
    }

    /**
     * Processa webhook do Mercado Pago
     */
    public function processWebhook(array $webhookData)
    {
        try {
            if (!isset($webhookData['data']['id'])) {
                return [
                    'success' => false,
                    'error' => 'Webhook inválido'
                ];
            }

            $paymentId = $webhookData['data']['id'];
            $paymentStatus = $this->getPaymentStatus($paymentId);

            if ($paymentStatus['success']) {
                return [
                    'success' => true,
                    'payment_data' => $paymentStatus
                ];
            }

            return $paymentStatus;

        } catch (Exception $e) {
            Log::error('Webhook Processing Exception', [
                'webhook_data' => $webhookData,
                'message' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'Erro ao processar webhook'
            ];
        }
    }

    /**
     * Obtém métodos de pagamento disponíveis
     */
    public function getPaymentMethods()
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken
            ])->get($this->baseUrl . '/v1/payment_methods');

            if ($response->successful()) {
                return [
                    'success' => true,
                    'methods' => $response->json()
                ];
            }

            return [
                'success' => false,
                'error' => 'Erro ao obter métodos de pagamento'
            ];

        } catch (Exception $e) {
            Log::error('Payment Methods Exception', [
                'message' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'Erro interno ao obter métodos'
            ];
        }
    }

    /**
     * Cria token de cartão (para uso com Mercado Pago JS SDK)
     */
    public function createCardToken(array $cardData)
    {
        try {
            $token = [
                'card_number' => $cardData['number'],
                'security_code' => $cardData['security_code'],
                'expiration_month' => $cardData['expiration_month'],
                'expiration_year' => $cardData['expiration_year'],
                'cardholder' => [
                    'name' => $cardData['cardholder_name'],
                    'identification' => [
                        'type' => $cardData['document_type'] ?? 'CPF',
                        'number' => $cardData['document_number'] ?? ''
                    ]
                ]
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl . '/v1/card_tokens', $token);

            if ($response->successful()) {
                $data = $response->json();
                
                return [
                    'success' => true,
                    'token' => $data['id'],
                    'first_six_digits' => $data['first_six_digits'],
                    'last_four_digits' => $data['last_four_digits']
                ];
            }

            return [
                'success' => false,
                'error' => 'Erro ao criar token do cartão',
                'details' => $response->json()
            ];

        } catch (Exception $e) {
            Log::error('Card Token Exception', [
                'message' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'Erro interno ao processar cartão'
            ];
        }
    }

    /**
     * Simula pagamento para ambiente de desenvolvimento
     */
    public function simulatePayment(array $paymentData)
    {
        // Simula diferentes cenários baseado no valor
        $amount = (float) $paymentData['amount'];
        $method = $paymentData['method'];

        // Simula tempo de processamento realístico
        sleep(rand(2, 4));

        // Cenários de teste baseados no valor e método
        if ($amount == 149.90) {
            // Sempre aprova para plano básico
            $status = 'approved';
        } elseif ($amount == 299.90) {
            // 95% de aprovação para plano premium
            $status = rand(1, 100) <= 95 ? 'approved' : 'rejected';
        } else {
            // Cenário geral com taxa realística
            $status = rand(1, 100) <= 85 ? 'approved' : 'rejected';
        }

        $paymentId = 'sim_' . $method . '_' . uniqid();

        if ($method === 'pix') {
            return [
                'success' => true,
                'payment_id' => $paymentId,
                'status' => 'pending',
                'qr_code' => $this->generateSimulatedPixQrCode($amount),
                'qr_code_base64' => base64_encode("PIX_QR_CODE_DATA_{$amount}"),
                'qr_code_url' => $this->generatePixQrCodeUrl($amount),
                'pix_key' => config('payment.pix.key', 'contato@gertslex.com'),
                'expires_at' => now()->addMinutes(30)->toISOString(),
                'simulated' => true
            ];
        }

        // Para cartão de crédito e débito
        $statusDetail = $status === 'approved' ? 'accredited' : $this->getRandomRejectionReason();

        return [
            'success' => $status === 'approved',
            'payment_id' => $paymentId,
            'status' => $status,
            'status_detail' => $statusDetail,
            'authorization_code' => $status === 'approved' ? 'AUTH_' . rand(100000, 999999) : null,
            'installments' => $paymentData['installments'] ?? 1,
            'simulated' => true
        ];
    }

    /**
     * Gera motivos aleatórios de rejeição para simulação
     */
    private function getRandomRejectionReason()
    {
        $reasons = [
            'cc_rejected_insufficient_amount',
            'cc_rejected_bad_filled_security_code',
            'cc_rejected_bad_filled_date',
            'cc_rejected_high_risk',
            'cc_rejected_blacklist',
            'cc_rejected_insufficient_amount'
        ];

        return $reasons[array_rand($reasons)];
    }

    /**
     * Processa pagamento PIX com validação completa
     */
    public function processPixPayment(array $paymentData)
    {
        try {
            // Validação dos dados
            $this->validatePaymentData($paymentData, 'pix');

            // Se estiver em modo de simulação
            if (config('payment.simulation_mode', true)) {
                return $this->simulatePayment(array_merge($paymentData, ['method' => 'pix']));
            }

            // Processamento real via Mercado Pago
            return $this->createPixPayment($paymentData);

        } catch (Exception $e) {
            Log::error('PIX Processing Error', [
                'message' => $e->getMessage(),
                'data' => $paymentData
            ]);

            return [
                'success' => false,
                'error' => 'Erro ao processar pagamento PIX: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Processa pagamento com cartão (crédito ou débito)
     */
    public function processCardPayment(array $paymentData, string $cardType = 'credit')
    {
        try {
            // Validação dos dados
            $this->validatePaymentData($paymentData, $cardType);

            // Se estiver em modo de simulação
            if (config('payment.simulation_mode', true)) {
                return $this->simulatePayment(array_merge($paymentData, ['method' => $cardType]));
            }

            // Processamento real via Mercado Pago
            $payment = [
                'transaction_amount' => (float) $paymentData['amount'],
                'token' => $paymentData['card_token'],
                'description' => $paymentData['description'],
                'installments' => (int) ($paymentData['installments'] ?? 1),
                'payment_method_id' => $cardType === 'credit' ? 'visa' : 'debvisa',
                'payer' => [
                    'email' => $paymentData['payer']['email'],
                    'identification' => [
                        'type' => 'CPF',
                        'number' => '12345678901'
                    ]
                ],
                'external_reference' => $paymentData['external_reference'] ?? 'payment_' . time(),
                'notification_url' => $paymentData['webhook_url'] ?? route('payment.webhook')
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json',
                'X-Idempotency-Key' => uniqid()
            ])->post($this->baseUrl . '/v1/payments', $payment);

            if ($response->successful()) {
                $data = $response->json();
                
                return [
                    'success' => true,
                    'payment_id' => $data['id'],
                    'status' => $data['status'],
                    'status_detail' => $data['status_detail'],
                    'authorization_code' => $data['authorization_code'] ?? null
                ];
            }

            return [
                'success' => false,
                'error' => 'Erro ao processar pagamento com cartão',
                'details' => $response->json()
            ];

        } catch (Exception $e) {
            Log::error('Card Processing Error', [
                'type' => $cardType,
                'message' => $e->getMessage(),
                'data' => $paymentData
            ]);

            return [
                'success' => false,
                'error' => "Erro ao processar pagamento com cartão: " . $e->getMessage()
            ];
        }
    }

    /**
     * Valida dados de pagamento
     */
    private function validatePaymentData(array $data, string $method)
    {
        $required = ['amount', 'description', 'payer'];

        foreach ($required as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                throw new Exception("Campo obrigatório ausente: {$field}");
            }
        }

        if ($data['amount'] <= 0) {
            throw new Exception("Valor deve ser maior que zero");
        }

        if ($method !== 'pix' && !isset($data['card_token'])) {
            throw new Exception("Token do cartão é obrigatório");
        }
    }

    /**
     * Gera URL do QR Code PIX usando API externa
     */
    private function generatePixQrCodeUrl(float $amount)
    {
        $pixString = $this->generateSimulatedPixQrCode($amount);
        return "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($pixString);
    }

    /**
     * Atualiza status do tenant após pagamento aprovado
     */
    public function updateTenantPaymentStatus(string $paymentId, string $status, int $tenantId = null)
    {
        try {
            if (!$tenantId) {
                // Extrair tenant_id do external_reference ou payment_id
                $tenantId = $this->extractTenantIdFromPayment($paymentId);
            }

            if ($tenantId && $status === 'approved') {
                // Atualizar status do tenant para ativo
                \DB::table('tenants')
                    ->where('id', $tenantId)
                    ->update([
                        'subscription_status' => 'active',
                        'last_payment_date' => now(),
                        'next_billing_date' => now()->addMonth(),
                        'updated_at' => now()
                    ]);

                Log::info("Tenant {$tenantId} reativado após pagamento {$paymentId}");
                
                return true;
            }

            return false;

        } catch (Exception $e) {
            Log::error('Update Tenant Payment Status Error', [
                'payment_id' => $paymentId,
                'tenant_id' => $tenantId,
                'status' => $status,
                'message' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Extrai ID do tenant do payment_id ou external_reference
     */
    private function extractTenantIdFromPayment(string $paymentId)
    {
        // Se for simulação, extrair do formato sim_method_tenantId_timestamp
        if (strpos($paymentId, 'sim_') === 0) {
            $parts = explode('_', $paymentId);
            if (count($parts) >= 3) {
                return (int) $parts[2];
            }
        }

        // Para pagamentos reais, consultar no banco de dados
        $payment = \DB::table('payment_history')
            ->where('payment_id', $paymentId)
            ->first();

        return $payment ? $payment->tenant_id : null;
    }

    /**
     * Gera QR Code PIX simulado
     */
    private function generateSimulatedPixQrCode(float $amount)
    {
        $pixKey = config('payment.pix.key', 'contato@gertslex.com');
        $merchantName = 'GERTS LEX LTDA';
        $merchantCity = 'SAO PAULO';
        $txId = 'GLEX' . time();

        // Formato simplificado do PIX QR Code
        return "00020126580014BR.GOV.BCB.PIX0136{$pixKey}520400005303986540{$amount}5802BR5913{$merchantName}6009{$merchantCity}62070503{$txId}6304";
    }
}

