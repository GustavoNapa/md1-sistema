<!-- /documents/user-historyes/02-1-select-coluna-kanban.md -->

# 02.1 – Seletor de Coluna do Kanban (Status / Semana / Fase / Faixa de Faturamento)

## História do Usuário
> **Como** atendente interno  
> **quero** escolher rapidamente qual campo determina as colunas do Kanban de Inscrições  
> **para** visualizar o fluxo do jeito que faz mais sentido para o meu trabalho e gravar essa preferência para acessos futuros.

---

## Contexto
O Kanban precisa ser flexível. Cada atendente poderá alternar entre quatro perspectivas:

1. **Status** – Foco no progresso operacional.  
2. **Semana** – Acompanhar inscrições por semana do curso ou entrega.  
3. **Fase** – Ver em que etapa do funil cada inscrição está.  
4. **Faixa de Faturamento** – Avaliar impacto financeiro agrupado por faixas (CRUD 02.3).

A escolha deve ser persistida por **usuário** para não exigir reseleção a cada login.

---

## Critérios de Aceite (Gherkin)

```gherkin
Funcionalidade: Seletor de Colunas para Kanban
  Cenário: Exibir opções corretas
    Dado que estou na tela de Kanban de Inscrições
    Quando clico no seletor de colunas
    Então vejo as opções "Status, Semana, Fase, Faixa de Faturamento"

  Cenário: Persistir escolha do usuário
    Dado que seleciono "Fase" no seletor
    Quando atualizo a página ou faço logout/login
    Então o Kanban volta a exibir colunas baseadas em "Fase"

  Cenário: Alterar opção recarrega Kanban
    Dado que o Kanban está exibindo colunas por "Status"
    Quando seleciono "Faixa de Faturamento"
    Então o Kanban recarrega mostrando colunas por faixas
    E nenhuma requisição desnecessária é feita para colunas antigas
Definition of Done (DoD)
 <select id="kanban_column"> com 4 option traduzidas PT-BR.

 Preferência salva em tabela user_settings (coluna kanban_column).

 Endpoint ou Livewire action atualiza valor e emite evento para recarregar colunas sem full reload (AJAX).

 Fallback para Status se valor inválido ou vazio.

 Testes PHPUnit/Pest:

Persistência de preferência.

Fallback padrão.

 Teste Dusk garante recarregamento visual após troca de opção.

 Documentação deste arquivo concluída; referência adicionada no README (Kanban).

 Checkbox 02.1 em /documents/todos/HEAD_CS_2025-07.md marcado DONE após aprovação da Head de CS.

Notas Técnicas
Item	Implementação
Componente UI	Seletor dentro do KanbanHeader.vue ou Livewire blade.
Persistência	Model UserSetting (user_id, key, value) ou coluna JSON settings no User.
Evento UI	@change="reloadKanban" (Vue) ou wire:change="reloadKanban" (Livewire).
Default	Valor inicial 'status'.
Acessibilidade	aria-label="Selecionar agrupamento do Kanban".