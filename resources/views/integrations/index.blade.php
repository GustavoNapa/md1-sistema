@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-plug me-2"></i>Integrações</h2>
            </div>

            <!-- Estatísticas Gerais -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4>{{ $stats['zapsign_templates'] }}</h4>
                                    <p class="mb-0">Templates ZapSign</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-file-contract fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4>{{ $stats['zapsign_documents_signed'] }}</h4>
                                    <p class="mb-0">Documentos Assinados</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4>{{ $stats['zapsign_documents_pending'] }}</h4>
                                    <p class="mb-0">Documentos Pendentes</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-clock fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4>{{ $stats['zapsign_documents_total'] }}</h4>
                                    <p class="mb-0">Total de Documentos</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-file-alt fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Integrações Disponíveis -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-signature me-2"></i>ZapSign
                            </h5>
                            <span class="badge {{ $zapsignConfigured ? 'bg-success' : 'bg-warning' }}">
                                {{ $zapsignConfigured ? 'Configurado' : 'Não Configurado' }}
                            </span>
                        </div>
                        <div class="card-body">
                            <p class="card-text">
                                Integração com ZapSign para assinatura digital de documentos e contratos.
                            </p>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success me-2"></i>Criação automática de documentos</li>
                                <li><i class="fas fa-check text-success me-2"></i>Mapeamento de campos personalizados</li>
                                <li><i class="fas fa-check text-success me-2"></i>Assinatura automática</li>
                                <li><i class="fas fa-check text-success me-2"></i>Webhooks para atualizações</li>
                            </ul>
                            <div class="d-grid gap-2">
                                <a href="{{ route('integrations.zapsign') }}" class="btn btn-primary">
                                    <i class="fas fa-cog me-2"></i>Configurar ZapSign
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-plus me-2"></i>Outras Integrações
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="card-text text-muted">
                                Mais integrações serão adicionadas em breve.
                            </p>
                            <ul class="list-unstyled text-muted">
                                <li><i class="fas fa-envelope me-2"></i>E-mail Marketing</li>
                                <li><i class="fas fa-credit-card me-2"></i>Gateways de Pagamento</li>
                                <li><i class="fas fa-chart-bar me-2"></i>Analytics</li>
                                <li><i class="fas fa-comments me-2"></i>WhatsApp Business</li>
                            </ul>
                            <div class="d-grid gap-2">
                                <button class="btn btn-outline-secondary" disabled>
                                    <i class="fas fa-clock me-2"></i>Em Breve
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documentos Recentes -->
            @if($recentDocuments->count() > 0)
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-history me-2"></i>Documentos Recentes
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Documento</th>
                                            <th>Cliente</th>
                                            <th>Template</th>
                                            <th>Status</th>
                                            <th>Data</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentDocuments as $document)
                                        <tr>
                                            <td>
                                                <strong>{{ $document->name }}</strong>
                                            </td>
                                            <td>{{ $document->client->name ?? '-' }}</td>
                                            <td>{{ $document->templateMapping->name ?? '-' }}</td>
                                            <td>
                                                <span class="badge {{ $document->status_badge_class }}">
                                                    {{ $document->status_label }}
                                                </span>
                                            </td>
                                            <td>{{ $document->created_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

