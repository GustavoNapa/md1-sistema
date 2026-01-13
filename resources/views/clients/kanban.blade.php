@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="page-title mb-0">Kanban de Clientes</h4>
                    <div>
                        <a href="{{ route('clients.index') }}" class="btn btn-secondary me-2">
                            <i class="fas fa-list"></i> Visualização em Lista
                        </a>
                        <a href="{{ route('clients.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Novo Cliente
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('clients.kanban') }}" class="row g-2 mb-4">
                        <div class="col-md-4">
                            <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Buscar por nome, CPF, email ou telefone">
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-select">
                                <option value="">Todos os status</option>
                                <option value="active" @if(request('status')=='active') selected @endif>Ativo</option>
                                <option value="inactive" @if(request('status')=='inactive') selected @endif>Inativo</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="specialty" class="form-select">
                                <option value="">Todas as especialidades</option>
                                @foreach($specialties ?? [] as $sp)
                                    <option value="{{ $sp }}" @if(request('specialty') == $sp) selected @endif>{{ $sp }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-outline-primary">Pesquisar</button>
                            <a href="{{ route('clients.kanban') }}" class="btn btn-outline-secondary ms-1">Limpar</a>
                        </div>
                    </form>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="row">
                        <!-- Coluna Leads -->
                        <div class="col-md-3">
                            <div class="kanban-column" data-column="lead">
                                <div class="kanban-header bg-info text-white p-2 rounded mb-2">
                                    <h5 class="mb-0">
                                        <i class="fas fa-user-plus"></i> Leads
                                        <span class="badge bg-white text-info float-end">{{ count($leadClients) }}</span>
                                    </h5>
                                </div>
                                <div class="kanban-cards" data-column="lead">
                                    @forelse($leadClients as $client)
                                        <div class="card mb-2 shadow-sm client-card" draggable="true" data-client-id="{{ $client->id }}" data-client-name="{{ $client->name }}">
                                            <div class="card-body p-3">
                                                <div class="d-flex align-items-start">
                                                    <input type="checkbox" class="form-check-input me-2 client-checkbox" data-client-id="{{ $client->id }}">
                                                    <div class="flex-grow-1">
                                                        <h6 class="card-title mb-1">
                                                            {{ $client->name }}
                                                        </h6>
                                                        <p class="card-text small mb-1">
                                                            <i class="fas fa-envelope"></i> {{ $client->email ?? '-' }}
                                                        </p>
                                                        <p class="card-text small mb-1">
                                                            <i class="fas fa-phone"></i> {{ $client->phone ? $client->formatted_phone : '-' }}
                                                        </p>
                                                        @if($client->specialty)
                                                            <span class="badge bg-secondary">{{ $client->specialty }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center text-muted small">Nenhum lead</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <!-- Coluna Ativos -->
                        <div class="col-md-3">
                            <div class="kanban-column" data-column="active">
                                <div class="kanban-header bg-success text-white p-2 rounded mb-2">
                                    <h5 class="mb-0">
                                        <i class="fas fa-user-check"></i> Ativos
                                        <span class="badge bg-white text-success float-end">{{ count($activeClients) }}</span>
                                    </h5>
                                </div>
                                <div class="kanban-cards" data-column="active">
                                    @forelse($activeClients as $client)
                                        <div class="card mb-2 shadow-sm client-card" draggable="true" data-client-id="{{ $client->id }}" data-client-name="{{ $client->name }}">
                                            <div class="card-body p-3">
                                                <div class="d-flex align-items-start">
                                                    <input type="checkbox" class="form-check-input me-2 client-checkbox" data-client-id="{{ $client->id }}">
                                                    <div class="flex-grow-1">
                                                        <h6 class="card-title mb-1">
                                                            {{ $client->name }}
                                                        </h6>
                                                        <p class="card-text small mb-1">
                                                            <i class="fas fa-envelope"></i> {{ $client->email ?? '-' }}
                                                        </p>
                                                        <p class="card-text small mb-1">
                                                            <i class="fas fa-phone"></i> {{ $client->phone ? $client->formatted_phone : '-' }}
                                                        </p>
                                                        @if($client->specialty)
                                                            <span class="badge bg-secondary">{{ $client->specialty }}</span>
                                                        @endif
                                                        <span class="badge bg-info">{{ $client->inscriptions_count }} inscrições</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center text-muted small">Nenhum cliente ativo</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <!-- Coluna Concluídos -->
                        <div class="col-md-3">
                            <div class="kanban-column" data-column="completed">
                                <div class="kanban-header bg-primary text-white p-2 rounded mb-2">
                                    <h5 class="mb-0">
                                        <i class="fas fa-check-circle"></i> Concluídos
                                        <span class="badge bg-white text-primary float-end">{{ count($completedClients) }}</span>
                                    </h5>
                                </div>
                                <div class="kanban-cards" data-column="completed">
                                    @forelse($completedClients as $client)
                                        <div class="card mb-2 shadow-sm client-card" draggable="true" data-client-id="{{ $client->id }}" data-client-name="{{ $client->name }}">
                                            <div class="card-body p-3">
                                                <div class="d-flex align-items-start">
                                                    <input type="checkbox" class="form-check-input me-2 client-checkbox" data-client-id="{{ $client->id }}">
                                                    <div class="flex-grow-1">
                                                        <h6 class="card-title mb-1">
                                                            {{ $client->name }}
                                                        </h6>
                                                        <p class="card-text small mb-1">
                                                            <i class="fas fa-envelope"></i> {{ $client->email ?? '-' }}
                                                        </p>
                                                        <p class="card-text small mb-1">
                                                            <i class="fas fa-phone"></i> {{ $client->phone ? $client->formatted_phone : '-' }}
                                                        </p>
                                                        @if($client->specialty)
                                                            <span class="badge bg-secondary">{{ $client->specialty }}</span>
                                                        @endif
                                                        <span class="badge bg-info">{{ $client->inscriptions_count }} inscrições</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center text-muted small">Nenhum cliente concluído</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <!-- Coluna Inativos -->
                        <div class="col-md-3">
                            <div class="kanban-column" data-column="inactive">
                                <div class="kanban-header bg-danger text-white p-2 rounded mb-2">
                                    <h5 class="mb-0">
                                        <i class="fas fa-user-times"></i> Inativos
                                        <span class="badge bg-white text-danger float-end">{{ count($inactiveClients) }}</span>
                                    </h5>
                                </div>
                                <div class="kanban-cards" data-column="inactive">
                                    @forelse($inactiveClients as $client)
                                        <div class="card mb-2 shadow-sm client-card" draggable="true" data-client-id="{{ $client->id }}" data-client-name="{{ $client->name }}">
                                            <div class="card-body p-3">
                                                <div class="d-flex align-items-start">
                                                    <input type="checkbox" class="form-check-input me-2 client-checkbox" data-client-id="{{ $client->id }}">
                                                    <div class="flex-grow-1">
                                                        <h6 class="card-title mb-1">
                                                            {{ $client->name }}
                                                        </h6>
                                                        <p class="card-text small mb-1">
                                                            <i class="fas fa-envelope"></i> {{ $client->email ?? '-' }}
                                                        </p>
                                                        <p class="card-text small mb-1">
                                                            <i class="fas fa-phone"></i> {{ $client->phone ? $client->formatted_phone : '-' }}
                                                        </p>
                                                        @if($client->specialty)
                                                            <span class="badge bg-secondary">{{ $client->specialty }}</span>
                                                        @endif
                                                        @if($client->inscriptions_count > 0)
                                                            <span class="badge bg-info">{{ $client->inscriptions_count }} inscrições</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center text-muted small">Nenhum cliente inativo</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Barra Flutuante de Ações -->
<div id="floating-action-bar" class="floating-action-bar" style="display: none;">
    <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between py-2">
            <div class="d-flex align-items-center">
                <button type="button" class="btn btn-link text-white" id="close-action-bar">
                    <i class="fas fa-times"></i>
                </button>
                <span class="badge bg-white text-primary ms-2" id="selected-count">0</span>
                <span class="text-white ms-2" id="selected-text">SELECIONADOS</span>
            </div>
            <div class="d-flex gap-3">
                <button type="button" class="btn btn-link text-white" id="view-client" title="Visualizar">
                    Visualizar
                </button>
                <button type="button" class="btn btn-link text-white" id="edit-client" title="Editar">
                    Editar
                </button>
                <button type="button" class="btn btn-link text-danger" id="delete-clients" title="Excluir">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.kanban-column {
    min-height: 600px;
}

.kanban-cards {
    max-height: calc(100vh - 300px);
    overflow-y: auto;
    min-height: 500px;
}

.kanban-header h5 {
    font-size: 1rem;
}

.card {
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-2px);
}

.client-card {
    cursor: move;
}

.client-card.dragging {
    opacity: 0.5;
}

.client-card.selected {
    background-color: #e7f3ff;
    border-color: #0d6efd;
}

.kanban-cards.drag-over {
    background-color: #f0f0f0;
    border: 2px dashed #007bff;
    border-radius: 5px;
}

.floating-action-bar {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
    z-index: 1000;
    animation: slideUp 0.3s ease-out;
}

@keyframes slideUp {
    from {
        transform: translateY(100%);
    }
    to {
        transform: translateY(0);
    }
}

.floating-action-bar .btn-link {
    text-decoration: none;
    font-weight: 500;
}

.floating-action-bar .btn-link:hover {
    text-decoration: underline;
}

.client-checkbox {
    cursor: pointer;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let draggedElement = null;
    let selectedClients = new Set();

    // === DRAG AND DROP ===
    
    // Adicionar event listeners para todos os cards
    const cards = document.querySelectorAll('.client-card');
    cards.forEach(card => {
        card.addEventListener('dragstart', handleDragStart);
        card.addEventListener('dragend', handleDragEnd);
    });

    // Adicionar event listeners para todas as zonas de drop
    const dropZones = document.querySelectorAll('.kanban-cards');
    dropZones.forEach(zone => {
        zone.addEventListener('dragover', handleDragOver);
        zone.addEventListener('drop', handleDrop);
        zone.addEventListener('dragleave', handleDragLeave);
    });

    function handleDragStart(e) {
        // Não arrastar se estiver clicando no checkbox
        if (e.target.classList.contains('client-checkbox') || e.target.closest('.form-check-input')) {
            e.preventDefault();
            return;
        }
        
        draggedElement = this;
        this.classList.add('dragging');
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/html', this.innerHTML);
    }

    function handleDragEnd(e) {
        this.classList.remove('dragging');
    }

    function handleDragOver(e) {
        if (e.preventDefault) {
            e.preventDefault();
        }
        e.dataTransfer.dropEffect = 'move';
        this.classList.add('drag-over');
        return false;
    }

    function handleDragLeave(e) {
        this.classList.remove('drag-over');
    }

    function handleDrop(e) {
        if (e.stopPropagation) {
            e.stopPropagation();
        }
        e.preventDefault();

        this.classList.remove('drag-over');

        if (draggedElement && draggedElement !== this) {
            const draggedClientId = draggedElement.getAttribute('data-client-id');
            const targetColumn = this.getAttribute('data-column');
            const sourceColumn = draggedElement.closest('.kanban-cards').getAttribute('data-column');

            // Se moveu para uma coluna diferente
            if (targetColumn !== sourceColumn) {
                // Verificar se existem clientes selecionados
                const clientsToMove = [];
                
                if (selectedClients.size > 0 && selectedClients.has(draggedClientId)) {
                    // Mover todos os clientes selecionados
                    selectedClients.forEach(clientId => {
                        const card = document.querySelector(`.client-card[data-client-id="${clientId}"]`);
                        if (card) {
                            this.appendChild(card);
                            clientsToMove.push(clientId);
                        }
                    });
                } else {
                    // Mover apenas o card arrastado
                    this.appendChild(draggedElement);
                    clientsToMove.push(draggedClientId);
                }

                // Fazer requisição para atualizar no backend
                clientsToMove.forEach(clientId => {
                    updateClientStatus(clientId, targetColumn);
                });

                // Se moveu cards selecionados, limpar seleção
                if (selectedClients.size > 0) {
                    clearAllSelections();
                }
            }
        }

        return false;
    }

    function updateClientStatus(clientId, column) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch(`/clients/${clientId}/update-kanban-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                column: column
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('success', data.message);
                updateCounters();
            } else {
                showNotification('error', 'Erro ao mover cliente');
                setTimeout(() => location.reload(), 1500);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            showNotification('error', 'Erro ao mover cliente');
            setTimeout(() => location.reload(), 1500);
        });
    }

    // === SELEÇÃO DE CLIENTES ===
    
    const checkboxes = document.querySelectorAll('.client-checkbox');
    const floatingBar = document.getElementById('floating-action-bar');
    const selectedCountBadge = document.getElementById('selected-count');
    const selectedText = document.getElementById('selected-text');
    const closeBarBtn = document.getElementById('close-action-bar');

    // Adicionar listener para cada checkbox
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function(e) {
            e.stopPropagation();
            const clientId = this.getAttribute('data-client-id');
            const card = this.closest('.client-card');
            
            if (this.checked) {
                selectedClients.add(clientId);
                card.classList.add('selected');
            } else {
                selectedClients.delete(clientId);
                card.classList.remove('selected');
            }
            
            updateFloatingBar();
        });
    });

    // Fechar barra
    closeBarBtn.addEventListener('click', function() {
        clearAllSelections();
    });

    function clearAllSelections() {
        selectedClients.clear();
        checkboxes.forEach(cb => {
            cb.checked = false;
            cb.closest('.client-card').classList.remove('selected');
        });
        updateFloatingBar();
    }

    function updateFloatingBar() {
        const count = selectedClients.size;
        
        if (count > 0) {
            floatingBar.style.display = 'block';
            selectedCountBadge.textContent = count;
            selectedText.textContent = count === 1 ? 'SELECIONADO' : 'SELECIONADOS';
        } else {
            floatingBar.style.display = 'none';
        }
    }

    // === AÇÕES DA BARRA ===
    
    document.getElementById('view-client').addEventListener('click', function() {
        if (selectedClients.size === 1) {
            const clientId = Array.from(selectedClients)[0];
            window.location.href = `/clients/${clientId}`;
        } else {
            showNotification('error', 'Selecione apenas 1 cliente para visualizar');
        }
    });

    document.getElementById('edit-client').addEventListener('click', function() {
        if (selectedClients.size === 1) {
            const clientId = Array.from(selectedClients)[0];
            window.location.href = `/clients/${clientId}/edit`;
        } else {
            showNotification('error', 'Selecione apenas 1 cliente para editar');
        }
    });

    document.getElementById('delete-clients').addEventListener('click', function() {
        if (selectedClients.size === 0) {
            showNotification('error', 'Selecione pelo menos 1 cliente');
            return;
        }

        const count = selectedClients.size;
        const confirmMessage = count === 1 
            ? 'Tem certeza que deseja excluir este cliente?' 
            : `Tem certeza que deseja excluir ${count} clientes?`;

        if (confirm(confirmMessage)) {
            deleteSelectedClients();
        }
    });

    function deleteSelectedClients() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const clientIds = Array.from(selectedClients);

        // Deletar cada cliente
        let deletedCount = 0;
        clientIds.forEach(clientId => {
            fetch(`/clients/${clientId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                deletedCount++;
                
                // Remover card da interface
                const card = document.querySelector(`.client-card[data-client-id="${clientId}"]`);
                if (card) {
                    card.remove();
                }
                
                if (deletedCount === clientIds.length) {
                    showNotification('success', `${deletedCount} cliente(s) excluído(s) com sucesso`);
                    clearAllSelections();
                    updateCounters();
                }
            })
            .catch(error => {
                console.error('Erro ao deletar cliente:', error);
                showNotification('error', 'Erro ao deletar alguns clientes');
            });
        });
    }

    function updateCounters() {
        const columns = document.querySelectorAll('.kanban-cards');
        columns.forEach(column => {
            const count = column.querySelectorAll('.client-card').length;
            const columnName = column.getAttribute('data-column');
            const badge = document.querySelector(`.kanban-column[data-column="${columnName}"] .kanban-header .badge`);
            if (badge) {
                badge.textContent = count;
            }
        });
    }

    function showNotification(type, message) {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
        notification.style.top = '20px';
        notification.style.right = '20px';
        notification.style.zIndex = '9999';
        notification.style.minWidth = '300px';
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
});
</script>
@endsection
