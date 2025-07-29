@extends('layouts.app')

@section('content')
<div class="container-fluid h-100">
    <div class="row h-100">
        <!-- Sidebar de Conversas -->
        <div class="col-md-3 col-lg-2 d-none d-md-block bg-light border-right h-100 overflow-auto" id="whatsapp-sidebar">
            <div class="d-flex flex-column p-3">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Buscar conversas..." id="search-conversations">
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="mine-conversations">
                    <label class="form-check-label" for="mine-conversations">
                        Somente minhas
                    </label>
                </div>
                <div id="conversations-list">
                    <!-- Conversas serão carregadas aqui -->
                </div>
                <div class="text-center mt-3">
                    <button class="btn btn-sm btn-outline-primary" id="load-more-conversations">Carregar Mais</button>
                </div>
            </div>
        </div>

        <!-- Painel de Chat -->
        <div class="col-md-9 col-lg-10 h-100 d-flex flex-column" id="whatsapp-chat-panel">
            <div class="card flex-grow-1 border-0">
                <div class="card-header bg-white border-bottom d-flex align-items-center">
                    <button class="btn btn-link d-md-none mr-2" id="back-to-sidebar"><i class="fas fa-arrow-left"></i></button>
                    <h5 class="mb-0" id="chat-contact-name">Selecione uma conversa</h5>
                    <span class="ml-auto badge badge-secondary" id="chat-status"></span>
                </div>
                <div class="card-body overflow-auto p-3" id="messages-container">
                    <!-- Mensagens serão carregadas aqui -->
                </div>
                <div class="card-footer bg-white border-top p-3">
                    <div class="input-group">
                        <textarea class="form-control" placeholder="Digite sua mensagem..." rows="1" id="message-input"></textarea>
                        <div class="input-group-append">
                            <button class="btn btn-primary" id="send-message-btn"><i class="fas fa-paper-plane"></i></button>
                        </div>
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
        const sidebar = document.getElementById('whatsapp-sidebar');
        const chatPanel = document.getElementById('whatsapp-chat-panel');
        const backToSidebarBtn = document.getElementById('back-to-sidebar');
        const conversationsList = document.getElementById('conversations-list');
        const loadMoreConversationsBtn = document.getElementById('load-more-conversations');
        const searchConversationsInput = document.getElementById('search-conversations');
        const mineConversationsCheckbox = document.getElementById('mine-conversations');
        const chatContactName = document.getElementById('chat-contact-name');
        const messagesContainer = document.getElementById('messages-container');
        const messageInput = document.getElementById('message-input');
        const sendMessageBtn = document.getElementById('send-message-btn');

        let currentConversationId = null;
        let conversationsOffset = 0;
        const conversationsLimit = 20;
        let messagesOffset = 0;
        const messagesLimit = 50;
        let isLoadingConversations = false;
        let isLoadingMessages = false;
        let searchTimeout = null;

        // Função para carregar conversas
        async function loadConversations() {
            if (isLoadingConversations) return;
            isLoadingConversations = true;

            const search = searchConversationsInput.value;
            const mine = mineConversationsCheckbox.checked;

            try {
                const response = await fetch(`/api/whatsapp/conversations?offset=${conversationsOffset}&limit=${conversationsLimit}&mine=${mine}&search=${search}`);
                const data = await response.json();

                if (data.conversations.length === 0) {
                    loadMoreConversationsBtn.style.display = 'none';
                } else {
                    data.conversations.forEach(conversation => {
                        const conversationItem = document.createElement('div');
                        conversationItem.classList.add('list-group-item', 'list-group-item-action', 'py-2', 'px-3', 'border-bottom');
                        conversationItem.dataset.id = conversation.id;
                        conversationItem.innerHTML = `
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">${conversation.contact_name || conversation.contact_phone}</h6>
                                <small>${conversation.last_message_at}</small>
                            </div>
                            <p class="mb-1 text-muted">${conversation.client_name ? `Vinculado: ${conversation.client_name}` : 'Não vinculado'}</p>
                            ${conversation.unread_count > 0 ? `<span class="badge badge-primary badge-pill">${conversation.unread_count}</span>` : ''}
                        `;
                        conversationsList.appendChild(conversationItem);

                        conversationItem.addEventListener('click', () => openConversation(conversation.id, conversation.contact_name || conversation.contact_phone));
                    });
                    conversationsOffset += data.conversations.length;
                }
            } catch (error) {
                console.error('Erro ao carregar conversas:', error);
                alert('Erro ao carregar conversas.');
            } finally {
                isLoadingConversations = false;
            }
        }

        // Função para abrir uma conversa
        async function openConversation(conversationId, contactName) {
            currentConversationId = conversationId;
            chatContactName.textContent = contactName;
            messagesContainer.innerHTML = ''; // Limpa mensagens anteriores
            messagesOffset = 0; // Reseta offset para novas mensagens
            await loadMessages();
            messagesContainer.scrollTop = messagesContainer.scrollHeight; // Scroll para o final

            // Marcar como lida
            try {
                await fetch(`/api/whatsapp/conversations/${conversationId}/read`, { method: 'POST' });
                // Atualizar badge na sidebar (se houver)
                const conversationItem = conversationsList.querySelector(`[data-id="${conversationId}"]`);
                if (conversationItem) {
                    const badge = conversationItem.querySelector('.badge');
                    if (badge) badge.remove();
                }
            } catch (error) {
                console.error('Erro ao marcar como lida:', error);
            }

            // Alternar visualização em mobile
            if (window.innerWidth < 768) {
                sidebar.classList.remove('d-flex');
                sidebar.classList.add('d-none');
                chatPanel.classList.remove('d-none');
                chatPanel.classList.add('d-flex');
            }
        }

        // Função para carregar mensagens
        async function loadMessages() {
            if (isLoadingMessages || !currentConversationId) return;
            isLoadingMessages = true;

            try {
                const response = await fetch(`/api/whatsapp/conversations/${currentConversationId}/messages?offset=${messagesOffset}&limit=${messagesLimit}`);
                const data = await response.json();

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
                        'X-CSRF-TOKEN': '{{ csrf_token() }}' // Laravel CSRF token
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
        loadMoreConversationsBtn.addEventListener('click', loadConversations);
        sendMessageBtn.addEventListener('click', sendMessage);
        messageInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });

        searchConversationsInput.addEventListener('keyup', function () {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                conversationsList.innerHTML = '';
                conversationsOffset = 0;
                loadConversations();
            }, 300);
        });

        mineConversationsCheckbox.addEventListener('change', function () {
            conversationsList.innerHTML = '';
            conversationsOffset = 0;
            loadConversations();
        });

        backToSidebarBtn.addEventListener('click', () => {
            sidebar.classList.remove('d-none');
            sidebar.classList.add('d-flex');
            chatPanel.classList.remove('d-flex');
            chatPanel.classList.add('d-none');
        });

        // Carregar conversas iniciais
        loadConversations();
    });
</script>
@endpush


