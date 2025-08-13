# Análise das Funcionalidades do Astrea para Implementação no SaaS

## Funcionalidades Principais Identificadas

### 1. **Gestão de Agenda e Prazos**
- Agenda integrada com visualização de compromissos
- Alertas automáticos de movimentações, prazos e tarefas iminentes
- Controle avançado de prazos processuais
- Gestão de tarefas com delegação e acompanhamento

### 2. **Gestão de Processos Jurídicos**
- Busca automática de processos por OAB ou CNJ
- Atualizações processuais automáticas
- Recebimento de publicações e intimações
- Controle de movimentações processuais

### 3. **Gestão Financeira e Honorários**
- Emissão de boletos com PIX integrado
- Controle de honorários por caso
- Gestão de fluxo de caixa
- Lembretes automáticos de pagamento
- Visão detalhada da saúde financeira

### 4. **Comunicação com Clientes**
- Integração com WhatsApp Web
- Envio de informações sobre audiências, tarefas e prazos
- Comunicação centralizada
- Atendimento ao cliente otimizado

### 5. **Gestão de Casos/Clientes**
- Cadastro completo de clientes
- Organização de casos por cliente
- Histórico de atendimentos
- Documentos anexados por caso

## Estrutura de Dados Necessária

### Tabelas Principais:
1. **lawyers** (advogados)
   - id, user_id, oab_number, oab_state, specialties, created_at, updated_at

2. **clients** (clientes)
   - id, tenant_id, name, email, phone, cpf_cnpj, address, created_at, updated_at

3. **cases** (casos/processos)
   - id, tenant_id, client_id, lawyer_id, case_number, case_type, court, status, description, created_at, updated_at

4. **fees** (honorários)
   - id, case_id, amount, type (fixed/percentage), status, due_date, paid_date, created_at, updated_at

5. **appointments** (compromissos/agenda)
   - id, tenant_id, lawyer_id, case_id, title, description, start_time, end_time, type, created_at, updated_at

6. **deadlines** (prazos)
   - id, case_id, title, description, due_date, status, priority, created_at, updated_at

7. **case_documents** (documentos do caso)
   - id, case_id, name, file_path, type, uploaded_by, created_at, updated_at

## Funcionalidades a Implementar

### Fase 5: Funcionalidades Específicas para Advogados

1. **Sistema de Agenda**
   - Calendário interativo
   - Agendamento de compromissos
   - Alertas e notificações
   - Visualização por dia/semana/mês

2. **Gestão de Casos**
   - CRUD completo de casos
   - Vinculação com clientes
   - Status e acompanhamento
   - Timeline de atividades

3. **Controle de Honorários**
   - Cadastro de valores por caso
   - Geração de boletos
   - Controle de pagamentos
   - Relatórios financeiros

4. **Dashboard Específico**
   - Casos em andamento
   - Prazos próximos
   - Honorários pendentes
   - Agenda do dia

5. **Gestão de Clientes**
   - Cadastro completo
   - Histórico de casos
   - Documentos anexados
   - Comunicação registrada

## Tecnologias e Pacotes Adicionais

- **Laravel Calendar**: Para gestão de agenda
- **Laravel Excel**: Para relatórios
- **Laravel Notifications**: Para alertas
- **Spatie Media Library**: Para documentos
- **Laravel Charts**: Para dashboards
- **Laravel PDF**: Para geração de documentos

## Próximos Passos

1. Criar migrations para as novas tabelas
2. Implementar models com relacionamentos
3. Desenvolver controllers específicos
4. Criar views para cada funcionalidade
5. Implementar sistema de notificações
6. Desenvolver dashboard específico para advogados

