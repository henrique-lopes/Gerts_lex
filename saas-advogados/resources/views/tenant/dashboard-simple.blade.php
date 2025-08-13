<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - {{ $tenant->name }}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .card { border: 1px solid #ddd; padding: 20px; margin: 10px 0; border-radius: 5px; }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; }
        .stat { background: #f8f9fa; padding: 15px; border-radius: 5px; text-align: center; }
        .stat h3 { margin: 0; color: #333; }
        .stat p { margin: 5px 0 0 0; font-size: 24px; font-weight: bold; color: #007bff; }
    </style>
</head>
<body>
    <h1>Dashboard - {{ $tenant->name }}</h1>
    
    <div class="card">
        <h2>Informações do Tenant</h2>
        <p><strong>Nome:</strong> {{ $tenant->name }}</p>
        <p><strong>Domínio:</strong> {{ $tenant->domain }}</p>
        <p><strong>Status:</strong> {{ $stats['subscription_status'] }}</p>
        <p><strong>Plano:</strong> {{ $stats['subscription_plan'] }}</p>
    </div>

    <div class="card">
        <h2>Estatísticas</h2>
        <div class="stats">
            <div class="stat">
                <h3>Usuários</h3>
                <p>{{ $stats['users_count'] }}</p>
            </div>
            <div class="stat">
                <h3>Advogados</h3>
                <p>{{ $stats['lawyers_count'] }}</p>
            </div>
            <div class="stat">
                <h3>Clientes</h3>
                <p>{{ $stats['clients_count'] }}</p>
            </div>
            <div class="stat">
                <h3>Casos</h3>
                <p>{{ $stats['cases_count'] }}</p>
            </div>
            <div class="stat">
                <h3>Honorários</h3>
                <p>{{ $stats['fees_count'] }}</p>
            </div>
            <div class="stat">
                <h3>Compromissos</h3>
                <p>{{ $stats['appointments_count'] }}</p>
            </div>
            <div class="stat">
                <h3>Prazos</h3>
                <p>{{ $stats['deadlines_count'] }}</p>
            </div>
            <div class="stat">
                <h3>Documentos</h3>
                <p>{{ $stats['documents_count'] }}</p>
            </div>
        </div>
    </div>

    <div class="card">
        <h2>Links Rápidos</h2>
        <ul>
            <li><a href="/tenant/lawyers">Gerenciar Advogados</a></li>
            <li><a href="/tenant/clients">Gerenciar Clientes</a></li>
            <li><a href="/tenant/cases">Gerenciar Casos</a></li>
            <li><a href="/tenant/fees">Gerenciar Honorários</a></li>
            <li><a href="/tenant/appointments">Gerenciar Compromissos</a></li>
            <li><a href="/tenant/deadlines">Gerenciar Prazos</a></li>
            <li><a href="/tenant/case-documents">Gerenciar Documentos</a></li>
        </ul>
    </div>
</body>
</html>

