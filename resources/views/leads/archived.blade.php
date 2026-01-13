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
    border-left-color: #6c757d;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.lead-card {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 1rem;
    margin-bottom: 1rem;
    opacity: 0.8;
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
                <div class="pipeline-item" onclick="window.location='{{ route('leads.without-pipeline') }}'">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-circle me-2 text-danger" style="font-size: 0.6rem;"></i>
                        <div class="flex-grow-1">
                            <div class="pipeline-name">Leads sem Pipeline</div>
                            <div class="pipeline-meta">Leads não categorizados</div>
                        </div>
                    </div>
                </div>

                <div class="pipeline-item active">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-archive me-2 text-secondary" style="font-size: 0.6rem;"></i>
                        <div class="flex-grow-1">
                            <div class="pipeline-name">Arquivados</div>
                            <div class="pipeline-meta">Leads arquivados <span class="badge bg-secondary">{{ $leads->count() }}</span></div>
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
                            <i class="fas fa-archive text-secondary"></i>
                            Leads Arquivados
                        </h3>
                        <p class="text-muted mb-0">Leads que foram arquivados e removidos dos pipelines</p>
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
                        <h5 class="text-muted">Nenhum lead arquivado</h5>
                        <p class="text-muted">Não há leads arquivados no momento.</p>
                    </div>
                @else
                    <div class="row">
                        @foreach($leads as $lead)
                            <div class="col-md-6 mb-3">
                                <div class="lead-card">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h5 class="mb-0">{{ $lead->name }}</h5>
                                            <span class="badge bg-secondary">Arquivado</span>
                                        </div>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-link text-secondary p-0" type="button" 
                                                    data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <form action="{{ route('leads.restore', $lead) }}" method="POST" 
                                                          onsubmit="return confirm('Tem certeza que deseja restaurar este lead?');">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item text-success">
                                                            <i class="fas fa-undo"></i> Restaurar
                                                        </button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('leads.show', $lead) }}">
                                                        <i class="fas fa-eye"></i> Ver detalhes
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="{{ route('leads.destroy', $lead) }}" method="POST" 
                                                          onsubmit="return confirm('Tem certeza que deseja remover permanentemente este lead?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="fas fa-trash"></i> Excluir Permanentemente
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
                                    @if($lead->pipeline)
                                        <p class="mb-1 small">
                                            <i class="fas fa-sitemap"></i> 
                                            Pipeline anterior: <strong>{{ $lead->pipeline->name }}</strong>
                                            @if($lead->stage)
                                                - {{ $lead->stage->name }}
                                            @endif
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
                                    <small class="text-muted">Arquivado {{ $lead->updated_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
