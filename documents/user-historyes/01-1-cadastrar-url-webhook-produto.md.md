<!-- /documents/user-historyes/01-1-cadastrar-url-webhook-produto.md -->

# 01.1 – Cadastro da URL do Webhook no CRUD **Produto**

## História do Usuário
> **Como** atendente interno  
> **quero** informar a **URL do webhook** diretamente no cadastro de Produto  
> **para** que cada inscrição desse produto seja enviada ao endpoint correto, sem configurações manuais posteriores.

---

## Contexto
Cada Produto pode ter um webhook diferente (ex.: produtos distintos integram-se a CRMs ou fluxos de contrato diferentes).  
Esta sub-tarefa cria o campo **webhook_url** (e exibe-o na UI) dentro do CRUD Produto.

---

## Critérios de Aceite (Gherkin)

```gherkin
Funcionalidade: Campo URL do Webhook no Produto
  Cenário: Exibir novo campo no formulário
    Dado que estou na tela "Criar Produto"
    Quando visualizo o formulário
    Então vejo o campo "URL do Webhook" com placeholder "https://..."

  Cenário: Validação de URL obrigatória
    Dado que estou na tela "Editar Produto"
    Quando preencho "URL do Webhook" com texto não-válido
    E clico em "Salvar"
    Então recebo mensagem de erro "Informe uma URL válida"

  Cenário: Persistência de dados
    Dado um produto existente sem webhook
    Quando adiciono uma URL válida e salvo
    Então o valor é gravado na coluna `webhook_url`
    E o sistema exibe o valor salvo ao retornar à tela de edição
gherkin```
