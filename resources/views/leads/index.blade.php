@extends('layouts.app')

@section('content')
<style>
.sidebar-pipelines {
    background: #f8f9fa;
    border-right: 1px solid #dee2e6;
    min-height: calc(100vh - 56px);
    padding: 1rem;
}

.pipeline-item {
    padding: 0.75rem;
    margin-bottom: 0.5rem;
    border-radius: 0.375rem;
    cursor: pointer;
    transition: all 0.2s;
    border-left: 4px solid transparent;
}

.pipeline-item:hover {
    background: #e9ecef;
}

.pipeline-item.active {
    background: white;
    border-left-color: var(--pipeline-color);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.pipeline-item .pipeline-name {
    font-weight: 500;
    margin-bottom: 0.25rem;
}

.pipeline-item .pipeline-meta {
    font-size: 0.875rem;
    color: #6c757d;
}

.stages-container {
    display: flex;
    overflow-x: auto;
    gap: 1rem;
    padding-bottom: 1rem;
}

.stage-column {
    background: #f8f9fa;
    border-radius: 0.375rem;
    padding: 1rem;
    min-height: 500px;
    min-width: 300px;
    flex-shrink: 0;
}

.stage-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #dee2e6;
}

.lead-card {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 0.75rem;
    margin-bottom: 0.75rem;
    cursor: move;
    transition: all 0.2s;
}

.lead-card:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.lead-card-content {
    cursor: pointer;
}

.lead-card-content:hover {
    opacity: 0.9;
}

.lead-card.dragging {
    opacity: 0.5;
}

.stage-column.drag-over {
    background: #e7f3ff;
    border: 2px dashed #0d6efd;
}

.empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: #6c757d;
}

.no-pipeline-state {
    text-align: center;
    padding: 5rem 2rem;
}
</style>

<div class="container-fluid p-0">
    <div class="row g-0">
        <!-- Sidebar com pipelines -->
        <div class="col-md-2 sidebar-pipelines">
            <div class="mb-3">
                <h6 class="text-uppercase text-muted mb-3" style="font-size: 0.75rem;">
                    <i class="fas fa-sitemap"></i> Pipelines
                </h6>
                
                @if($pipelines->isEmpty())
                    <p class="text-muted small">Nenhum pipeline criado</p>
                @else
                    @foreach($pipelines as $pipeline)
                        <div class="pipeline-item {{ $selectedPipeline && $selectedPipeline->id == $pipeline->id ? 'active' : '' }}" 
                             style="--pipeline-color: {{ $pipeline->color }}"
                             onclick="window.location='{{ route('leads.index', ['pipeline_id' => $pipeline->id]) }}'">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-circle me-2" style="color: {{ $pipeline->color }}; font-size: 0.6rem;"></i>
                                <div class="flex-grow-1">
                                    <div class="pipeline-name">{{ $pipeline->name }}</div>
                                    <div class="pipeline-meta">{{ $pipeline->stages->count() }} etapas</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif

                <button type="button" class="btn btn-primary btn-sm w-100 mt-3" onclick="window.location='{{ route('pipelines.create') }}'">
                    <i class="fas fa-plus"></i> Criar Pipeline
                </button>
            </div>

            <hr>

            <div>
                <div class="pipeline-item" onclick="window.location='{{ route('leads.without-pipeline') }}'">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-circle me-2 text-danger" style="font-size: 0.6rem;"></i>
                        <div class="flex-grow-1">
                            <div class="pipeline-name">Leads sem Pipeline</div>
                            <div class="pipeline-meta">Leads não categorizados <span class="badge bg-danger">{{ $withoutPipelineCount }}</span></div>
                        </div>
                    </div>
                </div>

                <div class="pipeline-item" onclick="window.location='{{ route('leads.archived') }}'">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-archive me-2 text-secondary" style="font-size: 0.6rem;"></i>
                        <div class="flex-grow-1">
                            <div class="pipeline-name">Arquivados</div>
                            <div class="pipeline-meta">Leads arquivados <span class="badge bg-secondary">{{ $archivedCount }}</span></div>
                        </div>
                    </div>
                </div>
            </div>

            <hr>

            <div class="mt-3">
                <a href="{{ route('pipelines.index') }}" class="btn btn-outline-secondary btn-sm w-100">
                    <i class="fas fa-list"></i> Todos os Pipelines
                </a>
            </div>
        </div>

        <!-- Área principal -->
        <div class="col-md-10">
            <div class="p-4">
                @if($pipelines->isEmpty())
                    <!-- Estado vazio: sem pipelines -->
                    <div class="no-pipeline-state">
                        <i class="fas fa-sitemap fa-4x text-muted mb-4"></i>
                        <h3>Nenhum pipeline encontrado</h3>
                        <p class="text-muted mb-4">Escolha um pipeline na barra lateral para visualizar seus leads organizados por etapas.</p>
                        <button type="button" class="btn btn-primary btn-lg" onclick="window.location='{{ route('pipelines.create') }}'">
                            <i class="fas fa-plus"></i> Criar Primeiro Pipeline
                        </button>
                    </div>
                @elseif(!$selectedPipeline)
                    <!-- Estado: pipelines existem mas nenhum selecionado -->
                    <div class="no-pipeline-state">
                        <i class="fas fa-hand-pointer fa-4x text-muted mb-4"></i>
                        <h3>Selecione um Pipeline</h3>
                        <p class="text-muted">Escolha um pipeline na barra lateral para visualizar seus leads organizados por etapas.</p>
                    </div>
                @else
                    <!-- Cabeçalho do pipeline selecionado -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h3 class="mb-0">
                                <i class="fas fa-circle" style="color: {{ $selectedPipeline->color }}; font-size: 0.8rem;"></i>
                                {{ $selectedPipeline->name }}
                            </h3>
                            <p class="text-muted mb-0">{{ $selectedPipeline->stages->count() }} etapas coloridas</p>
                        </div>
                        <div>
                            <select class="form-select form-select-sm d-inline-block me-2" style="width: auto;">
                                <option>Todos os responsáveis</option>
                                @foreach(\App\Models\User::orderBy('name')->get() as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-sm btn-outline-secondary me-2" onclick="window.location.reload()">
                                <i class="fas fa-sync"></i>
                            </button>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-outline-secondary" title="Editar Pipeline" 
                                        onclick="window.location='{{ route('pipelines.edit', $selectedPipeline) }}'">
                                    <i class="fas fa-cog"></i> Editar Pipeline
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" title="Todos os Pipelines" 
                                        onclick="window.location='{{ route('pipelines.index') }}'">
                                    <i class="fas fa-list"></i> Todos os Pipelines
                                </button>
                            </div>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Kanban com etapas -->
                    <div class="stages-container">
                        @foreach($selectedPipeline->stages as $stage)
                            <div class="stage-column" data-stage-id="{{ $stage->id }}">
                                <div class="stage-header">
                                        <div>
                                            <i class="fas fa-circle" style="color: {{ $stage->color }}; font-size: 0.6rem;"></i>
                                            <strong>{{ $stage->name }}</strong>
                                            @if($stage->type === 'ganho')
                                                <i class="fas fa-trophy text-success ms-1" title="Etapa de ganho"></i>
                                            @elseif($stage->type === 'perdido')
                                                <i class="fas fa-times-circle text-danger ms-1" title="Etapa de perdido"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <button type="button" class="btn btn-sm btn-primary" 
                                                    onclick="openCreateLeadModal({{ $selectedPipeline->id }}, {{ $stage->id }})">
                                                Adicionar
                                            </button>
                                            <span class="badge bg-secondary">{{ $stage->leads->count() }}</span>
                                        </div>
                                    </div>

                                    <div class="leads-container" data-stage-id="{{ $stage->id }}">
                                        @forelse($stage->leads as $lead)
                                            <div class="lead-card" draggable="true" data-lead-id="{{ $lead->id }}">
                                                <div class="d-flex align-items-start">
                                                    <div class="flex-grow-1 lead-card-content" onclick="window.location='{{ route('leads.show', $lead) }}'">
                                                        <h6 class="mb-1">
                                                            <i class="fas fa-user-circle text-primary"></i>
                                                            {{ $lead->name }}
                                                        </h6>
                                                        <p class="mb-1 small">
                                                            <i class="fas fa-phone"></i> {{ $lead->formatted_phone }}
                                                            @if($lead->is_whatsapp)
                                                                <i class="fab fa-whatsapp text-success ms-1"></i>
                                                            @endif
                                                        </p>
                                                        @if($lead->email)
                                                            <p class="mb-1 small text-muted">
                                                                <i class="fas fa-envelope"></i> {{ $lead->email }}
                                                            </p>
                                                        @endif
                                                    </div>
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-link text-secondary p-0" type="button" 
                                                                data-bs-toggle="dropdown" onclick="event.stopPropagation();">
                                                            <i class="fas fa-ellipsis-v"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                            <li>
                                                                <a class="dropdown-item" href="#" onclick="openChangeStageModal({{ $lead->id }}, {{ $selectedPipeline->id }}, {{ $lead->pipeline_stage_id }}); return false;">
                                                                    <i class="fas fa-exchange-alt"></i> Alterar etapa
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item" href="#" onclick="openChangePipelineModal({{ $lead->id }}, {{ $selectedPipeline->id }}); return false;">
                                                                    <i class="fas fa-sitemap"></i> Alterar pipeline
                                                                </a>
                                                            </li>
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li>
                                                                <form action="{{ route('leads.destroy', $lead) }}" method="POST" 
                                                                      onsubmit="return confirm('Tem certeza que deseja remover este lead?');">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="dropdown-item text-danger">
                                                                        <i class="fas fa-trash"></i> Remover
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="empty-state">
                                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                                <p class="mb-0">Arraste leads aqui</p>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal para criar novo lead -->
<div class="modal fade" id="createLeadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('leads.store') }}" id="createLeadForm">
                @csrf
                <input type="hidden" name="pipeline_id" id="lead_pipeline_id">
                <input type="hidden" name="pipeline_stage_id" id="lead_stage_id">

                <div class="modal-header">
                    <h5 class="modal-title">Novo Lead</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="lead_name" class="form-label">Nome *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="lead_name" name="name" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="lead_phone" class="form-label">Telefone *</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                   id="lead_phone" name="phone" required>
                            <div class="form-check mt-1">
                                <input class="form-check-input" type="checkbox" id="lead_is_whatsapp" name="is_whatsapp">
                                <label class="form-check-label small" for="lead_is_whatsapp">
                                    <i class="fab fa-whatsapp text-success"></i> Este número é WhatsApp
                                </label>
                            </div>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="lead_email" class="form-label">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               id="lead_email" name="email">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="lead_origin" class="form-label">Origem *</label>
                        <select class="form-select @error('origin') is-invalid @enderror" 
                                id="lead_origin" name="origin" required>
                            <option value="">Selecione a origem</option>
                            <option value="campanha">Campanha</option>
                            <option value="email">Email</option>
                            <option value="facebook">Facebook</option>
                            <option value="indicacao">Indicação</option>
                            <option value="instagram">Instagram</option>
                            <option value="whatsapp">Whatsapp</option>
                            <option value="outro">Outro (especificar)</option>
                        </select>
                        @error('origin')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3" id="origin_other_div" style="display: none;">
                        <label for="lead_origin_other" class="form-label">Outro (especificar)</label>
                        <input type="text" class="form-control" id="lead_origin_other" name="origin_other" 
                               placeholder="Especifique a origem">
                    </div>

                    <div class="mb-3">
                        <label for="lead_user_id" class="form-label">Responsável</label>
                        <select class="form-select" id="lead_user_id" name="user_id">
                            <option value="">Atribuir automaticamente</option>
                            @foreach(\App\Models\User::orderBy('name')->get() as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="lead_notes" class="form-label">Observações</label>
                        <textarea class="form-control" id="lead_notes" name="notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Criar Lead</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para alterar etapa -->
<div class="modal fade" id="changeStageModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="changeStageForm">
                @csrf
                <input type="hidden" id="change_stage_lead_id">
                
                <div class="modal-header">
                    <h5 class="modal-title">Alterar Etapa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="change_stage_select" class="form-label">Selecione a nova etapa</label>
                        <select class="form-select" id="change_stage_select" required>
                            <option value="">Selecione uma etapa</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Alterar Etapa</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para alterar pipeline -->
<div class="modal fade" id="changePipelineModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="changePipelineForm">
                @csrf
                <input type="hidden" id="change_pipeline_lead_id">
                
                <div class="modal-header">
                    <h5 class="modal-title">Alterar Pipeline</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="change_pipeline_select" class="form-label">Selecione o novo pipeline</label>
                        <select class="form-select" id="change_pipeline_select" required>
                            <option value="">Selecione um pipeline</option>
                            @foreach($pipelines as $pipeline)
                                <option value="{{ $pipeline->id }}" data-stages='@json($pipeline->stages)'>{{ $pipeline->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3" id="change_pipeline_stage_div" style="display: none;">
                        <label for="change_pipeline_stage_select" class="form-label">Selecione a etapa inicial</label>
                        <select class="form-select" id="change_pipeline_stage_select" required>
                            <option value="">Selecione uma etapa</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Alterar Pipeline</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Mostrar/ocultar campo "Outro" da origem
document.getElementById('lead_origin')?.addEventListener('change', function() {
    const otherDiv = document.getElementById('origin_other_div');
    if (this.value === 'outro') {
        otherDiv.style.display = 'block';
    } else {
        otherDiv.style.display = 'none';
    }
});

// Abrir modal de criação de lead
function openCreateLeadModal(pipelineId, stageId) {
    document.getElementById('lead_pipeline_id').value = pipelineId;
    document.getElementById('lead_stage_id').value = stageId;
    new bootstrap.Modal(document.getElementById('createLeadModal')).show();
}

// Abrir modal de alteração de etapa
function openChangeStageModal(leadId, pipelineId, currentStageId) {
    document.getElementById('change_stage_lead_id').value = leadId;
    
    // Carregar as etapas do pipeline atual
    const stageSelect = document.getElementById('change_stage_select');
    stageSelect.innerHTML = '<option value="">Selecione uma etapa</option>';
    
    @if($selectedPipeline)
        const stages = @json($selectedPipeline->stages);
        stages.forEach(stage => {
            const option = document.createElement('option');
            option.value = stage.id;
            option.textContent = stage.name;
            if (stage.id === currentStageId) {
                option.selected = true;
            }
            stageSelect.appendChild(option);
        });
    @endif
    
    new bootstrap.Modal(document.getElementById('changeStageModal')).show();
}

// Abrir modal de alteração de pipeline
function openChangePipelineModal(leadId, currentPipelineId) {
    document.getElementById('change_pipeline_lead_id').value = leadId;
    document.getElementById('change_pipeline_select').value = '';
    document.getElementById('change_pipeline_stage_div').style.display = 'none';
    
    new bootstrap.Modal(document.getElementById('changePipelineModal')).show();
}

// Atualizar etapas quando pipeline é selecionado
document.getElementById('change_pipeline_select')?.addEventListener('change', function() {
    const stageDiv = document.getElementById('change_pipeline_stage_div');
    const stageSelect = document.getElementById('change_pipeline_stage_select');
    
    if (this.value) {
        const selectedOption = this.options[this.selectedIndex];
        const stages = JSON.parse(selectedOption.dataset.stages || '[]');
        
        stageSelect.innerHTML = '<option value="">Selecione uma etapa</option>';
        stages.forEach(stage => {
            const option = document.createElement('option');
            option.value = stage.id;
            option.textContent = stage.name;
            stageSelect.appendChild(option);
        });
        
        stageDiv.style.display = 'block';
    } else {
        stageDiv.style.display = 'none';
    }
});

// Submeter formulário de alteração de etapa
document.getElementById('changeStageForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const leadId = document.getElementById('change_stage_lead_id').value;
    const stageId = document.getElementById('change_stage_select').value;
    
    fetch(`/leads/${leadId}/move-stage`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ stage_id: stageId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('changeStageModal')).hide();
            location.reload();
        } else {
            alert('Erro ao alterar etapa: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao alterar etapa');
    });
});

// Submeter formulário de alteração de pipeline
document.getElementById('changePipelineForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const leadId = document.getElementById('change_pipeline_lead_id').value;
    const pipelineId = document.getElementById('change_pipeline_select').value;
    const stageId = document.getElementById('change_pipeline_stage_select').value;
    
    fetch(`/leads/${leadId}/change-pipeline`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ 
            pipeline_id: pipelineId,
            pipeline_stage_id: stageId 
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('changePipelineModal')).hide();
            window.location.href = `/leads?pipeline_id=${pipelineId}`;
        } else {
            alert('Erro ao alterar pipeline: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao alterar pipeline');
    });
});

// Reabrir modal se houver erros de validação
@if($errors->any())
    document.addEventListener('DOMContentLoaded', function() {
        @if($selectedPipeline)
            openCreateLeadModal({{ $selectedPipeline->id }}, {{ $selectedPipeline->stages->first()->id ?? 0 }});
        @endif
    });
@endif

// Drag and Drop functionality
document.addEventListener('DOMContentLoaded', function() {
    let draggedElement = null;

    // Adicionar eventos de drag aos cards de lead
    document.querySelectorAll('.lead-card').forEach(card => {
        card.addEventListener('dragstart', function(e) {
            draggedElement = this;
            this.classList.add('dragging');
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/html', this.innerHTML);
        });

        card.addEventListener('dragend', function(e) {
            this.classList.remove('dragging');
            document.querySelectorAll('.stage-column').forEach(col => {
                col.classList.remove('drag-over');
            });
        });
    });

    // Adicionar eventos aos containers de leads
    document.querySelectorAll('.leads-container').forEach(container => {
        container.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            this.closest('.stage-column').classList.add('drag-over');
        });

        container.addEventListener('dragleave', function(e) {
            // Verificar se o mouse está realmente saindo do container
            const rect = this.getBoundingClientRect();
            const x = e.clientX;
            const y = e.clientY;
            
            if (x < rect.left || x >= rect.right || y < rect.top || y >= rect.bottom) {
                this.closest('.stage-column').classList.remove('drag-over');
            }
        });

        container.addEventListener('drop', function(e) {
            e.preventDefault();
            this.closest('.stage-column').classList.remove('drag-over');

            if (draggedElement) {
                const leadId = draggedElement.dataset.leadId;
                const newStageId = this.dataset.stageId;
                
                // Obter o container de origem antes de mover
                const oldLeadsContainer = draggedElement.parentElement;
                
                // Remover empty-state se existir no destino
                const emptyState = this.querySelector('.empty-state');
                if (emptyState) {
                    emptyState.remove();
                }
                
                // Mover visualmente o card
                this.appendChild(draggedElement);
                
                // Verificar se o container de origem ficou vazio
                if (oldLeadsContainer && oldLeadsContainer !== this) {
                    const remainingLeads = oldLeadsContainer.querySelectorAll('.lead-card').length;
                    
                    if (remainingLeads === 0) {
                        // Adicionar empty-state no container de origem
                        const emptyDiv = document.createElement('div');
                        emptyDiv.className = 'empty-state';
                        emptyDiv.innerHTML = '<i class="fas fa-inbox fa-2x mb-2"></i><p class="mb-0">Arraste leads aqui</p>';
                        oldLeadsContainer.appendChild(emptyDiv);
                    }
                }

                // Atualizar no servidor
                fetch(`/leads/${leadId}/move-stage`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        stage_id: newStageId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Atualizar contadores
                        updateStageCounts();
                        
                        // Mostrar mensagem de sucesso (opcional)
                        console.log('Lead movido com sucesso!');
                    } else {
                        // Reverter movimento se falhar
                        alert('Erro ao mover lead: ' + data.message);
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao mover lead');
                    location.reload();
                });
            }
        });
    });

    // Função para atualizar contadores de leads em cada etapa
    function updateStageCounts() {
        document.querySelectorAll('.stage-column').forEach(column => {
            const leadsCount = column.querySelectorAll('.lead-card').length;
            const badge = column.querySelector('.stage-header .badge');
            if (badge) {
                badge.textContent = leadsCount;
            }
        });
    }
});
</script>
@endsection
