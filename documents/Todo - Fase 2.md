# TODO — Fase 2

## Legenda
- [ ] pendente   |  [x] concluído

---

### Lista de tarefas

- [ ] **01 Webhook de Inscrição** ([user story](../user-historyes/01-webhook-inscricao.md))
  - [x] **01.1** Criar tela para cadastrar a **URL do webhook** no CRUD **Produto**
  - [x] **01.2** Disparar **POST JSON** ao criar/atualizar **Inscrição**
    - [x] **01.2.1** Enviar todos os campos de **Cliente** + **Inscrição**
    - [x] **01.2.2** Incluir objeto **mapping** conforme tabela de equivalência
  - [x] **01.3** Executar disparo **somente** quando o **status** da Inscrição corresponder às regras
  - [x] **01.4** Implementar testes PHPUnit/Pest para o webhook de inscrição (sucesso, falha, retries)
  - [x] **01.5** Criar tela "Histórico de Webhooks" com listagem de envios, status, tentativas e botão Reenviar
  - [x] **01.6** Implementar logs detalhados na tabela `webhook_logs` para cada tentativa de envio

- [x] **02 Kanban de Inscrições** ([user story](../user-historyes/02-kanban-inscricoes.md))
  - [x] **02.1** Adicionar seletor (`<select>`) para escolher coluna: **Status / Semana / Fase / Faixa de Faturamento**
  - [x] **02.2** Renderizar colunas em **cards** Bootstrap (drag-and-drop opcional)
  - [x] **02.3** Criar **CRUD "Faixa de Faturamento"**
  - [ ] **02.4** Implementar Drag-and-Drop para cards do Kanban
  - [ ] **02.5** Implementar persistência da escolha do usuário para o seletor de colunas do Kanban
  - [ ] **02.6** Implementar logs de movimento do Kanban na tabela `kanban_movements`
  - [ ] **02.7** Implementar scroll infinito para as colunas do Kanban

- [x] **03 Integração WhatsApp (Evolution API)** ([user story](../user-historyes/03-integracao-whatsapp.md))
  - [x] **03.1** Configurar credenciais no `.env`
  - [x] **03.2** Criar tela de chat
    - [x] **03.2.1** Sidebar (15 – 20 %) listando conversas recentes
    - [x] **03.2.2** Painel de chat (80 – 85 %) com balões — cliente à esquerda, atendente à direita
  - [x] **03.3** Associar conversa a **clientes** ou **contatos** (estrutura de banco criada)
  - [x] **03.4** Implementar **webhook** de recepção
  - [x] **03.5** Implementar **endpoint** de envio via **Evolution API**
  - [ ] **03.6** Criar tela "Canais" para gerenciar números/instâncias (status, QR, logs)
  - [ ] **03.7** Implementar sincronização de histórico (importação em lote + cache)
  - [ ] **03.8** Implementar notificações (badge nas conversas e sino global para mensagens não lidas)
  - [ ] **03.9** Implementar importação obrigatória do histórico – processamento em lote usando filas `messages_sync`
  - [ ] **03.10** Implementar `whatsapp_logs` para registrar latência, payload e resposta
  - [ ] **03.11** Implementar `whatsapp_channels` para gerenciar instâncias (número, instanceId, status, QR atual, última renovação, log)
  - [ ] **03.12** Implementar `whatsapp_channels` CRUD para gerenciar instâncias
  - [ ] **03.13** Implementar `whatsapp_channels` para renovação de QR a cada ~40s até "Connected"
  - [ ] **03.14** Implementar atribuição de atendente para cada conversa
  - [ ] **03.15** Implementar notificações (badge nas conversas e sino global para mensagens não lidas do atendente atribuído)
  - [ ] **03.16** Implementar UI inspirada no WhatsApp Web (balões cinza cliente / verde atendente); avatar, data, ticks ✓✓
  - [ ] **03.17** Implementar Sandbox para ambiente de teste via Evolution sandbox para CI/Dusk