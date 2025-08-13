<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Configurações') }} - {{ $tenant->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-6">Configurações do Tenant</h3>
                    
                    <form method="POST" action="{{ route('tenant.settings.update') }}">
                        @csrf
                        
                        <!-- Tenant Name -->
                        <div class="mb-6">
                            <label for="name" class="block text-sm font-medium text-gray-700">
                                Nome da Organização
                            </label>
                            <input type="text" 
                                   name="name" 
                                   id="name" 
                                   value="{{ old('name', $tenant->name) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   required>
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Domain (Read-only) -->
                        <div class="mb-6">
                            <label for="domain" class="block text-sm font-medium text-gray-700">
                                Domínio
                            </label>
                            <input type="text" 
                                   name="domain" 
                                   id="domain" 
                                   value="{{ $tenant->domain }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-50 sm:text-sm"
                                   readonly>
                            <p class="mt-2 text-sm text-gray-500">
                                Seu tenant está acessível em: <strong>{{ $tenant->domain }}.seudominio.com</strong>
                            </p>
                        </div>

                        <!-- Settings JSON (for future customizations) -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                Configurações Personalizadas
                            </label>
                            
                            <!-- Example settings -->
                            <div class="space-y-4">
                                <div class="flex items-center">
                                    <input type="checkbox" 
                                           name="settings[notifications_enabled]" 
                                           id="notifications_enabled"
                                           value="1"
                                           {{ (old('settings.notifications_enabled', $tenant->settings['notifications_enabled'] ?? true)) ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="notifications_enabled" class="ml-2 block text-sm text-gray-900">
                                        Habilitar notificações por email
                                    </label>
                                </div>
                                
                                <div class="flex items-center">
                                    <input type="checkbox" 
                                           name="settings[public_registration]" 
                                           id="public_registration"
                                           value="1"
                                           {{ (old('settings.public_registration', $tenant->settings['public_registration'] ?? false)) ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="public_registration" class="ml-2 block text-sm text-gray-900">
                                        Permitir registro público de usuários
                                    </label>
                                </div>
                                
                                <div>
                                    <label for="timezone" class="block text-sm font-medium text-gray-700">
                                        Fuso Horário
                                    </label>
                                    <select name="settings[timezone]" 
                                            id="timezone"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        <option value="America/Sao_Paulo" {{ (old('settings.timezone', $tenant->settings['timezone'] ?? 'America/Sao_Paulo') === 'America/Sao_Paulo') ? 'selected' : '' }}>
                                            São Paulo (UTC-3)
                                        </option>
                                        <option value="America/New_York" {{ (old('settings.timezone', $tenant->settings['timezone'] ?? '') === 'America/New_York') ? 'selected' : '' }}>
                                            New York (UTC-5)
                                        </option>
                                        <option value="Europe/London" {{ (old('settings.timezone', $tenant->settings['timezone'] ?? '') === 'Europe/London') ? 'selected' : '' }}>
                                            London (UTC+0)
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end">
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Salvar Configurações
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-red-900 mb-4">Zona de Perigo</h3>
                    
                    <div class="bg-red-50 border border-red-200 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">
                                    Cancelar Conta
                                </h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <p>
                                        Uma vez que você cancelar sua conta, todos os dados serão permanentemente deletados.
                                        Esta ação não pode ser desfeita.
                                    </p>
                                </div>
                                <div class="mt-4">
                                    <button type="button" 
                                            class="bg-red-600 text-white py-2 px-4 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition duration-150 ease-in-out text-sm">
                                        Cancelar Conta
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

