<!-- /documents/user-historyes/05-acl-permissoes.md -->

# 05 – Ajustar Permissões (ACL) + Seeders

## História do Usuário
> **Como** Head de CS ou outro usuário  
> **quero** ver apenas telas e ações para as quais tenho permissão  
> **para** evitar erros de acesso e garantir segurança de dados.

### Sub-tarefas
| ID  | Descrição                                                          |
|-----|--------------------------------------------------------------------|
| 05.1| **Scanner** que varre rotas/controllers e gera lista de ações      | ✅
| 05.2| Seeder `permissions_table` (idempotente)                            | ✅
| 05.3| Seeder `roles` + atribui permissões aos 5 cargos padrão            | ✅
| 05.4| Middleware `permission:<slug>` aplicado em rotas + controllers      | ✅
| 05.5| Handler 403 → redirect back + Toastr “Permissão insuficiente”       | ✅

### Critérios de Aceite
```gherkin
Funcionalidade: Controle de acesso por ação
  Cenário: Usuário sem permissão
    Dado que sou Especialista de Suporte
    Quando tento acessar /produtos/create
    Então sou redirecionado à página anterior
    E vejo alerta "Você não tem permissão"

  Cenário: Seeder idempotente
    Dado que executo db:seed duas vezes
    Então nenhum registro duplicado é criado
Definition of Done
Usa pacote spatie/laravel-permission (já instalado)

Seeder pode rodar em produção sem sobrescrever permissões manuais

Testes PHPUnit (policy) + Dusk (fluxo sem permissão)

yaml
Copiar
Editar

---

```markdown
