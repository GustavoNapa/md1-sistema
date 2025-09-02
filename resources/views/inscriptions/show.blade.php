@php
    use Carbon\Carbon;
@endphp
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
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="bonus-tab" data-bs-toggle="tab" data-bs-target="#bonus" type="button" role="tab">
                                Bônus 
                                <span class="badge bg-primary rounded-pill ms-1">{{ $inscription->bonuses->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="faturamento-tab" data-bs-toggle="tab" data-bs-target="#faturamento" type="button" role="tab">
                                Faturamento Mês a Mês
                                <span class="badge bg-primary rounded-pill ms-1">{{ $inscription->faturamentos->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="renovacao-tab" data-bs-toggle="tab" data-bs-target="#renovacao" type="button" role="tab">
                                Renovação
                                <span class="badge bg-primary rounded-pill ms-1">{{ $inscription->renovacoes->count() }}</span>
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

                            <!-- Contrato Assinado e Contrato na Pasta -->
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <h5 class="border-bottom pb-2">Contrato</h5>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Contrato Assinado:</strong> 
                                        <span class="badge {{ $inscription->contrato_assinado ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $inscription->contrato_assinado ? 'Sim' : 'Não' }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Contrato na Pasta:</strong> 
                                        <span class="badge {{ $inscription->contrato_na_pasta ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $inscription->contrato_na_pasta ? 'Sim' : 'Não' }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Link da Pasta do Contrato:</strong>
                                        @if($inscription->contract_folder_link)
                                            <a href="{{ $inscription->contract_folder_link }}" target="_blank" rel="noopener noreferrer">Abrir Pasta</a>
                                        @else
                                            <span class="text-muted">Não informado</span>
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <!-- Semanas e Pagamento -->
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <h5 class="border-bottom pb-2">Semanas</h5>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Semana Calendário:</strong> {{ $inscription->calendar_week ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Semana Atual:</strong> {{ $inscription->current_week ?? 'N/A' }}</p>
                                </div>
                            </div>

                            <!-- Semanas e Pagamento -->
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <h5 class="border-bottom pb-2">Pagamento</h5>
                                </div>
                                <div class="col-md-5">
                                    <p><strong>Valor Pago:</strong> 
                                        {{ $inscription->amount_paid ? 'R$ ' . number_format($inscription->amount_paid, 2, ',', '.') : 'Não informado' }}
                                    </p>
                                </div>
                                <div class="col-md-5">
                                    <p><strong>Método Pagamento:</strong> 
                                        {{ $inscription->payment_method ? AppHttpControllersInscriptionController::getPaymentMethodOptions()[$inscription->payment_method] ?? $inscription->payment_method : 'Não informado' }}
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
                                                        <th>Ações</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($inscription->preceptorRecords as $record)
                                                        <tr>
                                                            <td>{{ $record->preceptor->name ?? 'N/A' }}</td>
                                                            <td>{{ $record->informed_date ? $record->informed_date->format('d/m/Y') : 'N/A' }}</td>
                                                            <td>{{ $record->contact_date ? $record->contact_date->format('d/m/Y') : 'N/A' }}</td>
                                                            <td>
                                                                <button class="btn btn-sm btn-info" onclick="editarPreceptor({{ $record->id }})">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                                <button class="btn btn-sm btn-danger" onclick="excluirPreceptor({{ $record->id }})">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p>Nenhum registro de preceptor encontrado.</p>
                                    @endif
                                </div>

                        <!-- Pagamentos Tab -->
                        <div class="tab-pane fade" id="pagamentos" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">Registros de Pagamentos</h6>
                                <button class="btn btn-sm btn-primary" onclick="abrirModalPagamento()">
                                    <i class="fas fa-plus"></i> Novo Pagamento
                                </button>
                            </div>
                            @if($inscription->payments->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Valor</th>
                                                <th>Data</th>
                                                <th>Método</th>
                                                <th>Status</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($inscription->payments as $payment)
                                                <tr>
                                                    <td>R$ {{ number_format($payment->valor, 2, ',', '.') }}</td>
                                                    <td>{{ $payment->data_pagamento ? $payment->data_pagamento->format('d/m/Y') : 'N/A' }}</td>
                                                    <td>{{ $payment->forma_pagamento }}</td>
                                                    <td>
                                                        <span class="badge {{ $payment->status === 'paid' ? 'bg-success' : 'bg-warning' }}">
                                                            {{ $payment->status }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-info" onclick="editarPagamento({{ $payment->id }})">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-danger" onclick="excluirPagamento({{ $payment->id }})">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p>Nenhum registro de pagamento encontrado.</p>
                            @endif
                        </div>

                        <!-- Sessões Tab -->
                        <div class="tab-pane fade" id="sessoes" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">Registros de Sessões</h6>
                                <button class="btn btn-sm btn-primary" onclick="abrirModalSessao()">
                                    <i class="fas fa-plus"></i> Nova Sessão
                                </button>
                            </div>
                            @if($inscription->sessions->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Data</th>
                                                <th>Tipo</th>
                                                <th>Status</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($inscription->sessions as $session)
                                                <tr>
                                                    <td>{{ $session->session_date->format('d/m/Y') }}</td>
                                                    <td>{{ $session->type }}</td>
                                                    <td>
                                                        <span class="badge {{ $session->status === 'completed' ? 'bg-success' : 'bg-warning' }}">
                                                            {{ $session->status }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-info" onclick="editarSessao({{ $session->id }})">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-danger" onclick="excluirSessao({{ $session->id }})">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p>Nenhum registro de sessão encontrado.</p>
                            @endif
                        </div>

                        <!-- Diagnósticos Tab -->
                        <div class="tab-pane fade" id="diagnosticos" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">Registros de Diagnósticos</h6>
                                <button class="btn btn-sm btn-primary" onclick="abrirModalDiagnostico()">
                                    <i class="fas fa-plus"></i> Novo Diagnóstico
                                </button>
                            </div>
                            @if($inscription->diagnostics->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Data</th>
                                                <th>Descrição</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($inscription->diagnostics as $diagnostic)
                                                <tr>
                                                    <td>{{ $diagnostic->diagnostic_date->format('d/m/Y') }}</td>
                                                    <td>{{ $diagnostic->description }}</td>
                                                    <td>
                                                        <button class="btn btn-sm btn-info" onclick="editarDiagnostico({{ $diagnostic->id }})">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-danger" onclick="excluirDiagnostico({{ $diagnostic->id }})">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p>Nenhum registro de diagnóstico encontrado.</p>
                            @endif
                        </div>

                        <!-- Conquistas Tab -->
                        <div class="tab-pane fade" id="conquistas" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">Registros de Conquistas</h6>
                                <button class="btn btn-sm btn-primary" onclick="abrirModalConquista()">
                                    <i class="fas fa-plus"></i> Nova Conquista
                                </button>
                            </div>
                            @if($inscription->achievements->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Data</th>
                                                <th>Descrição</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($inscription->achievements as $achievement)
                                                <tr>
                                                    <td>{{ $achievement->achievement_date ? $achievement->achievement_date->format('d/m/Y') : 'N/A' }}</td>
                                                    <td>{{ $achievement->description }}</td>
                                                    <td>
                                                        <button class="btn btn-sm btn-info" onclick="editarConquista({{ $achievement->id }})">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-danger" onclick="excluirConquista({{ $achievement->id }})">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p>Nenhum registro de conquista encontrado.</p>
                            @endif
                        </div>

                        <!-- Follow-ups Tab -->
                        <div class="tab-pane fade" id="followups" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">Registros de Follow-ups</h6>
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
                                                <th>Descrição</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($inscription->followUps as $followUp)
                                                <tr>
                                                    <td>{{ $followUp->followup_date ? $followUp->followup_date->format('d/m/Y') : 'N/A' }}</td>
                                                    <td>{{ $followUp->description }}</td>
                                                    <td>
                                                        <button class="btn btn-sm btn-info" onclick="editarFollowUp({{ $followUp->id }})">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-danger" onclick="excluirFollowUp({{ $followUp->id }})">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p>Nenhum registro de follow-up encontrado.</p>
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

                                <!-- Bônus Tab -->
                                <div class="tab-pane fade" id="bonus" role="tabpanel">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0">Bônus</h6>
                                        <button type="button" class="btn btn-sm btn-primary" id="btnNovoBonus">
                                            <i class="fas fa-plus"></i> Novo Bônus
                                        </button>
                                    </div>
                                    
                                    @if($inscription->bonuses && $inscription->bonuses->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Descrição</th>
                                                        <th>Data de Liberação</th>
                                                        <th>Data de Expiração</th>
                                                        <th>Ações</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($inscription->bonuses as $bonus)
                                                        <tr>
                                                            <td>{{ $bonus->description }}</td>
                                                            <td>{{ $bonus->release_date ? Carbon::parse($bonus->release_date)->format('d/m/Y') : 'N/A' }}</td>
                                                            <td>{{ $bonus->expiration_date ? Carbon::parse($bonus->expiration_date)->format('d/m/Y') : 'N/A' }}</td>
                                                            <td>
                                                                <button class="btn btn-sm btn-outline-secondary btn-editar-bonus" data-bonus-id="{{ $bonus->id }}">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                                <button class="btn btn-sm btn-outline-danger btn-excluir-bonus" data-bonus-id="{{ $bonus->id }}">
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
                                            <i class="fas fa-gift fa-3x mb-3"></i>
                                            <p>Nenhum bônus cadastrado para esta inscrição.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Faturamento Mês a Mês Tab -->
                        <div class="tab-pane fade" id="faturamento" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5>Faturamento Mês a Mês</h5>
                                <button type="button" class="btn btn-primary btn-sm" onclick="abrirModalFaturamento()">
                                    Novo Faturamento
                                </button>
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-striped" id="tabelaFaturamentos">
                                    <thead>
                                        <tr>
                                            <th>Mês/Ano</th>
                                            <th>Valor Faturado</th>
                                            <th>Data de Vencimento</th>
                                            <th>Status</th>
                                            <th>Observações</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($inscription->faturamentos->count() > 0)
                                            @foreach($inscription->faturamentos as $faturamento)
                                                <tr>
                                                    <td>{{ $faturamento->mes_ano }}</td>
                                                    <td>R$ {{ number_format($faturamento->valor, 2, ',', '.') }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($faturamento->data_vencimento)->format('d/m/Y') }}</td>
                                                    <td>
                                                        <span class="badge {{ $faturamento->status === 'pago' ? 'bg-success' : 
                                                            ($faturamento->status === 'pendente' ? 'bg-warning' : 
                                                            ($faturamento->status === 'vencido' ? 'bg-danger' : 'bg-secondary')) }}">
                                                            {{ ucfirst($faturamento->status) }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $faturamento->observacoes ?? '-' }}</td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="editarFaturamento({{ $faturamento->id }})">
                                                            Editar
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="excluirFaturamento({{ $faturamento->id }})">
                                                            Excluir
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="6" class="text-center text-muted">
                                                    Nenhum faturamento cadastrado ainda.
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Renovação Tab -->
                        <div class="tab-pane fade" id="renovacao" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5>Renovação</h5>
                                <button type="button" class="btn btn-primary btn-sm" onclick="abrirModalRenovacao()">
                                    Nova Renovação
                                </button>
                            </div>
                            
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <h6 class="border-bottom pb-2">Status da Renovação</h6>
                                </div>
                                <div class="col-md-3">
                                    <p><strong>Status Atual:</strong> 
                                        @php
                                            $ultimaRenovacao = $inscription->renovacoes()->latest()->first();
                                        @endphp
                                        @if($ultimaRenovacao)
                                            <span class="badge {{ $ultimaRenovacao->status === 'aprovada' ? 'bg-success' : 
                                                ($ultimaRenovacao->status === 'pendente' ? 'bg-warning' : 
                                                ($ultimaRenovacao->status === 'rejeitada' ? 'bg-danger' : 'bg-secondary')) }}">
                                                {{ ucfirst($ultimaRenovacao->status) }}
                                            </span>
                                        @else
                                            <span class="badge bg-warning">Pendente</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-3">
                                    <p><strong>Data de Vencimento:</strong> 
                                        {{ $inscription->actual_end_date ? $inscription->actual_end_date->format('d/m/Y') : 'Não definida' }}
                                    </p>
                                </div>
                                <div class="col-md-3">
                                    <p><strong>Dias para Vencimento:</strong> 
                                        @if($inscription->actual_end_date)
                                            @php
                                                $diasDiferenca = $inscription->actual_end_date->diffInDays(now(), false);
                                            @endphp
                                            @if($diasDiferenca > 0)
                                                <span class="text-danger">Vencido há {{ $diasDiferenca }} dias</span>
                                            @else
                                                <span class="text-success">{{ abs($diasDiferenca) }} dias</span>
                                            @endif
                                        @else
                                            Não definido
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-3">
                                    <p><strong>Valor Renovação:</strong> 
                                        {{ $inscription->product ? 'R$ ' . number_format($inscription->product->price, 2, ',', '.') : 'Não definido' }}
                                    </p>
                                </div>
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-striped" id="tabelaRenovacoes">
                                    <thead>
                                        <tr>
                                            <th>Data da Renovação</th>
                                            <th>Período</th>
                                            <th>Valor</th>
                                            <th>Status</th>
                                            <th>Observações</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($inscription->renovacoes->count() > 0)
                                            @foreach($inscription->renovacoes as $renovacao)
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::parse($renovacao->data_inicio)->format('d/m/Y') }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($renovacao->data_inicio)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($renovacao->data_fim)->format('d/m/Y') }}</td>
                                                    <td>R$ {{ number_format($renovacao->valor, 2, ',', '.') }}</td>
                                                    <td>
                                                        <span class="badge {{ $renovacao->status === 'aprovada' ? 'bg-success' : 
                                                            ($renovacao->status === 'pendente' ? 'bg-warning' : 
                                                            ($renovacao->status === 'rejeitada' ? 'bg-danger' : 'bg-secondary')) }}">
                                                            {{ ucfirst($renovacao->status) }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $renovacao->observacoes ?? '-' }}</td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="editarRenovacao({{ $renovacao->id }})">
                                                            Editar
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="excluirRenovacao({{ $renovacao->id }})">
                                                            Excluir
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="6" class="text-center text-muted">
                                                    Nenhuma renovação cadastrada ainda.
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Bônus -->
<div class="modal fade" id="modalBonus" tabindex="-1" aria-labelledby="modalBonusLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalBonusLabel">Adicionar/Editar Bônus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formBonus">
                    <input type="hidden" id="bonusId" name="id">
                    <div class="mb-3">
                        <label for="bonusDescription" class="form-label">Descrição</label>
                        <input type="text" class="form-control" id="bonusDescription" name="description" required>
                    </div>
                    <div class="mb-3">
                        <label for="bonusReleaseDate" class="form-label">Data de Liberação</label>
                        <input type="date" class="form-control" id="bonusReleaseDate" name="release_date">
                    </div>
                    <div class="mb-3">
                        <label for="bonusExpirationDate" class="form-label">Data de Expiração (Opcional)</label>
                        <input type="date" class="form-control" id="bonusExpirationDate" name="expiration_date">
                    </div>
                    <button type="submit" class="btn btn-primary">Salvar Bônus</button>
                </form>
            </div>
        </div>
    </div>
</div>



<!-- Modal Faturamento -->
<div class="modal fade" id="modalFaturamento" tabindex="-1" aria-labelledby="modalFaturamentoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalFaturamentoLabel">Novo Faturamento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formFaturamento">
                <div class="modal-body">
                    <input type="hidden" id="faturamentoId" name="id">
                    
                    <div class="mb-3">
                        <label for="faturamentoMesAno" class="form-label">Mês/Ano</label>
                        <input type="month" class="form-control" id="faturamentoMesAno" name="mes_ano" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="faturamentoValor" class="form-label">Valor Faturado</label>
                        <input type="number" step="0.01" class="form-control" id="faturamentoValor" name="valor" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="faturamentoVencimento" class="form-label">Data de Vencimento</label>
                        <input type="date" class="form-control" id="faturamentoVencimento" name="data_vencimento" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="faturamentoStatus" class="form-label">Status</label>
                        <select class="form-control" id="faturamentoStatus" name="status" required>
                            <option value="">Selecione...</option>
                            <option value="pendente">Pendente</option>
                            <option value="pago">Pago</option>
                            <option value="vencido">Vencido</option>
                            <option value="cancelado">Cancelado</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="faturamentoObservacoes" class="form-label">Observações</label>
                        <textarea class="form-control" id="faturamentoObservacoes" name="observacoes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Renovação -->
<div class="modal fade" id="modalRenovacao" tabindex="-1" aria-labelledby="modalRenovacaoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalRenovacaoLabel">Nova Renovação</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formRenovacao">
                <div class="modal-body">
                    <input type="hidden" id="renovacaoId" name="id">
                    
                    <div class="mb-3">
                        <label for="renovacaoDataInicio" class="form-label">Data de Início</label>
                        <input type="date" class="form-control" id="renovacaoDataInicio" name="data_inicio" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="renovacaoDataFim" class="form-label">Data de Fim</label>
                        <input type="date" class="form-control" id="renovacaoDataFim" name="data_fim" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="renovacaoValor" class="form-label">Valor da Renovação</label>
                        <input type="number" step="0.01" class="form-control" id="renovacaoValor" name="valor" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="renovacaoStatus" class="form-label">Status</label>
                        <select class="form-control" id="renovacaoStatus" name="status" required>
                            <option value="">Selecione...</option>
                            <option value="pendente">Pendente</option>
                            <option value="aprovada">Aprovada</option>
                            <option value="rejeitada">Rejeitada</option>
                            <option value="cancelada">Cancelada</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="renovacaoObservacoes" class="form-label">Observações</label>
                        <textarea class="form-control" id="renovacaoObservacoes" name="observacoes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
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


<style>
/* Fallback CSS para garantir que o modal apareça */
.modal.show {
    display: block !important;
    z-index: 1055 !important;
}
.modal-backdrop.show {
    opacity: 0.5;
    z-index: 1050 !important;
}

/* Garantir que o modal de bônus tenha prioridade */
#modalBonus {
    z-index: 1060 !important;
}

#modalBonus.show {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
}

/* Override para qualquer CSS conflitante */
.modal-open {
    overflow: hidden !important;
}

/* Debug: adicionar bordas temporárias */
#modalBonus {
    border: 3px solid red !important;
}

#modalBonus .modal-content {
    border: 2px solid blue !important;
}
</style>

@push('scripts')
    <script>
        // Scripts para o modal de bônus
        window.formatDateForInput = function(dateStr) {
            if (!dateStr) return '';
            const d = new Date(dateStr);
            if (isNaN(d.getTime())) return '';
            return d.toISOString().slice(0, 10);
        };

        window.abrirModalBonus = function(bonus = null) {
            console.log('=== INICIANDO ABERTURA DO MODAL DE BÔNUS ===');
            const modalElement = document.getElementById('modalBonus');
            
            if (!modalElement) {
                console.error('❌ Modal não encontrado no DOM');
                alert('Erro: Modal não encontrado no DOM');
                return;
            }
            
            console.log('✅ Modal encontrado:', modalElement);
            
            const form = document.getElementById('formBonus');
            if (!form) {
                console.error('❌ Formulário não encontrado');
                alert('Erro: Formulário não encontrado');
                return;
            }
            
            // Resetar formulário primeiro
            form.reset();
            document.getElementById('bonusId').value = '';
            document.getElementById('bonusDescription').value = '';
            document.getElementById('bonusReleaseDate').value = '';
            document.getElementById('bonusExpirationDate').value = '';
            
            console.log('✅ Formulário resetado');
            
            // Método 1: jQuery
            let tentativas = 0;
            let modalAberto = false;
            
            if (typeof $ !== 'undefined') {
                console.log('🔄 Tentativa 1: jQuery');
                try {
                    $('#modalBonus').modal('show');
                    // Verificar se abriu
                    setTimeout(() => {
                        if (modalElement.classList.contains('show')) {
                            console.log('✅ Modal aberto com jQuery');
                            modalAberto = true;
                        } else {
                            console.log('❌ jQuery falhou, tentando Bootstrap nativo');
                            tentarBootstrapNativo();
                        }
                    }, 100);
                } catch (e) {
                    console.error('❌ Erro com jQuery:', e);
                    tentarBootstrapNativo();
                }
            } else {
                tentarBootstrapNativo();
            }
            
            function tentarBootstrapNativo() {
                if (modalAberto) return;
                
                console.log('🔄 Tentativa 2: Bootstrap nativo');
                if (typeof bootstrap !== 'undefined') {
                    try {
                        const modal = new bootstrap.Modal(modalElement);
                        modal.show();
                        
                        setTimeout(() => {
                            if (modalElement.classList.contains('show')) {
                                console.log('✅ Modal aberto com Bootstrap nativo');
                                modalAberto = true;
                            } else {
                                console.log('❌ Bootstrap nativo falhou, forçando manualmente');
                                forcarModalManual();
                            }
                        }, 100);
                    } catch (e) {
                        console.error('❌ Erro com Bootstrap nativo:', e);
                        forcarModalManual();
                    }
                } else {
                    forcarModalManual();
                }
            }
            
            function forcarModalManual() {
                if (modalAberto) return;
                
                console.log('🔄 Tentativa 3: Forçando manualmente');
                try {
                    // Remover aria-hidden e adicionar classes necessárias
                    modalElement.style.display = 'block';
                    modalElement.classList.add('show');
                    modalElement.setAttribute('aria-hidden', 'false');
                    modalElement.setAttribute('aria-modal', 'true');
                    
                    // Adicionar backdrop
                    let backdrop = document.querySelector('.modal-backdrop');
                    if (!backdrop) {
                        backdrop = document.createElement('div');
                        backdrop.className = 'modal-backdrop fade show';
                        document.body.appendChild(backdrop);
                    }
                    
                    // Adicionar classe ao body
                    document.body.classList.add('modal-open');
                    
                    console.log('✅ Modal forçado manualmente');
                    modalAberto = true;
                    
                    // Listener para fechar com backdrop
                    backdrop.addEventListener('click', function() {
                        fecharModal();
                    });
                    
                } catch (e) {
                    console.error('❌ Erro ao forçar modal:', e);
                    alert('Erro crítico: Não foi possível abrir o modal de bônus');
                }
            }
            
            function fecharModal() {
                modalElement.style.display = 'none';
                modalElement.classList.remove('show');
                modalElement.setAttribute('aria-hidden', 'true');
                modalElement.removeAttribute('aria-modal');
                
                const backdrop = document.querySelector('.modal-backdrop');
                if (backdrop) {
                    backdrop.remove();
                }
                
                document.body.classList.remove('modal-open');
            }

            if (bonus) {
                document.getElementById('bonusId').value = bonus.id;
                document.getElementById('bonusDescription').value = bonus.description || '';
                document.getElementById('bonusReleaseDate').value = formatDateForInput(bonus.release_date);
                document.getElementById('bonusExpirationDate').value = formatDateForInput(bonus.expiration_date);
            }
        };

        // Função de teste para ser chamada pelo console
        window.testarModalBonus = function() {
            console.log('=== TESTE DO MODAL DE BÔNUS ===');
            console.log('DOM carregado:', document.readyState);
            console.log('jQuery disponível:', typeof $ !== 'undefined');
            console.log('Bootstrap disponível:', typeof bootstrap !== 'undefined');
            
            const modal = document.getElementById('modalBonus');
            console.log('Modal encontrado:', !!modal);
            
            if (modal) {
                console.log('Classes do modal:', modal.className);
                console.log('Display do modal:', getComputedStyle(modal).display);
                console.log('Z-index do modal:', getComputedStyle(modal).zIndex);
            }
            
            const btnNovoBonus = document.getElementById('btnNovoBonus');
            console.log('Botão encontrado:', !!btnNovoBonus);
            
            if (btnNovoBonus) {
                console.log('Forçando clique no botão...');
                btnNovoBonus.click();
            }
            
            return {
                dom: document.readyState,
                jquery: typeof $ !== 'undefined',
                bootstrap: typeof bootstrap !== 'undefined',
                modal: !!modal,
                botao: !!btnNovoBonus
            };
        };

        // Função para forçar modal via console
        window.forceShowModal = function() {
            console.log('=== FORÇANDO MODAL VIA CONSOLE ===');
            const modal = document.getElementById('modalBonus');
            if (!modal) {
                console.error('Modal não encontrado');
                return;
            }
            
            modal.style.display = 'block';
            modal.classList.add('show');
            modal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('modal-open');
            
            let backdrop = document.querySelector('.modal-backdrop');
            if (!backdrop) {
                backdrop = document.createElement('div');
                backdrop.className = 'modal-backdrop fade show';
                document.body.appendChild(backdrop);
            }
            
            console.log('Modal forçado com sucesso!');
        };

        window.editarBonus = function(bonusId) {
            console.log('Editando bônus', bonusId);
            fetch(`/api/inscriptions/{{ $inscription->id }}/bonuses/${bonusId}`)
                .then(response => response.json())
                .then(data => {
                    window.abrirModalBonus(data);
                })
                .catch(error => {
                    console.error('Erro ao buscar bônus para edição:', error);
                    alert('Erro ao buscar dados do bônus');
                });
        };

        window.excluirBonus = function(bonusId) {
            if (confirm('Tem certeza que deseja excluir este bônus?')) {
                fetch(`/api/inscriptions/{{ $inscription->id }}/bonuses/${bonusId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    alert('Bônus excluído com sucesso!');
                    location.reload();
                })
                .catch(error => {
                    console.error('Erro ao excluir bônus:', error);
                    alert('Erro ao excluir bônus');
                });
            }
        };

        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM carregado, configurando eventos para modal de bônus');
            
            // Debug: verificar se todos os elementos estão disponíveis
            const btnNovoBonus = document.getElementById('btnNovoBonus');
            const modalBonus = document.getElementById('modalBonus');
            const formBonus = document.getElementById('formBonus');
            
            console.log('Botão Novo Bônus encontrado:', !!btnNovoBonus);
            console.log('Modal Bônus encontrado:', !!modalBonus);
            console.log('Form Bônus encontrado:', !!formBonus);
            console.log('Bootstrap disponível:', typeof bootstrap !== 'undefined');
            console.log('jQuery disponível:', typeof $ !== 'undefined');
            
            // Listener para o botão Novo Bônus
            if (btnNovoBonus) {
                console.log('Configurando evento do botão Novo Bônus');
                btnNovoBonus.addEventListener('click', function(e) {
                    e.preventDefault();
                    console.log('Clique no botão Novo Bônus - chamando abrirModalBonus');
                    window.abrirModalBonus();
                });
            } else {
                console.error('Botão Novo Bônus não encontrado');
            }

            // Listeners para botões de editar bônus
            document.querySelectorAll('.btn-editar-bonus').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const bonusId = this.getAttribute('data-bonus-id');
                    console.log('Editando bônus:', bonusId);
                    window.editarBonus(bonusId);
                });
            });

            // Listeners para botões de excluir bônus
            document.querySelectorAll('.btn-excluir-bonus').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const bonusId = this.getAttribute('data-bonus-id');
                    console.log('Excluindo bônus:', bonusId);
                    window.excluirBonus(bonusId);
                });
            });

            // Submit do formulário de bônus
            if (formBonus) {
                formBonus.addEventListener('submit', function(event) {
                    event.preventDefault();
                    const bonusId = document.getElementById('bonusId').value;
                    const url = bonusId
                        ? `/api/inscriptions/{{ $inscription->id }}/bonuses/${bonusId}`
                        : `/api/inscriptions/{{ $inscription->id }}/bonuses`;
                    const method = bonusId ? 'PUT' : 'POST';

                    fetch(url, {
                        method: method,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            description: document.getElementById('bonusDescription').value,
                            release_date: document.getElementById('bonusReleaseDate').value || null,
                            expiration_date: document.getElementById('bonusExpirationDate').value || null
                        })
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Erro ao salvar bônus');
                        return response.json();
                    })
                    .then(data => {
                        alert('Bônus salvo com sucesso!');
                        location.reload();
                    })
                    .catch(error => {
                        alert('Erro: ' + error.message);
                        console.error('Erro ao salvar bônus:', error);
                    });
                });
            } else {
                console.error('Formulário de bônus não encontrado');
            }
            
            // Fallback: tentar abrir modal manualmente usando CSS se necessário
            window.forceShowModal = function() {
                const modal = document.getElementById('modalBonus');
                if (modal) {
                    modal.style.display = 'block';
                    modal.classList.add('show');
                    modal.setAttribute('aria-hidden', 'false');
                    document.body.classList.add('modal-open');
                    
                    // Criar backdrop se não existir
                    if (!document.querySelector('.modal-backdrop')) {
                        const backdrop = document.createElement('div');
                        backdrop.className = 'modal-backdrop fade show';
                        document.body.appendChild(backdrop);
                    }
                }
            };

            // Funções para o modal de Faturamento
            window.abrirModalFaturamento = function(faturamento = null) {
                const modal = new bootstrap.Modal(document.getElementById("modalFaturamento"));
                const form = document.getElementById("formFaturamento");
                form.reset();

                if (faturamento) {
                    document.getElementById("faturamentoId").value = faturamento.id;
                    document.getElementById("faturamentoMesAno").value = faturamento.mes_ano;
                    document.getElementById("faturamentoValor").value = faturamento.valor;
                    document.getElementById("faturamentoVencimento").value = faturamento.data_vencimento;
                    document.getElementById("faturamentoStatus").value = faturamento.status;
                    document.getElementById("faturamentoObservacoes").value = faturamento.observacoes;
                } else {
                    document.getElementById("faturamentoId").value = "";
                }

                modal.show();
            };

            // Funções para o modal de Renovação
            window.abrirModalRenovacao = function(renovacao = null) {
                const modal = new bootstrap.Modal(document.getElementById("modalRenovacao"));
                const form = document.getElementById("formRenovacao");
                form.reset();

                if (renovacao) {
                    document.getElementById("renovacaoId").value = renovacao.id;
                    document.getElementById("renovacaoDataInicio").value = renovacao.data_inicio;
                    document.getElementById("renovacaoDataFim").value = renovacao.data_fim;
                    document.getElementById("renovacaoValor").value = renovacao.valor;
                    document.getElementById("renovacaoStatus").value = renovacao.status;
                    document.getElementById("renovacaoObservacoes").value = renovacao.observacoes;
                } else {
                    document.getElementById("renovacaoId").value = "";
                }

                modal.show();
            };

            // Event listeners para os formulários
            const formFaturamento = document.getElementById("formFaturamento");
            if (formFaturamento) {
                formFaturamento.addEventListener("submit", function(event) {
                    event.preventDefault();
                    const faturamentoId = document.getElementById("faturamentoId").value;
                    const url = faturamentoId 
                        ? `/inscriptions/{{ $inscription->id }}/faturamentos/${faturamentoId}` 
                        : `/inscriptions/{{ $inscription->id }}/faturamentos`;
                    const method = faturamentoId ? "PUT" : "POST";

                    const formData = {
                        mes_ano: document.getElementById("faturamentoMesAno").value,
                        valor: document.getElementById("faturamentoValor").value,
                        data_vencimento: document.getElementById("faturamentoVencimento").value,
                        status: document.getElementById("faturamentoStatus").value,
                        observacoes: document.getElementById("faturamentoObservacoes").value || null,
                    };

                    fetch(url, {
                        method: method,
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify(formData)
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(errorData => {
                                throw new Error(errorData.message || "Erro ao salvar faturamento.");
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        alert("Faturamento salvo com sucesso!");
                        location.reload();
                    })
                    .catch(error => {
                        alert("Erro: " + error.message);
                        console.error("Erro ao salvar faturamento:", error);
                    });
                });
            }

            const formRenovacao = document.getElementById("formRenovacao");
            if (formRenovacao) {
                formRenovacao.addEventListener("submit", function(event) {
                    event.preventDefault();
                    const renovacaoId = document.getElementById("renovacaoId").value;
                    const url = renovacaoId 
                        ? `/inscriptions/{{ $inscription->id }}/renovacoes/${renovacaoId}` 
                        : `/inscriptions/{{ $inscription->id }}/renovacoes`;
                    const method = renovacaoId ? "PUT" : "POST";

                    const formData = {
                        data_inicio: document.getElementById("renovacaoDataInicio").value,
                        data_fim: document.getElementById("renovacaoDataFim").value,
                        valor: document.getElementById("renovacaoValor").value,
                        status: document.getElementById("renovacaoStatus").value,
                        observacoes: document.getElementById("renovacaoObservacoes").value || null,
                    };

                    fetch(url, {
                        method: method,
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify(formData)
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(errorData => {
                                throw new Error(errorData.message || "Erro ao salvar renovação.");
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        alert("Renovação salva com sucesso!");
                        location.reload();
                    })
                    .catch(error => {
                        alert("Erro: " + error.message);
                        console.error("Erro ao salvar renovação:", error);
                    });
                });
            }

            // Funções para editar e excluir faturamentos
            window.editarFaturamento = function(faturamentoId) {
                fetch(`/inscriptions/{{ $inscription->id }}/faturamentos/${faturamentoId}`)
                    .then(response => response.json())
                    .then(data => {
                        abrirModalFaturamento(data);
                    })
                    .catch(error => console.error("Erro ao buscar faturamento para edição:", error));
            };

            window.excluirFaturamento = function(faturamentoId) {
                if (confirm("Tem certeza que deseja excluir este faturamento?")) {
                    fetch(`/inscriptions/{{ $inscription->id }}/faturamentos/${faturamentoId}`, {
                        method: "DELETE",
                        headers: {
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(errorData => {
                                throw new Error(errorData.message || "Erro ao excluir faturamento.");
                            });
                        }
                        alert("Faturamento excluído com sucesso!");
                        location.reload();
                    })
                    .catch(error => {
                        alert("Erro: " + error.message);
                        console.error("Erro ao excluir faturamento:", error);
                    });
                }
            };

            // Funções para editar e excluir renovações
            window.editarRenovacao = function(renovacaoId) {
                fetch(`/inscriptions/{{ $inscription->id }}/renovacoes/${renovacaoId}`)
                    .then(response => response.json())
                    .then(data => {
                        abrirModalRenovacao(data);
                    })
                    .catch(error => console.error("Erro ao buscar renovação para edição:", error));
            };

            window.excluirRenovacao = function(renovacaoId) {
                if (confirm("Tem certeza que deseja excluir esta renovação?")) {
                    fetch(`/inscriptions/{{ $inscription->id }}/renovacoes/${renovacaoId}`, {
                        method: "DELETE",
                        headers: {
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(errorData => {
                                throw new Error(errorData.message || "Erro ao excluir renovação.");
                            });
                        }
                        alert("Renovação excluída com sucesso!");
                        location.reload();
                    })
                    .catch(error => {
                        alert("Erro: " + error.message);
                        console.error("Erro ao excluir renovação:", error);
                    });
                }
            };
        });

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
@endpush
