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
                                <h6 class="mb-0 d-flex align-items-center" id="contactName">
                                    Nome do Contato
                                    <span class="badge bg-warning ms-2 d-none" id="notAssociatedBadge">
                                        <i class="fas fa-exclamation-triangle me-1"></i>Não vinculado
                                    </span>
                                    <span class="badge bg-success ms-2 d-none" id="associatedBadge">
                                        <i class="fas fa-link me-1"></i>Vinculado
                                    </span>
                                </h6>
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
                                <li><a class="dropdown-item" href="#" id="manageAssociation">
                                    <i class="fas fa-link me-2"></i>Gerenciar vínculo
                                </a></li>
                                <li><a class="dropdown-item" href="#" id="createClientFromChat">
                                    <i class="fas fa-user-plus me-2"></i>Criar cliente
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#" id="markAsUnread">
                                    <i class="fas fa-envelope me-2"></i>Marcar como não lida
                                </a></li>
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

<!-- Modal para Gerenciar Associação -->
<div class="modal fade" id="associationModal" tabindex="-1" aria-labelledby="associationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="associationModalLabel">Gerenciar Vínculo da Conversa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Estado atual -->
                <div class="mb-4">
                    <h6>Estado Atual</h6>
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center me-3" 
                                     style="width: 48px; height: 48px;">
                                    <i class="fas fa-user text-white"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1" id="currentAssociationName">Carregando...</h6>
                                    <small class="text-muted" id="currentAssociationPhone"></small>
                                    <div>
                                        <span class="badge" id="currentAssociationStatus"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Possíveis matches -->
                <div class="mb-4" id="possibleMatchesSection">
                    <h6>Possíveis Vínculos</h6>
                    <div id="possibleMatchesList">
                        <div class="text-center p-3">
                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                <span class="visually-hidden">Carregando...</span>
                            </div>
                            <p class="small mt-2 mb-0">Buscando possíveis vínculos...</p>
                        </div>
                    </div>
                </div>
                
                <!-- Ações -->
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-primary" id="createNewClientBtn">
                        <i class="fas fa-user-plus me-2"></i>Criar Novo Cliente
                    </button>
                    <button type="button" class="btn btn-outline-danger" id="unlinkBtn">
                        <i class="fas fa-unlink me-2"></i>Desvincular
                    </button>
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
    let currentConversation = null;
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
        
        // Gerenciar associação
        document.getElementById('manageAssociation').addEventListener('click', function(e) {
            e.preventDefault();
            openAssociationModal();
        });
        
        // Criar cliente a partir do chat
        document.getElementById('createClientFromChat').addEventListener('click', function(e) {
            e.preventDefault();
            if (currentConversation) {
                window.open(`/whatsapp/create-client?phone=${encodeURIComponent(currentConversation.contact_phone)}`, '_blank');
            }
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
            currentConversation = data.conversation;
            
            // Atualizar cabeçalho
            updateChatHeader(currentConversation);
            
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


    
    function updateChatHeader(conversation) {
        const contactNameEl = document.getElementById('contactName');
        const contactPhoneEl = document.getElementById('contactPhone');
        const notAssociatedBadge = document.getElementById('notAssociatedBadge');
        const associatedBadge = document.getElementById('associatedBadge');
        
        // Atualizar nome e telefone
        contactNameEl.childNodes[0].textContent = conversation.contact_name;
        contactPhoneEl.textContent = conversation.contact_phone;
        
        // Atualizar badges de associação
        if (conversation.client && conversation.client.id) {
            notAssociatedBadge.classList.add('d-none');
            associatedBadge.classList.remove('d-none');
        } else {
            notAssociatedBadge.classList.remove('d-none');
            associatedBadge.classList.add('d-none');
        }
    }
    
    async function openAssociationModal() {
        if (!currentConversationId) return;
        
        const modal = new bootstrap.Modal(document.getElementById('associationModal'));
        modal.show();
        
        // Atualizar estado atual
        updateCurrentAssociationDisplay();
        
        // Carregar possíveis matches
        await loadPossibleMatches();
        
        // Setup event listeners do modal
        setupModalEventListeners();
    }
    
    function updateCurrentAssociationDisplay() {
        const nameEl = document.getElementById('currentAssociationName');
        const phoneEl = document.getElementById('currentAssociationPhone');
        const statusEl = document.getElementById('currentAssociationStatus');
        
        if (currentConversation) {
            nameEl.textContent = currentConversation.contact_name;
            phoneEl.textContent = currentConversation.contact_phone;
            
            if (currentConversation.client && currentConversation.client.id) {
                statusEl.textContent = 'Vinculado ao cliente: ' + currentConversation.client.name;
                statusEl.className = 'badge bg-success';
            } else {
                statusEl.textContent = 'Não vinculado';
                statusEl.className = 'badge bg-warning';
            }
        }
    }
    
    async function loadPossibleMatches() {
        const listEl = document.getElementById('possibleMatchesList');
        
        try {
            const response = await fetch(`/api/whatsapp/conversations/${currentConversationId}/matches`);
            const data = await response.json();
            
            if (data.matches.length === 0) {
                listEl.innerHTML = `
                    <div class="text-center p-3 text-muted">
                        <i class="fas fa-search fa-2x mb-2"></i>
                        <p>Nenhum cliente encontrado com este telefone</p>
                    </div>
                `;
                return;
            }
            
            listEl.innerHTML = '';
            data.matches.forEach(match => {
                const matchEl = document.createElement('div');
                matchEl.className = 'card mb-2';
                matchEl.innerHTML = `
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" 
                                     style="width: 40px; height: 40px;">
                                    <i class="fas fa-user text-white"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">${match.name}</h6>
                                    <small class="text-muted">${match.phone}</small>
                                    ${match.email ? `<br><small class="text-muted">${match.email}</small>` : ''}
                                </div>
                            </div>
                            <button class="btn btn-outline-primary btn-sm" 
                                    onclick="associateConversation('${match.type}', ${match.id})">
                                <i class="fas fa-link me-1"></i>Vincular
                            </button>
                        </div>
                    </div>
                `;
                listEl.appendChild(matchEl);
            });
            
        } catch (error) {
            console.error('Erro ao carregar matches:', error);
            listEl.innerHTML = `
                <div class="text-center p-3 text-danger">
                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                    <p>Erro ao carregar possíveis vínculos</p>
                </div>
            `;
        }
    }
    
    function setupModalEventListeners() {
        // Criar novo cliente
        document.getElementById('createNewClientBtn').onclick = function() {
            if (currentConversation) {
                window.open(`/whatsapp/create-client?phone=${encodeURIComponent(currentConversation.contact_phone)}`, '_blank');
            }
        };
        
        // Desvincular
        document.getElementById('unlinkBtn').onclick = async function() {
            if (confirm('Tem certeza que deseja desvincular esta conversa?')) {
                await unlinkConversation();
            }
        };
    }
    
    async function associateConversation(type, id) {
        try {
            const response = await fetch(`/api/whatsapp/conversations/${currentConversationId}/associate`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    type: type,
                    id: id,
                    reason: 'Associação manual via interface'
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Atualizar conversa atual
                currentConversation.client = data.association.type === 'client' ? {
                    id: data.association.id,
                    name: data.association.name
                } : null;
                
                // Atualizar UI
                updateChatHeader(currentConversation);
                updateCurrentAssociationDisplay();
                
                // Fechar modal
                bootstrap.Modal.getInstance(document.getElementById('associationModal')).hide();
                
                // Mostrar sucesso
                alert('Conversa vinculada com sucesso!');
            } else {
                alert('Erro ao vincular conversa: ' + (data.error || 'Erro desconhecido'));
            }
            
        } catch (error) {
            console.error('Erro ao associar conversa:', error);
            alert('Erro ao vincular conversa');
        }
    }
    
    async function unlinkConversation() {
        try {
            const response = await fetch(`/api/whatsapp/conversations/${currentConversationId}/unlink`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    reason: 'Desvinculação manual via interface'
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Atualizar conversa atual
                currentConversation.client = null;
                
                // Atualizar UI
                updateChatHeader(currentConversation);
                updateCurrentAssociationDisplay();
                
                // Fechar modal
                bootstrap.Modal.getInstance(document.getElementById('associationModal')).hide();
                
                // Mostrar sucesso
                alert('Conversa desvinculada com sucesso!');
            } else {
                alert('Erro ao desvincular conversa: ' + (data.error || 'Erro desconhecido'));
            }
            
        } catch (error) {
            console.error('Erro ao desvincular conversa:', error);
            alert('Erro ao desvincular conversa');
        }
    }
});
</script>
@endsection
