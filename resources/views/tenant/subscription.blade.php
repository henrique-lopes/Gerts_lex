<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __("Assinatura") }} - {{ $tenant->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Current Subscription Status -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Status Atual da Assinatura</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    @if($tenant->subscription("default") && $tenant->subscription("default")->active()) bg-green-100 text-green-800
                                    @elseif($tenant->isOnTrial()) bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800 @endif">
                                    @if($tenant->subscription("default") && $tenant->subscription("default")->active())
                                        Ativo
                                    @elseif($tenant->isOnTrial())
                                        Trial
                                    @else
                                        Inativo
                                    @endif
                                </span>
                            </dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Plano Atual</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $tenant->subscription("default") && $tenant->subscription("default")->active() ? ucfirst($tenant->subscription("default")->stripe_plan) : ($tenant->isOnTrial() ? "Trial" : "Nenhum") }}
                            </dd>
                        </div>
                        
                        @if($tenant->isOnTrial())
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Trial Expira em</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $tenant->trial_ends_at->format("d/m/Y") }}
                                ({{ now()->diffInDays($tenant->trial_ends_at, false) }} dias)
                            </dd>
                        </div>
                        @endif
                    </div>

                    @if($tenant->subscription("default") && $tenant->subscription("default")->active())
                        <div class="mt-6 flex space-x-4">
                            <form action="{{ route("tenant.subscription.cancel") }}" method="POST">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    Cancelar Assinatura
                                </button>
                            </form>
                            @if($tenant->subscription("default")->onGracePeriod())
                                <form action="{{ route("tenant.subscription.resume") }}" method="POST">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                        Reativar Assinatura
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Available Plans -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-6">Planos Disponíveis</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @foreach($plans as $priceId => $plan)
                        <div class="border border-gray-200 rounded-lg p-6 {{ ($tenant->subscription("default") && $tenant->subscription("default")->stripe_plan === $priceId) ? "ring-2 ring-blue-500 bg-blue-50" : "" }}">
                            <div class="text-center">
                                <h4 class="text-lg font-medium text-gray-900">{{ $plan["name"] }}</h4>
                                <p class="mt-2 text-3xl font-bold text-gray-900">{{ $plan["price"] }}</p>
                                
                                @if($tenant->subscription("default") && $tenant->subscription("default")->stripe_plan === $priceId)
                                <span class="mt-4 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    Plano Atual
                                </span>
                                @endif
                            </div>
                            
                            <ul class="mt-6 space-y-3">
                                <li class="flex items-start">
                                    <span class="ml-3 text-sm text-gray-700">{{ $plan["description"] }}</span>
                                </li>
                            </ul>
                            
                            @if(!($tenant->subscription("default") && $tenant->subscription("default")->stripe_plan === $priceId))
                            <div class="mt-6">
                                <form action="{{ route("tenant.subscription.subscribe") }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="price_id" value="{{ $priceId }}">
                                    <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-150 ease-in-out">
                                        @if($tenant->isOnTrial() || !$tenant->subscription("default"))
                                            Escolher Plano
                                        @else
                                            Alterar para este Plano
                                        @endif
                                    </button>
                                </form>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    
                    <div class="mt-8 text-center">
                        <p class="text-sm text-gray-600">
                            Todos os planos incluem suporte técnico e atualizações automáticas.
                        </p>
                        <p class="text-sm text-gray-600 mt-2">
                            Você pode cancelar ou alterar seu plano a qualquer momento.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>


