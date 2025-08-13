# Sistema SaaS para Advogados - Inspirado no Astrea

Um sistema SaaS completo para gestão de escritórios de advocacia, desenvolvido em Laravel com arquitetura multi-tenant.

## 🚀 Funcionalidades Principais

### 📋 Gestão de Escritório
- **Dashboard Personalizado** - Visão geral com estatísticas em tempo real
- **Gestão de Advogados** - CRUD completo com especialidades e OAB
- **Gestão de Clientes** - Pessoas físicas e jurídicas
- **Gestão de Casos** - Acompanhamento completo de processos
- **Gestão de Honorários** - Controle financeiro detalhado
- **Agenda de Compromissos** - Audiências, reuniões e eventos
- **Controle de Prazos** - Alertas e notificações automáticas
- **Documentos** - Gestão de arquivos por caso

### 🏢 Multi-Tenancy
- **Isolamento Completo** - Dados separados por tenant
- **Subdomínios** - Acesso via subdomínio personalizado
- **Planos de Assinatura** - Sistema de trial e planos pagos
- **Configurações por Tenant** - Personalização individual

### 🔐 Autenticação e Segurança
- **Laravel Breeze** - Sistema de autenticação robusto
- **Controle de Acesso** - Permissões por usuário
- **Convites** - Sistema de convite para novos usuários
- **Auditoria** - Log de atividades com Spatie Activity Log

### 💳 Sistema de Pagamentos
- **Laravel Cashier** - Integração com Stripe
- **Webhooks** - Processamento automático de pagamentos
- **Planos Flexíveis** - Múltiplos planos de assinatura

### 🚀 Performance e Escalabilidade
- **Cache Inteligente** - Cache por tenant com invalidação automática
- **Rate Limiting** - Proteção contra abuso de APIs
- **Detecção N+1** - Middleware para otimização de queries
- **Filas com Horizon** - Processamento assíncrono

### 📱 APIs RESTful
- **Documentação Completa** - Endpoints bem documentados
- **Autenticação Sanctum** - Tokens de API seguros
- **Paginação** - Respostas otimizadas
- **Filtros e Busca** - Consultas flexíveis

## 🛠️ Tecnologias Utilizadas

### Backend
- **Laravel 10** - Framework PHP moderno
- **PHP 8.1** - Versão mais recente
- **SQLite** - Banco de dados para desenvolvimento
- **Laravel Breeze** - Autenticação
- **Spatie Laravel Multitenancy** - Multi-tenancy
- **Laravel Cashier** - Pagamentos
- **Laravel Horizon** - Filas
- **Spatie Permission** - Controle de acesso
- **Spatie Activity Log** - Auditoria

### Frontend
- **Blade Templates** - Sistema de templates do Laravel
- **Tailwind CSS** - Framework CSS utilitário
- **Alpine.js** - JavaScript reativo

## 📦 Instalação

### Pré-requisitos
- PHP 8.1 ou superior
- Composer
- Node.js e NPM

### Passos de Instalação

1. **Clone o repositório**
```bash
git clone <repository-url>
cd saas-laravel
```

2. **Instale as dependências**
```bash
composer install
npm install
```

3. **Configure o ambiente**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure o banco de dados**
```bash
touch database/database.sqlite
```

5. **Execute as migrações**
```bash
# Migrações do landlord (tenants)
php artisan migrate --path=database/migrations/landlord

# Demais migrações
php artisan migrate
```

6. **Execute os seeders**
```bash
php artisan db:seed
```

7. **Compile os assets**
```bash
npm run dev
```

8. **Inicie o servidor**
```bash
php artisan serve
```

## 🌐 Acesso ao Sistema

### Tenants Criados
- **demo.localhost:8000** - Empresa Demo
- **tech.localhost:8000** - Tech Solutions
- **startup.localhost:8000** - Startup Inovadora

### Usuários de Teste
- **Email:** admin@demo.com
- **Senha:** password123

## 📚 Estrutura do Projeto

### Models Principais
- `Tenant` - Inquilinos do sistema
- `User` - Usuários do sistema
- `Lawyer` - Advogados
- `Client` - Clientes
- `CaseModel` - Casos/Processos
- `Fee` - Honorários
- `Appointment` - Compromissos
- `Deadline` - Prazos
- `CaseDocument` - Documentos

### Controllers
- `TenantController` - Gestão de tenants
- `TenantDashboardController` - Dashboard do tenant
- `LawyerController` - Gestão de advogados
- `ClientController` - Gestão de clientes
- `CaseController` - Gestão de casos
- `FeeController` - Gestão de honorários
- `AppointmentController` - Gestão de compromissos
- `DeadlineController` - Gestão de prazos
- `CaseDocumentController` - Gestão de documentos

### APIs
- `LawyerApiController` - API de advogados
- `ClientApiController` - API de clientes

## 🔧 Configuração

### Multi-Tenancy
O sistema utiliza identificação por subdomínio. Configure seu `/etc/hosts`:
```
127.0.0.1 demo.localhost
127.0.0.1 tech.localhost
127.0.0.1 startup.localhost
```

### Filas
Para processar jobs em background:
```bash
php artisan queue:work
```

Para monitorar com Horizon:
```bash
php artisan horizon
```

### Cache
Para otimizar performance:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 📊 APIs Disponíveis

### Endpoints Públicos (Teste)
- `GET /api/test/lawyers` - Lista advogados
- `GET /api/test/clients` - Lista clientes
- `GET /api/test/stats` - Estatísticas do tenant
- `GET /api/test/send-email` - Testa envio de email via fila

### Endpoints Autenticados
- `GET /api/lawyers` - Lista advogados
- `POST /api/lawyers` - Cria advogado
- `GET /api/lawyers/{id}` - Mostra advogado
- `PUT /api/lawyers/{id}` - Atualiza advogado
- `DELETE /api/lawyers/{id}` - Remove advogado

### Autenticação
Use Laravel Sanctum para autenticação de API:
```bash
POST /api/login
Authorization: Bearer {token}
```

## 🧪 Testes

### Executar Testes
```bash
# Todos os testes
php artisan test

# Apenas testes unitários
php artisan test --testsuite=Unit

# Apenas testes de feature
php artisan test --testsuite=Feature
```

### Testes Implementados
- **LawyerTest** - Testes unitários do model Lawyer
- **LawyerApiTest** - Testes de feature da API de advogados

## 🚀 Deploy

### Preparação para Produção
1. Configure variáveis de ambiente de produção
2. Configure banco de dados MySQL/PostgreSQL
3. Configure Redis para cache e filas
4. Configure Stripe para pagamentos
5. Configure servidor web (Nginx/Apache)

### Comandos de Deploy
```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force
```

## 📈 Monitoramento

### Logs
- Logs de aplicação: `storage/logs/laravel.log`
- Logs de jobs: Integrado com sistema de logs

### Performance
- Cache de queries implementado
- Rate limiting configurado
- Detecção de N+1 queries ativa

### Filas
- Monitor com Laravel Horizon
- Jobs de email implementados
- Retry automático configurado

## 🤝 Contribuição

1. Fork o projeto
2. Crie uma branch para sua feature
3. Commit suas mudanças
4. Push para a branch
5. Abra um Pull Request

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo LICENSE para mais detalhes.

## 📞 Suporte

Para suporte e dúvidas:
- Email: suporte@exemplo.com
- Documentação: [Link para documentação]
- Issues: [Link para issues do GitHub]

---

**Desenvolvido com ❤️ usando Laravel e as melhores práticas de desenvolvimento SaaS.**
