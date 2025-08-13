<<<<<<< HEAD
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
=======
# 🏛️ Gert's Lex - Sistema Jurídico SaaS

![Gert's Lex Logo](public/images/gerts-lex-logo.png)

**Gert's Lex** é um sistema SaaS (Software as a Service) completo para gestão de escritórios de advocacia, desenvolvido em Laravel com design profissional focado no ramo jurídico.

## 🎯 Visão Geral

O Gert's Lex oferece uma solução completa para escritórios de advocacia gerenciarem seus clientes, casos, documentos, agenda e equipe jurídica em uma plataforma moderna e intuitiva.

### ✨ Características Principais

- **🎨 Design Profissional**: Interface moderna com cores do ramo jurídico (azul marinho, branco, cinza)
- **🏢 Multi-Tenant**: Suporte a múltiplos escritórios com isolamento completo de dados
- **📱 Responsivo**: Interface adaptável para desktop, tablet e mobile
- **⚡ Performance**: Carregamento rápido com Laravel otimizado
- **🔒 Seguro**: Controle de acesso e autenticação robusta

## 🚀 Funcionalidades Implementadas

### ✅ **Dashboard Principal**
- Estatísticas em tempo real
- Visão geral de advogados, clientes e casos
- Cards informativos com gradientes elegantes
- Banner de boas-vindas personalizado

### ✅ **Gestão de Advogados**
- Cadastro completo de advogados
- Informações de OAB e especialidades
- Sistema de usuários associados
- Interface de listagem e edição

### ✅ **Gestão de Clientes**
- Cadastro de pessoas físicas e jurídicas
- Informações de contato completas
- CPF/CNPJ e dados de endereço
- Histórico de relacionamento

### ✅ **Gestão de Casos**
- Controle de processos jurídicos
- Números de processo e tribunais
- Status e datas importantes
- Associação com advogados e clientes

### ✅ **Sistema Multi-Tenant**
- Isolamento completo de dados por escritório
- Middleware de detecção de tenant por domínio
- Controle de acesso e permissões
- Gestão centralizada

### ✅ **APIs RESTful**
- Endpoints completos para todas as funcionalidades
- Operações CRUD (Create, Read, Update, Delete)
- Validação e tratamento de erros
- Suporte a JSON

### ✅ **Sistema de Seeds**
- Dados de demonstração pré-carregados
- Seeds para advogados, clientes e casos
- Dados realistas para testes
- Fácil reset do banco de dados

## 🛠️ Tecnologias Utilizadas

### **Backend**
- **Laravel 10** - Framework PHP moderno
- **PHP 8.2+** - Linguagem principal
- **MySQL/SQLite** - Banco de dados
- **Eloquent ORM** - Mapeamento objeto-relacional

### **Frontend**
- **Blade Templates** - Sistema de templates do Laravel
- **Tailwind CSS** - Framework CSS utilitário
- **Alpine.js** - Framework JavaScript reativo
- **Vite** - Build tool moderno

### **Ferramentas de Desenvolvimento**
- **Laravel Horizon** - Gerenciamento de filas
- **Laravel Telescope** - Debug e monitoramento
- **PHPUnit** - Testes automatizados
- **Laravel Pint** - Code style

## 📋 Pré-requisitos

Antes de executar o projeto, certifique-se de ter instalado:

- **PHP 8.2+**
- **Composer** (gerenciador de dependências PHP)
- **Node.js 18+** e **npm**
- **MySQL** ou **SQLite**
- **Git** (para clonagem do repositório)

## 🚀 Como Executar Localmente

### 1. **Clone o Repositório**
```bash
git clone https://github.com/henrique-lopes/Gerts_lex.git
cd Gerts_lex
```

### 2. **Instale as Dependências PHP**
```bash
composer install
```

### 3. **Instale as Dependências Node.js**
```bash
npm install
```

### 4. **Configure o Ambiente**
```bash
# Copie o arquivo de configuração
cp .env.example .env

# Gere a chave da aplicação
php artisan key:generate
```

### 5. **Configure o Banco de Dados**
Edite o arquivo `.env` com suas configurações de banco:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gerts_lex
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

### 6. **Execute as Migrações e Seeds**
```bash
# Execute as migrações
php artisan migrate

# Execute os seeds (dados de exemplo)
php artisan db:seed
```

### 7. **Compile os Assets**
```bash
# Para desenvolvimento
npm run dev

# Para produção
npm run build
```

### 8. **Inicie o Servidor**
>>>>>>> 75d276e55e8071ac5037fb79472c91094dcb9e2e
```bash
php artisan serve
```

<<<<<<< HEAD
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
=======
### 9. **Acesse o Sistema**
Abra seu navegador e acesse:
```
http://localhost:8000
```

## 📁 Estrutura do Projeto

```
Gerts_lex/
├── app/
│   ├── Http/
│   │   ├── Controllers/          # Controllers da aplicação
│   │   ├── Middleware/           # Middlewares customizados
│   │   └── Kernel.php           # Kernel HTTP
│   ├── Models/                  # Models Eloquent
│   └── Jobs/                    # Jobs para filas
├── database/
│   ├── migrations/              # Migrações do banco
│   ├── seeders/                 # Seeds para dados de exemplo
│   └── factories/               # Factories para testes
├── resources/
│   ├── views/                   # Templates Blade
│   ├── js/                      # JavaScript
│   └── css/                     # Estilos CSS
├── routes/
│   ├── web.php                  # Rotas web
│   └── api.php                  # Rotas da API
├── tests/                       # Testes automatizados
├── config/                      # Arquivos de configuração
└── public/                      # Arquivos públicos
```

## 🔧 Configuração Multi-Tenant

### **Middleware de Tenant**
O sistema utiliza o middleware `InitializeTenancyByDomain` para detectar o tenant baseado no domínio:

```php
// app/Http/Middleware/InitializeTenancyByDomain.php
public function handle($request, Closure $next)
{
    $domain = $request->getHost();
    // Lógica de detecção do tenant
    return $next($request);
}
```

### **Configuração de Domínios**
Para desenvolvimento local, adicione ao seu `/etc/hosts`:
```
127.0.0.1 silva.localhost
127.0.0.1 santos.localhost
127.0.0.1 oliveira.localhost
```

## 🎨 Design System

### **Cores Principais**
- **Azul Marinho**: `#1e3a8a` (cor primária)
- **Azul Claro**: `#3b82f6` (acentos)
- **Branco**: `#ffffff` (backgrounds)
- **Cinza**: `#6b7280` (textos secundários)

### **Componentes Blade**
- **Cards**: Componentes reutilizáveis
- **Modais**: Sistema de modais interativos
- **Formulários**: Validação e feedback
- **Navegação**: Sidebar responsiva

## 🔄 APIs Disponíveis

### **Endpoints Principais**

#### **Advogados**
- `GET /api/lawyers` - Listar advogados
- `POST /api/lawyers` - Criar advogado
- `PUT /api/lawyers/{id}` - Atualizar advogado
- `DELETE /api/lawyers/{id}` - Excluir advogado

#### **Clientes**
- `GET /api/clients` - Listar clientes
- `POST /api/clients` - Criar cliente
- `PUT /api/clients/{id}` - Atualizar cliente
- `DELETE /api/clients/{id}` - Excluir cliente

#### **Casos**
- `GET /api/cases` - Listar casos
- `POST /api/cases` - Criar caso
- `PUT /api/cases/{id}` - Atualizar caso
- `DELETE /api/cases/{id}` - Excluir caso

## 🧪 Testes

### **Executando Testes**
```bash
# Execute todos os testes
php artisan test

# Testes com cobertura
php artisan test --coverage

# Testes específicos
php artisan test --filter LawyerTest
```

### **Tipos de Testes**
- **Unit Tests**: Testes de unidade para models
- **Feature Tests**: Testes de funcionalidades
- **API Tests**: Testes das APIs

## 🚧 Funcionalidades em Desenvolvimento

### 🔐 **Sistema de Autenticação**
- [ ] Tela de login profissional
- [ ] Controle de sessão por escritório
- [ ] Middleware de verificação de pagamentos
- [ ] Logout automático para contas suspensas
- [ ] Recuperação de senha

### 💳 **Sistema de Pagamentos**
- [ ] Gateway de pagamento brasileiro
- [ ] Suporte a cartão de crédito/débito
- [ ] Integração com PIX (QR Code + chave)
- [ ] Webhook para confirmação automática
- [ ] Histórico completo de transações
- [ ] Dashboard de pagamentos para contas bloqueadas

### 📅 **Sistema de Agenda**
- [ ] Calendário interativo
- [ ] Agendamento de compromissos
- [ ] Lembretes automáticos
- [ ] Integração com Google Calendar

### 📁 **Gestão de Documentos**
- [ ] Upload de arquivos
- [ ] Categorização de documentos
- [ ] Versionamento
- [ ] Assinatura digital

### 🔒 **Controle de Acesso**
- [ ] Bloqueio automático por falta de pagamento
- [ ] Período de carência configurável
- [ ] Notificações de vencimento
- [ ] Restrição de funcionalidades por plano

### 📊 **Relatórios e Analytics**
- [ ] Relatórios de casos por período
- [ ] Estatísticas de produtividade
- [ ] Gráficos de receita e honorários
- [ ] Exportação em PDF/Excel

## 🔧 Comandos Artisan Úteis

```bash
# Limpar cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Gerar componentes
php artisan make:controller NomeController
php artisan make:model NomeModel -m
php artisan make:seeder NomeSeeder

# Executar filas
php artisan queue:work

# Executar Horizon (se instalado)
php artisan horizon

# Executar testes
php artisan test
```

## 🤝 Contribuição

Para contribuir com o projeto:

1. **Fork** o repositório
2. Crie uma **branch** para sua feature (`git checkout -b feature/nova-funcionalidade`)
3. **Commit** suas mudanças (`git commit -m 'Adiciona nova funcionalidade'`)
4. **Push** para a branch (`git push origin feature/nova-funcionalidade`)
5. Abra um **Pull Request**

## 📝 Licença

Este projeto está sob a licença MIT. Veja o arquivo `LICENSE` para mais detalhes.

## 👥 Equipe

- **Desenvolvimento**: Henrique Lopes
- **Design**: Sistema baseado em cores jurídicas profissionais
- **Logo**: Design oficial Gert's Lex

## 📞 Suporte

Para suporte técnico ou dúvidas:
- **Email**: suporte@gertslex.com
- **GitHub Issues**: [Reportar Bug](https://github.com/henrique-lopes/Gerts_lex/issues)

## 🎯 Roadmap

### **Versão 1.0** (Atual)
- ✅ Interface básica e navegação
- ✅ CRUD completo para todas as entidades
- ✅ Sistema multi-tenant
- ✅ Seeds e dados de exemplo

### **Versão 1.1** (Próxima)
- 🔐 Sistema de autenticação
- 💳 Pagamentos e assinaturas
- 🔒 Controle de acesso

### **Versão 1.2** (Futuro)
- 📊 Relatórios avançados
- 🔔 Sistema de notificações
- 📱 App mobile

### **Versão 2.0** (Longo Prazo)
- 🔧 Integrações externas
- 🤖 IA para análise de documentos
- 📈 Analytics avançados

---

**Gert's Lex** - Transformando a gestão jurídica com tecnologia Laravel moderna e design profissional.

>>>>>>> 75d276e55e8071ac5037fb79472c91094dcb9e2e
