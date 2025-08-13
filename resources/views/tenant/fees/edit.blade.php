<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __("Editar Honorário") }} - {{ $fee->case->case_number ?? $fee->case->case_type }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route("tenant.fees.update", $fee) }}">
                        @csrf
                        @method("PUT")

                        <!-- Case -->
                        <div class="mb-4">
                            <label for="case_id" class="block text-sm font-medium text-gray-700">Caso</label>
                            <select name="case_id" id="case_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                <option value="">Selecione um caso</option>
                                @foreach ($cases as $case)
                                    <option value="{{ $case->id }}" {{ old("case_id", $fee->case_id) == $case->id ? "selected" : "" }}>{{ $case->case_number ?? $case->case_type }}</option>
                                @endforeach
                            </select>
                            @error("case_id")
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Amount -->
                        <div class="mb-4">
                            <label for="amount" class="block text-sm font-medium text-gray-700">Valor</label>
                            <input type="number" step="0.01" name="amount" id="amount" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="{{ old("amount", $fee->amount) }}" required>
                            @error("amount")
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Type -->
                        <div class="mb-4">
                            <label for="type" class="block text-sm font-medium text-gray-700">Tipo</label>
                            <select name="type" id="type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                <option value="fixed" {{ old("type", $fee->type) == "fixed" ? "selected" : "" }}>Fixo</option>
                                <option value="percentage" {{ old("type", $fee->type) == "percentage" ? "selected" : "" }}>Porcentagem</option>
                            </select>
                            @error("type")
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="mb-4">
                            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                            <select name="status" id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                <option value="pending" {{ old("status", $fee->status) == "pending" ? "selected" : "" }}>Pendente</option>
                                <option value="paid" {{ old("status", $fee->status) == "paid" ? "selected" : "" }}>Pago</option>
                                <option value="overdue" {{ old("status", $fee->status) == "overdue" ? "selected" : "" }}>Atrasado</option>
                            </select>
                            @error("status")
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Due Date -->
                        <div class="mb-4">
                            <label for="due_date" class="block text-sm font-medium text-gray-700">Data de Vencimento</label>
                            <input type="date" name="due_date" id="due_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="{{ old("due_date", $fee->due_date?->format("Y-m-d")) }}">
                            @error("due_date")
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Paid Date -->
                        <div class="mb-4">
                            <label for="paid_date" class="block text-sm font-medium text-gray-700">Data de Pagamento</label>
                            <input type="date" name="paid_date" id="paid_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="{{ old("paid_date", $fee->paid_date?->format("Y-m-d")) }}">
                            @error("paid_date")
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Atualizar Honorário
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

