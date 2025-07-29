<!-- /documents/user-historyes/06-menu-ux.md -->

# 06 – Ajustar & Ordenar Menu

## História do Usuário
> **Como** qualquer usuário  
> **quero** um menu organizado, só com itens que posso acessar  
> **para** navegar rapidamente sem ver links quebrados ou proibidos.

### Sub-tarefas
| ID  | Descrição                                                        |
|-----|------------------------------------------------------------------|
| 06.1| Definir **mapa de navegação** (grupos: Administração, CS, Relatórios…) | ✅
| 06.2| Refatorar Blade `sidebar.blade.php` para **esconder** itens sem permissão | ✅
| 06.3| Ajustar ordem e adicionar headers de grupo                       | ✅

### Critérios de Aceite
```gherkin
Cenário: Menu filtrado por permissão
  Dado que estou logado como Especialista de Suporte
  Então não vejo itens "Funcionalidades" ou "Administração"

Cenário: Agrupamento visual
  Quando acesso o menu
  Então vejo headers "Administração", "CS", "Relatórios"
Definition of Done
Sidebar bootstrapped; nenhum ícone necessário

Todas as rotas cobertas; 100 % Lighthouse accessibility > 90 %

yaml
Copiar
Editar

---

```markdown