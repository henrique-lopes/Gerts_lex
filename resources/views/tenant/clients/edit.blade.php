<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __("Editar Cliente") }} - {{ $client->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route("tenant.clients.update", $client) }}">
                        @csrf
                        @method("PUT")

                        <!-- Name -->
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">Nome</label>
                            <input type="text" name="name" id="name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="{{ old("name", $client->name) }}" required autofocus>
                            @error("name")
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email Address -->
                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" id="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="{{ old("email", $client->email) }}">
                            @error("email")
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div class="mb-4">
                            <label for="phone" class="block text-sm font-medium text-gray-700">Telefone</label>
                            <input type="text" name="phone" id="phone" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="{{ old("phone", $client->phone) }}">
                            @error("phone")
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- CPF/CNPJ -->
                        <div class="mb-4">
                            <label for="cpf_cnpj" class="block text-sm font-medium text-gray-700">CPF/CNPJ</label>
                            <input type="text" name="cpf_cnpj" id="cpf_cnpj" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="{{ old("cpf_cnpj", $client->cpf_cnpj) }}">
                            @error("cpf_cnpj")
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Address -->
                        <div class="mb-4">
                            <label for="address" class="block text-sm font-medium text-gray-700">Endereço</label>
                            <textarea name="address" id="address" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old("address", $client->address) }}</textarea>
                            @error("address")
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Atualizar Cliente
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

