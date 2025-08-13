<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __("Adicionar Novo Advogado") }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route("tenant.lawyers.store") }}">
                        @csrf

                        <!-- Name -->
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">Nome</label>
                            <input type="text" name="name" id="name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="{{ old("name") }}" required autofocus>
                            @error("name")
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email Address -->
                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" id="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="{{ old("email") }}" required>
                            @error("email")
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-4">
                            <label for="password" class="block text-sm font-medium text-gray-700">Senha</label>
                            <input type="password" name="password" id="password" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required autocomplete="new-password">
                            @error("password")
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-4">
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar Senha</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required autocomplete="new-password">
                        </div>

                        <!-- OAB Number -->
                        <div class="mb-4">
                            <label for="oab_number" class="block text-sm font-medium text-gray-700">Número OAB</label>
                            <input type="text" name="oab_number" id="oab_number" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="{{ old("oab_number") }}">
                            @error("oab_number")
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- OAB State -->
                        <div class="mb-4">
                            <label for="oab_state" class="block text-sm font-medium text-gray-700">Estado OAB</label>
                            <input type="text" name="oab_state" id="oab_state" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="{{ old("oab_state") }}">
                            @error("oab_state")
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Specialties -->
                        <div class="mb-4">
                            <label for="specialties" class="block text-sm font-medium text-gray-700">Especialidades (separadas por vírgula)</label>
                            <input type="text" name="specialties" id="specialties" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="{{ old("specialties") }}">
                            @error("specialties")
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Adicionar Advogado
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

