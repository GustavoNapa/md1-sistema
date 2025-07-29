<!-- /documents/user-historyes/01-2-disparo-post-json-inscricao.md -->

# 01.2 – Disparar POST JSON ao Criar/Atualizar **Inscrição**

## História do Usuário
> **Como** atendente interno  
> **quero** que, ao salvar uma Inscrição, o sistema envie um **POST JSON** com todos os dados do Cliente e da Inscrição  
> **para** que os sistemas externos recebam informações completas e mapeadas automaticamente.

---

## Contexto
Após o campo **webhook_url** ter sido configurado no Produto (tarefa 01.1), cada vez que uma **Inscrição** é criada ou atualizada **e** seu status corresponde ao status-gatilho do produto, o sistema deve:

1. Montar o payload unificado (Cliente + Inscrição).  
2. Adicionar o objeto **mapping** com a tabela de equivalência.  
3. Enviar um **POST JSON** para a URL definida, incluindo header `Authorization: Bearer <token_configurado>`.

---

## Sub-tarefas
- **01.2.1** Enviar todos os campos de **Cliente** + **Inscrição**.  
- **01.2.2** Incluir objeto **mapping** conforme tabela de equivalência.

---

## Critérios de Aceite (Gherkin)

```gherkin
Funcionalidade: Envio de POST JSON no evento da Inscrição
  Cenário: Criação de inscrição com status-gatilho
    Dado que existe um Produto com webhook_url e status-gatilho "Ativo"
    E o token "abc123" configurado
    Quando crio uma nova Inscrição para esse Produto com status "Ativo"
    Então o sistema envia um POST JSON para webhook_url
    E o header "Authorization" contém "Bearer abc123"
    E o corpo contém objeto "cliente" com todos os atributos do cliente
    E o corpo contém objeto "inscricao" com todos os atributos da inscrição
    E o corpo contém objeto "mapping" com as chaves especificadas

  Cenário: Atualização de inscrição com status-gatilho
    Dado uma inscrição existente com status "Ativo"
    Quando atualizo o campo "valor" da inscrição
    Então o webhook é reenviado com dados atualizados

  Cenário: Status não correspondente
    Dado um Produto cujo status-gatilho é "Ativo"
    Quando crio uma inscrição com status "Pendente"
    Então nenhum webhook é enviado
