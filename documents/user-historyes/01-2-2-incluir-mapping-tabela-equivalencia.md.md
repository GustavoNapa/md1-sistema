<!-- /documents/user-historyes/01-2-2-incluir-mapping-tabela-equivalencia.md -->

# 01.2.2 – Incluir objeto **mapping** conforme tabela de equivalência

## História do Usuário
> **Como** atendente interno  
> **quero** que o webhook inclua um objeto **mapping** que traduza os nomes dos campos  
> **para** que o sistema externo possa ingerir e armazenar os dados nos campos corretos sem ambiguidade.

---

## Contexto
A tabela de equivalência (fornecida pela Head de CS) mapeia rótulos usados na MD1 para identificadores usados pelo CRM/gerador de contratos.  
Exemplo: `"Nome" → "contact_name"`.

---

## Critérios de Aceite (Gherkin)

```gherkin
Funcionalidade: Inclusão do objeto mapping
  Cenário: Mapping presente no payload
    Dado que existe uma inscrição válida
    Quando o webhook é disparado
    Então o corpo da requisição contém objeto "mapping"
    E "mapping.Nome" é igual a "contact_name"
    E "mapping.Valor_mentoria" é igual a "contact_produto"
Definition of Done (DoD)
 Arquivo de configuração config/webhook_mapping.php contendo chave-valor completo.

 buildWebhookPayload() inclui "mapping" => config('webhook_mapping').

 Teste PHPUnit/Pest verifica presença de todas as 20+ chaves.

 Documentação atualizada neste arquivo.

 Checklist 01.2.2 marcado DONE após aprovação.

Notas Técnicas
Item	Detalhe
Manutenção	Se novas colunas surgirem, atualizar arquivo de config.
Reuso	Mapping pode ser usado em futuras integrações (ex.: CSV).