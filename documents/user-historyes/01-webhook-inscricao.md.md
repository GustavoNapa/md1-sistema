<!-- /documents/user-historyes/01-webhook-inscricao.md -->

# 01 – Webhook de Inscrição

## História do Usuário
> **Como** atendente interno  
> **quero** que o sistema dispare automaticamente um webhook com os dados completos da inscrição e do cliente  
> **para** que o contrato seja gerado, os dados sejam enviados ao CRM e as partes interessadas sejam notificadas sem esforço manual.

---

## Contexto & Visão Geral
Ao cadastrar ou atualizar uma **Inscrição**, o sistema deve enviar um **POST JSON** para a URL definida no cadastro de **Produto**.  
O envio só ocorre quando o **Status** da Inscrição corresponde ao status-gatilho configurado para aquele webhook.

### Estrutura do Payload
```json
{
  "timestamp": "2025-07-28T14:35:22-03:00",
  "event_type": "inscricao.updated",
  "status": "Ativo",
  "cliente": { /* todos os campos do cliente */ },
  "inscricao": { /* todos os campos da inscrição */ },
  "mapping": {
    "Nome": "contact_name",
    "Email": "contact_email",
    "Telefone": "contact_phone",
    "Etapa do funil": "deal_stage",
    "Dono do Negócio": "deal_user",
    "Status do Negócio": "deal_status",
    "Natureza_juridica": "contact_natureza_juridica",
    "CPF/CNPJ": "contact_cpfcnpj",
    "Valor_mentoria": "contact_produto",
    "forma_pagamento_entrada": "contact_forma_pagamento_entr",
    "Forma_pagamento_restante": "contact_parcelas_cartao",
    "Data_pagamento": "contact_data_contrato",
    "Rua": "contact_endereco",
    "Numero_casa": "contact_numero_casa",
    "Complemento": "contact_complemento",
    "Bairro": "contact_bairro",
    "Cidade": "contact_cidade",
    "Estado": "contact_estado",
    "CEP": "contact_cep",
    "Pagamento_entrada": "contact_pagamento_entrada",
    "Pagamento_restante": "contact_pagamento_restante",
    "Data_pagamento_entrada": "contact_data_pagamento_entra"
  }
}
json```

Headers obrigatórios:

Authorization: Bearer <TOKEN_DO_WEBHOOK>
Content-Type: application/json


Critérios de Aceite (Gherkin)
gherkin
Funcionalidade: Disparo de Webhook de Inscrição
  Cenário: Criação de inscrição com status-gatilho
    Dado que existe um produto com webhook configurado para o status "Ativo"
    E estou autenticado como atendente interno
    Quando crio uma inscrição para esse produto com status "Ativo"
    Então o sistema envia um POST JSON para a URL do webhook
    E o cabeçalho "Authorization" contém o token configurado
    E o corpo da requisição segue a estrutura especificada
    E recebo resposta HTTP 200

  Cenário: Atualização de inscrição sem status-gatilho
    Dado uma inscrição existente ligada a um produto com webhook configurado para status "Ativo"
    Quando atualizo a inscrição mantendo status "Pendente"
    Então nenhum webhook é disparado

  Cenário: Reenvio automático em caso de falha
    Dado que o endpoint do webhook responde com erro 500
    Quando o sistema tenta enviar o webhook
    Então ele agenda novo envio em 5 minutos
    E repete o processo no máximo mais 2 vezes
Definition of Done (DoD)
 Código implementado em Laravel 12 (Jobs + Event listeners).

 Testes PHPUnit/Pest cobrindo cenários de sucesso, falha e limites de tentativas.

 Tela “Histórico de Webhooks” listando envios, status, tentativa e botão Reenviar.

 Campos de configuração adicionados ao CRUD Produto (URL, Token, Status-gatilho).

 Documentação atualizada (Rotas.md, este arquivo).

 Checklist da tarefa atualizado para DONE.

 Demonstração aprovada pela Head de CS.

Notas Técnicas
Item	Detalhe
Retries	3 tentativas totais (1 inicial + 2 reenvios) a intervalos de 5 minutos.
Queue	Usar fila webhooks com retryUntil.
Segurança	Token simples em header; sem assinatura HMAC por enquanto.
Logs	Registrar cada tentativa (request, response, status) na tabela webhook_logs.
Teste E2E	Utilizar ngrok + endpoint de mock para validar payload.