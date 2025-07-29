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

- [ ] **02 Kanban de Inscrições** ([user story](../user-historyes/02-kanban-inscricoes.md))
  - [ ] **02.1** Adicionar seletor (`<select>`) para escolher coluna: **Status / Semana / Fase / Faixa de Faturamento**
  - [ ] **02.2** Renderizar colunas em **cards** Bootstrap (drag-and-drop opcional)
  - [ ] **02.3** Criar **CRUD “Faixa de Faturamento”**

- [ ] **03 Integração WhatsApp (Evolution API)** ([user story](../user-historyes/03-integracao-whatsapp.md))
  - [ ] **03.1** Configurar credenciais no `.env`
  - [ ] **03.2** Criar tela de chat
    - [ ] **03.2.1** Sidebar (15 – 20 %) listando conversas recentes
    - [ ] **03.2.2** Painel de chat (80 – 85 %) com balões — cliente à esquerda, atendente à direita
  - [ ] **03.3** Associar conversa a **clientes** ou **contatos**
  - [ ] **03.4** Implementar **webhook** de recepção
  - [ ] **03.5** Implementar **endpoint** de envio via **Evolution API**

---

> Atualize as caixas de seleção à medida que avançar. Mantemos todos os documentos em português e slugs numerados para preservar a ordem.
