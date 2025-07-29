<!-- /documents/user-historyes/03-4-webhook-recepcao.md -->

# 03.4 – Webhook de Recepção de Mensagens WhatsApp (Evolution API)

## História do Usuário
> **Como** atendente interno  
> **quero** que o sistema receba automaticamente todas as mensagens enviadas pelos clientes via Evolution API  
> **para** que o histórico fique completo, a tela de chat seja atualizada em tempo real e eu não perca nenhum contato.

---

## Fluxo de Alto-Nível
1. **Evolution API** envia `POST` para `POST /api/webhooks/evolution` sempre que chega mensagem nova ou evento (delivered, read, reaction…).  
2. **Endpoint** valida assinatura/​token, normaliza payload e grava em `whatsapp_messages`.  
3. Dispara **Broadcast** (`whatsapp.conversation.{id}`) para atualizar UI.  
4. Se a conversa ainda não existir → cria em `whatsapp_conversations` e aciona **03.3** (associação).  
5. Se entrega falhar (timeout/4xx), Evolution reenvia; o endpoint deve ser **idempotente**.

---

## Estrutura Esperada do Payload  
*(exemplo simplificado – versão Evolution 2.3.0)*

```json
{
  "instanceId": "42-ABCD",
  "timestamp": 1722263859,
  "type": "chat",
  "from": "5511998765432",
  "body": "Olá 👋",
  "messageId": "3EB0CE6512345678A1",
  "quotedMsgId": null,
  "media": null,
  "ack": 0
}
Campos comuns:
type (chat, image, audio, document, sticker, reaction) • from (fone cliente) • body ou media.url • messageId (único) • timestamp (epoch).

Critérios de Aceite (Gherkin)
gherkin
Copiar
Editar
Funcionalidade: Webhook de recepção Evolution
  Cenário: Receber mensagem de texto
    Dado que a Evolution API envia POST com tipo "chat"
    Quando o webhook /api/webhooks/evolution processa o payload
    Então uma linha é inserida em whatsapp_messages com message_id correspondente
    E a mensagem aparece na UI via WebSocket

  Cenário: Mensagem duplicada
    Dado que o mesmo messageId já existe no banco
    Quando o mesmo payload é recebido novamente
    Então o endpoint responde 200
    E nenhuma nova mensagem é criada (idempotência)

  Cenário: Media message
    Quando recebo payload com type "image" e media.url preenchido
    Então uma mensagem com `type=image` é gravada
    E o arquivo é baixado para storage/app/whatsapp

  Cenário: Assinatura inválida
    Dado que o header Authorization contém token incorreto
    Quando o webhook é chamado
    Então retorna 401 "Unauthorized"
    E registra tentativa em whatsapp_logs com status "unauthorized"
Definition of Done (DoD)
 Rota protegida: Route::post('webhooks/evolution', EvolutionWebhookController::class).

 Verificar token em header (Authorization: Bearer <EVOLUTION_API_KEY>).

 Validar JSON → Form Request (required fields).

 Idempotência: unique index em message_id (whatsapp_messages.message_id).

 Media: baixar arquivo (Storage::disk('whatsapp')) e salvar media_path.

 Criar conversa se não existir; acionar ConversationLinker (03.3).

 Emitir event NewWhatsappMessage → Broadcast (whatsapp.conversation.{id}).

 Responder 200 OK com { "status":"received" }.

 Testes PHPUnit/Pest cobrindo: texto, mídia, duplicado, token inválido.

 Monitoramento: logar latência em whatsapp_logs (route, ms, status).

 Documentação concluída (este arquivo); README seção Webhooks atualizada.

 Checkbox 03.4 no todo marcado DONE após aprovação da Head de CS.

Notas Técnicas
Item	Implementação
Controller	EvolutionWebhookController::__invoke(Request $request)
Model	WhatsappMessage (id, conversation_id, type, body, media_path, message_id)
Storage	Sub-pasta whatsapp/{conversation_id}/ para mídias, protegida via signed URL
Queues	Heavy media downloads off-loaded to media_downloads queue.
Broadcast Driver	Redis + Socket.IO (já usado em tasks anteriores).
Security	Registrar IP no log; considerar ratelimit throttle:100,1.