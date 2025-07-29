<!-- /documents/user-historyes/03-integracao-whatsapp-evolution-api.md -->

# 03 – Integração WhatsApp (Evolution API)

## História do Usuário
> **Como** atendente interno  
> **quero** conversar com os clientes via WhatsApp em uma tela única dentro do sistema  
> **para** acelerar o atendimento, manter histórico completo, ver quem está digitando e evitar trocas de ambiente.

---

## Visão Geral & Requisitos-Chave
| Tema                       | Decisão / Regra                                                                                                                     |
|----------------------------|-------------------------------------------------------------------------------------------------------------------------------------|
| **Instâncias**             | Sistema gerencia **várias** instâncias (números corporativos). Cria, obtém QR-Code, renova QR a cada ~40 s até “Connected”.         |
| **Tela “Conexões”**        | CRUD de **Canais** (nº, instanceId, status, QR atual, última renovação, log).                                                       |
| **Tipos de mensagem**      | Texto, mídia (imagem, pdf, áudio), figurinhas, emojis, reações, replies.                                                            |
| **Histórico**              | Importação **obrigatória** do histórico – processamento em lote usando filas **messages_sync** (chunk + cache in-memory).            |
| **Tempo-real**             | WebSocket (Laravel Echo + Pusher/Socket.IO) para novas mensagens, indicadores “digitando”, badges de não lidas.                     |
| **Modelo de dados**        | `whatsapp_conversations`, `whatsapp_messages`, `whatsapp_channels`, relacionando `clientes`, `inscricoes`, `users (atendentes)`.     |
| **Atribuição**             | Cada conversa possui **um atendente atribuído** (pode mudar). Vários atendentes podem responder se a conversa estiver com eles.      |
| **Fila de envio**          | Fila `whatsapp_outbox`, back-off progressivo **10 s → 30 s → 60 s → 2 min → 5 min**; status `queued/sent/failed`.                    |
| **Retries & Logs**         | Máx. 5 tentativas; registrar em `whatsapp_logs` (latência, payload, resposta).                                                     |
| **Notificações**           | Badge nas conversas e sino global para mensagens não lidas do atendente atribuído.                                                 |
| **UI**                     | Inspirada no WhatsApp Web (balões cinza cliente / verde atendente); avatar, data, ticks ✓✓.                                         |
| **Evolução API**           | Endpoint base: `https://evolution.gmc.app.br` (vers. 2.3.0). Operações: `/instance/create`, `/instance/qr`, `/message/send`, etc.   |
| **Sandbox**                | Ambiente de teste via Evolution **sandbox** para CI/Dusk.                                                                           |

---

## Sub-tarefas

| ID  | Descrição                                                                                             |
|-----|--------------------------------------------------------------------------------------------------------|
| **03.1** | Configurar credenciais e processo de **criação/renovação de instância** com QR-Code. |
| **03.2** | Implementar **Tela de Chat** (sidebar conversas + painel chat, balões, typing).                  |
| **03.3** | **Associar** conversa a cliente/contato, inscrição e atendente.                                   |
| **03.4** | **Webhook de recepção** de mensagens → salva em BD + Broadcast WebSocket.                         |
| **03.5** | **Endpoint de envio** via fila com back-off progressivo e indicação “na fila”.                    |
| **03.6** | **Tela “Canais”** para gerenciar números/instâncias (status, QR, logs).                           |
| **03.7** | **Sincronização de histórico** (importação em lote + cache).                                      |

---

## Critérios de Aceite (Gherkin)

```gherkin
Funcionalidade: Integração WhatsApp com Evolution API
  Cenário: Conectar novo número
    Dado que estou na tela "Canais"
    Quando clico em "Adicionar Número"
    Então o sistema chama Evolution /instance/create
    E exibe o QR-Code retornado
    Quando o QR é escaneado e o status muda para "CONNECTED"
    Então o canal aparece como "Conectado" na lista

  Cenário: Receber mensagem em tempo real
    Dado que um cliente envia mensagem para um número conectado
    Então o webhook Evolution envia POST ao sistema
    E a conversa aparece (ou atualiza) na sidebar com badge "1"
    E o painel de chat, se aberto, exibe a nova mensagem instantaneamente via WebSocket

  Cenário: Enviar mensagem com fila e feedback
    Dado que digito "Olá, tudo bem?"
    Quando clico em "Enviar"
    Então o card de mensagem exibe indicador "🕒 na fila"
    E após envio com sucesso exibe "✓✓"
    E se falhar 5 vezes exibe "❗ falha" e loga erro grave

  Cenário: Sincronizar histórico
    Dado que o canal foi conectado
    Quando clico "Sincronizar histórico"
    Então o sistema enfileira lote de importação
    E após concluído, todas as mensagens dos últimos 30 dias aparecem no chat

  Cenário: Limites e back-off
    Dado que há volume alto de envios
    Quando envio mais de 30 mensagens em 1 minuto
    Então o sistema espaça os envios progressivamente e avisa "Mensagens em fila para evitar bloqueio"
Definition of Done (DoD)
 Config .env (EVOLUTION_BASE_URL, EVOLUTION_API_KEY).

 API Service EvolutionClient com métodos createInstance, getQR, sendMessage, etc.

 Job SyncWhatsappHistoryJob multiprocessado (chunks 500 msgs, cache Redis).

 Broadcast via Laravel Echo + Redis / Pusher; canal whatsapp.conversation.{id}.

 Queue whatsapp_outbox com policy de back-off; flag “queued/sent/failed”.

 Web UI 2-painéis: sidebar 15–20 % (conversas + badge), chat 80–85 % (balões).

 “Canais” CRUD com QR auto-refresh a cada 40 s.

 Typing indicator via WebSocket (typing:true/false).

 Logs & Metrics: tabela whatsapp_logs + Horizon dashboard.

 Testes: PHPUnit/Pest (client wrapper), Dusk (UI), integração Evolution sandbox.

 Documento atualizado (este arquivo) + GIF demo no README (opcional).

 Checkbox 03 e subtarefas 03.1–03.7 marcados DONE após aprovação da Head de CS.

Notas Técnicas
Item	Implementação
Criação Instância	POST /instance/create → salva instanceId, token no whatsapp_channels
QR polling	Task FetchQrJob roda até 3 min ou status CONNECTED; exibe QR via endpoint qr.svg.
Envio mensagem	POST /message/send com payload dynamic (media/voice/docs)
Histórico	Endpoint Evolution /messages/${phone}?page=N; enfileirar pages paralelamente (8 workers).
DB Modelagem	conversations(id, channel_id, contact_phone, cliente_id, inscricao_id, user_id, unread) etc
Reação & Sticker	Salvar type enum (text, image, audio, sticker, reaction).
Typing	Broadcast typing event em 3 s enquanto usuário digita.
Back-off formula	attempt_delay = [10,30,60,120,300][attempt-1].
Security	Verificar IP de origem do webhook; header x-evolution-signature opcional para futuro.
