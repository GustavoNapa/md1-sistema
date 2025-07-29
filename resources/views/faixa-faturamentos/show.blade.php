@extends('layouts.app')

@section('title', 'Faixa de Faturamento: ' . $faixaFaturamento->label)

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ $faixaFaturamento->label }}</h4>
                    <div class="btn-group">
                        @can('update', $faixaFaturamento)
                            <a href="{{ route('faixa-faturamentos.edit', $faixaFaturamento) }}" 
                               class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                        @endcan
                        @can('delete', $faixaFaturamento)
                            <button type="button" class="btn btn-sm btn-danger" 
                                    data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <i class="fas fa-trash"></i> Excluir
                            </button>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Nome da Faixa</h6>
                            <p class="fs-5">{{ $faixaFaturamento->label }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Range de Valores</h6>
                            <p class="fs-5">
                                <span class="badge bg-info fs-6">{{ $faixaFaturamento->range_formatted }}</span>
                            </p>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Valor Mínimo</h6>
                            <p class="fs-5 text-success">{{ $faixaFaturamento->valor_min_formatted }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Valor Máximo</h6>
                            <p class="fs-5 text-success">{{ $faixaFaturamento->valor_max_formatted }}</p>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Criado em</h6>
                            <p>{{ $faixaFaturamento->created_at->format('d/m/Y H:i:s') }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Atualizado em</h6>
                            <p>{{ $faixaFaturamento->updated_at->format('d/m/Y H:i:s') }}</p>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Esta faixa pode ser usada no Kanban para agrupar inscrições por potencial financeiro.
                        Inscrições com valores entre {{ $faixaFaturamento->range_formatted }} serão agrupadas nesta faixa.
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('faixa-faturamentos.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar para Lista
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@can('delete', $faixaFaturamento)
    <!-- Modal de Confirmação -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar Exclusão</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Tem certeza que deseja excluir a faixa <strong>{{ $faixaFaturamento->label }}</strong>?
                    <br><small class="text-muted">Esta ação não pode ser desfeita.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form method="POST" action="{{ route('faixa-faturamentos.destroy', $faixaFaturamento) }}" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Excluir</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endcan
@endsection

