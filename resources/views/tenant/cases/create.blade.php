<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __("Adicionar Novo Caso") }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route("tenant.cases.store") }}">
                        @csrf

                        <!-- Client -->
                        <div class="mb-4">
                            <label for="client_id" class="block text-sm font-medium text-gray-700">Cliente</label>
                            <select name="client_id" id="client_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                <option value="">Selecione um cliente</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}" {{ old("client_id") == $client->id ? "selected" : "" }}>{{ $client->name }}</option>
                                @endforeach
                            </select>
                            @error("client_id")
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Lawyer -->
                        <div class="mb-4">
                            <label for="lawyer_id" class="block text-sm font-medium text-gray-700">Advogado Responsável</label>
                            <select name="lawyer_id" id="lawyer_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">Nenhum</option>
                                @foreach ($lawyers as $lawyer)
                                    <option value="{{ $lawyer->id }}" {{ old("lawyer_id") == $lawyer->id ? "selected" : "" }}>{{ $lawyer->user->name }}</option>
                                @endforeach
                            </select>
                            @error("lawyer_id")
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Case Number -->
                        <div class="mb-4">
                            <label for="case_number" class="block text-sm font-medium text-gray-700">Número do Caso (Opcional)</label>
                            <input type="text" name="case_number" id="case_number" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="{{ old("case_number") }}">
                            @error("case_number")
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Case Type -->
                        <div class="mb-4">
                            <label for="case_type" class="block text-sm font-medium text-gray-700">Tipo de Caso</label>
                            <input type="text" name="case_type" id="case_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="{{ old("case_type") }}" required>
                            @error("case_type")
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Court -->
                        <div class="mb-4">
                            <label for="court" class="block text-sm font-medium text-gray-700">Tribunal (Opcional)</label>
                            <input type="text" name="court" id="court" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="{{ old("court") }}">
                            @error("court")
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="mb-4">
                            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                            <select name="status" id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                <option value="pending" {{ old("status") == "pending" ? "selected" : "" }}>Pendente</option>
                                <option value="active" {{ old("status") == "active" ? "selected" : "" }}>Ativo</option>
                                <option value="closed" {{ old("status") == "closed" ? "selected" : "" }}>Fechado</option>
                            </select>
                            @error("status")
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label for="description" class="block text-sm font-medium text-gray-700">Descrição (Opcional)</label>
                            <textarea name="description" id="description" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old("description") }}</textarea>
                            @error("description")
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Adicionar Caso
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

