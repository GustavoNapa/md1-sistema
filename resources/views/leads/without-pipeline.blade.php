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
    border-left-color: #dc3545;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.lead-card {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 1rem;
    margin-bottom: 1rem;
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
                
                @foreach(\App\Models\Pipeline::where('type', 'leads')->get() as $pipeline)
                    <div class="pipeline-item" 
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

                <button type="button" class="btn btn-primary btn-sm w-100 mt-3" onclick="window.location='{{ route('pipelines.create') }}'">
                    <i class="fas fa-plus"></i> Criar Pipeline
                </button>
            </div>

            <hr>

            <div>
                <div class="pipeline-item active">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-circle me-2 text-danger" style="font-size: 0.6rem;"></i>
                        <div class="flex-grow-1">
                            <div class="pipeline-name">Leads sem Pipeline</div>
                            <div class="pipeline-meta">Leads não categorizados <span class="badge bg-danger">{{ $leads->count() }}</span></div>
                        </div>
                    </div>
                </div>

                <div class="pipeline-item" onclick="window.location='{{ route('leads.archived') }}'">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-archive me-2 text-secondary" style="font-size: 0.6rem;"></i>
                        <div class="flex-grow-1">
                            <div class="pipeline-name">Arquivados</div>
                            <div class="pipeline-meta">Leads arquivados</div>
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
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h3 class="mb-0">
                            <i class="fas fa-exclamation-triangle text-danger"></i>
                            Leads sem Pipeline
                        </h3>
                        <p class="text-muted mb-0">Leads que não foram categorizados em nenhum pipeline</p>
                    </div>
                    <div>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createLeadWithoutPipelineModal">
                            <i class="fas fa-plus"></i> Novo Lead
                        </button>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($leads->isEmpty())
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Nenhum lead sem pipeline</h5>
                        <p class="text-muted">Todos os leads estão categorizados em pipelines.</p>
                    </div>
                @else
                    <div class="row">
                        @foreach($leads as $lead)
                            <div class="col-md-6 mb-3">
                                <div class="lead-card">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h5 class="mb-0">{{ $lead->name }}</h5>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-link text-secondary p-0" type="button" 
                                                    data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item" href="#" onclick="event.preventDefault(); openAssignPipelineModal({{ $lead->id }}, '{{ $lead->name }}')">
                                                        <i class="fas fa-share"></i> Atribuir Pipeline
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('leads.edit', $lead) }}">
                                                        <i class="fas fa-edit"></i> Editar
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="{{ route('leads.archive', $lead) }}" method="POST" 
                                                          onsubmit="return confirm('Tem certeza que deseja arquivar este lead?');">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item text-warning">
                                                            <i class="fas fa-archive"></i> Arquivar
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <p class="mb-1">
                                        <i class="fas fa-phone"></i> {{ $lead->formatted_phone }}
                                        @if($lead->is_whatsapp)
                                            <i class="fab fa-whatsapp text-success"></i>
                                        @endif
                                    </p>
                                    @if($lead->email)
                                        <p class="mb-1">
                                            <i class="fas fa-envelope"></i> {{ $lead->email }}
                                        </p>
                                    @endif
                                    @if($lead->origin)
                                        <p class="mb-1">
                                            <span class="badge bg-secondary">{{ $lead->origin_label }}</span>
                                        </p>
                                    @endif
                                    @if($lead->user)
                                        <p class="mb-1 small text-muted">
                                            <i class="fas fa-user"></i> {{ $lead->user->name }}
                                        </p>
                                    @endif
                                    <small class="text-muted">há {{ $lead->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal para criar novo lead sem pipeline -->
<div class="modal fade" id="createLeadWithoutPipelineModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('leads.store') }}">
                @csrf
                <input type="hidden" name="pipeline_id" value="">

                <div class="modal-header">
                    <h5 class="modal-title">Novo Lead (sem pipeline)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="lead_name" class="form-label">Nome *</label>
                            <input type="text" class="form-control" id="lead_name" name="name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="lead_phone" class="form-label">Telefone *</label>
                            <input type="text" class="form-control" id="lead_phone" name="phone" required>
                            <div class="form-check mt-1">
                                <input class="form-check-input" type="checkbox" id="lead_is_whatsapp" name="is_whatsapp">
                                <label class="form-check-label small" for="lead_is_whatsapp">
                                    <i class="fab fa-whatsapp text-success"></i> Este número é WhatsApp
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="lead_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="lead_email" name="email">
                    </div>

                    <div class="mb-3">
                        <label for="lead_origin" class="form-label">Origem *</label>
                        <select class="form-select" id="lead_origin" name="origin" required>
                            <option value="">Selecione a origem</option>
                            <option value="campanha">Campanha</option>
                            <option value="email">Email</option>
                            <option value="facebook">Facebook</option>
                            <option value="indicacao">Indicação</option>
                            <option value="instagram">Instagram</option>
                            <option value="whatsapp">Whatsapp</option>
                            <option value="outro">Outro (especificar)</option>
                        </select>
                    </div>

                    <div class="mb-3" id="origin_other_div" style="display: none;">
                        <label for="lead_origin_other" class="form-label">Outro (especificar)</label>
                        <input type="text" class="form-control" id="lead_origin_other" name="origin_other">
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

<!-- Modal para atribuir pipeline -->
<div class="modal fade" id="assignPipelineModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="assignPipelineForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Atribuir Pipeline</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Selecione o pipeline para o lead: <strong id="leadNameDisplay"></strong></p>
                    <div class="mb-3">
                        <label for="assign_pipeline_id" class="form-label">Pipeline *</label>
                        <select class="form-select" id="assign_pipeline_id" name="pipeline_id" required>
                            <option value="">Selecione um pipeline</option>
                            @foreach($pipelines as $pipeline)
                                <option value="{{ $pipeline->id }}">{{ $pipeline->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Atribuir Pipeline</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('lead_origin')?.addEventListener('change', function() {
    const otherDiv = document.getElementById('origin_other_div');
    if (this.value === 'outro') {
        otherDiv.style.display = 'block';
    } else {
        otherDiv.style.display = 'none';
    }
});

function openAssignPipelineModal(leadId, leadName) {
    document.getElementById('leadNameDisplay').textContent = leadName;
    document.getElementById('assignPipelineForm').action = `/leads/${leadId}/assign-pipeline`;
    new bootstrap.Modal(document.getElementById('assignPipelineModal')).show();
}
</script>
@endsection
