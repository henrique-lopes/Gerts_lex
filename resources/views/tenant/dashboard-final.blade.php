<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard - {{ $tenant->name }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-white border-b border-gray-100 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <h1 class="text-xl font-bold text-gray-800">Gert's Lex</h1>
                    </div>
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <a href="/dashboard" class="border-indigo-500 text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Dashboard
                        </a>
                    </div>
                </div>
                <div class="flex items-center">
                    <div class="ml-3 relative">
                        <div class="text-sm text-gray-700">
                            Bem-vindo, <span class="font-medium">Henrique Lopes</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Header -->
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-900">Dashboard - {{ $tenant->name }}</h1>
        </div>
    </header>

    <!-- Main Content -->
    <main class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Informações do Tenant -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800">Informações do Escritório</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                            <div class="text-sm text-blue-600 font-medium">Nome</div>
                            <div class="text-lg font-semibold text-blue-900">{{ $tenant->name }}</div>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                            <div class="text-sm text-green-600 font-medium">Domínio</div>
                            <div class="text-lg font-semibold text-green-900">{{ $tenant->domain }}</div>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                            <div class="text-sm text-purple-600 font-medium">Status</div>
                            <div class="text-lg font-semibold text-purple-900 capitalize">{{ $stats['subscription_status'] }}</div>
                        </div>
                        <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                            <div class="text-sm text-yellow-600 font-medium">Plano</div>
                            <div class="text-lg font-semibold text-yellow-900 capitalize">{{ $stats['subscription_plan'] }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estatísticas -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800">Estatísticas do Sistema</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-4">
                        <div class="bg-gradient-to-br from-blue-500 to-blue-600 p-4 rounded-lg text-white text-center shadow-lg">
                            <div class="text-2xl font-bold">{{ $stats['users_count'] }}</div>
                            <div class="text-sm opacity-90">Usuários</div>
                        </div>
                        <div class="bg-gradient-to-br from-green-500 to-green-600 p-4 rounded-lg text-white text-center shadow-lg">
                            <div class="text-2xl font-bold">{{ $stats['lawyers_count'] }}</div>
                            <div class="text-sm opacity-90">Advogados</div>
                        </div>
                        <div class="bg-gradient-to-br from-purple-500 to-purple-600 p-4 rounded-lg text-white text-center shadow-lg">
                            <div class="text-2xl font-bold">{{ $stats['clients_count'] }}</div>
                            <div class="text-sm opacity-90">Clientes</div>
                        </div>
                        <div class="bg-gradient-to-br from-red-500 to-red-600 p-4 rounded-lg text-white text-center shadow-lg">
                            <div class="text-2xl font-bold">{{ $stats['cases_count'] }}</div>
                            <div class="text-sm opacity-90">Casos</div>
                        </div>
                        <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 p-4 rounded-lg text-white text-center shadow-lg">
                            <div class="text-2xl font-bold">{{ $stats['fees_count'] }}</div>
                            <div class="text-sm opacity-90">Honorários</div>
                        </div>
                        <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 p-4 rounded-lg text-white text-center shadow-lg">
                            <div class="text-2xl font-bold">{{ $stats['appointments_count'] }}</div>
                            <div class="text-sm opacity-90">Compromissos</div>
                        </div>
                        <div class="bg-gradient-to-br from-pink-500 to-pink-600 p-4 rounded-lg text-white text-center shadow-lg">
                            <div class="text-2xl font-bold">{{ $stats['deadlines_count'] }}</div>
                            <div class="text-sm opacity-90">Prazos</div>
                        </div>
                        <div class="bg-gradient-to-br from-teal-500 to-teal-600 p-4 rounded-lg text-white text-center shadow-lg">
                            <div class="text-2xl font-bold">{{ $stats['documents_count'] }}</div>
                            <div class="text-sm opacity-90">Documentos</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ações Rápidas -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800">Ações Rápidas</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="group bg-blue-50 hover:bg-blue-100 p-4 rounded-lg transition-all duration-200 border border-blue-200 hover:border-blue-300 hover:shadow-md cursor-pointer">
                            <div class="flex items-center">
                                <div class="bg-blue-500 p-3 rounded-lg mr-4 group-hover:bg-blue-600 transition-colors">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-semibold text-blue-900 group-hover:text-blue-800">Gerenciar Advogados</div>
                                    <div class="text-sm text-blue-600">Cadastrar e gerenciar advogados</div>
                                </div>
                            </div>
                        </div>

                        <div class="group bg-green-50 hover:bg-green-100 p-4 rounded-lg transition-all duration-200 border border-green-200 hover:border-green-300 hover:shadow-md cursor-pointer">
                            <div class="flex items-center">
                                <div class="bg-green-500 p-3 rounded-lg mr-4 group-hover:bg-green-600 transition-colors">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-semibold text-green-900 group-hover:text-green-800">Gerenciar Clientes</div>
                                    <div class="text-sm text-green-600">Cadastrar e gerenciar clientes</div>
                                </div>
                            </div>
                        </div>

                        <div class="group bg-purple-50 hover:bg-purple-100 p-4 rounded-lg transition-all duration-200 border border-purple-200 hover:border-purple-300 hover:shadow-md cursor-pointer">
                            <div class="flex items-center">
                                <div class="bg-purple-500 p-3 rounded-lg mr-4 group-hover:bg-purple-600 transition-colors">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-semibold text-purple-900 group-hover:text-purple-800">Gerenciar Casos</div>
                                    <div class="text-sm text-purple-600">Acompanhar processos jurídicos</div>
                                </div>
                            </div>
                        </div>

                        <div class="group bg-yellow-50 hover:bg-yellow-100 p-4 rounded-lg transition-all duration-200 border border-yellow-200 hover:border-yellow-300 hover:shadow-md cursor-pointer">
                            <div class="flex items-center">
                                <div class="bg-yellow-500 p-3 rounded-lg mr-4 group-hover:bg-yellow-600 transition-colors">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-semibold text-yellow-900 group-hover:text-yellow-800">Gerenciar Honorários</div>
                                    <div class="text-sm text-yellow-600">Controlar pagamentos e valores</div>
                                </div>
                            </div>
                        </div>

                        <div class="group bg-indigo-50 hover:bg-indigo-100 p-4 rounded-lg transition-all duration-200 border border-indigo-200 hover:border-indigo-300 hover:shadow-md cursor-pointer">
                            <div class="flex items-center">
                                <div class="bg-indigo-500 p-3 rounded-lg mr-4 group-hover:bg-indigo-600 transition-colors">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-semibold text-indigo-900 group-hover:text-indigo-800">Compromissos</div>
                                    <div class="text-sm text-indigo-600">Agendar reuniões e audiências</div>
                                </div>
                            </div>
                        </div>

                        <div class="group bg-pink-50 hover:bg-pink-100 p-4 rounded-lg transition-all duration-200 border border-pink-200 hover:border-pink-300 hover:shadow-md cursor-pointer">
                            <div class="flex items-center">
                                <div class="bg-pink-500 p-3 rounded-lg mr-4 group-hover:bg-pink-600 transition-colors">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-semibold text-pink-900 group-hover:text-pink-800">Prazos</div>
                                    <div class="text-sm text-pink-600">Controlar prazos processuais</div>
                                </div>
                            </div>
                        </div>

                        <div class="group bg-teal-50 hover:bg-teal-100 p-4 rounded-lg transition-all duration-200 border border-teal-200 hover:border-teal-300 hover:shadow-md cursor-pointer">
                            <div class="flex items-center">
                                <div class="bg-teal-500 p-3 rounded-lg mr-4 group-hover:bg-teal-600 transition-colors">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-semibold text-teal-900 group-hover:text-teal-800">Documentos</div>
                                    <div class="text-sm text-teal-600">Gerenciar documentos do caso</div>
                                </div>
                            </div>
                        </div>

                        <div class="group bg-red-50 hover:bg-red-100 p-4 rounded-lg transition-all duration-200 border border-red-200 hover:border-red-300 hover:shadow-md cursor-pointer">
                            <div class="flex items-center">
                                <div class="bg-red-500 p-3 rounded-lg mr-4 group-hover:bg-red-600 transition-colors">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-semibold text-red-900 group-hover:text-red-800">Pagamentos</div>
                                    <div class="text-sm text-red-600">Sistema de pagamentos</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="text-center text-sm text-gray-500">
                © 2025 Gert's Lex - Sistema de Gestão Jurídica
            </div>
        </div>
    </footer>
</body>
</html>

