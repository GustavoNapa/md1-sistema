<!-- /documents/user-historyes/03-2-tela-chat.md -->

# 03.2 – Tela de Chat WhatsApp

## História do Usuário
> **Como** atendente interno  
> **quero** uma tela de chat que reúna minhas conversas WhatsApp em uma sidebar e um painel principal de mensagens  
> **para** responder rapidamente aos clientes, ver mensagens em tempo real e manter a interface familiar ao WhatsApp Web.

---

## Estrutura Geral

| Região (Desktop) | Largura | Descrição                                                                                         |
|------------------|---------|---------------------------------------------------------------------------------------------------|
| **Sidebar**      | 15 – 20 % | Lista de conversas recentes, badge de não lidas, busca, filtro “Somente atribuídas a mim”.        |
| **Painel Chat**  | 80 – 85 % | Balões de conversa, campo de texto, anexos, indicador *digitando…*, header com nome/telefone.      |

> **Mobile:** exibe primeiro a lista (layout vertical). Ao tocar numa conversa, navega para o painel de chat; swipe lateral retorna à lista.

---

## Sub-tarefas

| ID        | Descrição                                                                                                      |
|-----------|----------------------------------------------------------------------------------------------------------------|
| **03.2.1** | Implementar **Sidebar** de conversas (componente `ChatSidebar.vue/Livewire`).                                 |
| **03.2.2** | Implementar **Painel de Chat** (componente `ChatPanel.vue/Livewire`), balões, input, anexos, typing.          |

---

### 03.2.1 – Sidebar de Conversas

#### Critérios de Aceite (Gherkin)

```gherkin
Funcionalidade: Sidebar de conversas
  Cenário: Exibir conversas recentes
    Dado que existem conversas no banco
    Quando acesso a tela de chat
    Então vejo a lista ordenada pela data da última mensagem desc
    E cada item mostra nome/telefone e badge com quantidade de não lidas

  Cenário: Buscar conversa
    Quando digito "João" no campo de busca
    Então a lista exibe somente conversas que contenham "João"

  Cenário: Filtrar apenas minhas conversas
    Dado que seleciono filtro "Atribuídas a mim"
    Então apenas conversas onde eu sou atendente aparecem
Definition of Done
 Componente renderiza lista paginada (20 conversas, scroll infinito).

 Badges atualizam via WebSocket em tempo real.

 Barra de busca com debounce 300 ms.

 Campo toggle “Somente minhas”.

 Dusk test: selecionar conversa abre painel.