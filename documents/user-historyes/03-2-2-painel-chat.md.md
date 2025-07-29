<!-- /documents/user-historyes/03-2-2-painel-chat.md -->

# 03.2.2 – Painel de Chat (80 – 85 %) com Balões

## História do Usuário
> **Como** atendente interno  
> **quero** um painel de chat semelhante ao WhatsApp Web, com balões alinhados (cliente à esquerda, eu à direita)  
> **para** trocar mensagens de texto e mídia, ver o status de entrega, saber quando o cliente está digitando e navegar pelo histórico sem atrasos.

---

## Objetivos Funcionais
1. **Renderizar histórico** (50 mensagens iniciais, scroll infinito para antigas).  
2. **Balões**  
   - Cinza – mensagens do **cliente** (esquerda).  
   - Verde – mensagens do **atendente** (direita).  
   - Ícones 🕒 (fila), ✓ (enviado), ✓✓ (entregue), ❗ (falha).  
3. **Envio** de texto, imagem, áudio, documento, sticker.  
4. **Input** com suporte a emoji, anexo (drag-and-drop ou botão).  
5. **Typing indicator** (“Cliente está digitando…”) via WebSocket.  
6. **Recepção em tempo real** – exibir novas mensagens instantaneamente.  
7. **Responsivo**: em mobile ocupa 100 % da largura; voltar à lista por swipe ou botão back.  
8. **Ações rápidas**: copiar texto, baixar mídia, marcar como não lida.

---

## Critérios de Aceite (Gherkin)

```gherkin
Funcionalidade: Painel de Chat WhatsApp
  Cenário: Exibir últimas mensagens
    Dado que abro a conversa com João
    Então vejo até 50 mensagens recentes
    E posso rolar para carregar mais antigas

  Cenário: Enviar mensagem de texto
    Quando digito "Boa tarde!" e clico Enviar
    Então a mensagem aparece com ícone "🕒"
    E após resposta 200 da Evolution API muda para "✓✓"

  Cenário: Enviar imagem como anexo
    Quando anexo um arquivo "foto.png" e envio
    Então um balão à direita mostra miniatura da imagem

  Cenário: Indicador digitando
    Dado que o cliente João está digitando
    Então vejo "João está digitando…" abaixo do cabeçalho

  Cenário: Falha no envio
    Dado que a Evolution API retorna erro 429
    Então a mensagem mostra ícone "❗"
    E ao clicar exibe tooltip "Falha após 5 tentativas"

  Cenário: Responsividade mobile
    Quando acesso a conversa pelo celular
    Então só o painel de chat é exibido
    E um gesto swipe para a direita retorna à lista de conversas
Definition of Done (DoD)
 Componente ChatPanel (Vue 3 / Livewire) ocupa 80 – 85 % largura desktop.

 Requisições:

GET /api/whatsapp/conversations/{id}/messages?offset=x&limit=50 (histórico).

POST /api/whatsapp/messages (envio) – coloca na fila whatsapp_outbox.

 WebSocket: canal whatsapp.conversation.{id} envia eventos new-message, typing.

 Input Composer: textarea auto-resize + botão emoji + botão anexo (aceita múltiplos).

 Preview de mídia antes do envio; upload direto para Evolution API (se necessário).

 Scroll Infinito (IntersectionObserver) para carregar mensagens antigas.

 A11y: aria-live="polite" para novas mensagens; navegação teclado nas ações.

 Dusk tests: enviar texto, imagem, falha simulada, scroll infinito e mobile layout.

 Documentação concluída (este arquivo); README seção “Chat WhatsApp” atualizada.

 Checkbox 03.2.2 em /documents/todos/HEAD_CS_2025-07.md marcado DONE após aprovação.

Notas Técnicas
Item	Implementação
Balões	Componente ChatBubble.vue → prop direction, type, status.
Typing	Emitir evento typing enquanto usuário digita (throttle 3 s).
Status Icons	Set via CSS background-image sprite for performance.
Media Storage	Salvar media_url retornado pela Evolution API; servir via proxy se privado.
Scroll Logic	Manter scroll na posição após prepend de mensagens (calc altura diff).
Hook Mobile	@swipe.right="goBackToList" usando vue3-gestures ou Alpine plugin.
Error Handling	Fallback a retries já definidos em fila whatsapp_outbox; UI reflete tentativas.