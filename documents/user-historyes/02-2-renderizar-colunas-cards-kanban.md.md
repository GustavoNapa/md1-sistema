<!-- /documents/user-historyes/02-2-renderizar-colunas-cards-kanban.md -->

# 02.2 – Renderizar Colunas em **Cards** Bootstrap com Drag-and-Drop

## História do Usuário
> **Como** atendente interno  
> **quero** ver cada inscrição representada por um **card** em colunas dinâmicas do Kanban  
> **para** mover inscrições entre etapas arrastando-as e, assim, atualizar seu status/fase/semana/faixa de faturamento de forma visual e intuitiva.

---

## Contexto
Esta tarefa implementa o núcleo visual e interativo do Kanban:

* **Cards Bootstrap 5**: título (nome do cliente), subtítulo (produto) e badges (valor, data).  
* **Colunas**: criadas dinamicamente conforme a opção selecionada (task 02.1).  
* **Drag-and-Drop**: ao soltar, dispara requisição que atualiza o campo correspondente e grava log (ver tarefa 01.3 & logs Kanban).  
* **Scroll infinito**: cada coluna carrega os primeiros 10 cards e busca mais ao rolar.  
* **Contador**: badge com total de cards no cabeçalho da coluna.  
* **Cores**: classes utilitárias Bootstrap (`bg-primary`, `bg-success`, etc.) definidas via mapa `$kanban-colors`.

---

## Critérios de Aceite (Gherkin)

```gherkin
Funcionalidade: Colunas Kanban com Cards arrastáveis
  Cenário: Renderizar colunas corretas
    Dado que escolhi "Status" como agrupamento
    Quando a página do Kanban carrega
    Então vejo colunas "Pendente", "Ativo", "Concluído" (conforme valores existentes)

  Cenário: Exibir contador de cards
    Quando o Kanban é exibido
    Então cada cabeçalho de coluna mostra a quantidade de inscrições daquela coluna

  Cenário: Arrastar card entre colunas
    Dado um card na coluna "Pendente"
    Quando arrasto o card para a coluna "Ativo"
    Então o card aparece imediatamente na nova coluna
    E recebo toast "Inscrição atualizada com sucesso"
    E o contador de cada coluna é atualizado

  Cenário: Scroll infinito
    Dado que a coluna "Ativo" tem mais de 10 cards
    Quando rolo até o final da coluna
    Então o sistema carrega os próximos 10 cards via AJAX
Definition of Done (DoD)
 Componente KanbanColumn (Vue/Livewire) renderiza cabeçalho + lista de cards.

 Componente KanbanCard mostra nome do cliente, produto, badges (valor, data).

 Uso de SortableJS / vuedraggable para drag-and-drop com animação.

 Requisição POST /api/inscricoes/{id}/move atualiza campo e devolve 200.

 Scroll infinito: endpoint GET /api/kanban?column=X&offset=Y&limit=10.

 Classe SCSS kanban-column-{key} aplica cor via mapa $kanban-colors.

 Testes Dusk (drag-and-drop, scroll) + snapshot UI básico.

 Performance: Eager load cliente e paginação por coluna.

 Documentação atualizada neste arquivo; GIF de demonstração adicionado ao README (opcional).

 Checkbox 02.2 marcado DONE após validação da Head de CS.

Notas Técnicas
Item	Implementação
Framework	Vue 3 + Composition API ou Livewire + Alpine.js (decidir conforme stack do projeto).
Key de coluna	Slug gerado a partir do valor (ex.: ativo, fase-proposta).
Eventos	@end="handleDrop($event)" no Sortable; em Livewire usar wire:sortable.
Feedback	Toast Bootstrap 5 (.toast) para sucesso/falha.
Lazy load	IntersectionObserver → dispara loadMore() quando sentinel entra em viewport.
