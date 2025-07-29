@extends('layouts.app')

@section('content')
<div class="container-fluid p-0 h-100">
    <div class="row g-0 h-100">
        <!-- Sidebar de Conversas (15-20%) -->
        <div class="col-md-3 col-lg-2 border-end bg-light" id="chatSidebar">
            <div class="d-flex flex-column h-100">
                <!-- Cabeçalho da Sidebar -->
                <div class="p-3 border-bottom bg-white">
                    <h5 class="mb-2">WhatsApp</h5>
                    
                    <!-- Barra de Busca -->
                    <div class="input-group input-group-sm mb-2">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" 
                               class="form-control" 
                               id="searchConversations" 
                               placeholder="Buscar conversas...">
                    </div>
                    
                    <!-- Filtro "Somente Minhas" -->
                    <div class="form-check form-switch">
                        <input class="form-check-input" 
                               type="checkbox" 
                               id="filterMineOnly">
                        <label class="form-check-label small" for="filterMineOnly">
                            Somente minhas
                        </label>
                    </div>
                </div>
                
                <!-- Lista de Conversas -->
                <div class="flex-grow-1 overflow-auto" id="conversationsList">
                    <!-- Loading inicial -->
                    <div class="text-center p-3" id="conversationsLoading">
                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                            <span class="visually-hidden">Carregando...</span>
                        </div>
                        <p class="small mt-2 mb-0">Carregando conversas...</p>
                    </div>
                    
                    <!-- Lista será preenchida via JavaScript -->
                    <div id="conversationsContainer"></div>
                    
                    <!-- Loading para scroll infinito -->
                    <div class="text-center p-2 d-none" id="loadMoreSpinner">
                        <div class="spinner-border spinner-border-sm text-secondary" role="status">
                            <span class="visually-hidden">Carregando mais...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Painel de Chat (80-85%) -->
        <div class="col-md-9 col-lg-10 d-flex flex-column" id="chatPanel">
            <!-- Estado inicial - nenhuma conversa selecionada -->
            <div class="d-flex align-items-center justify-content-center h-100" id="noChatSelected">
                <div class="text-center text-muted">
                    <i class="fas fa-comments fa-3x mb-3"></i>
                    <h4>Selecione uma conversa</h4>
                    <p>Escolha uma conversa na barra lateral para começar a conversar</p>
                </div>
            </div>
            
            <!-- Área de chat ativa (inicialmente oculta) -->
            <div class="d-flex flex-column h-100 d-none" id="activeChatArea">
                <!-- Cabeçalho do Chat -->
                <div class="border-bottom bg-white p-3" id="chatHeader">
                    <div class="d-flex align-items-center">
                        <!-- Botão voltar (mobile) -->
                        <button class="btn btn-link d-md-none me-2 p-0" id="backToSidebar">
                            <i class="fas fa-arrow-left"></i>
                        </button>
                        
                        <!-- Avatar e Info do Contato -->
                        <div class="d-flex align-items-center flex-grow-1">
                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" 
                                 style="width: 40px; height: 40px;">
                                <i class="fas fa-user text-white"></i>
                            </div>
                            <div>
                                <h6 class="mb-0" id="contactName">Nome do Contato</h6>
                                <small class="text-muted" id="contactPhone">+55 11 99999-9999</small>
                                <div class="small text-success d-none" id="typingIndicator">
                                    <i class="fas fa-circle fa-xs me-1"></i>
                                    digitando...
                                </div>
                            </div>
                        </div>
                        
                        <!-- Ações do Chat -->
                        <div class="dropdown">
                            <button class="btn btn-link text-muted" 
                                    type="button" 
                                    data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#" id="viewClientInfo">
                                    <i class="fas fa-user me-2"></i>Ver cliente
                                </a></li>
                                <li><a class="dropdown-item" href="#" id="markAsUnread">
                                    <i class="fas fa-envelope me-2"></i>Marcar como não lida
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="#" id="archiveConversation">
                                    <i class="fas fa-archive me-2"></i>Arquivar conversa
                                </a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <!-- Área de Mensagens -->
                <div class="flex-grow-1 overflow-auto p-3" id="messagesArea">
                    <!-- Loading de mensagens -->
                    <div class="text-center p-3" id="messagesLoading">
                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                            <span class="visually-hidden">Carregando mensagens...</span>
                        </div>
                    </div>
                    
                    <!-- Container das mensagens -->
                    <div id="messagesContainer"></div>
                </div>
                
                <!-- Área de Composição -->
                <div class="border-top bg-white p-3" id="messageComposer">
                    <div class="d-flex align-items-end gap-2">
                        <!-- Botão de Anexo -->
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary" 
                                    type="button" 
                                    data-bs-toggle="dropdown">
                                <i class="fas fa-paperclip"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" id="attachImage">
                                    <i class="fas fa-image me-2"></i>Imagem
                                </a></li>
                                <li><a class="dropdown-item" href="#" id="attachDocument">
                                    <i class="fas fa-file me-2"></i>Documento
                                </a></li>
                                <li><a class="dropdown-item" href="#" id="attachAudio">
                                    <i class="fas fa-microphone me-2"></i>Áudio
                                </a></li>
                            </ul>
                        </div>
                        
                        <!-- Input de Mensagem -->
                        <div class="flex-grow-1">
                            <textarea class="form-control" 
                                      id="messageInput" 
                                      rows="1" 
                                      placeholder="Digite uma mensagem..."
                                      style="resize: none; max-height: 120px;"></textarea>
                        </div>
                        
                        <!-- Botão Emoji -->
                        <button class="btn btn-outline-secondary" id="emojiButton">
                            <i class="fas fa-smile"></i>
                        </button>
                        
                        <!-- Botão Enviar -->
                        <button class="btn btn-primary" id="sendButton">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                    
                    <!-- Input de arquivo oculto -->
                    <input type="file" 
                           id="fileInput" 
                           class="d-none" 
                           accept="image/*,audio/*,video/*,.pdf,.doc,.docx">
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Estilos específicos do chat */
.chat-bubble {
    max-width: 70%;
    margin-bottom: 10px;
    padding: 8px 12px;
    border-radius: 18px;
    word-wrap: break-word;
}

.chat-bubble.outbound {
    background-color: #dcf8c6;
    margin-left: auto;
    border-bottom-right-radius: 4px;
}

.chat-bubble.inbound {
    background-color: #f1f1f1;
    margin-right: auto;
    border-bottom-left-radius: 4px;
}

.chat-bubble .message-time {
    font-size: 0.75rem;
    color: #666;
    margin-top: 4px;
    display: flex;
    align-items: center;
    justify-content: flex-end;
}

.chat-bubble.inbound .message-time {
    justify-content: flex-start;
}

.conversation-item {
    padding: 12px 16px;
    border-bottom: 1px solid #f0f0f0;
    cursor: pointer;
    transition: background-color 0.2s;
}

.conversation-item:hover {
    background-color: #f8f9fa;
}

.conversation-item.active {
    background-color: #e3f2fd;
    border-left: 3px solid #2196f3;
}

.conversation-item .unread-badge {
    background-color: #25d366;
    color: white;
    font-size: 0.75rem;
    padding: 2px 6px;
    border-radius: 10px;
    min-width: 18px;
    text-align: center;
}

.message-status {
    margin-left: 4px;
    font-size: 0.8rem;
}

/* Responsividade */
@media (max-width: 768px) {
    #chatSidebar {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 1050;
        transform: translateX(-100%);
        transition: transform 0.3s ease;
    }
    
    #chatSidebar.show {
        transform: translateX(0);
    }
    
    #chatPanel {
        width: 100%;
    }
}

/* Auto-resize textarea */
#messageInput {
    overflow-y: hidden;
}
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Estado da aplicação
    let currentConversationId = null;
    let conversations = [];
    let messages = [];
    let isLoadingConversations = false;
    let isLoadingMessages = false;
    let conversationsOffset = 0;
    let searchTimeout = null;
    
    // Elementos DOM
    const conversationsList = document.getElementById('conversationsContainer');
    const messagesContainer = document.getElementById('messagesContainer');
    const messageInput = document.getElementById('messageInput');
    const sendButton = document.getElementById('sendButton');
    const searchInput = document.getElementById('searchConversations');
    const filterMineOnly = document.getElementById('filterMineOnly');
    const noChatSelected = document.getElementById('noChatSelected');
    const activeChatArea = document.getElementById('activeChatArea');
    const conversationsLoading = document.getElementById('conversationsLoading');
    const messagesLoading = document.getElementById('messagesLoading');
    
    // Inicializar
    loadConversations();
    setupEventListeners();
    
    function setupEventListeners() {
        // Busca de conversas com debounce
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                conversationsOffset = 0;
                loadConversations(true);
            }, 300);
        });
        
        // Filtro "somente minhas"
        filterMineOnly.addEventListener('change', function() {
            conversationsOffset = 0;
            loadConversations(true);
        });
        
        // Envio de mensagem
        sendButton.addEventListener('click', sendMessage);
        messageInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });
        
        // Auto-resize do textarea
        messageInput.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 120) + 'px';
        });
        
        // Scroll infinito para conversas
        document.getElementById('conversationsList').addEventListener('scroll', function() {
            if (this.scrollTop + this.clientHeight >= this.scrollHeight - 5) {
                loadMoreConversations();
            }
        });
        
        // Botão voltar (mobile)
        document.getElementById('backToSidebar').addEventListener('click', function() {
            document.getElementById('chatSidebar').classList.add('show');
        });
    }
    
    async function loadConversations(reset = false) {
        if (isLoadingConversations) return;
        
        isLoadingConversations = true;
        
        if (reset) {
            conversationsOffset = 0;
            conversationsLoading.classList.remove('d-none');
        }
        
        try {
            const params = new URLSearchParams({
                offset: conversationsOffset,
                limit: 20,
                mine: filterMineOnly.checked,
                search: searchInput.value
            });
            
            const response = await fetch(`/api/whatsapp/conversations?${params}`);
            const data = await response.json();
            
            if (reset) {
                conversations = data.conversations;
                renderConversations();
            } else {
                conversations.push(...data.conversations);
                appendConversations(data.conversations);
            }
            
            conversationsOffset += data.conversations.length;
            
        } catch (error) {
            console.error('Erro ao carregar conversas:', error);
        } finally {
            isLoadingConversations = false;
            conversationsLoading.classList.add('d-none');
        }
    }
    
    function renderConversations() {
        conversationsList.innerHTML = '';
        conversations.forEach(conversation => {
            appendConversationItem(conversation);
        });
    }
    
    function appendConversations(newConversations) {
        newConversations.forEach(conversation => {
            appendConversationItem(conversation);
        });
    }
    
    function appendConversationItem(conversation) {
        const item = document.createElement('div');
        item.className = 'conversation-item';
        item.dataset.conversationId = conversation.id;
        
        const lastMessage = conversation.last_message;
        const lastMessageText = lastMessage ? 
            (lastMessage.type === 'text' ? lastMessage.content : `📎 ${lastMessage.type}`) : 
            'Nenhuma mensagem';
        
        const unreadBadge = conversation.unread_count > 0 ? 
            `<span class="unread-badge">${conversation.unread_count}</span>` : '';
        
        item.innerHTML = `
            <div class="d-flex align-items-center">
                <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center me-3" 
                     style="width: 48px; height: 48px;">
                    <i class="fas fa-user text-white"></i>
                </div>
                <div class="flex-grow-1 min-width-0">
                    <div class="d-flex justify-content-between align-items-start">
                        <h6 class="mb-1 text-truncate">${conversation.contact_name}</h6>
                        <div class="d-flex align-items-center">
                            ${unreadBadge}
                            <small class="text-muted ms-2">${formatTime(conversation.last_message_at)}</small>
                        </div>
                    </div>
                    <p class="mb-0 text-muted small text-truncate">${lastMessageText}</p>
                </div>
            </div>
        `;
        
        item.addEventListener('click', () => openConversation(conversation.id));
        conversationsList.appendChild(item);
    }
    
    async function openConversation(conversationId) {
        if (currentConversationId === conversationId) return;
        
        currentConversationId = conversationId;
        
        // Atualizar UI
        document.querySelectorAll('.conversation-item').forEach(item => {
            item.classList.remove('active');
        });
        document.querySelector(`[data-conversation-id="${conversationId}"]`).classList.add('active');
        
        // Mostrar área de chat
        noChatSelected.classList.add('d-none');
        activeChatArea.classList.remove('d-none');
        messagesLoading.classList.remove('d-none');
        
        // Carregar mensagens
        await loadMessages(conversationId);
        
        // Marcar como lida
        await markAsRead(conversationId);
        
        // Ocultar sidebar no mobile
        if (window.innerWidth < 768) {
            document.getElementById('chatSidebar').classList.remove('show');
        }
    }
    
    async function loadMessages(conversationId) {
        if (isLoadingMessages) return;
        
        isLoadingMessages = true;
        
        try {
            const response = await fetch(`/api/whatsapp/conversations/${conversationId}/messages`);
            const data = await response.json();
            
            messages = data.messages;
            
            // Atualizar cabeçalho
            document.getElementById('contactName').textContent = data.conversation.contact_name;
            document.getElementById('contactPhone').textContent = data.conversation.contact_phone;
            
            // Renderizar mensagens
            renderMessages();
            scrollToBottom();
            
        } catch (error) {
            console.error('Erro ao carregar mensagens:', error);
        } finally {
            isLoadingMessages = false;
            messagesLoading.classList.add('d-none');
        }
    }
    
    function renderMessages() {
        messagesContainer.innerHTML = '';
        messages.forEach(message => {
            appendMessage(message);
        });
    }
    
    function appendMessage(message) {
        const bubble = document.createElement('div');
        bubble.className = `chat-bubble ${message.direction}`;
        
        let content = '';
        if (message.type === 'text') {
            content = message.content;
        } else {
            content = `<i class="fas fa-file me-2"></i>${message.type}`;
        }
        
        bubble.innerHTML = `
            <div>${content}</div>
            <div class="message-time">
                ${formatTime(message.sent_at)}
                ${message.direction === 'outbound' ? `<span class="message-status ${message.status_class}">${message.status_icon}</span>` : ''}
            </div>
        `;
        
        messagesContainer.appendChild(bubble);
    }
    
    async function sendMessage() {
        const content = messageInput.value.trim();
        if (!content || !currentConversationId) return;
        
        try {
            const response = await fetch('/api/whatsapp/messages', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    conversation_id: currentConversationId,
                    type: 'text',
                    content: content
                })
            });
            
            const data = await response.json();
            
            // Adicionar mensagem à lista
            messages.push(data.message);
            appendMessage(data.message);
            scrollToBottom();
            
            // Limpar input
            messageInput.value = '';
            messageInput.style.height = 'auto';
            
        } catch (error) {
            console.error('Erro ao enviar mensagem:', error);
        }
    }
    
    async function markAsRead(conversationId) {
        try {
            await fetch(`/api/whatsapp/conversations/${conversationId}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            
            // Atualizar badge na sidebar
            const conversationItem = document.querySelector(`[data-conversation-id="${conversationId}"]`);
            const badge = conversationItem.querySelector('.unread-badge');
            if (badge) {
                badge.remove();
            }
            
        } catch (error) {
            console.error('Erro ao marcar como lida:', error);
        }
    }
    
    function loadMoreConversations() {
        if (!isLoadingConversations && conversations.length > 0) {
            loadConversations();
        }
    }
    
    function scrollToBottom() {
        const messagesArea = document.getElementById('messagesArea');
        messagesArea.scrollTop = messagesArea.scrollHeight;
    }
    
    function formatTime(timestamp) {
        if (!timestamp) return '';
        
        const date = new Date(timestamp);
        const now = new Date();
        const diffInHours = (now - date) / (1000 * 60 * 60);
        
        if (diffInHours < 24) {
            return date.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
        } else if (diffInHours < 168) { // 7 dias
            return date.toLocaleDateString('pt-BR', { weekday: 'short' });
        } else {
            return date.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit' });
        }
    }
});
</script>
@endsection

