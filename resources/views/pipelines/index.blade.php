@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="page-title mb-0">Pipelines</h4>
                    <a href="{{ route('pipelines.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Novo Pipeline
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($pipelines->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-sitemap fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Nenhum pipeline encontrado</h5>
                            <p class="text-muted">Crie seu primeiro pipeline para começar a gerenciar seus leads.</p>
                            <a href="{{ route('pipelines.create') }}" class="btn btn-primary mt-3">
                                <i class="fas fa-plus"></i> Criar Primeiro Pipeline
                            </a>
                        </div>
                    @else
                        <div class="row">
                            @foreach($pipelines as $pipeline)
                                <div class="col-md-6 mb-4">
                                    <div class="card" style="border-left: 4px solid {{ $pipeline->color }};">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h5 class="card-title mb-1">
                                                        <i class="fas fa-circle" style="color: {{ $pipeline->color }}; font-size: 0.8rem;"></i>
                                                        {{ $pipeline->name }}
                                                    </h5>
                                                    <p class="text-muted mb-2">
                                                        <small>Pipeline de {{ ucfirst($pipeline->type) }}</small>
                                                    </p>
                                                </div>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-link text-secondary" type="button" data-bs-toggle="dropdown">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('leads.index', ['pipeline_id' => $pipeline->id]) }}">
                                                                <i class="fas fa-eye"></i> Visualizar
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('pipelines.edit', $pipeline) }}">
                                                                <i class="fas fa-edit"></i> Editar Pipeline
                                                            </a>
                                                        </li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <form action="{{ route('pipelines.destroy', $pipeline) }}" method="POST" 
                                                                  onsubmit="return confirm('Tem certeza que deseja remover este pipeline? Todos os leads serão arquivados.');">
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

                                            <div class="mt-3">
                                                <p class="mb-2"><strong>{{ $pipeline->stages_count }} etapas coloridas</strong></p>
                                                @if($pipeline->is_default)
                                                    <span class="badge bg-primary">Pipeline padrão</span>
                                                @endif
                                            </div>

                                            <div class="mt-3 d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h3 class="mb-0">{{ $pipeline->leads_count }}</h3>
                                                    <small class="text-muted">Registros</small>
                                                </div>
                                                <div>
                                                    <h3 class="mb-0 text-success">0%</h3>
                                                    <small class="text-muted">Conversão</small>
                                                </div>
                                                <div>
                                                    <h3 class="mb-0">{{ $pipeline->leads_count }}</h3>
                                                    <small class="text-muted">Hoje</small>
                                                </div>
                                            </div>

                                            <div class="mt-3">
                                                <a href="{{ route('leads.index', ['pipeline_id' => $pipeline->id]) }}" class="btn btn-primary w-100">
                                                    <i class="fas fa-th-large"></i> Visualizar
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
