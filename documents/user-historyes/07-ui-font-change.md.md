<!-- /documents/user-historyes/07-ui-font-change.md -->

# 07 – Alterar Fonte (UX)

## História do Usuário
> **Como** usuário final  
> **quero** uma fonte mais leve e legível que Oswald  
> **para** ter conforto visual durante uso prolongado.

### Sub-tarefas
| ID  | Descrição                                          |
|-----|----------------------------------------------------|
| 07.1| Escolher fonte (ex. **Inter** via Google Fonts)    |
| 07.2| Atualizar `resources/sass/app.scss` (`@import`)    |
| 07.3| Re-compilar assets (`npm run build`) e testar      |

### Critérios de Aceite
```gherkin
Cenário: Fonte aplicada
  Quando carrego qualquer tela
  Então a família de fonte principal é "Inter"
  E o CLS não muda perceptivelmente
Definition of Done
Peso ≤ 300 kB woff2 total

Variáveis SCSS $font-family-sans-serif atualizadas

Teste visual cross-browser (Chrome, Firefox, Edge)