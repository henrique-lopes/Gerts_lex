<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __("Dashboard do Advogado") }} - {{ $lawyer->user->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        Bem-vindo, {{ $lawyer->user->name }}!
                    </h3>
                    <p class="text-gray-600 mb-4">
                        Aqui você pode visualizar seus casos, compromissos, prazos e honorários.
                    </p>

                    <!-- Stats Cards for Lawyer -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 truncate">
                                                Total de Casos
                                            </dt>
                                            <dd class="text-lg font-medium text-gray-900">
                                                {{ $cases->count() }}
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2-1.343-2-3-2zM12 14c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2-1.343-2-3-2z" />
                                        </svg>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 truncate">
                                                Honorários Recebidos
                                            </dt>
                                            <dd class="text-lg font-medium text-gray-900">
                                                R$ {{ number_format($totalFeesReceived, 2, ",", ".") }}
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2-1.343-2-3-2zM12 14c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2-1.343-2-3-2z" />
                                        </svg>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 truncate">
                                                Total de Honorários
                                            </dt>
                                            <dd class="text-lg font-medium text-gray-900">
                                                R$ {{ number_format($totalFeesAmount, 2, ",", ".") }}
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Upcoming Appointments -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Próximos Compromissos</h3>
                            @if ($upcomingAppointments->isEmpty())
                                <p class="text-gray-600">Nenhum compromisso futuro.</p>
                            @else
                                <ul class="divide-y divide-gray-200">
                                    @foreach ($upcomingAppointments as $appointment)
                                        <li class="py-4 flex justify-between items-center">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $appointment->title }}</p>
                                                <p class="text-sm text-gray-500">{{ $appointment->start_time->format("d/m/Y H:i") }} - {{ $appointment->end_time->format("H:i") }}</p>
                                                @if ($appointment->case)
                                                    <p class="text-sm text-gray-500">Caso: {{ $appointment->case->case_number ?? $appointment->case->case_type }}</p>
                                                @endif
                                            </div>
                                            <a href="{{ route("tenant.appointments.edit", $appointment) }}" class="text-indigo-600 hover:text-indigo-900">Ver Detalhes</a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>

                    <!-- Upcoming Deadlines -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Próximos Prazos</h3>
                            @if ($upcomingDeadlines->isEmpty())
                                <p class="text-gray-600">Nenhum prazo futuro.</p>
                            @else
                                <ul class="divide-y divide-gray-200">
                                    @foreach ($upcomingDeadlines as $deadline)
                                        <li class="py-4 flex justify-between items-center">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $deadline->title }}</p>
                                                <p class="text-sm text-gray-500">Vencimento: {{ $deadline->due_date->format("d/m/Y") }}</p>
                                                @if ($deadline->case)
                                                    <p class="text-sm text-gray-500">Caso: {{ $deadline->case->case_number ?? $deadline->case->case_type }}</p>
                                                @endif
                                            </div>
                                            <a href="{{ route("tenant.deadlines.edit", $deadline) }}" class="text-indigo-600 hover:text-indigo-900">Ver Detalhes</a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

