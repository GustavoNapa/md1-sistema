@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Detalhes do Webhook #{{ $webhookLog->id }}</h1>
                <div>
                    <a href="{{ route('webhook-logs.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                    @if($webhookLog->status === 'failed')
                        <form method="POST" action="{{ route('webhook-logs.resend', $webhookLog) }}" 
                              class="d-inline ms-2" onsubmit="return confirm('Tem certeza que deseja reenviar este webhook?')">
                            @csrf
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-redo"></i> Reenviar
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="row">
                <!-- Informações Gerais -->
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Informações Gerais</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>ID:</strong></div>
                                <div class="col-sm-8">#{{ $webhookLog->id }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Status:</strong></div>
                                <div class="col-sm-8">
                                    <span class="badge {{ App\Http\Controllers\WebhookLogController::getStatusBadgeClass($webhookLog->status) }}">
                                        {{ App\Http\Controllers\WebhookLogController::getStatusLabel($webhookLog->status) }}
                                    </span>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Tentativa:</strong></div>
                                <div class="col-sm-8">
                                    <span class="badge bg-info">{{ $webhookLog->attempt_number }}</span>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Evento:</strong></div>
                                <div class="col-sm-8">{{ $webhookLog->event_type }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>URL:</strong></div>
                                <div class="col-sm-8">
                                    <code class="small">{{ $webhookLog->webhook_url }}</code>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Criado em:</strong></div>
                                <div class="col-sm-8">{{ $webhookLog->created_at->format('d/m/Y H:i:s') }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Enviado em:</strong></div>
                                <div class="col-sm-8">
                                    @if($webhookLog->sent_at)
                                        {{ $webhookLog->sent_at->format('d/m/Y H:i:s') }}
                                    @else
                                        <span class="text-muted">Não enviado</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informações da Inscrição -->
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Inscrição Relacionada</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>ID:</strong></div>
                                <div class="col-sm-8">
                                    <a href="{{ route('inscriptions.show', $webhookLog->inscription) }}" class="text-decoration-none">
                                        #{{ $webhookLog->inscription_id }}
                                    </a>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Cliente:</strong></div>
                                <div class="col-sm-8">{{ $webhookLog->inscription->client->name ?? 'N/A' }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Produto:</strong></div>
                                <div class="col-sm-8">{{ $webhookLog->inscription->product->name ?? 'N/A' }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Status:</strong></div>
                                <div class="col-sm-8">
                                    <span class="badge {{ $webhookLog->inscription->status_badge_class }}">
                                        {{ $webhookLog->inscription->status_label }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resposta do Webhook -->
            @if($webhookLog->response_status || $webhookLog->response_body)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Resposta do Webhook</h5>
                    </div>
                    <div class="card-body">
                        @if($webhookLog->response_status)
                            <div class="row mb-3">
                                <div class="col-sm-2"><strong>Status HTTP:</strong></div>
                                <div class="col-sm-10">
                                    <span class="badge {{ $webhookLog->response_status >= 200 && $webhookLog->response_status < 300 ? 'bg-success' : 'bg-danger' }}">
                                        {{ $webhookLog->response_status }}
                                    </span>
                                </div>
                            </div>
                        @endif
                        @if($webhookLog->response_body)
                            <div class="row">
                                <div class="col-sm-2"><strong>Corpo da Resposta:</strong></div>
                                <div class="col-sm-10">
                                    <pre class="bg-light p-3 rounded small"><code>{{ $webhookLog->response_body }}</code></pre>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Payload Enviado -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Payload Enviado</h5>
                </div>
                <div class="card-body">
                    <pre class="bg-light p-3 rounded small"><code>{{ json_encode($webhookLog->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
pre {
    max-height: 400px;
    overflow-y: auto;
    font-size: 0.8rem;
    line-height: 1.4;
}

code {
    color: #495057;
}

.card-title {
    color: #495057;
    font-weight: 600;
}

.row.mb-3:last-child {
    margin-bottom: 0 !important;
}
</style>
@endpush

