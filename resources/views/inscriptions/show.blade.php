@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Inscrição #{{ $inscription->id }}</h4>
                    <div>
                        <a href="{{ route('inscriptions.edit', $inscription) }}" class="btn btn-sm btn-warning">
                            Editar
                        </a>
                        <a href="{{ route('inscriptions.index') }}" class="btn btn-sm btn-secondary">
                            Voltar
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Informações Básicas -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h5 class="border-bottom pb-2">Informações Básicas</h5>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Cliente:</strong> {{ $inscription->client->name ?? 'N/A' }}</p>
                            <p><strong>Email:</strong> {{ $inscription->client->email ?? 'N/A' }}</p>
                            <p><strong>CPF:</strong> {{ $inscription->client->cpf ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Vendedor:</strong> {{ $inscription->vendor->name ?? 'Não informado' }}</p>
                            <p><strong>Produto:</strong> {{ $inscription->product->name ?? 'N/A' }}</p>
                            @if($inscription->product)
                                <p><strong>Preço do Produto:</strong> 
                                    @if($inscription->product->offer_price && $inscription->product->offer_price < $inscription->product->price)
                                        <span class="text-success">
                                            R$ {{ number_format($inscription->product->offer_price, 2, ',', '.') }}
                                        </span>
                                        <small class="text-muted text-decoration-line-through">
                                            R$ {{ number_format($inscription->product->price, 2, ',', '.') }}
                                        </small>
                                    @else
                                        R$ {{ number_format($inscription->product->price, 2, ',', '.') }}
                                    @endif
                                </p>
                            @endif
                        </div>
                    </div>

                    <!-- Status e Classificação -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h5 class="border-bottom pb-2">Status e Classificação</h5>
                        </div>
                        <div class="col-md-3">
                            <p><strong>Status:</strong> 
                                <span class="badge {{ $inscription->status === 'active' ? 'bg-success' : 
                                    ($inscription->status === 'paused' ? 'bg-warning' : 
                                    ($inscription->status === 'cancelled' ? 'bg-danger' : 'bg-primary')) }}">
                                    {{ \App\Http\Controllers\InscriptionController::getStatusOptions()[$inscription->status] ?? $inscription->status }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-3">
                            <p><strong>Turma:</strong> {{ $inscription->class_group ?? 'Não informado' }}</p>
                        </div>
                        <div class="col-md-3">
                            <p><strong>Classificação:</strong> {{ $inscription->classification ?? 'Não informado' }}</p>
                        </div>
                        <div class="col-md-3">
                            <p><strong>CRMB:</strong> {{ $inscription->crmb_number ?? 'Não informado' }}</p>
                        </div>
                    </div>

                    <!-- Datas -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h5 class="border-bottom pb-2">Datas</h5>
                        </div>
                        <div class="col-md-3">
                            <p><strong>Data de Início:</strong> 
                                {{ $inscription->start_date ? $inscription->start_date->format('d/m/Y') : 'Não informado' }}
                            </p>
                        </div>
                        <div class="col-md-3">
                            <p><strong>Data Fim Original:</strong> 
                                {{ $inscription->original_end_date ? $inscription->original_end_date->format('d/m/Y') : 'Não informado' }}
                            </p>
                        </div>
                        <div class="col-md-3">
                            <p><strong>Data Fim Real:</strong> 
                                {{ $inscription->actual_end_date ? $inscription->actual_end_date->format('d/m/Y') : 'Não informado' }}
                            </p>
                        </div>
                        <div class="col-md-3">
                            <p><strong>Liberação Plataforma:</strong> 
                                {{ $inscription->platform_release_date ? $inscription->platform_release_date->format('d/m/Y') : 'Não informado' }}
                            </p>
                        </div>
                    </div>

                    <!-- Semanas e Pagamento -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h5 class="border-bottom pb-2">Semanas e Pagamento</h5>
                        </div>
                        <div class="col-md-2">
                            <p><strong>Semana Calendário:</strong> {{ $inscription->calendar_week ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-2">
                            <p><strong>Semana Atual:</strong> {{ $inscription->current_week ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-3">
                            <p><strong>Valor Pago:</strong> 
                                {{ $inscription->amount_paid ? 'R$ ' . number_format($inscription->amount_paid, 2, ',', '.') : 'Não informado' }}
                            </p>
                        </div>
                        <div class="col-md-3">
                            <p><strong>Método Pagamento:</strong> 
                                {{ $inscription->payment_method ? \App\Http\Controllers\InscriptionController::getPaymentMethodOptions()[$inscription->payment_method] ?? $inscription->payment_method : 'Não informado' }}
                            </p>
                        </div>
                        <div class="col-md-2">
                            <p><strong>Tem MedBoss:</strong> 
                                <span class="badge {{ $inscription->has_medboss ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $inscription->has_medboss ? 'Sim' : 'Não' }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <!-- Observações -->
                    @if($inscription->commercial_notes || $inscription->general_notes)
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h5 class="border-bottom pb-2">Observações</h5>
                        </div>
                        @if($inscription->commercial_notes)
                        <div class="col-md-6">
                            <p><strong>Observações Comerciais:</strong></p>
                            <div class="p-3 bg-light rounded">
                                {{ $inscription->commercial_notes }}
                            </div>
                        </div>
                        @endif
                        @if($inscription->general_notes)
                        <div class="col-md-6">
                            <p><strong>Observações Gerais:</strong></p>
                            <div class="p-3 bg-light rounded">
                                {{ $inscription->general_notes }}
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif

                    <!-- Timestamps -->
                    <div class="row">
                        <div class="col-md-12">
                            <h5 class="border-bottom pb-2">Controle</h5>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Criado em:</strong> {{ $inscription->created_at->format('d/m/Y H:i:s') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Atualizado em:</strong> {{ $inscription->updated_at->format('d/m/Y H:i:s') }}</p>
                        </div>
                    </div>

                    <!-- Registros Relacionados -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h5 class="border-bottom pb-2">Registros Relacionados</h5>
                        </div>
                        <div class="col-md-2">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h6 class="card-title">Preceptores</h6>
                                    <h4 class="text-primary">{{ $inscription->preceptorRecords->count() }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h6 class="card-title">Pagamentos</h6>
                                    <h4 class="text-success">{{ $inscription->payments->count() }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h6 class="card-title">Sessões</h6>
                                    <h4 class="text-info">{{ $inscription->sessions->count() }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h6 class="card-title">Diagnósticos</h6>
                                    <h4 class="text-warning">{{ $inscription->diagnostics->count() }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h6 class="card-title">Conquistas</h6>
                                    <h4 class="text-success">{{ $inscription->achievements->count() }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h6 class="card-title">Follow-ups</h6>
                                    <h4 class="text-primary">{{ $inscription->followUps->count() }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
