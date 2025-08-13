<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __("Adicionar Novo Documento de Caso") }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route("tenant.case-documents.store") }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Case -->
                        <div class="mb-4">
                            <label for="case_id" class="block text-sm font-medium text-gray-700">Caso</label>
                            <select name="case_id" id="case_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                <option value="">Selecione um caso</option>
                                @foreach ($cases as $case)
                                    <option value="{{ $case->id }}" {{ old("case_id") == $case->id ? "selected" : "" }}>{{ $case->case_number ?? $case->case_type }}</option>
                                @endforeach
                            </select>
                            @error("case_id")
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Title -->
                        <div class="mb-4">
                            <label for="title" class="block text-sm font-medium text-gray-700">Título</label>
                            <input type="text" name="title" id="title" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="{{ old("title") }}" required>
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

                        <!-- File -->
                        <div class="mb-4">
                            <label for="file" class="block text-sm font-medium text-gray-700">Arquivo</label>
                            <input type="file" name="file" id="file" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            @error("file")
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Adicionar Documento
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

