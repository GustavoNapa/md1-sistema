### 03.2.2 – Painel de Chat

#### Critérios de Aceite (Gherkin)

gherkin

CopiarEditar

`Funcionalidade: Painel de Chat   Cenário: Exibir histórico     Dado que abro uma conversa     Então vejo as últimas 50 mensagens (scroll infinito para mais antigas)     E balões do cliente aparecem à esquerda em cinza     E balões do atendente aparecem à direita em verde    Cenário: Enviar mensagem de texto     Quando digito "Olá"     E clico em Enviar     Então a mensagem surge com indicador "🕒 na fila"     E após envio bem-sucedido exibe "✓✓"    Cenário: Mostrar indicador "digitando"     Dado que o cliente está digitando     Então abaixo do header vejo "Cliente está digitando…"    Cenário: Responsividade     Quando acesso pelo celular     Então o painel ocupa 100 % da tela e posso voltar à lista por swipe`

#### Definition of Done

-  Componente balão (`ChatBubble`) suporta texto, imagem, áudio, documento, sticker.
    
-  Campo de texto com upload de anexo (preview).
    
-  WebSocket recebe `typing` e atualiza indicador.
    
-  Scroll infinito (IntersectionObserver) para mensagens antigas.
    
-  Mensagens enviadas entram na fila `whatsapp_outbox`.
    
-  Toast erro em caso de falha (após 5 tentativas).
    
-  Dusk: envio texto, imagem, scroll infinito.
    

---

## Definition of Done (geral 03.2)

-  Layout obedecendo proporções 15–20 % / 80–85 %, Bootstrap 5 flex.
    
-  Acessibilidade básica (`aria-label`, contraste, foco).
    
-  Componentes Vue 3 (ou Livewire) documentados.
    
-  Testes unitários + Dusk cobrindo Sidebar e Painel.
    
-  Logs de abertura/fechamento de conversa salvos (para analytics).
    
-  Documentação finalizada (este arquivo) e linkada no README.
    
-  Checkbox **03.2**, **03.2.1**, **03.2.2** marcados **DONE** após aprovação da Head de CS.
    

---

## Notas Técnicas

|Item|Detalhe|
|---|---|
|**Componente Parent**|`<whatsapp-chat />` carrega `ChatSidebar` e `ChatPanel` via slots.|
|**Routing**|`GET /whatsapp` (lista) • `GET /whatsapp/{conversation}` (chat)|
|**Broadcast Channel**|`whatsapp.conversation.{id}` para mensagens/typing.|
|**Indicator Icons**|`🕒` fila, `✓` enviado, `✓✓` entregue, `❗` falha.|
|**CSS Vars**|`--bubble-client-bg: #f0f0f0; --bubble-agent-bg: #d1f9d1;` para fácil theming.|