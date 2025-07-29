<!-- /documents/user-historyes/04-feature-toggle.md -->

# 04 – Tela de Funcionalidades (Feature Toggle)

## História do Usuário
> **Como** Administrador geral (dev)  
> **quero** ligar ou desligar funcionalidades do sistema em uma tela única  
> **para** ativar betas, remover telas obsoletas e controlar entregas sem redeploy.

### Sub-tarefas
| ID       | Descrição                                                         |
|----------|-------------------------------------------------------------------|
| 04.1     | Migration + Model `feature_flags` (`key`, `enabled`, `user_id`)   |
| 04.2     | CRUD Blade/Livewire “Funcionalidades” (switch on/off + logs)      |
| 04.3     | Middleware `CheckFeatureFlag` bloqueia rota/lógica                |
| 04.4     | Audit log (`feature_flag_logs`) – quem, o quê, quando             |

### Critérios de Aceite (Gherkin)
```gherkin
Funcionalidade: Gerenciar funcionalidades
  Cenário: Ativar feature
    Dado que estou na tela Funcionalidades
    Quando ligo a chave "ChatWhatsApp"
    Então vejo toast "Feature ativada"
    E registro em feature_flag_logs

  Cenário: Rota bloqueada
    Dado que desligo "KanbanInscricoes"
    Quando acesso /inscricoes
    Então sou redirecionado ao dashboard
    E vejo toast "Funcionalidade desativada"
Definition of Done
Feature flag resolvida via cache()->rememberForever('feature:foo', ...)

Apenas usuários role=admin veem a tela

Auditoria completa e testes PHPUnit/Pest + Dusk