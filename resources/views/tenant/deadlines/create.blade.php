<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __("Adicionar Novo Prazo") }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route("tenant.deadlines.store") }}">
                        @csrf

                        <!-- Title -->
                        <div class="mb-4">
                            <label for="title" class="block text-sm font-medium text-gray-700">Título</label>
                            <input type="text" name="title" id="title" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="{{ old("title") }}" required autofocus>
                            @error("title")
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

                        <!-- Due Date -->
                        <div class="mb-4">
                            <label for="due_date" class="block text-sm font-medium text-gray-700">Data de Vencimento</label>
                            <input type="date" name="due_date" id="due_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="{{ old("due_date") }}" required>
                            @error("due_date")
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="mb-4">
                            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                            <select name="status" id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                <option value="pending" {{ old("status") == "pending" ? "selected" : "" }}>Pendente</option>
                                <option value="completed" {{ old("status") == "completed" ? "selected" : "" }}>Concluído</option>
                                <option value="overdue" {{ old("status") == "overdue" ? "selected" : "" }}>Atrasado</option>
                            </select>
                            @error("status")
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Case -->
                        <div class="mb-4">
                            <label for="case_id" class="block text-sm font-medium text-gray-700">Caso (Opcional)</label>
                            <select name="case_id" id="case_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">Nenhum</option>
                                @foreach ($cases as $case)
                                    <option value="{{ $case->id }}" {{ old("case_id") == $case->id ? "selected" : "" }}>{{ $case->case_number ?? $case->case_type }}</option>
                                @endforeach
                            </select>
                            @error("case_id")
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Adicionar Prazo
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

