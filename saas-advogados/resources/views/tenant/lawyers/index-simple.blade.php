<!DOCTYPE html>
<html>
<head>
    <title>Advogados - {{ $tenant->name ?? 'Sistema' }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { background: #f8f9fa; padding: 20px; margin-bottom: 20px; }
        .lawyer { border: 1px solid #ddd; padding: 15px; margin: 10px 0; }
        .btn { background: #007bff; color: white; padding: 8px 16px; text-decoration: none; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Advogados - {{ $tenant->name ?? 'Sistema' }}</h1>
        <a href="{{ route('dashboard') }}" class="btn">← Voltar ao Dashboard</a>
    </div>

    <h2>Lista de Advogados ({{ $lawyers->total() }})</h2>

    @if($lawyers->count() > 0)
        @foreach($lawyers as $lawyer)
            <div class="lawyer">
                <h3>{{ $lawyer->name }}</h3>
                <p><strong>Email:</strong> {{ $lawyer->email }}</p>
                <p><strong>OAB:</strong> {{ $lawyer->oab_number }} - {{ $lawyer->oab_state }}</p>
                <p><strong>Especialidades:</strong> {{ $lawyer->specialties }}</p>
                <p><strong>Usuário:</strong> {{ $lawyer->user->name ?? 'N/A' }}</p>
            </div>
        @endforeach

        <div style="margin-top: 20px;">
            {{ $lawyers->links() }}
        </div>
    @else
        <p>Nenhum advogado encontrado.</p>
    @endif
</body>
</html>

