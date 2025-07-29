<!-- /documents/user-historyes/02-3-crud-faixa-faturamento.md -->

# 02.3 – CRUD **Faixa de Faturamento**

## História do Usuário
> **Como** atendente interno (ou administrador)  
> **quero** cadastrar, editar e excluir **Faixas de Faturamento** (valor mínimo, valor máximo, label)  
> **para** agrupar inscrições por potencial financeiro e visualizar essas faixas como colunas no Kanban.

---

## Contexto
O campo **Faixa de Faturamento** será usado como uma das opções de agrupamento no Kanban (ver 02.1 e 02.2).  
Cada faixa deve possuir:

| Campo         | Tipo / Regra                                               |
|---------------|------------------------------------------------------------|
| **label**     | `string` obrigatória, máx. 50 caracteres                   |
| **valor_min** | `decimal(12,2)` ≥ 0                                        |
| **valor_max** | `decimal(12,2)` > `valor_min`                              |

Permissões: apenas usuários com `role = admin` podem criar/editar/excluir; atendentes podem **listar**.

---

## Critérios de Aceite (Gherkin)

```gherkin
Funcionalidade: CRUD Faixa de Faturamento
  Cenário: Criar faixa válida
    Dado que estou logado como admin
    Quando acesso "Nova Faixa de Faturamento"
    E preencho label "Bronze", valor_min "5000", valor_max "10000"
    E clico em "Salvar"
    Então vejo mensagem "Faixa criada com sucesso"
    E a faixa aparece na listagem

  Cenário: Validação de valores
    Quando tento salvar com valor_max menor que valor_min
    Então vejo erro "Valor máximo deve ser maior que valor mínimo"

  Cenário: Editar faixa
    Dado uma faixa "Bronze"
    Quando altero valor_max para "12000" e salvo
    Então a listagem mostra o novo valor

  Cenário: Excluir faixa
    Quando clico em "Excluir" ao lado da faixa "Bronze"
    Então aparece modal de confirmação
    E confirmo exclusão
    Então a faixa não aparece mais na listagem

  Cenário: Permissão de atendente
    Dado que estou logado como atendente
    Quando acesso "Faixas de Faturamento"
    Então posso ver a lista
    E não vejo botões "Novo", "Editar" ou "Excluir"
Definition of Done (DoD)
 Migration faixas_faturamento com colunas id, label, valor_min, valor_max, timestamps.

 Model FaixaFaturamento com regras de validação (FormRequest).

 Controllers e Blade ou Livewire CRUD completo: index, create, store, edit, update, destroy.

 Policy ou Gate restringe criação/edição/exclusão a role=admin.

 Listagem paginada (10 por página) com busca por label.

 Ao salvar ou excluir, emitir toast Bootstrap 5 de feedback.

 Testes PHPUnit/Pest (validação, permissão) + Dusk (fluxo CRUD).

 Faixas disponíveis no seletor do Kanban (carregar via API ou View Composer).

 Documentação concluída (este arquivo + update no README).

 Checkbox 02.3 em /documents/todos/HEAD_CS_2025-07.md marcado DONE após aprovação da Head de CS.

Notas Técnicas
Item	Implementação
Rotas	Route::resource('faixas-faturamento', FaixaFaturamentoController::class)
Validação Server	valor_min e valor_max `numeric
Validação Front	step="0.01" em campos number; máscara monetária opcional.
Seeders	Criar faixas demo (Bronze, Prata, Ouro) para testes locais.
Integração Kanban	Endpoint GET /api/faixas-faturamento devolve lista para seletor.