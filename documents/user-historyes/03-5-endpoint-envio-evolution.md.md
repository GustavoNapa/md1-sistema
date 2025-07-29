<!-- /documents/user-historyes/03-5-endpoint-envio-evolution.md -->

# 03.5 – Endpoint de **Envio** de Mensagens via Evolution API

## História do Usuário
> **Como** atendente interno  
> **quero** enviar mensagens (texto e mídia) para o cliente a partir do painel de chat  
> **para** manter a conversa fluindo sem sair do sistema, acompanhando status (fila, enviado, entregue) em tempo real.

---

## Fluxo de Alto-Nível

1. **UI** (Painel de Chat) dispara `POST /api/whatsapp/messages` com conteúdo e metadados.  
2. **Controller** valida input e coloca mensagem na fila `whatsapp_outbox` com status **queued**.  
3. **Job** `SendWhatsappMessageJob` processa a fila, chamando Evolution API (`/message/send`).  
4. API responde `messageId`; Job atualiza registro (`status=sent`, `message_id`).  
5. Job respeita **back-off progressivo** (10 s → 30 s → 60 s → 2 min → 5 min) até 5 tentativas.  
6. **Broadcast** eventos `message-status-updated` para que a UI troque ícones 🕒 → ✓ → ✓✓ ou ❗.  
7. Em caso de mídia, arquivo é primeiro enviado para Evolution endpoint `/sendFile` e, após sucesso, agrega link no payload de chat.

---

## Estrutura do Request UI → Sistema

```json
POST /api/whatsapp/messages
{
  "conversation_id": 123,
  "type": "text",               // text, image, audio, document, sticker
  "body": "Olá 👋",
  "media_path": null,           // tmp path se for upload de arquivo
  "quoted_message_id": null     // opcional
}
Critérios de Aceite (Gherkin)
gherkin
Copiar
Editar
Funcionalidade: Envio de mensagens via Evolution API
  Cenário: Enviar texto com sucesso
    Dado que estou na conversa 123
    Quando envio mensagem "Boa tarde!"
    Então registro em whatsapp_messages é criado com status "queued"
    E ícone 🕒 aparece no balão
    E após Evolution API retornar 200, status muda para "sent"
    E ícone ✓✓ aparece via WebSocket

  Cenário: Enviar mídia (imagem)
    Quando anexo "foto.png" e clico enviar
    Então o sistema faz upload para Evolution /sendFile
    E após sucesso envia message/send com mediaId
    E exibe miniatura no chat

  Cenário: Falha e retries
    Quando Evolution API retorna 429
    Então status permanece "queued"
    E job é re-agendado para 30 segundos depois
    E após 5ª falha, status "failed" e ícone ❗ no balão

  Cenário: Validação de payload
    Quando envio request sem conversation_id
    Então recebo 422 "conversation_id requerido"
Definition of Done (DoD)
 Route POST /api/whatsapp/messages (controller + FormRequest).

 Model WhatsappMessage adiciona colunas status, attempts, last_attempt_at.

 Queue whatsapp_outbox com back-off array [10,30,60,120,300].

 SendWhatsappMessageJob:

Monta payload Evolution (type, body, media etc.).

Chama endpoint correto (/message/send ou /sendFile).

Atualiza status sent/failed.

 Broadcast evento MessageStatusUpdated (icons).

 Media Handling: uploads temporários → Storage::disk('whatsapp_tmp'); deletar após envio.

 Rate-Limit Safety: se canal está em limite alto, job re-fila com delay extra (config).

 Unit Tests (FormRequest, Job back-off, success/fail), Dusk (UI feedback).

 Documentação concluída (este arquivo) + update README (“Envio de mensagens”).

 Checkbox 03.5 marcado DONE no todo após aprovação da Head de CS.

Notas Técnicas
Item	Implementação
HTTP Client	Guzzle com timeout = config('services.evolution.timeout').
API Endpoint	/instance/{id}/message/send (texto) • /instance/{id}/sendFile (mídia).
Headers	Authorization: Bearer {channel_token}
Idempotência	Armazenar local_uuid gerado pelo sistema e enviar em customData para evitar duplicados.
Back-off Formula	delay = [10,30,60,120,300][attempt-1]
Error Handling	Gravar resposta no whatsapp_logs; se failed, UI exibe tooltip com código/descrição.
Attachments Size	Limitar upload front-end (imagem ≤ 5 MB, docs ≤ 20 MB); validar server-side.
