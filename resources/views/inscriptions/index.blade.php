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
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Cliente</th>
                                        <th>Produto</th>
                                        <th>Turma</th>
                                        <th>Status</th>
                                        <th>Vendedor</th>
                                        <th>Valor</th>
                                        <th>Data Início</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($inscriptions as $inscription)
                                        <tr>
                                            <td>
                                                <strong>{{ $inscription->client->name }}</strong><br>
                                                <small class="text-muted">{{ $inscription->client->email }}</small>
                                            </td>
                                            <td>{{ $inscription->product->name ?? '-' }}</td>
                                            <td>{{ $inscription->class_group ?? '-' }}</td>
                                            <td>
                                                <span class="badge {{ $inscription->status_badge_class }}">
                                                    {{ $inscription->status_label }}
                                                </span>
                                            </td>
                                            <td>{{ $inscription->vendor->name ?? '-' }}</td>
                                            <td>{{ $inscription->valor_total }}</td>
                                            <td>{{ $inscription->start_date ? $inscription->start_date->format('d/m/Y') : '-' }}</td>
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

});
</script>
@endsection

