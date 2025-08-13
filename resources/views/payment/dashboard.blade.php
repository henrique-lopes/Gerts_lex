<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Área de Pagamentos - {{ $dashboardData['tenant']['name'] }} | Gert's Lex</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #1e3a8a 0%, #3730a3 50%, #1e40af 100%);
            min-height: 100vh;
            color: #1f2937;
            overflow-x: hidden;
        }

        /* Background Animation */
        .bg-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }

        .floating-shape {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .floating-shape:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }

        .floating-shape:nth-child(2) {
            width: 120px;
            height: 120px;
            top: 60%;
            right: 10%;
            animation-delay: 2s;
        }

        .floating-shape:nth-child(3) {
            width: 60px;
            height: 60px;
            bottom: 20%;
            left: 20%;
            animation-delay: 4s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        /* Header */
        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #1e3a8a, #3730a3);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
            box-shadow: 0 4px 12px rgba(30, 58, 138, 0.3);
        }

        .logo-text {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e3a8a;
        }

        .status-badge {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: #fee2e2;
            color: #dc2626;
            border-radius: 50px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .status-badge.warning {
            background: #fef3c7;
            color: #d97706;
        }

        /* Main Container */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
            min-height: calc(100vh - 80px);
        }

        /* Alert Banner */
        .alert-banner {
            background: linear-gradient(135deg, #dc2626, #ef4444);
            color: white;
            padding: 1.5rem;
            border-radius: 16px;
            margin-bottom: 2rem;
            box-shadow: 0 8px 32px rgba(220, 38, 38, 0.3);
            animation: slideInDown 0.6s ease-out;
        }

        .alert-banner.warning {
            background: linear-gradient(135deg, #d97706, #f59e0b);
            box-shadow: 0 8px 32px rgba(217, 119, 6, 0.3);
        }

        .alert-content {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .alert-icon {
            font-size: 2rem;
            animation: pulse 2s infinite;
        }

        .alert-text h3 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .alert-text p {
            opacity: 0.9;
            line-height: 1.5;
        }

        /* Grid Layout */
        .dashboard-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        @media (max-width: 768px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Cards */
        .card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
            animation: fadeInUp 0.6s ease-out;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
        }

        .card-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .card-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }

        .card-icon.info {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        }

        .card-icon.payment {
            background: linear-gradient(135deg, #10b981, #059669);
        }

        .card-icon.history {
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        }

        .card-icon.plans {
            background: linear-gradient(135deg, #f59e0b, #d97706);
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1f2937;
        }

        /* Tenant Info */
        .tenant-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .info-item {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .info-label {
            font-size: 0.875rem;
            color: #6b7280;
            font-weight: 500;
        }

        .info-value {
            font-size: 1rem;
            color: #1f2937;
            font-weight: 600;
        }

        /* Payment Status */
        .status-card {
            text-align: center;
            padding: 2rem;
        }

        .status-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 1rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
            animation: pulse 2s infinite;
        }

        .status-icon.blocked {
            background: linear-gradient(135deg, #dc2626, #ef4444);
        }

        .status-icon.trial {
            background: linear-gradient(135deg, #d97706, #f59e0b);
        }

        .status-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .status-description {
            color: #6b7280;
            line-height: 1.5;
        }

        /* Payment History */
        .history-list {
            max-height: 300px;
            overflow-y: auto;
        }

        .history-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem;
            border-bottom: 1px solid #f3f4f6;
            transition: background-color 0.2s ease;
        }

        .history-item:hover {
            background-color: #f9fafb;
        }

        .history-item:last-child {
            border-bottom: none;
        }

        .history-info {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .history-description {
            font-weight: 500;
            color: #1f2937;
        }

        .history-date {
            font-size: 0.875rem;
            color: #6b7280;
        }

        .history-amount {
            font-weight: 600;
            color: #1f2937;
        }

        .history-status {
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .history-status.approved {
            background: #d1fae5;
            color: #065f46;
        }

        .history-status.failed {
            background: #fee2e2;
            color: #991b1b;
        }

        /* Plans */
        .plans-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .plan-card {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            border: 2px solid #e5e7eb;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .plan-card:hover {
            border-color: #3b82f6;
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(59, 130, 246, 0.15);
        }

        .plan-card.popular {
            border-color: #f59e0b;
            background: linear-gradient(135deg, #fffbeb, #fef3c7);
        }

        .plan-card.popular::before {
            content: 'Mais Popular';
            position: absolute;
            top: 1rem;
            right: -2rem;
            background: #f59e0b;
            color: white;
            padding: 0.25rem 3rem;
            font-size: 0.75rem;
            font-weight: 600;
            transform: rotate(45deg);
        }

        .plan-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .plan-name {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }

        .plan-price {
            font-size: 2.5rem;
            font-weight: 800;
            color: #1e3a8a;
            margin-bottom: 0.25rem;
        }

        .plan-period {
            color: #6b7280;
            font-size: 0.875rem;
        }

        .plan-features {
            list-style: none;
            margin-bottom: 2rem;
        }

        .plan-features li {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem 0;
            color: #4b5563;
        }

        .plan-features li::before {
            content: '✓';
            color: #10b981;
            font-weight: bold;
            font-size: 1.1rem;
        }

        .plan-button {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #1e3a8a, #3730a3);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .plan-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(30, 58, 138, 0.3);
        }

        /* Payment Methods */
        .payment-methods {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .payment-method {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .payment-method:hover {
            border-color: #3b82f6;
            background-color: #f8fafc;
        }

        .payment-method.selected {
            border-color: #1e3a8a;
            background-color: #eff6ff;
        }

        .payment-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            color: white;
        }

        .payment-icon.credit {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        }

        .payment-icon.debit {
            background: linear-gradient(135deg, #10b981, #059669);
        }

        .payment-icon.pix {
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        }

        /* Animations */
        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }

        /* Loading States */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(8px);
            z-index: 1000;
            animation: fadeIn 0.3s ease;
        }

        .modal.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            max-width: 500px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            animation: slideInUp 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .header-content {
                padding: 0 1rem;
            }

            .card {
                padding: 1.5rem;
            }

            .plans-grid {
                grid-template-columns: 1fr;
            }

            .payment-methods {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Background Animation -->
    <div class="bg-animation">
        <div class="floating-shape"></div>
        <div class="floating-shape"></div>
        <div class="floating-shape"></div>
    </div>

    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <div class="logo">
                <div class="logo-icon">
                    <i class="fas fa-balance-scale"></i>
                </div>
                <div class="logo-text">Gert's Lex</div>
            </div>
            <div class="status-badge {{ $dashboardData['payment_status']['is_trial'] ? 'warning' : '' }}">
                <i class="fas fa-{{ $dashboardData['payment_status']['is_blocked'] ? 'exclamation-triangle' : 'clock' }}"></i>
                {{ $dashboardData['payment_status']['status_label'] }}
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <div class="container">
        <!-- Alert Banner -->
        <div class="alert-banner {{ $dashboardData['payment_status']['is_trial'] ? 'warning' : '' }}">
            <div class="alert-content">
                <div class="alert-icon">
                    <i class="fas fa-{{ $dashboardData['payment_status']['is_blocked'] ? 'exclamation-triangle' : 'clock' }}"></i>
                </div>
                <div class="alert-text">
                    @if($dashboardData['payment_status']['is_blocked'])
                        <h3>Conta Suspensa - Pagamento Necessário</h3>
                        <p>Sua conta está temporariamente suspensa devido ao pagamento em atraso. Regularize sua situação para continuar usando o sistema.</p>
                    @elseif($dashboardData['payment_status']['is_trial'])
                        <h3>Período de Teste - Escolha um Plano</h3>
                        <p>Você está no período de teste. Escolha um plano para continuar aproveitando todos os recursos do Gert's Lex.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Dashboard Grid -->
        <div class="dashboard-grid">
            <!-- Tenant Information -->
            <div class="card">
                <div class="card-header">
                    <div class="card-icon info">
                        <i class="fas fa-building"></i>
                    </div>
                    <h2 class="card-title">Informações do Escritório</h2>
                </div>
                <div class="tenant-info">
                    <div class="info-item">
                        <span class="info-label">Nome</span>
                        <span class="info-value">{{ $dashboardData['tenant']['name'] }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Domínio</span>
                        <span class="info-value">{{ $dashboardData['tenant']['domain'] }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Email</span>
                        <span class="info-value">{{ $dashboardData['tenant']['email'] ?? 'Não informado' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Telefone</span>
                        <span class="info-value">{{ $dashboardData['tenant']['phone'] ?? 'Não informado' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Plano Atual</span>
                        <span class="info-value">{{ ucfirst($dashboardData['tenant']['subscription_plan']) }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Status</span>
                        <span class="info-value">{{ $dashboardData['payment_status']['status_label'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Payment Status -->
            <div class="card">
                <div class="card-header">
                    <div class="card-icon payment">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <h2 class="card-title">Status de Pagamento</h2>
                </div>
                <div class="status-card">
                    <div class="status-icon {{ $dashboardData['payment_status']['is_blocked'] ? 'blocked' : 'trial' }}">
                        <i class="fas fa-{{ $dashboardData['payment_status']['is_blocked'] ? 'exclamation-triangle' : 'clock' }}"></i>
                    </div>
                    <h3 class="status-title">{{ $dashboardData['payment_status']['status_label'] }}</h3>
                    <p class="status-description">
                        @if($dashboardData['payment_status']['is_blocked'])
                            Regularize seu pagamento para reativar sua conta e continuar usando o sistema.
                        @elseif($dashboardData['payment_status']['is_trial'])
                            Escolha um plano para continuar aproveitando todos os recursos.
                        @endif
                    </p>
                    @if($dashboardData['payment_status']['next_billing_date'])
                        <p class="status-description" style="margin-top: 1rem;">
                            <strong>Próxima cobrança:</strong> {{ \Carbon\Carbon::parse($dashboardData['payment_status']['next_billing_date'])->format('d/m/Y') }}
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Payment History -->
        <div class="card">
            <div class="card-header">
                <div class="card-icon history">
                    <i class="fas fa-history"></i>
                </div>
                <h2 class="card-title">Histórico de Pagamentos</h2>
            </div>
            <div class="history-list">
                @forelse($dashboardData['payment_history'] as $payment)
                    <div class="history-item">
                        <div class="history-info">
                            <div class="history-description">{{ $payment['description'] }}</div>
                            <div class="history-date">{{ \Carbon\Carbon::parse($payment['date'])->format('d/m/Y') }}</div>
                        </div>
                        <div style="text-align: right;">
                            <div class="history-amount">R$ {{ number_format($payment['amount'], 2, ',', '.') }}</div>
                            <div class="history-status {{ $payment['status'] }}">
                                {{ $payment['status'] === 'approved' ? 'Aprovado' : 'Falhou' }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="history-item">
                        <div class="history-info">
                            <div class="history-description">Nenhum pagamento encontrado</div>
                            <div class="history-date">Histórico vazio</div>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Plans -->
        <div class="card">
            <div class="card-header">
                <div class="card-icon plans">
                    <i class="fas fa-star"></i>
                </div>
                <h2 class="card-title">Escolha seu Plano</h2>
            </div>
            <div class="plans-grid">
                @foreach($dashboardData['plans'] as $planKey => $plan)
                    <div class="plan-card {{ $plan['popular'] ? 'popular' : '' }}">
                        <div class="plan-header">
                            <h3 class="plan-name">{{ $plan['name'] }}</h3>
                            <div class="plan-price">R$ {{ number_format($plan['price'], 2, ',', '.') }}</div>
                            <div class="plan-period">por mês</div>
                        </div>
                        <ul class="plan-features">
                            @foreach($plan['features'] as $feature)
                                <li>{{ $feature }}</li>
                            @endforeach
                        </ul>
                        <button class="plan-button" onclick="selectPlan('{{ $planKey }}', '{{ $plan['name'] }}', {{ $plan['price'] }})">
                            Escolher {{ $plan['name'] }}
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div id="paymentModal" class="modal">
        <div class="modal-content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <h2 style="margin: 0;">Finalizar Pagamento</h2>
                <button onclick="closeModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">×</button>
            </div>
            
            <div id="selectedPlanInfo" style="background: #f8fafc; padding: 1rem; border-radius: 12px; margin-bottom: 2rem;">
                <!-- Plan info will be inserted here -->
            </div>

            <h3 style="margin-bottom: 1rem;">Escolha a forma de pagamento:</h3>
            <div class="payment-methods">
                <div class="payment-method" onclick="selectPaymentMethod('credit_card')">
                    <div class="payment-icon credit">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <div>
                        <div style="font-weight: 600;">Cartão de Crédito</div>
                        <div style="font-size: 0.875rem; color: #6b7280;">Visa, Mastercard, Elo</div>
                    </div>
                </div>
                <div class="payment-method" onclick="selectPaymentMethod('debit_card')">
                    <div class="payment-icon debit">
                        <i class="fas fa-money-check-alt"></i>
                    </div>
                    <div>
                        <div style="font-weight: 600;">Cartão de Débito</div>
                        <div style="font-size: 0.875rem; color: #6b7280;">Débito em conta</div>
                    </div>
                </div>
                <div class="payment-method" onclick="selectPaymentMethod('pix')">
                    <div class="payment-icon pix">
                        <i class="fas fa-qrcode"></i>
                    </div>
                    <div>
                        <div style="font-weight: 600;">PIX</div>
                        <div style="font-size: 0.875rem; color: #6b7280;">Pagamento instantâneo</div>
                    </div>
                </div>
            </div>

            <button id="processPaymentBtn" class="plan-button" style="margin-top: 2rem;" onclick="processPayment()" disabled>
                <span id="paymentBtnText">Selecione uma forma de pagamento</span>
                <span id="paymentBtnLoading" class="loading" style="display: none;"></span>
            </button>
        </div>
    </div>

    <script>
        let selectedPlan = null;
        let selectedPaymentMethod = null;

        function selectPlan(planKey, planName, planPrice) {
            selectedPlan = { key: planKey, name: planName, price: planPrice };
            
            document.getElementById('selectedPlanInfo').innerHTML = `
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <div style="font-weight: 600; font-size: 1.1rem;">${planName}</div>
                        <div style="color: #6b7280;">Cobrança mensal</div>
                    </div>
                    <div style="font-size: 1.5rem; font-weight: 700; color: #1e3a8a;">
                        R$ ${planPrice.toFixed(2).replace('.', ',')}
                    </div>
                </div>
            `;
            
            document.getElementById('paymentModal').classList.add('show');
        }

        function selectPaymentMethod(method) {
            selectedPaymentMethod = method;
            
            // Remove selected class from all methods
            document.querySelectorAll('.payment-method').forEach(el => {
                el.classList.remove('selected');
            });
            
            // Add selected class to clicked method
            event.currentTarget.classList.add('selected');
            
            // Enable payment button
            const btn = document.getElementById('processPaymentBtn');
            btn.disabled = false;
            document.getElementById('paymentBtnText').textContent = 'Processar Pagamento';
        }

        function closeModal() {
            document.getElementById('paymentModal').classList.remove('show');
            selectedPlan = null;
            selectedPaymentMethod = null;
            
            // Reset form
            document.querySelectorAll('.payment-method').forEach(el => {
                el.classList.remove('selected');
            });
            
            const btn = document.getElementById('processPaymentBtn');
            btn.disabled = true;
            document.getElementById('paymentBtnText').textContent = 'Selecione uma forma de pagamento';
        }

        async function processPayment() {
            if (!selectedPlan || !selectedPaymentMethod) {
                alert('Por favor, selecione um plano e forma de pagamento.');
                return;
            }

            const btn = document.getElementById('processPaymentBtn');
            const btnText = document.getElementById('paymentBtnText');
            const btnLoading = document.getElementById('paymentBtnLoading');
            
            btn.disabled = true;
            btnText.style.display = 'none';
            btnLoading.style.display = 'inline-block';

            try {
                const response = await fetch('{{ route("payment.process") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        plan: selectedPlan.key,
                        payment_method: selectedPaymentMethod,
                        tenant_id: {{ $dashboardData['tenant']['id'] }}
                    })
                });

                const result = await response.json();

                if (result.success) {
                    alert('Pagamento processado com sucesso! Redirecionando...');
                    window.location.href = result.redirect_url || '{{ route("login") }}';
                } else {
                    alert('Erro no pagamento: ' + result.message);
                }
            } catch (error) {
                alert('Erro ao processar pagamento. Tente novamente.');
                console.error('Payment error:', error);
            } finally {
                btn.disabled = false;
                btnText.style.display = 'inline';
                btnLoading.style.display = 'none';
            }
        }

        // Close modal when clicking outside
        document.getElementById('paymentModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Auto-refresh page every 30 seconds to check payment status
        setInterval(() => {
            // Check if payment was completed externally (e.g., PIX webhook)
            fetch('{{ route("api.session.info") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.payment_status === 'active') {
                        window.location.href = '{{ route("login") }}';
                    }
                })
                .catch(error => console.log('Status check failed:', error));
        }, 30000);
    </script>
</body>
</html>

