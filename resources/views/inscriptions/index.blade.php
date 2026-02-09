@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="page-title mb-0">Inscrições</h4>
                    <div class="d-flex gap-3 align-items-center">
                        <!-- Seletor de Visualização -->
                        <div class="d-flex align-items-center">
                            <label for="viewMode" class="form-label me-2 mb-0">Visualização:</label>
                            <select id="viewMode" class="form-select form-select-sm" style="width: auto;">
                                <option value="table">Tabela</option>
                                <option value="kanban-status">Kanban por Status</option>
                                <option value="kanban-faixa">Kanban por Faixa de Faturamento</option>
                                <option value="kanban-semana">Kanban por Semana</option>
                                <option value="kanban-fase">Kanban por Fase</option>
                            </select>
                        </div>
                        <a href="{{ route('inscriptions.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nova Inscrição
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Visualização em Tabela (padrão) -->
                    <div id="tableView" class="view-container">
                        <form method="GET" action="{{ route('inscriptions.index') }}" class="row g-2 mb-3">
                            <div class="col-md-3">
                                <input type="text" name="client_name" value="{{ request('client_name') }}" class="form-control" placeholder="Nome do cliente">
                            </div>
                            <div class="col-md-2">
                                <select name="vendor_id" class="form-select">
                                    <option value="">Todos os vendedores</option>
                                    @foreach($vendors ?? [] as $vendor)
                                        <option value="{{ $vendor->id }}" @if(request('vendor_id') == $vendor->id) selected @endif>{{ $vendor->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="product_id" class="form-select">
                                    <option value="">Todos os produtos</option>
                                    @foreach($products ?? [] as $product)
                                        <option value="{{ $product->id }}" @if(request('product_id') == $product->id) selected @endif>{{ $product->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="status" class="form-select">
                                    <option value="">Todos os status</option>
                                    @foreach($statusOptions ?? [] as $key => $label)
                                        <option value="{{ $key }}" @if(request('status') == $key) selected @endif>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="text" name="class_group" value="{{ request('class_group') }}" class="form-control" placeholder="Turma">
                            </div>

                            <div class="col-md-6 mt-2">
                                <div class="input-group">
                                    <span class="input-group-text">De</span>
                                        <input type="text" name="start_date_from" value="{{ old('start_date_from', $displayStartFrom ?? request('start_date_from')) }}" class="form-control date-br" placeholder="dd/mm/yyyy">
                                    <span class="input-group-text">Até</span>
                                        <input type="text" name="start_date_to" value="{{ old('start_date_to', $displayStartTo ?? request('start_date_to')) }}" class="form-control date-br" placeholder="dd/mm/yyyy">
                                </div>
                            </div>

                            <div class="col-md-3 mt-2">
                                <select name="order_by" class="form-select">
                                    <option value="created_at_desc" @if(request('order_by', 'created_at_desc')=='created_at_desc') selected @endif>Criação (mais recentes)</option>
                                    <option value="created_at_asc" @if(request('order_by')=='created_at_asc') selected @endif>Criação (mais antigas)</option>
                                    <option value="date_desc" @if(request('order_by')=='date_desc') selected @endif>Data Início (mais recentes)</option>
                                    <option value="date_asc" @if(request('order_by')=='date_asc') selected @endif>Data Início (mais antigas)</option>
                                    <option value="value_desc" @if(request('order_by')=='value_desc') selected @endif>Valor (maior)</option>
                                    <option value="value_asc" @if(request('order_by')=='value_asc') selected @endif>Valor (menor)</option>
                                    <option value="name_asc" @if(request('order_by')=='name_asc') selected @endif>Nome (A-Z)</option>
                                    <option value="name_desc" @if(request('order_by')=='name_desc') selected @endif>Nome (Z-A)</option>
                                </select>
                            </div>

                            <div class="col-md-2 mt-2">
                                <button type="submit" class="btn btn-outline-primary">Filtrar</button>
                                <a href="{{ route('inscriptions.index') }}" class="btn btn-outline-secondary ms-1">Limpar</a>
                            </div>
                        </form>

                        <form method="POST" action="{{ route('inscriptions.columns') }}" class="mb-3">
                            @csrf
                            <div class="d-flex flex-wrap align-items-center gap-3">
                                <div class="fw-semibold">Colunas visíveis:</div>
                                @foreach($availableColumns as $key => $label)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="{{ $key }}" id="col_{{ $key }}" name="columns[]" @if(in_array($key, $visibleColumns)) checked @endif>
                                        <label class="form-check-label" for="col_{{ $key }}">{{ $label }}</label>
                                    </div>
                                @endforeach
                                <button type="submit" class="btn btn-sm btn-outline-primary">Salvar colunas</button>
                            </div>
                        </form>

                        @php
                            $sortMap = [
                                'client' => ['asc' => 'name_asc', 'desc' => 'name_desc'],
                                'client_email' => null,
                                'product' => ['asc' => 'product_asc', 'desc' => 'product_desc'],
                                'class_group' => ['asc' => 'class_group_asc', 'desc' => 'class_group_desc'],
                                'status' => ['asc' => 'status_asc', 'desc' => 'status_desc'],
                                'vendor' => ['asc' => 'vendor_asc', 'desc' => 'vendor_desc'],
                                'valor_total' => ['asc' => 'value_asc', 'desc' => 'value_desc'],
                                'amount_paid' => ['asc' => 'amount_paid_asc', 'desc' => 'amount_paid_desc'],
                                'payment_method' => null,
                                'start_date' => ['asc' => 'date_asc', 'desc' => 'date_desc'],
                                'calendar_week' => null,
                                'classification' => null,
                                'created_at' => ['asc' => 'created_at_asc', 'desc' => 'created_at_desc'],
                            ];
                            $currentOrder = request('order_by', 'created_at_desc');
                        @endphp
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        @foreach($availableColumns as $key => $label)
                                            @if(in_array($key, $visibleColumns))
                                                @php
                                                    $sortConfig = $sortMap[$key] ?? null;
                                                    $isActive = $sortConfig && in_array($currentOrder, $sortConfig);
                                                    $nextOrder = $sortConfig
                                                        ? (($isActive && $currentOrder === $sortConfig['asc']) ? $sortConfig['desc'] : $sortConfig['asc'])
                                                        : null;
                                                    $icon = 'fa-sort';
                                                    if ($isActive) {
                                                        $icon = str_ends_with($currentOrder, '_asc') ? 'fa-arrow-up' : 'fa-arrow-down';
                                                    }
                                                @endphp
                                                <th>
                                                    @if($sortConfig)
                                                        <a href="{{ request()->fullUrlWithQuery(['order_by' => $nextOrder]) }}" class="text-decoration-none text-reset d-flex align-items-center gap-1">
                                                            <span>{{ $label }}</span>
                                                            <i class="fas {{ $icon }}"></i>
                                                        </a>
                                                    @else
                                                        {{ $label }}
                                                    @endif
                                                </th>
                                            @endif
                                        @endforeach
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($inscriptions as $inscription)
                                        <tr>
                                            @foreach($availableColumns as $key => $label)
                                                @if(in_array($key, $visibleColumns))
                                                    <td>
                                                        @switch($key)
                                                            @case('client')
                                                                <a href="{{ route('clients.show', $inscription->client) }}" class="text-decoration-none" title="Ver perfil do cliente">
                                                                    <strong class="text-primary">{{ $inscription->client->name }}</strong>
                                                                </a>
                                                                <br>
                                                                <small class="text-muted">{{ $inscription->client->email ?? '-' }}</small>
                                                                @break
                                                            @case('client_email')
                                                                <small class="text-muted">{{ $inscription->client->email ?? '-' }}</small>
                                                                @break
                                                            @case('product')
                                                                {{ $inscription->product->name ?? '-' }}
                                                                @break
                                                            @case('class_group')
                                                                {{ $inscription->class_group ?? '-' }}
                                                                @break
                                                            @case('status')
                                                                <span class="badge {{ $inscription->status_badge_class }}">{{ $inscription->status_label }}</span>
                                                                @break
                                                            @case('vendor')
                                                                {{ $inscription->vendor->name ?? '-' }}
                                                                @break
                                                            @case('valor_total')
                                                                R$ {{ number_format($inscription->valor_total ?? 0, 2, ',', '.') }}
                                                                @break
                                                            @case('amount_paid')
                                                                R$ {{ number_format($inscription->amount_paid ?? 0, 2, ',', '.') }}
                                                                @break
                                                            @case('payment_method')
                                                                {{ $inscription->payment_method ?? '-' }}
                                                                @break
                                                            @case('start_date')
                                                                {{ $inscription->start_date ? $inscription->start_date->format('d/m/Y') : '-' }}
                                                                @break
                                                            @case('calendar_week')
                                                                {{ $inscription->calendar_week ?? '-' }}
                                                                @break
                                                            @case('classification')
                                                                {{ $inscription->classification ?? '-' }}
                                                                @break
                                                            @case('created_at')
                                                                {{ $inscription->created_at ? $inscription->created_at->format('d/m/Y H:i') : '-' }}
                                                                @break
                                                            @default
                                                                -
                                                        @endswitch
                                                    </td>
                                                @endif
                                            @endforeach
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group" aria-label="Ações da inscrição">
                                                    <a href="{{ route('inscriptions.show', $inscription) }}" 
                                                       class="btn btn-primary" 
                                                       title="Visualizar inscrição"
                                                       data-bs-toggle="tooltip">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('inscriptions.edit', $inscription) }}" 
                                                       class="btn btn-warning" 
                                                       title="Editar inscrição"
                                                       data-bs-toggle="tooltip">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('inscriptions.destroy', $inscription) }}" 
                                                          method="POST" 
                                                          style="display: inline;"
                                                          onsubmit="return confirm('Tem certeza que deseja excluir esta inscrição? Esta ação não pode ser desfeita.')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="btn btn-danger btn-sm" 
                                                                title="Excluir inscrição"
                                                                data-bs-toggle="tooltip">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted">
                                                Nenhuma inscrição cadastrada.
                                                <a href="{{ route('inscriptions.create') }}">Cadastre a primeira inscrição</a>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if(method_exists($inscriptions, 'links'))
                            <div class="d-flex justify-content-center">
                                {{ $inscriptions->links() }}
                            </div>
                        @endif
                    </div>

                    <!-- Visualização Kanban -->
                    <div id="kanbanView" class="view-container" style="display: none;">
                        <div id="kanbanBoard" class="kanban-board">
                            <!-- As colunas serão geradas dinamicamente via JavaScript -->
                        </div>
                    </div>

                    <!-- Loading Spinner -->
                    <div id="loadingSpinner" class="text-center" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Carregando...</span>
                        </div>
                        <p class="mt-2">Carregando dados do Kanban...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.kanban-board {
    display: flex;
    gap: 20px;
    overflow-x: auto;
    padding: 20px 0;
    min-height: 500px;
}

.kanban-column {
    min-width: 300px;
    background-color: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    border: 1px solid #dee2e6;
}

.kanban-column-header {
    background-color: #e9ecef;
    padding: 10px 15px;
    border-radius: 6px;
    margin-bottom: 15px;
    font-weight: 600;
    text-align: center;
    border: 1px solid #ced4da;
}

.kanban-card {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    padding: 12px;
    margin-bottom: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    cursor: pointer;
    transition: all 0.2s ease;
}

.kanban-card:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    transform: translateY(-2px);
}

.kanban-card-title {
    font-weight: 600;
    margin-bottom: 8px;
    color: #495057;
}

.kanban-card-info {
    font-size: 0.875rem;
    color: #6c757d;
    margin-bottom: 4px;
}

.kanban-card-actions {
    margin-top: 10px;
    padding-top: 10px;
    border-top: 1px solid #e9ecef;
}

.view-container {
    transition: opacity 0.3s ease;
}

.gap-3 {
    gap: 1rem !important;
}
</style>

@endsection

@section('scripts')
    <!-- flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <!-- flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <!-- flatpickr locale PT (Português/Brasil) -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/pt.js"></script>
    <!-- Inputmask for date typing mask -->
    <script src="https://cdn.jsdelivr.net/npm/inputmask@5.0.8/dist/inputmask.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const viewModeSelect = document.getElementById('viewMode');
    const tableView = document.getElementById('tableView');
    const kanbanView = document.getElementById('kanbanView');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const kanbanBoard = document.getElementById('kanbanBoard');

    viewModeSelect.addEventListener('change', function() {
        const selectedMode = this.value;
        
        if (selectedMode === 'table') {
            showTableView();
        } else {
            showKanbanView(selectedMode);
        }
    });

    function showTableView() {
        tableView.style.display = 'block';
        kanbanView.style.display = 'none';
        loadingSpinner.style.display = 'none';
    }

    function showKanbanView(mode) {
        tableView.style.display = 'none';
        kanbanView.style.display = 'block';
        loadingSpinner.style.display = 'block';

        // Mapear modo para group_by
        let groupBy = '';
        switch(mode) {
            case 'kanban-status':
                groupBy = 'status';
                break;
            case 'kanban-faixa':
                groupBy = 'faixa_faturamento';
                break;
            case 'kanban-semana':
                groupBy = 'calendar_week';
                break;
            case 'kanban-fase':
                groupBy = 'classification';
                break;
        }

        // Buscar dados via API
        fetch(`/api/inscriptions/kanban?group_by=${groupBy}`)
            .then(response => response.json())
            .then(data => {
                loadingSpinner.style.display = 'none';
                generateKanbanBoard(data.grouped);
            })
            .catch(error => {
                console.error('Erro ao carregar dados do Kanban:', error);
                loadingSpinner.style.display = 'none';
                kanbanBoard.innerHTML = '<div class="alert alert-danger">Erro ao carregar dados do Kanban</div>';
            });
    }

    function generateKanbanBoard(grouped) {
        kanbanBoard.innerHTML = '';
        
        // Criar colunas para cada grupo
        Object.entries(grouped).forEach(([key, group]) => {
            const column = createColumn(key, group.label);
            kanbanBoard.appendChild(column);

            // Adicionar cards das inscrições
            group.items.forEach(inscription => {
                const card = createInscriptionCard(inscription);
                column.appendChild(card);
                updateColumnCount(column); // Mover para depois de adicionar o card
            });
        });
    }

    function createColumn(key, title) {
        const column = document.createElement('div');
        column.className = 'kanban-column';
        column.setAttribute('data-column', key);
        
        const header = document.createElement('div');
        header.className = 'kanban-column-header';
        header.innerHTML = `
            <div>${title}</div>
            <small class="text-muted">0 itens</small>
        `;
        
        column.appendChild(header);
        return column;
    }

    function createInscriptionCard(inscription) {
        const card = document.createElement('div');
        card.className = 'kanban-card';
        card.setAttribute('data-inscription-id', inscription.id);
        
        card.innerHTML = `
            <div class="kanban-card-title">${inscription.client.name}</div>
            <div class="kanban-card-info">
                <strong>Produto:</strong> ${inscription.product.name || '-'}
            </div>
            <div class="kanban-card-info">
                <strong>Valor:</strong> ${inscription.formatted_amount || 'R$ 0,00'}
            </div>
            <div class="kanban-card-info">
                <strong>Status:</strong> 
                <span class="badge ${inscription.status_badge_class}">${inscription.status_label}</span>
            </div>
            ${inscription.class_group ? `<div class="kanban-card-info"><strong>Turma:</strong> ${inscription.class_group}</div>` : ''}
            ${inscription.vendor.name ? `<div class="kanban-card-info"><strong>Vendedor:</strong> ${inscription.vendor.name}</div>` : ''}
            <div class="kanban-card-actions">
                <a href="/inscriptions/${inscription.id}" class="btn btn-sm btn-primary">
                    <i class="fas fa-eye"></i> Ver
                </a>
                <a href="/inscriptions/${inscription.id}/edit" class="btn btn-sm btn-warning">
                    <i class="fas fa-edit"></i> Editar
                </a>
            </div>
        `;
        
        return card;
    }

    function updateColumnCount(column) {
        const cards = column.querySelectorAll('.kanban-card');
        const countElement = column.querySelector('.kanban-column-header small');
        if (countElement) {
            countElement.textContent = `${cards.length} ${cards.length === 1 ? 'item' : 'itens'}`;
        }
    }

    // Inicializar flatpickr nos campos de data com formato brasileiro
    if (window.flatpickr) {
        flatpickr('.date-br', {
            dateFormat: 'd/m/Y',
            allowInput: true,
            locale: 'pt'
        });
    }
    // Inicializar máscara de entrada para dd/mm/YYYY
    if (window.Inputmask) {
        var elements = document.querySelectorAll('.date-br');
        Inputmask({'mask': '99/99/9999', 'placeholder': 'dd/mm/aaaa', 'clearIncomplete': false}).mask(elements);
    }

});
</script>
@endsection

