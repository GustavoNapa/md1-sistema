@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Histórico de Webhooks</h1>
            </div>

            <!-- Filtros -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Filtros</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('webhook-logs.index') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="">Todos</option>
                                    @foreach(App\Http\Controllers\WebhookLogController::getStatusOptions() as $value => $label)
                                        <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="inscription_id" class="form-label">ID da Inscrição</label>
                                <input type="number" name="inscription_id" id="inscription_id" class="form-control" 
                                       value="{{ request('inscription_id') }}" placeholder="Ex: 123">
                            </div>
                            <div class="col-md-3">
                                <label for="webhook_url" class="form-label">URL do Webhook</label>
                                <input type="text" name="webhook_url" id="webhook_url" class="form-control" 
                                       value="{{ request('webhook_url') }}" placeholder="Ex: webhook.site">
                            </div>
                            <div class="col-md-3">
                                <label for="date_from" class="form-label">Data Inicial</label>
                                <input type="date" name="date_from" id="date_from" class="form-control" 
                                       value="{{ request('date_from') }}">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-3">
                                <label for="date_to" class="form-label">Data Final</label>
                                <input type="date" name="date_to" id="date_to" class="form-control" 
                                       value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-9 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-search"></i> Filtrar
                                </button>
                                <a href="{{ route('webhook-logs.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i> Limpar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabela de Webhooks -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        Logs de Webhook 
                        <span class="badge bg-secondary">{{ $webhookLogs->total() }} registros</span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($webhookLogs->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Inscrição</th>
                                        <th>Cliente</th>
                                        <th>Produto</th>
                                        <th>URL</th>
                                        <th>Tentativa</th>
                                        <th>Status</th>
                                        <th>Resposta</th>
                                        <th>Enviado em</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($webhookLogs as $log)
                                        <tr>
                                            <td>
                                                <a href="{{ route('webhook-logs.show', $log) }}" class="text-decoration-none">
                                                    #{{ $log->id }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="{{ route('inscriptions.show', $log->inscription) }}" class="text-decoration-none">
                                                    #{{ $log->inscription_id }}
                                                </a>
                                            </td>
                                            <td>{{ $log->inscription->client->name ?? 'N/A' }}</td>
                                            <td>{{ $log->inscription->product->name ?? 'N/A' }}</td>
                                            <td>
                                                <span class="text-muted small" title="{{ $log->webhook_url }}">
                                                    {{ Str::limit($log->webhook_url, 30) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $log->attempt_number }}</span>
                                            </td>
                                            <td>
                                                <span class="badge {{ App\Http\Controllers\WebhookLogController::getStatusBadgeClass($log->status) }}">
                                                    {{ App\Http\Controllers\WebhookLogController::getStatusLabel($log->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($log->response_status)
                                                    <span class="badge {{ $log->response_status >= 200 && $log->response_status < 300 ? 'bg-success' : 'bg-danger' }}">
                                                        {{ $log->response_status }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($log->sent_at)
                                                    <span title="{{ $log->sent_at->format('d/m/Y H:i:s') }}">
                                                        {{ $log->sent_at->diffForHumans() }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">Não enviado</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="{{ route('webhook-logs.show', $log) }}" 
                                                       class="btn btn-outline-primary" title="Ver detalhes">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if($log->status === 'failed')
                                                        <form method="POST" action="{{ route('webhook-logs.resend', $log) }}" 
                                                              class="d-inline" onsubmit="return confirm('Tem certeza que deseja reenviar este webhook?')">
                                                            @csrf
                                                            <button type="submit" class="btn btn-outline-warning" title="Reenviar">
                                                                <i class="fas fa-redo"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Nenhum log de webhook encontrado</h5>
                            <p class="text-muted">Não há registros de webhooks com os filtros aplicados.</p>
                        </div>
                    @endif
                </div>
                @if($webhookLogs->hasPages())
                    <div class="card-footer">
                        {{ $webhookLogs->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.table th {
    border-top: none;
    font-weight: 600;
    font-size: 0.875rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.table td {
    vertical-align: middle;
    font-size: 0.875rem;
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

.badge {
    font-size: 0.75rem;
}
</style>
@endpush

