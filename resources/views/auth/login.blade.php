<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Gert's Lex</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'inter': ['Inter', 'sans-serif'],
                    },
                    colors: {
                        'navy': {
                            50: '#f0f4ff',
                            100: '#e0e9ff',
                            200: '#c7d6fe',
                            300: '#a5b8fc',
                            400: '#8b93f8',
                            500: '#7c6df2',
                            600: '#6d4de6',
                            700: '#5d3dcb',
                            800: '#4c32a4',
                            900: '#1e3a8a',
                            950: '#1e2a5e'
                        },
                        'legal': {
                            50: '#f8fafc',
                            100: '#f1f5f9',
                            200: '#e2e8f0',
                            300: '#cbd5e1',
                            400: '#94a3b8',
                            500: '#64748b',
                            600: '#475569',
                            700: '#334155',
                            800: '#1e293b',
                            900: '#0f172a'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #60a5fa 100%);
        }
        
        .floating-animation {
            animation: floating 6s ease-in-out infinite;
        }
        
        @keyframes floating {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        .fade-in {
            animation: fadeIn 1s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .input-focus {
            transition: all 0.3s ease;
        }
        
        .input-focus:focus {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(30, 58, 138, 0.15);
        }
        
        .btn-hover {
            transition: all 0.3s ease;
        }
        
        .btn-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(30, 58, 138, 0.3);
        }
        
        .logo-glow {
            filter: drop-shadow(0 0 20px rgba(30, 58, 138, 0.3));
        }
    </style>
</head>
<body class="min-h-screen gradient-bg flex items-center justify-center p-4">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-10 left-10 w-72 h-72 bg-white rounded-full mix-blend-multiply filter blur-xl floating-animation"></div>
        <div class="absolute top-32 right-10 w-72 h-72 bg-blue-200 rounded-full mix-blend-multiply filter blur-xl floating-animation" style="animation-delay: 2s;"></div>
        <div class="absolute bottom-10 left-32 w-72 h-72 bg-indigo-200 rounded-full mix-blend-multiply filter blur-xl floating-animation" style="animation-delay: 4s;"></div>
    </div>

    <!-- Login Container -->
    <div class="relative z-10 w-full max-w-md fade-in">
        <!-- Logo Section -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-white rounded-2xl shadow-2xl mb-6 logo-glow">
                <i class="fas fa-balance-scale text-3xl text-navy-900"></i>
            </div>
            <h1 class="text-3xl font-bold text-white mb-2">Gert's Lex</h1>
            <p class="text-blue-100 text-sm">Sistema Jurídico Profissional</p>
        </div>

        <!-- Login Form -->
        <div class="glass-effect rounded-3xl shadow-2xl p-8">
            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-navy-900 mb-2">Bem-vindo de volta</h2>
                <p class="text-legal-600">Acesse sua conta para continuar</p>
            </div>

            <form method="POST" action="/login" class="space-y-6">
                @csrf
                
                <!-- Email Field -->
                <div class="space-y-2">
                    <label for="email" class="block text-sm font-semibold text-navy-900">
                        <i class="fas fa-envelope mr-2 text-navy-600"></i>
                        Email ou Usuário
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="{{ old('email') }}"
                        class="w-full px-4 py-4 bg-white border-2 border-legal-200 rounded-xl focus:border-navy-500 focus:ring-4 focus:ring-navy-100 transition-all duration-300 input-focus @error('email') border-red-500 @enderror"
                        placeholder="seu@email.com"
                        required
                        autofocus
                    >
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Password Field -->
                <div class="space-y-2">
                    <label for="password" class="block text-sm font-semibold text-navy-900">
                        <i class="fas fa-lock mr-2 text-navy-600"></i>
                        Senha
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="password" 
                            name="password"
                            class="w-full px-4 py-4 bg-white border-2 border-legal-200 rounded-xl focus:border-navy-500 focus:ring-4 focus:ring-navy-100 transition-all duration-300 input-focus @error('password') border-red-500 @enderror"
                            placeholder="••••••••"
                            required
                        >
                        <button 
                            type="button" 
                            onclick="togglePassword()"
                            class="absolute right-4 top-1/2 transform -translate-y-1/2 text-legal-500 hover:text-navy-600 transition-colors"
                        >
                            <i id="password-icon" class="fas fa-eye"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <input 
                            type="checkbox" 
                            name="remember" 
                            class="w-4 h-4 text-navy-600 bg-white border-legal-300 rounded focus:ring-navy-500 focus:ring-2"
                        >
                        <span class="ml-2 text-sm text-legal-700">Lembrar-me</span>
                    </label>
                    
                    <a href="#" class="text-sm text-navy-600 hover:text-navy-800 font-medium transition-colors">
                        Esqueceu a senha?
                    </a>
                </div>

                <!-- Login Button -->
                <button 
                    type="submit"
                    class="w-full bg-gradient-to-r from-navy-900 to-navy-700 text-white font-semibold py-4 px-6 rounded-xl hover:from-navy-800 hover:to-navy-600 focus:outline-none focus:ring-4 focus:ring-navy-200 transition-all duration-300 btn-hover"
                >
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Entrar no Sistema
                </button>

                <!-- Divider -->
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-legal-200"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-white text-legal-500">ou</span>
                    </div>
                </div>

                <!-- Register Link -->
                <div class="text-center">
                    <p class="text-legal-600">
                        Não tem uma conta? 
                        <a href="#" class="text-navy-600 hover:text-navy-800 font-semibold transition-colors">
                            Solicite acesso
                        </a>
                    </p>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <div class="text-center mt-8">
            <p class="text-blue-100 text-sm">
                © {{ date('Y') }} Gert's Lex. Todos os direitos reservados.
            </p>
            <div class="flex justify-center space-x-4 mt-4">
                <a href="#" class="text-blue-200 hover:text-white transition-colors">
                    <i class="fab fa-linkedin text-lg"></i>
                </a>
                <a href="#" class="text-blue-200 hover:text-white transition-colors">
                    <i class="fab fa-twitter text-lg"></i>
                </a>
                <a href="#" class="text-blue-200 hover:text-white transition-colors">
                    <i class="fas fa-globe text-lg"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loading-overlay" class="fixed inset-0 bg-navy-900 bg-opacity-50 backdrop-blur-sm hidden items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-8 text-center shadow-2xl">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-navy-600 mx-auto mb-4"></div>
            <p class="text-navy-900 font-semibold">Autenticando...</p>
        </div>
    </div>

    <script>
        // Toggle Password Visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('password-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.classList.remove('fa-eye');
                passwordIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                passwordIcon.classList.remove('fa-eye-slash');
                passwordIcon.classList.add('fa-eye');
            }
        }

        // Form Submission with Loading
        document.querySelector('form').addEventListener('submit', function(e) {
            const loadingOverlay = document.getElementById('loading-overlay');
            loadingOverlay.classList.remove('hidden');
            loadingOverlay.classList.add('flex');
        });

        // Input Focus Effects
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('scale-105');
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('scale-105');
            });
        });

        // Keyboard Navigation
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && e.target.tagName !== 'BUTTON') {
                const form = document.querySelector('form');
                const inputs = form.querySelectorAll('input');
                const currentIndex = Array.from(inputs).indexOf(e.target);
                
                if (currentIndex < inputs.length - 1) {
                    e.preventDefault();
                    inputs[currentIndex + 1].focus();
                }
            }
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            });
        }, 5000);
    </script>
</body>
</html>
