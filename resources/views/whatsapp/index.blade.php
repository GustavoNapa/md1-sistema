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
                                <small class="text-muted" id="contactPhone"></small>
                            </div>
                        </div>
                        
                        <!-- Botões de Ação (Associar/Desassociar) -->
                        <div class="ms-auto">
                            <button class="btn btn-sm btn-outline-secondary d-none" id="associateBtn">
                                <i class="fas fa-link me-1"></i>Associar
                            </button>
                            <button class="btn btn-sm btn-outline-danger d-none" id="unlinkBtn">
                                <i class="fas fa-unlink me-1"></i>Desvincular
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Área de Mensagens -->
                <div class="flex-grow-1 overflow-auto p-3" id="messagesContainer">
                    <!-- Mensagens serão carregadas via JavaScript -->
                    <div class="text-center p-3" id="messagesLoading">
                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                            <span class="visually-hidden">Carregando...</span>
                        </div>
                        <p class="small mt-2 mb-0">Carregando mensagens...</p>
                    </div>
                </div>
                
                <!-- Input de Mensagem -->
                <div class="border-top bg-white p-3">
                    <div class="input-group">
                        <textarea class="form-control" 
                                  placeholder="Digite sua mensagem..." 
                                  rows="1" 
                                  id="messageInput"></textarea>
                        <button class="btn btn-primary" id="sendMessageBtn">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const chatSidebar = document.getElementById('chatSidebar');
        const chatPanel = document.getElementById('chatPanel');
        const backToSidebarBtn = document.getElementById('backToSidebar');
        const searchConversationsInput = document.getElementById('searchConversations');
        const filterMineOnlyCheckbox = document.getElementById('filterMineOnly');
        const conversationsList = document.getElementById('conversationsList');
        const conversationsContainer = document.getElementById('conversationsContainer');
        const conversationsLoading = document.getElementById('conversationsLoading');
        const loadMoreSpinner = document.getElementById('loadMoreSpinner');
        const noChatSelected = document.getElementById('noChatSelected');
        const activeChatArea = document.getElementById('activeChatArea');
        const contactNameEl = document.getElementById('contactName');
        const contactPhoneEl = document.getElementById('contactPhone');
        const notAssociatedBadge = document.getElementById('notAssociatedBadge');
        const associatedBadge = document.getElementById('associatedBadge');
        const associateBtn = document.getElementById('associateBtn');
        const unlinkBtn = document.getElementById('unlinkBtn');
        const messagesContainer = document.getElementById('messagesContainer');
        const messagesLoading = document.getElementById('messagesLoading');
        const messageInput = document.getElementById('messageInput');
        const sendMessageBtn = document.getElementById('sendMessageBtn');

        let currentConversationId = null;
        let conversationsOffset = 0;
        const conversationsLimit = 20;
        let messagesOffset = 0;
        const messagesLimit = 50;
        let isLoadingConversations = false;
        let isLoadingMessages = false;
        let searchTimeout = null;

        // CSRF Token para requisições AJAX
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Função para carregar conversas
        async function loadConversations(reset = false) {
            if (isLoadingConversations) return;
            isLoadingConversations = true;
            if (reset) {
                conversationsOffset = 0;
                conversationsContainer.innerHTML = '';
                conversationsLoading.classList.remove('d-none');
            }
            loadMoreSpinner.classList.remove('d-none');

            const search = searchConversationsInput.value;
            const mine = filterMineOnlyCheckbox.checked;

            try {
                const response = await fetch(`/api/whatsapp/conversations?offset=${conversationsOffset}&limit=${conversationsLimit}&mine=${mine}&search=${search}`);
                const data = await response.json();

                conversationsLoading.classList.add('d-none');

                if (data.conversations.length === 0 && reset) {
                    conversationsContainer.innerHTML = '<p class="text-center text-muted p-3">Nenhuma conversa encontrada.</p>';
                } else if (data.conversations.length === 0) {
                    // Não há mais conversas para carregar
                }

                data.conversations.forEach(conversation => {
                    const conversationItem = document.createElement('div');
                    conversationItem.classList.add('list-group-item', 'list-group-item-action', 'py-2', 'px-3', 'border-bottom');
                    if (conversation.id === currentConversationId) {
                        conversationItem.classList.add('active');
                    }
                    conversationItem.dataset.id = conversation.id;
                    conversationItem.innerHTML = `
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">${conversation.contact_name || conversation.contact_phone}</h6>
                            <small>${conversation.last_message_at}</small>
                        </div>
                        <p class="mb-1 text-muted">${conversation.client_name ? `Vinculado: ${conversation.client_name}` : 'Não vinculado'}</p>
                        ${conversation.unread_count > 0 ? `<span class="badge bg-primary rounded-pill">${conversation.unread_count}</span>` : ''}
                    `;
                    conversationsContainer.appendChild(conversationItem);

                    conversationItem.addEventListener('click', () => openConversation(conversation.id, conversation.contact_name, conversation.contact_phone, conversation.is_linked));
                });
                conversationsOffset += data.conversations.length;

            } catch (error) {
                console.error('Erro ao carregar conversas:', error);
                alert('Erro ao carregar conversas.');
            } finally {
                isLoadingConversations = false;
                loadMoreSpinner.classList.add('d-none');
            }
        }

        // Função para abrir uma conversa
        async function openConversation(conversationId, contactName, contactPhone, isLinked) {
            currentConversationId = conversationId;
            conversationsContainer.querySelectorAll('.list-group-item').forEach(item => {
                item.classList.remove('active');
            });
            document.querySelector(`[data-id="${conversationId}"]`).classList.add('active');

            noChatSelected.classList.add('d-none');
            activeChatArea.classList.remove('d-none');

            contactNameEl.textContent = contactName || contactPhone;
            contactPhoneEl.textContent = contactPhone;

            if (isLinked) {
                associatedBadge.classList.remove('d-none');
                notAssociatedBadge.classList.add('d-none');
                associateBtn.classList.add('d-none');
                unlinkBtn.classList.remove('d-none');
            } else {
                associatedBadge.classList.add('d-none');
                notAssociatedBadge.classList.remove('d-none');
                associateBtn.classList.remove('d-none');
                unlinkBtn.classList.add('d-none');
            }

            messagesContainer.innerHTML = ''; // Limpa mensagens anteriores
            messagesOffset = 0; // Reseta offset para novas mensagens
            messagesLoading.classList.remove('d-none');
            await loadMessages();
            messagesContainer.scrollTop = messagesContainer.scrollHeight; // Scroll para o final

            // Marcar como lida
            try {
                await fetch(`/api/whatsapp/conversations/${conversationId}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                // Atualizar badge na sidebar (se houver)
                const conversationItem = conversationsContainer.querySelector(`[data-id="${conversationId}"]`);
                if (conversationItem) {
                    const badge = conversationItem.querySelector('.badge');
                    if (badge) badge.remove();
                }
            } catch (error) {
                console.error('Erro ao marcar como lida:', error);
            }

            // Alternar visualização em mobile
            if (window.innerWidth < 768) {
                chatSidebar.classList.add('d-none');
                chatPanel.classList.remove('d-none');
            }
        }

        // Função para carregar mensagens
        async function loadMessages() {
            if (isLoadingMessages || !currentConversationId) return;
            isLoadingMessages = true;

            try {
                const response = await fetch(`/api/whatsapp/conversations/${currentConversationId}/messages?offset=${messagesOffset}&limit=${messagesLimit}`);
                const data = await response.json();

                messagesLoading.classList.add('d-none');

                data.messages.forEach(message => {
                    const messageElement = document.createElement('div');
                    messageElement.classList.add('d-flex', 'mb-2', message.direction === 'outbound' ? 'justify-content-end' : 'justify-content-start');
                    messageElement.innerHTML = `
                        <div class="card ${message.direction === 'outbound' ? 'bg-success text-white' : 'bg-light'} p-2 rounded">
                            <div class="card-body p-0">
                                ${message.content}
                                <small class="d-block text-right ${message.direction === 'outbound' ? 'text-white-50' : 'text-muted'}">${message.created_at} ${message.status_icon || ''}</small>
                            </div>
                        </div>
                    `;
                    messagesContainer.prepend(messageElement); // Adiciona no início para scroll infinito para cima
                });
                messagesOffset += data.messages.length;
            } catch (error) {
                console.error('Erro ao carregar mensagens:', error);
                alert('Erro ao carregar mensagens.');
            } finally {
                isLoadingMessages = false;
            }
        }

        // Função para enviar mensagem
        async function sendMessage() {
            if (!currentConversationId || messageInput.value.trim() === '') return;

            const content = messageInput.value.trim();
            messageInput.value = ''; // Limpa o input

            try {
                const response = await fetch(`/api/whatsapp/messages`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken // Laravel CSRF token
                    },
                    body: JSON.stringify({
                        conversation_id: currentConversationId,
                        content: content
                    })
                });
                const data = await response.json();

                if (response.ok) {
                    const messageElement = document.createElement('div');
                    messageElement.classList.add('d-flex', 'mb-2', 'justify-content-end');
                    messageElement.innerHTML = `
                        <div class="card bg-success text-white p-2 rounded">
                            <div class="card-body p-0">
                                ${data.message.content}
                                <small class="d-block text-right text-white-50">${data.message.created_at} ${data.message.status_icon || ''}</small>
                            </div>
                        </div>
                    `;
                    messagesContainer.appendChild(messageElement);
                    messagesContainer.scrollTop = messagesContainer.scrollHeight; // Scroll para o final
                } else {
                    alert('Erro ao enviar mensagem: ' + (data.error || 'Erro desconhecido'));
                }
            } catch (error) {
                console.error('Erro ao enviar mensagem:', error);
                alert('Erro ao enviar mensagem.');
            }
        }

        // Event Listeners
        searchConversationsInput.addEventListener('input', () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => loadConversations(true), 500);
        });
        filterMineOnlyCheckbox.addEventListener('change', () => loadConversations(true));
        sendMessageBtn.addEventListener('click', sendMessage);
        messageInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });

        // Scroll infinito para carregar mais conversas
        conversationsList.addEventListener('scroll', () => {
            if (conversationsList.scrollTop + conversationsList.clientHeight >= conversationsList.scrollHeight - 100) {
                loadConversations();
            }
        });

        // Botão voltar para sidebar em mobile
        backToSidebarBtn.addEventListener('click', () => {
            chatPanel.classList.add('d-none');
            chatSidebar.classList.remove('d-none');
        });

        // Carregar conversas iniciais
        loadConversations();
    });
</script>
@endpush


