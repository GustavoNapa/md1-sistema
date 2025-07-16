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
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" id="inscriptionTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="informacoes-tab" data-bs-toggle="tab" data-bs-target="#informacoes" type="button" role="tab">
                                Informações Básicas
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="preceptores-tab" data-bs-toggle="tab" data-bs-target="#preceptores" type="button" role="tab">
                                Preceptores 
                                <span class="badge bg-primary rounded-pill ms-1">{{ $inscription->preceptorRecords->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pagamentos-tab" data-bs-toggle="tab" data-bs-target="#pagamentos" type="button" role="tab">
                                Pagamentos 
                                <span class="badge bg-success rounded-pill ms-1">{{ $inscription->payments->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="sessoes-tab" data-bs-toggle="tab" data-bs-target="#sessoes" type="button" role="tab">
                                Sessões 
                                <span class="badge bg-info rounded-pill ms-1">{{ $inscription->sessions->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="diagnosticos-tab" data-bs-toggle="tab" data-bs-target="#diagnosticos" type="button" role="tab">
                                Diagnósticos 
                                <span class="badge bg-warning rounded-pill ms-1">{{ $inscription->diagnostics->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="conquistas-tab" data-bs-toggle="tab" data-bs-target="#conquistas" type="button" role="tab">
                                Conquistas 
                                <span class="badge bg-success rounded-pill ms-1">{{ $inscription->achievements->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="followups-tab" data-bs-toggle="tab" data-bs-target="#followups" type="button" role="tab">
                                Follow-ups 
                                <span class="badge bg-primary rounded-pill ms-1">{{ $inscription->followUps->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="documentos-tab" data-bs-toggle="tab" data-bs-target="#documentos" type="button" role="tab">
                                Documentos 
                                <span class="badge bg-secondary rounded-pill ms-1">{{ $inscription->documents->count() }}</span>
                            </button>
                        </li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content mt-3" id="inscriptionTabContent">
                        <!-- Informações Básicas Tab -->
                        <div class="tab-pane fade show active" id="informacoes" role="tabpanel">
                            <!-- Informações Básicas -->
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <h5 class="border-bottom pb-2">Informações do Cliente</h5>
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
                        </div>

                        <!-- Preceptores Tab -->
                        <div class="tab-pane fade" id="preceptores" role="tabpanel">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0">Registros de Preceptores</h6>
                                        <button class="btn btn-sm btn-primary" onclick="abrirModalPreceptor()">
                                            <i class="fas fa-plus"></i> Novo Preceptor
                                        </button>
                                    </div>
                                    
                                    @if($inscription->preceptorRecords->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-sm" id="tabelaPreceptores">
                                                <thead>
                                                <thead>
                                                    <tr>
                                                        <th>Nome</th>
                                                        <th>Data Informado</th>
                                                        <th>Data Contato</th>
                                                        <th>Secretária</th>
                                                        <th>Status</th>
                                                        <th>Ações</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($inscription->preceptorRecords as $record)
                                                    <tr>
                                                        <td>{{ $record->nome_preceptor ?? 'N/A' }}</td>
                                                        <td>{{ $record->data_preceptor_informado ? $record->data_preceptor_informado->format('d/m/Y') : 'N/A' }}</td>
                                                        <td>{{ $record->data_preceptor_contato ? $record->data_preceptor_contato->format('d/m/Y') : 'N/A' }}</td>
                                                        <td>{{ $record->nome_secretaria ?? 'N/A' }}</td>
                                                        <td>
                                                            @if($record->usm)
                                                                <span class="badge bg-primary">USM</span>
                                                            @endif
                                                            @if($record->acesso_vitrine_gmc)
                                                                <span class="badge bg-success">Vitrine GMC</span>
                                                            @endif
                                                            @if($record->medico_celebridade)
                                                                <span class="badge bg-warning">Celebridade</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-sm btn-outline-danger" onclick="excluirRegistro('preceptor', {{ $record->id }})">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-4 text-muted">
                                            <i class="fas fa-user-md fa-3x mb-3"></i>
                                            <p>Nenhum registro de preceptor encontrado.</p>
                                        </div>
                                    @endif
                                </div>

                                <!-- Pagamentos Tab -->
                                <div class="tab-pane fade" id="pagamentos" role="tabpanel">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0">Registros de Pagamentos</h6>
                                        <button class="btn btn-sm btn-success" onclick="abrirModalPagamento()">
                                            <i class="fas fa-plus"></i> Novo Pagamento
                                        </button>
                                    </div>
                                    
                                    @if($inscription->payments->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Valor</th>
                                                        <th>Data Pagamento</th>
                                                        <th>Forma</th>
                                                        <th>Status</th>
                                                        <th>Ações</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($inscription->payments as $payment)
                                                    <tr>
                                                        <td>R$ {{ number_format($payment->valor, 2, ',', '.') }}</td>
                                                        <td>{{ $payment->data_pagamento ? $payment->data_pagamento->format('d/m/Y') : 'N/A' }}</td>
                                                        <td>{{ $payment->forma_pagamento ?? 'N/A' }}</td>
                                                        <td>
                                                            <span class="badge bg-{{ $payment->status == 'pago' ? 'success' : ($payment->status == 'pendente' ? 'warning' : 'danger') }}">
                                                                {{ ucfirst($payment->status ?? 'pendente') }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-sm btn-outline-danger" onclick="excluirRegistro('payment', {{ $payment->id }})">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-4 text-muted">
                                            <i class="fas fa-money-bill fa-3x mb-3"></i>
                                            <p>Nenhum pagamento registrado.</p>
                                        </div>
                                    @endif
                                </div>

                                <!-- Sessões Tab -->
                                <div class="tab-pane fade" id="sessoes" role="tabpanel">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0">Sessões</h6>
                                        <button class="btn btn-sm btn-info" onclick="abrirModalSessao()">
                                            <i class="fas fa-plus"></i> Nova Sessão
                                        </button>
                                    </div>
                                    
                                    @if($inscription->sessions->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Número</th>
                                                        <th>Fase</th>
                                                        <th>Tipo</th>
                                                        <th>Data Agendada</th>
                                                        <th>Status</th>
                                                        <th>Ações</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($inscription->sessions as $session)
                                                    <tr>
                                                        <td>{{ $session->numero_sessao ?? 'N/A' }}</td>
                                                        <td>{{ $session->fase ?? 'N/A' }}</td>
                                                        <td>{{ $session->tipo ?? 'N/A' }}</td>
                                                        <td>{{ $session->data_agendada ? $session->data_agendada->format('d/m/Y H:i') : 'N/A' }}</td>
                                                        <td>
                                                            <span class="badge bg-{{ $session->status == 'realizada' ? 'success' : ($session->status == 'agendada' ? 'info' : 'warning') }}">
                                                                {{ ucfirst($session->status ?? 'agendada') }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-sm btn-outline-danger" onclick="excluirRegistro('session', {{ $session->id }})">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-4 text-muted">
                                            <i class="fas fa-calendar fa-3x mb-3"></i>
                                            <p>Nenhuma sessão registrada.</p>
                                        </div>
                                    @endif
                                </div>

                                <!-- Diagnósticos Tab -->
                                <div class="tab-pane fade" id="diagnosticos" role="tabpanel">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0">Diagnósticos</h6>
                                        <button class="btn btn-sm btn-warning" onclick="abrirModalDiagnostico()">
                                            <i class="fas fa-plus"></i> Novo Diagnóstico
                                        </button>
                                    </div>
                                    
                                    @if($inscription->diagnostics->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Diagnóstico</th>
                                                        <th>Data</th>
                                                        <th>Observações</th>
                                                        <th>Ações</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($inscription->diagnostics as $diagnostic)
                                                    <tr>
                                                        <td>{{ $diagnostic->diagnosis ?? 'N/A' }}</td>
                                                        <td>{{ $diagnostic->date ? $diagnostic->date->format('d/m/Y') : 'N/A' }}</td>
                                                        <td>{{ Str::limit($diagnostic->notes ?? '', 50) }}</td>
                                                        <td>
                                                            <button class="btn btn-sm btn-outline-danger" onclick="excluirRegistro('diagnostic', {{ $diagnostic->id }})">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-4 text-muted">
                                            <i class="fas fa-stethoscope fa-3x mb-3"></i>
                                            <p>Nenhum diagnóstico registrado.</p>
                                        </div>
                                    @endif
                                </div>

                                <!-- Conquistas Tab -->
                                <div class="tab-pane fade" id="conquistas" role="tabpanel">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0">Conquistas</h6>
                                        <button class="btn btn-sm btn-success" onclick="abrirModalConquista()">
                                            <i class="fas fa-plus"></i> Nova Conquista
                                        </button>
                                    </div>
                                    
                                    @if($inscription->achievements->count() > 0)
                                        <div class="row">
                                            @foreach($inscription->achievements as $achievement)
                                            <div class="col-md-6 mb-3">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="d-flex justify-content-between">
                                                            <h6 class="card-title">{{ $achievement->title ?? 'Conquista' }}</h6>
                                                            <button class="btn btn-sm btn-outline-danger" onclick="excluirRegistro('achievement', {{ $achievement->id }})">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                        <p class="card-text">{{ $achievement->description ?? '' }}</p>
                                                        <small class="text-muted">
                                                            {{ $achievement->achieved_at ? $achievement->achieved_at->format('d/m/Y') : $achievement->created_at->format('d/m/Y') }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center py-4 text-muted">
                                            <i class="fas fa-trophy fa-3x mb-3"></i>
                                            <p>Nenhuma conquista registrada.</p>
                                        </div>
                                    @endif
                                </div>

                                <!-- Follow-ups Tab -->
                                <div class="tab-pane fade" id="followups" role="tabpanel">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0">Follow-ups</h6>
                                        <button class="btn btn-sm btn-primary" onclick="abrirModalFollowUp()">
                                            <i class="fas fa-plus"></i> Novo Follow-up
                                        </button>
                                    </div>
                                    
                                    @if($inscription->followUps->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Data</th>
                                                        <th>Status</th>
                                                        <th>Observações</th>
                                                        <th>Ações</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($inscription->followUps as $followUp)
                                                    <tr>
                                                        <td>{{ $followUp->follow_up_date ? $followUp->follow_up_date->format('d/m/Y') : 'N/A' }}</td>
                                                        <td>
                                                            <span class="badge bg-{{ $followUp->status == 'completed' ? 'success' : ($followUp->status == 'pending' ? 'warning' : 'info') }}">
                                                                {{ ucfirst($followUp->status ?? 'pending') }}
                                                            </span>
                                                        </td>
                                                        <td>{{ Str::limit($followUp->notes ?? '', 50) }}</td>
                                                        <td>
                                                            <button class="btn btn-sm btn-outline-danger" onclick="excluirRegistro('followup', {{ $followUp->id }})">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-4 text-muted">
                                            <i class="fas fa-user-check fa-3x mb-3"></i>
                                            <p>Nenhum follow-up registrado.</p>
                                        </div>
                                    @endif
                                </div>

                                <!-- Documentos Tab -->
                                <div class="tab-pane fade" id="documentos" role="tabpanel">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0">
                                            <i class="fas fa-file-alt"></i> Documentos da Inscrição
                                        </h6>
                                        <button class="btn btn-sm btn-primary" onclick="abrirModalDocumento()">
                                            <i class="fas fa-plus"></i> Novo Documento
                                        </button>
                                    </div>

                                    <!-- Estatísticas dos Documentos -->
                                    <div class="row mb-3">
                                        <div class="col-md-3">
                                            <div class="card bg-light">
                                                <div class="card-body text-center py-2">
                                                    <h6 class="card-title mb-1">Total</h6>
                                                    <span class="badge bg-secondary fs-6" id="total-documentos">{{ $inscription->documents->count() }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card bg-light">
                                                <div class="card-body text-center py-2">
                                                    <h6 class="card-title mb-1">Verificados</h6>
                                                    <span class="badge bg-success fs-6" id="documentos-verificados">{{ $inscription->documents->where('is_verified', true)->count() }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card bg-light">
                                                <div class="card-body text-center py-2">
                                                    <h6 class="card-title mb-1">Obrigatórios</h6>
                                                    <span class="badge bg-warning fs-6" id="documentos-obrigatorios">{{ $inscription->documents->where('is_required', true)->count() }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card bg-light">
                                                <div class="card-body text-center py-2">
                                                    <h6 class="card-title mb-1">Contratos</h6>
                                                    <span class="badge bg-info fs-6" id="documentos-contratos">{{ $inscription->documents->where('category', 'contrato')->count() }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Lista de Documentos -->
                                    <div id="documentos-container">
                                        @if($inscription->documents->count() > 0)
                                            <div class="row" id="documentos-grid">
                                                @foreach($inscription->documents as $document)
                                                <div class="col-md-6 mb-3" data-document-id="{{ $document->id }}">
                                                    <div class="card h-100">
                                                        <div class="card-body">
                                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                                <h6 class="card-title mb-1">
                                                                    <i class="{{ $document->icon_class }}"></i>
                                                                    {{ $document->title }}
                                                                </h6>
                                                                <div class="dropdown">
                                                                    <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                                                        <i class="fas fa-ellipsis-v"></i>
                                                                    </button>
                                                                    <ul class="dropdown-menu">
                                                                        @if($document->getDownloadUrl())
                                                                        <li><a class="dropdown-item" href="{{ $document->getDownloadUrl() }}" target="_blank">
                                                                            <i class="fas fa-download"></i> Download/Abrir
                                                                        </a></li>
                                                                        @endif
                                                                        <li><a class="dropdown-item" href="#" onclick="editarDocumento({{ $document->id }})">
                                                                            <i class="fas fa-edit"></i> Editar
                                                                        </a></li>
                                                                        <li><a class="dropdown-item" href="#" onclick="toggleVerificacao({{ $document->id }})">
                                                                            <i class="fas fa-{{ $document->is_verified ? 'times' : 'check' }}"></i> 
                                                                            {{ $document->is_verified ? 'Remover Verificação' : 'Marcar como Verificado' }}
                                                                        </a></li>
                                                                        <li><hr class="dropdown-divider"></li>
                                                                        <li><a class="dropdown-item text-danger" href="#" onclick="excluirDocumento({{ $document->id }})">
                                                                            <i class="fas fa-trash"></i> Excluir
                                                                        </a></li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="mb-2">
                                                                <span class="badge bg-primary">{{ $document->category_label }}</span>
                                                                <span class="badge {{ $document->status_badge_class }}">{{ $document->status_label }}</span>
                                                                <span class="badge bg-light text-dark">{{ $document->type_label }}</span>
                                                            </div>
                                                            
                                                            @if($document->description)
                                                            <p class="card-text small text-muted mb-2">{{ Str::limit($document->description, 100) }}</p>
                                                            @endif
                                                            
                                                            <div class="small text-muted">
                                                                @if($document->type === 'upload')
                                                                <div><i class="fas fa-hdd"></i> {{ $document->formatted_file_size }}</div>
                                                                @endif
                                                                <div><i class="fas fa-calendar"></i> {{ $document->created_at->format('d/m/Y H:i') }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-center py-5 text-muted" id="documentos-empty">
                                                <i class="fas fa-file-alt fa-3x mb-3"></i>
                                                <h5>Nenhum documento anexado</h5>
                                                <p>Adicione contratos, certificados e outros documentos importantes para esta inscrição.</p>
                                                <button class="btn btn-primary" onclick="abrirModalDocumento()">
                                                    <i class="fas fa-plus"></i> Adicionar Primeiro Documento
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modais para cadastro -->
@include('inscriptions.modals.preceptor')
@include('inscriptions.modals.pagamento')
@include('inscriptions.modals.sessao')
@include('inscriptions.modals.diagnostico')
@include('inscriptions.modals.conquista')
@include('inscriptions.modals.followup')
@include('inscriptions.modals.documento')
@endsection

@section('scripts')
<script>
// Funções globais para abrir modais
function abrirModalPagamento() {
    $('#modalPagamento').modal('show');
}

function abrirModalSessao() {
    $('#modalSessao').modal('show');
}

function abrirModalDiagnostico() {
    $('#modalDiagnostico').modal('show');
}

function abrirModalConquista() {
    $('#modalConquista').modal('show');
}

function abrirModalFollowUp() {
    $('#modalFollowUp').modal('show');
}

// Função global para excluir registros
function excluirRegistro(tipo, id) {
    if (confirm('Tem certeza que deseja excluir este registro?')) {
        const routes = {
            'preceptor': `/preceptor-records/${id}`,
            'payment': `/payments/${id}`,
            'session': `/sessions/${id}`,
            'diagnostic': `/diagnostics/${id}`,
            'achievement': `/achievements/${id}`,
            'followup': `/follow-ups/${id}`
        };

        fetch(routes[tipo], {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload(); // Simplificado por enquanto
            } else {
                alert('Erro ao excluir registro');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao excluir registro');
        });
    }
}

// Funções globais para atualizar abas (usadas pelos modais)
function atualizarAbaPreceptores(novoRegistro) {
    const tabela = $('#preceptores tbody');
    const contadorBadge = $('#preceptores-tab .badge');
    
    // Adicionar nova linha na tabela
    const novaLinha = `
        <tr>
            <td>${novoRegistro.nome_preceptor || 'N/A'}</td>
            <td>${novoRegistro.data_preceptor_informado ? formatarData(novoRegistro.data_preceptor_informado) : 'N/A'}</td>
            <td>${novoRegistro.data_preceptor_contato ? formatarData(novoRegistro.data_preceptor_contato) : 'N/A'}</td>
            <td>${novoRegistro.nome_secretaria || 'N/A'}</td>
            <td>
                ${novoRegistro.usm ? '<span class="badge bg-primary">USM</span>' : ''}
                ${novoRegistro.acesso_vitrine_gmc ? '<span class="badge bg-success">Vitrine GMC</span>' : ''}
                ${novoRegistro.medico_celebridade ? '<span class="badge bg-warning">Celebridade</span>' : ''}
            </td>
            <td>
                <button class="btn btn-sm btn-outline-danger" onclick="excluirRegistro('preceptor', ${novoRegistro.id})">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `;
    
    // Se não há registros, remover mensagem de "nenhum registro"
    const mensagemVazia = $('#preceptores .text-center.py-4');
    if (mensagemVazia.length) {
        mensagemVazia.parent().html(`
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Data Informado</th>
                            <th>Data Contato</th>
                            <th>Secretária</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>${novaLinha}</tbody>
                </table>
            </div>
        `);
    } else {
        tabela.append(novaLinha);
    }
    
    // Atualizar contador na badge
    const novoCount = parseInt(contadorBadge.text()) + 1;
    contadorBadge.text(novoCount);
}

function atualizarAbaPagamentos(novoRegistro) {
    // Recarregar por enquanto - implementar depois
    location.reload();
}

function atualizarAbaSessoes(novoRegistro) {
    // Recarregar por enquanto - implementar depois
    location.reload();
}

function atualizarAbaDiagnosticos(novoRegistro) {
    // Recarregar por enquanto - implementar depois
    location.reload();
}

function atualizarAbaConquistas(novoRegistro) {
    // Recarregar por enquanto - implementar depois
    location.reload();
}

function atualizarAbaFollowUps(novoRegistro) {
    // Recarregar por enquanto - implementar depois
    location.reload();
}

// Funções utilitárias globais
function formatarData(dataString) {
    if (!dataString) return 'N/A';
    const data = new Date(dataString);
    return data.toLocaleDateString('pt-BR');
}

function mostrarMensagemSucesso(mensagem) {
    // Criar um toast ou alert temporário
    const alert = $(`
        <div class="alert alert-success alert-dismissible fade show position-fixed" 
             style="top: 20px; right: 20px; z-index: 9999;">
            ${mensagem}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `);
    
    $('body').append(alert);
    
    // Remover automaticamente após 3 segundos
    setTimeout(() => {
        alert.alert('close');
    }, 3000);
}
</script>
@endsection
