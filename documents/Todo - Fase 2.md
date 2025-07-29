# TODO — Fase 2

## Legenda
- [ ] pendente   |  [x] concluído

---

### Lista de tarefas

- [ ] **01 Webhook de Inscrição** ([user story](../user-historyes/01-webhook-inscricao.md))
  - [ ] **01.1** Criar tela para cadastrar a **URL do webhook** no CRUD **Produto**
  - [ ] **01.2** Disparar **POST JSON** ao criar/atualizar **Inscrição**
    - [ ] **01.2.1** Enviar todos os campos de **Cliente** + **Inscrição**
    - [ ] **01.2.2** Incluir objeto **mapping** conforme tabela de equivalência
  - [ ] **01.3** Executar disparo **somente** quando o **status** da Inscrição corresponder às regras

- [x] **02 Kanban de Inscrições** ([user story](../user-historyes/02-kanban-inscricoes.md))
  - [x] **02.1** Adicionar seletor (`<select>`) para escolher coluna: **Status / Semana / Fase / Faixa de Faturamento**
  - [x] **02.2** Renderizar colunas em **cards** Bootstrap (drag-and-drop opcional)
  - [x] **02.3** Criar **CRUD "Faixa de Faturamento"**

- [ ] **03 Integração WhatsApp (Evolution API)** ([user story](../user-historyes/03-integracao-whatsapp.md))
  - [x] **03.1** Configurar credenciais no `.env`
  - [ ] **03.2** Criar tela de chat
    - [ ] **03.2.1** Sidebar (15 – 20 %) listando conversas recentes
    - [ ] **03.2.2** Painel de chat (80 – 85 %) com balões — cliente à esquerda, atendente à direita
  - [x] **03.3** Associar conversa a **clientes** ou **contatos** (estrutura de banco criada)
  - [ ] **03.4** Implementar **webhook** de recepção
  - [ ] **03.5** Implementar **endpoint** de envio via **Evolution API**

---

## Progresso Atual (29/07/2025)

### ✅ Concluído
- **Kanban de Inscrições**: Totalmente implementado com seletor de visualização e CRUD de Faixa de Faturamento
- **WhatsApp - Credenciais**: Configuração no .env e config/services.php
- **WhatsApp - Estrutura de Banco**: Migrações e modelos criados (WhatsappConversation, WhatsappMessage, ConversationLink)
- **WhatsApp - Controller**: API endpoints para conversas, mensagens e associações

### 🔄 Em Andamento
- **WhatsApp - Tela de Chat**: Criando views e corrigindo erros JavaScript

### 📋 Próximas Tarefas
1. Finalizar interface do chat (sidebar + painel)
2. Implementar webhook de recepção
3. Implementar endpoint de envio via Evolution API
4. Adicionar sistema de filas para mensagens
5. Implementar WebSocket para tempo real

> Atualize as caixas de seleção à medida que avançar. Mantemos todos os documentos em português e slugs numerados para preservar a ordem.
