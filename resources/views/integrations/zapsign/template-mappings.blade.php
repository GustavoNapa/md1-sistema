@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2><i class="fas fa-file-contract me-2"></i>Mapeamento de Templates</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('integrations.index') }}">Integrações</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('integrations.zapsign') }}">ZapSign</a></li>
                            <li class="breadcrumb-item active">Templates</li>
                        </ol>
                    </nav>
                </div>
                <a href="{{ route('integrations.zapsign.template-mappings.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Novo Mapeamento
                </a>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Templates Configurados</h5>
                </div>
                <div class="card-body">
                    @if($mappings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>Template ZapSign</th>
                                        <th>Campos Mapeados</th>
                                        <th>Assinatura Automática</th>
                                        <th>Status</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($mappings as $mapping)
                                    <tr>
                                        <td>
                                            <strong>{{ $mapping->name }}</strong>
                                            @if($mapping->description)
                                                <br><small class="text-muted">{{ $mapping->description }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <code>{{ $mapping->zapsign_template_id }}</code>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ count($mapping->field_mappings) }} campo(s)
                                            </span>
                                        </td>
                                        <td>
                                            @if($mapping->auto_sign)
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check me-1"></i>Ativo
                                                </span>
                                                <br><small class="text-muted">{{ $mapping->signer_email }}</small>
                                            @else
                                                <span class="badge bg-secondary">
                                                    <i class="fas fa-times me-1"></i>Inativo
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($mapping->is_active)
                                                <span class="badge bg-success">Ativo</span>
                                            @else
                                                <span class="badge bg-danger">Inativo</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('integrations.zapsign.template-mappings.edit', $mapping) }}" 
                                                   class="btn btn-outline-primary"
                                                   title="Editar mapeamento"
                                                   data-bs-toggle="tooltip">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-outline-info"
                                                        title="Ver detalhes"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#detailsModal{{ $mapping->id }}">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <form action="{{ route('integrations.zapsign.template-mappings.destroy', $mapping) }}" 
                                                      method="POST" 
                                                      style="display: inline;"
                                                      onsubmit="return confirm('Tem certeza que deseja excluir este mapeamento?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-outline-danger"
                                                            title="Excluir mapeamento"
                                                            data-bs-toggle="tooltip">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginação -->
                        @if($mappings->hasPages())
                            <div class="d-flex justify-content-center">
                                {{ $mappings->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-file-contract fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Nenhum mapeamento configurado</h5>
                            <p class="text-muted">
                                Configure seu primeiro mapeamento de template para começar a usar a integração ZapSign.
                            </p>
                            <a href="{{ route('integrations.zapsign.template-mappings.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Criar Primeiro Mapeamento
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modais de Detalhes -->
@foreach($mappings as $mapping)
<div class="modal fade" id="detailsModal{{ $mapping->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalhes do Mapeamento: {{ $mapping->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Informações Gerais</h6>
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Nome:</strong></td>
                                <td>{{ $mapping->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Template ID:</strong></td>
                                <td><code>{{ $mapping->zapsign_template_id }}</code></td>
                            </tr>
                            <tr>
                                <td><strong>Descrição:</strong></td>
                                <td>{{ $mapping->description ?: '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td>
                                    <span class="badge {{ $mapping->is_active ? 'bg-success' : 'bg-danger' }}">
                                        {{ $mapping->is_active ? 'Ativo' : 'Inativo' }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Assinatura Automática</h6>
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Ativo:</strong></td>
                                <td>
                                    <span class="badge {{ $mapping->auto_sign ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $mapping->auto_sign ? 'Sim' : 'Não' }}
                                    </span>
                                </td>
                            </tr>
                            @if($mapping->auto_sign)
                            <tr>
                                <td><strong>Nome:</strong></td>
                                <td>{{ $mapping->signer_name }}</td>
                            </tr>
                            <tr>
                                <td><strong>E-mail:</strong></td>
                                <td>{{ $mapping->signer_email }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>

                <h6>Mapeamento de Campos</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Campo ZapSign</th>
                                <th>Campo do Sistema</th>
                                <th>Tipo</th>
                                <th>Valor Padrão</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($mapping->formatted_field_mappings as $fieldMapping)
                            <tr>
                                <td><code>{{ $fieldMapping['zapsign_field'] }}</code></td>
                                <td>{{ $fieldMapping['system_field'] }}</td>
                                <td>
                                    <span class="badge bg-secondary">{{ $fieldMapping['field_type'] }}</span>
                                </td>
                                <td>{{ $fieldMapping['default_value'] ?: '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <a href="{{ route('integrations.zapsign.template-mappings.edit', $mapping) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-2"></i>Editar
                </a>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection

