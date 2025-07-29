<!-- /documents/user-historyes/01-2-1-enviar-campos-cliente-inscricao.md -->

# 01.2.1 – Enviar todos os campos de **Cliente** + **Inscrição**

## História do Usuário
> **Como** atendente interno  
> **quero** que o payload do webhook contenha **todos** os atributos de Cliente e Inscrição  
> **para** garantir que o sistema externo receba informações completas, evitando consultas adicionais ou dados faltantes.

---

## Contexto
- O modelo **Cliente** possui ~N colunas (nome, email, telefone, etc.).  
- O modelo **Inscrição** possui ~M colunas (status, valor, data-início, etc.).  
- O job `DispatchInscricaoWebhook` deve unir ambas as coleções em objetos separados dentro do JSON.

---

## Critérios de Aceite (Gherkin)

```gherkin
Funcionalidade: Montagem de objetos cliente e inscrição
  Cenário: Payload contém 100% dos campos salvos
    Dado um cliente com todos os campos preenchidos
    E uma inscrição vinculada a esse cliente
    Quando o webhook é disparado
    Então o objeto "cliente" no corpo contém a chave "telefone"
    E contém a chave "cpfcnpj"
    E o objeto "inscricao" contém a chave "valor_total"
    E contém a chave "created_at"
Definition of Done (DoD)
 Função buildWebhookPayload() retorna objetos cliente e inscricao completos via ->toArray().

 Chaves sensíveis (ex.: senhas) excluídas: usar $hidden no Model ou Arr::except.

 Teste PHPUnit/Pest confirma presença de todas as colunas públicas (snapshot test).

 Documentação atualizada neste arquivo.

 Checklist 01.2.1 marcado DONE após aprovação.

Notas Técnicas
Item	Detalhe
Serialização	Cliente::toArray() e Inscricao::toArray() com $appends
Performance	Evitar N+1: carregar cliente via with('cliente').
Segurança	Omitir password, tokens ou colunas internas.