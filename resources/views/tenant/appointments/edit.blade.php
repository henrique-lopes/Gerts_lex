<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __("Editar Compromisso") }} - {{ $appointment->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route("tenant.appointments.update", $appointment) }}">
                        @csrf
                        @method("PUT")

                        <!-- Title -->
                        <div class="mb-4">
                            <label for="title" class="block text-sm font-medium text-gray-700">Título</label>
                            <input type="text" name="title" id="title" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="{{ old("title", $appointment->title) }}" required autofocus>
                            @error("title")
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label for="description" class="block text-sm font-medium text-gray-700">Descrição (Opcional)</label>
                            <textarea name="description" id="description" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old("description", $appointment->description) }}</textarea>
                            @error("description")
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Start Time -->
                        <div class="mb-4">
                            <label for="start_time" class="block text-sm font-medium text-gray-700">Início</label>
                            <input type="datetime-local" name="start_time" id="start_time" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="{{ old("start_time", $appointment->start_time->format("Y-m-d\\TH:i")) }}" required>
                            @error("start_time")
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- End Time -->
                        <div class="mb-4">
                            <label for="end_time" class="block text-sm font-medium text-gray-700">Fim</label>
                            <input type="datetime-local" name="end_time" id="end_time" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="{{ old("end_time", $appointment->end_time->format("Y-m-d\\TH:i")) }}" required>
                            @error("end_time")
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Type -->
                        <div class="mb-4">
                            <label for="type" class="block text-sm font-medium text-gray-700">Tipo</label>
                            <input type="text" name="type" id="type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="{{ old("type", $appointment->type) }}" required>
                            @error("type")
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Case -->
                        <div class="mb-4">
                            <label for="case_id" class="block text-sm font-medium text-gray-700">Caso (Opcional)</label>
                            <select name="case_id" id="case_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">Nenhum</option>
                                @foreach ($cases as $case)
                                    <option value="{{ $case->id }}" {{ old("case_id", $appointment->case_id) == $case->id ? "selected" : "" }}>{{ $case->case_number ?? $case->case_type }}</option>
                                @endforeach
                            </select>
                            @error("case_id")
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Lawyer -->
                        <div class="mb-4">
                            <label for="lawyer_id" class="block text-sm font-medium text-gray-700">Advogado Responsável (Opcional)</label>
                            <select name="lawyer_id" id="lawyer_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">Nenhum</option>
                                @foreach ($lawyers as $lawyer)
                                    <option value="{{ $lawyer->id }}" {{ old("lawyer_id", $appointment->lawyer_id) == $lawyer->id ? "selected" : "" }}>{{ $lawyer->user->name }}</option>
                                @endforeach
                            </select>
                            @error("lawyer_id")
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Atualizar Compromisso
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

