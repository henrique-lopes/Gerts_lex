<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Gateway de Pagamento Padrão
    |--------------------------------------------------------------------------
    |
    | Define qual gateway será usado por padrão para processar pagamentos.
    | Opções: 'mercadopago', 'pagseguro', 'stripe', 'simulation'
    |
    */
    'default_gateway' => env('PAYMENT_GATEWAY', 'simulation'),

    /*
    |--------------------------------------------------------------------------
    | Mercado Pago
    |--------------------------------------------------------------------------
    |
    | Configurações para integração com Mercado Pago
    |
    */
    'mercadopago' => [
        'access_token' => env('MERCADOPAGO_ACCESS_TOKEN'),
        'public_key' => env('MERCADOPAGO_PUBLIC_KEY'),
        'production' => env('MERCADOPAGO_PRODUCTION', false),
        'webhook_secret' => env('MERCADOPAGO_WEBHOOK_SECRET'),
    ],

    /*
    |--------------------------------------------------------------------------
    | PagSeguro
    |--------------------------------------------------------------------------
    |
    | Configurações para integração com PagSeguro
    |
    */
    'pagseguro' => [
        'email' => env('PAGSEGURO_EMAIL'),
        'token' => env('PAGSEGURO_TOKEN'),
        'app_id' => env('PAGSEGURO_APP_ID'),
        'app_key' => env('PAGSEGURO_APP_KEY'),
        'production' => env('PAGSEGURO_PRODUCTION', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Stripe
    |--------------------------------------------------------------------------
    |
    | Configurações para integração com Stripe
    |
    */
    'stripe' => [
        'secret_key' => env('STRIPE_SECRET_KEY'),
        'public_key' => env('STRIPE_PUBLIC_KEY'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    ],

    /*
    |--------------------------------------------------------------------------
    | PIX
    |--------------------------------------------------------------------------
    |
    | Configurações para pagamentos PIX
    |
    */
    'pix' => [
        'key' => env('PIX_KEY', 'contato@gertslex.com'),
        'merchant_name' => env('PIX_MERCHANT_NAME', 'GERTS LEX LTDA'),
        'merchant_city' => env('PIX_MERCHANT_CITY', 'SAO PAULO'),
        'expiration_minutes' => env('PIX_EXPIRATION_MINUTES', 30),
    ],

    /*
    |--------------------------------------------------------------------------
    | Planos de Assinatura
    |--------------------------------------------------------------------------
    |
    | Definição dos planos disponíveis no sistema
    |
    */
    'plans' => [
        'basic' => [
            'name' => 'Plano Básico',
            'price' => 149.90,
            'currency' => 'BRL',
            'interval' => 'monthly',
            'popular' => false,
            'features' => [
                'Até 3 advogados',
                'Gestão de clientes ilimitada',
                'Gestão de casos básica',
                'Agenda simples',
                'Documentos básicos',
                'Suporte por email'
            ]
        ],
        'premium' => [
            'name' => 'Plano Premium',
            'price' => 299.90,
            'currency' => 'BRL',
            'interval' => 'monthly',
            'popular' => true,
            'features' => [
                'Advogados ilimitados',
                'Gestão completa de clientes',
                'Gestão avançada de casos',
                'Agenda inteligente',
                'Documentos avançados',
                'Relatórios detalhados',
                'Integração com tribunais',
                'Suporte prioritário'
            ]
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | URLs de Retorno
    |--------------------------------------------------------------------------
    |
    | URLs para onde o usuário será redirecionado após o pagamento
    |
    */
    'urls' => [
        'success' => env('APP_URL') . '/payment/success',
        'failure' => env('APP_URL') . '/payment/failure',
        'pending' => env('APP_URL') . '/payment/pending',
        'webhook' => env('APP_URL') . '/webhook/payment',
    ],

    /*
    |--------------------------------------------------------------------------
    | Configurações de Simulação
    |--------------------------------------------------------------------------
    |
    | Configurações para modo de simulação (desenvolvimento)
    |
    */
    'simulation' => [
        'enabled' => env('PAYMENT_SIMULATION', true),
        'success_rate_pix' => env('PAYMENT_SUCCESS_RATE_PIX', 100),
        'success_rate_credit' => env('PAYMENT_SUCCESS_RATE_CREDIT', 85),
        'success_rate_debit' => env('PAYMENT_SUCCESS_RATE_DEBIT', 90),
        'processing_delay' => env('PAYMENT_PROCESSING_DELAY', 3),
        'realistic_errors' => true,
        'log_simulated_payments' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Métodos de Pagamento
    |--------------------------------------------------------------------------
    |
    | Configuração dos métodos de pagamento disponíveis
    |
    */
    'payment_methods' => [
        'pix' => [
            'enabled' => true,
            'name' => 'PIX',
            'description' => 'Pagamento instantâneo',
            'icon' => 'fas fa-qrcode',
            'processing_time' => 'Instantâneo',
            'fees' => 0,
            'min_amount' => 1.00,
            'max_amount' => 50000.00
        ],
        'credit_card' => [
            'enabled' => true,
            'name' => 'Cartão de Crédito',
            'description' => 'Visa, Mastercard, Elo, Amex',
            'icon' => 'fas fa-credit-card',
            'processing_time' => 'Até 2 dias úteis',
            'fees' => 3.99,
            'max_installments' => 12,
            'min_amount' => 5.00,
            'max_amount' => 100000.00
        ],
        'debit_card' => [
            'enabled' => true,
            'name' => 'Cartão de Débito',
            'description' => 'Débito online instantâneo',
            'icon' => 'fas fa-credit-card',
            'processing_time' => 'Instantâneo',
            'fees' => 2.99,
            'min_amount' => 5.00,
            'max_amount' => 10000.00
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Configurações de Cobrança
    |--------------------------------------------------------------------------
    |
    | Configurações do ciclo de cobrança e vencimentos
    |
    */
    'billing' => [
        'grace_period_days' => env('BILLING_GRACE_PERIOD_DAYS', 7),
        'trial_period_days' => env('BILLING_TRIAL_PERIOD_DAYS', 14),
        'suspension_after_days' => env('BILLING_SUSPENSION_AFTER_DAYS', 30),
        'auto_renewal' => env('BILLING_AUTO_RENEWAL', true),
        'invoice_due_days' => env('BILLING_INVOICE_DUE_DAYS', 5),
        'late_fee_percentage' => env('BILLING_LATE_FEE_PERCENTAGE', 2),
        'discount_annual' => env('BILLING_DISCOUNT_ANNUAL', 10),
    ],

    /*
    |--------------------------------------------------------------------------
    | Configurações de Segurança
    |--------------------------------------------------------------------------
    |
    | Configurações relacionadas à segurança dos pagamentos
    |
    */
    'security' => [
        'encrypt_card_data' => true,
        'log_transactions' => true,
        'webhook_validation' => true,
        'max_payment_attempts' => 3,
        'payment_timeout' => 300, // 5 minutos
    ],

    /*
    |--------------------------------------------------------------------------
    | Configurações de Notificação
    |--------------------------------------------------------------------------
    |
    | Configurações para notificações de pagamento
    |
    */
    'notifications' => [
        'email_on_success' => true,
        'email_on_failure' => true,
        'webhook_retries' => 3,
        'webhook_timeout' => 30,
    ],
];

