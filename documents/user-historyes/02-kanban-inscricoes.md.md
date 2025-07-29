<!-- /documents/user-historyes/02-kanban-inscricoes.md -->

# 02 – Kanban de Inscrições

## História do Usuário
> **Como** atendente interno  
> **quero** visualizar e gerenciar inscrições em um **Kanban personalizável**  
> **para** acompanhar rapidamente o fluxo de clientes e mover cartões entre etapas com apenas arrastar-e-soltar.

---

## Contexto & Requisitos Clave
| Item | Decisão |
|------|---------|
| **Coluna dinâmica** | Usuário escolhe entre **Status / Semana / Fase / Faixa de Faturamento**; preferência é salva por **usuário**. |
| **Drag-and-drop** | Arrastar cartão muda o campo correspondente e grava log de movimento. |
| **Ordenação & Filtros** | Ordenação (ex.: mais recente em cima) + filtros por cliente, data etc. |
| **Paginação** | Carregar **10** cartões por coluna inicialmente; **scroll infinito** (offset) até ~500. |
| **Indicadores visuais** | Contagem de cartões no cabeçalho; **cores distintas por status/fase** respeitando Bootstrap. |
| **Faixa de Faturamento** | CRUD com `valor_min`, `valor_max`, `label`. |
| **Responsividade** | Desktop: colunas horizontais; Mobile: lista vertical por padrão + swipe horizontal opcional. |
| **Logs** | Tabela `kanban_movements` (id, inscricao_id, from, to, user_id, timestamp). |
| **Tecnologia** | Laravel 12 + Laravel UI (Bootstrap 5); usar **Livewire** ou **Vue 3** + `vuedraggable`. |

---

## Sub-tarefas
- **02.1** Adicionar seletor (`<select>`) de coluna dinâmica no topo do Kanban.  
- **02.2** Renderizar colunas com cartões, drag‐and‐drop, contadores, cores, ordenação, filtros e scroll infinito.  
- **02.3** Criar **CRUD Faixa de Faturamento** (valor mínimo, máximo, label).

---

## Critérios de Aceite (Gherkin)

```gherkin
Funcionalidade: Kanban de Inscrições
  Cenário: Seleção de coluna preferida
    Dado que estou logado como atendente
    Quando escolho "Fase" no seletor de colunas
    Então o Kanban exibe colunas baseadas em fases
    E minha seleção é salva para meu usuário

  Cenário: Arrastar cartão e atualizar campo
    Dado um cartão na coluna "Pendente"
    Quando arrasto o cartão para a coluna "Ativo"
    Então o campo status da inscrição é atualizado para "Ativo"
    E um registro é adicionado em kanban_movements com meu user_id

  Cenário: Scroll infinito
    Dado que a coluna "Ativo" possui mais de 10 inscrições
    Quando rolo até o final da lista
    Então o sistema carrega os próximos 10 cartões

  Cenário: Contador e cores
    Quando o Kanban é exibido
    Então cada cabeçalho de coluna mostra a quantidade total de cartões
    E cada coluna possui cor de fundo conforme legenda padrão

  Cenário: CRUD Faixa de Faturamento
    Dado que estou na tela de faixas
    Quando crio faixa R$5k–R$10k com label "Bronze"
    Então ela aparece no seletor de colunas (Faixa de Faturamento)
Definition of Done (DoD)
 Seletor de Coluna salvo em user_settings (campo kanban_column).

 Drag-and-drop funcional com atualização via AJAX/Livewire + rollback on fail.

 Logs gravados em kanban_movements (atendente, from → to, datetime).

 CRUD Faixa de Faturamento completo (create, edit, delete) com validação numérica.

 Scroll infinito com paginação (skip/offset) e spinner Bootstrap.

 Testes PHPUnit/Pest (model, policy) + Dusk (drag-and-drop, scroll, mobile).

 Cores definidas via SCSS variável (bootstrap $kanban-colors).

 Documentação atualizada (este arquivo + README trecho Kanban).

 Checkbox principal 02 e subtarefas 02.1–02.3 marcados como DONE após aprovação da Head de CS.

Notas Técnicas
Tema	Implementação
Componente UI	KanbanBoard.vue usando SortableJS/vuedraggable (ou Livewire + Alpine).
Endpoint API	POST /api/inscricoes/{id}/move (novo_status
Filtro & Ordem	Padrão: orderBy('updated_at', 'desc'). Filtros via barra lateral.
Mobile Swipe	Usar touchstart/touchmove ou lib hammer.js para swipe horizontal.
Performance	Eloquent with(['cliente']) + índice db no campo da coluna atual.