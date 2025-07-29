<!-- /documents/user-historyes/03-2-1-sidebar-conversas.md -->

# 03.2.1 – Sidebar de Conversas (15 – 20 %)

## História do Usuário
> **Como** atendente interno  
> **quero** ver uma lista compacta das minhas conversas recentes de WhatsApp em uma barra lateral  
> **para** identificar rapidamente quem precisa de resposta, acessar o chat em um clique e monitorar novas mensagens em tempo real.

---

## Objetivos Funcionais
1. **Listar conversas** ordenadas por _última mensagem desc_ (LIFO).  
2. **Mostrar informações** principais:  
   - Nome do cliente ou telefone.  
   - Última mensagem (truncada).  
   - Hora/min da última mensagem.  
   - Badge de **não lidas**.  
3. **Busca** em tempo real por nome/telefone.  
4. **Filtro** _“Somente atribuídas a mim”_ (switch).  
5. **Scroll infinito** – carregar 20 conversas por vez.  
6. **WebSocket** atualiza badge e reposiciona conversa no topo quando chega nova mensagem.

---

## Critérios de Aceite (Gherkin)

```gherkin
Funcionalidade: Sidebar de conversas WhatsApp
  Cenário: Lista inicial
    Dado que existem mais de 20 conversas no sistema
    Quando acesso a tela de chat
    Então vejo as 20 conversas mais recentes
    E a barra lateral ocupa no máximo 20 % da largura

  Cenário: Badge de não lidas
    Quando chega uma nova mensagem de João
    Então a conversa “João” exibe badge “1”
    E a conversa é movida para o topo da lista

  Cenário: Busca de conversa
    Dado que digito "Maria" na barra de busca
    Então a lista exibe apenas conversas contendo "Maria" no nome ou telefone

  Cenário: Filtro por atendente
    Dado que ativo o filtro "Somente atribuídas a mim"
    Então conversas sem atendente = meu usuário são ocultadas

  Cenário: Scroll infinito
    Quando rolo até o final da lista
    Então o sistema requisita e exibe as próximas 20 conversas
Definition of Done (DoD)
 Componente Vue/Livewire ChatSidebar renderiza lista com 20 itens iniciais.

 API GET /api/whatsapp/conversations?offset=0&limit=20&mine=true|false&search=.

 WebSocket (whatsapp.conversation.*) atualiza badge/ordem em tempo real.

 Busca com debounce 300 ms; limpa ao fechar campo.

 Filtro toggle salva preferência em user_settings.sidebar_mine_only.

 Scroll Infinito via IntersectionObserver; spinner Bootstrap.

 Responsividade: em mobile, sidebar vira lista fullscreen.

 Testes PHPUnit/Pest (API, badge) + Dusk (scroll, busca, filtro).

 Documentação concluída (este arquivo); README ajustado.

 Checkbox 03.2.1 marcado DONE após aprovação da Head de CS.

Notas Técnicas
Item	Implementação
Modelo	WhatsappConversation (id, last_message, unread, user_id, etc.).
Eager load	with('cliente') para nome/telefone.
CSS	Utilizar overflow-y: auto; height: 100vh; e flex-basis: 18%.
Badges	Bootstrap badge bg-danger para não lidas.
Acessibilidade	Cada item role="button" + aria-label="Abrir conversa de {{nome}}".