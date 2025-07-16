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
                            </ul>

                            <!-- Tab panes -->
                            <div class="tab-content mt-3" id="registrosTabContent">
                                <!-- Preceptores Tab -->
                                <div class="tab-pane fade show active" id="preceptores" role="tabpanel">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0">Registros de Preceptores</h6>
                                        <button class="btn btn-sm btn-primary" onclick="abrirModalPreceptor()">
                                            <i class="fas fa-plus"></i> Novo Preceptor
                                        </button>
                                    </div>
                                    
                                    @if($inscription->preceptorRecords->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Nome</th>
                                                        <th>CRM</th>
                                                        <th>Especialidade</th>
                                                        <th>Data</th>
                                                        <th>Ações</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($inscription->preceptorRecords as $record)
                                                    <tr>
                                                        <td>{{ $record->nome_preceptor ?? 'N/A' }}</td>
                                                        <td>{{ $record->crm ?? 'N/A' }}</td>
                                                        <td>{{ $record->especialidade ?? 'N/A' }}</td>
                                                        <td>{{ $record->created_at->format('d/m/Y') }}</td>
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
@endsection

@section('scripts')
<script>
function abrirModalPreceptor() {
    $('#modalPreceptor').modal('show');
}

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
                location.reload();
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

// Submissão dos formulários via AJAX
$(document).ready(function() {
    $('.form-modal').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const formData = new FormData(this);
        const url = form.attr('action');
        
        fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                // Mostrar erros de validação
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').remove();
                
                if (data.errors) {
                    Object.keys(data.errors).forEach(field => {
                        const input = form.find(`[name="${field}"]`);
                        input.addClass('is-invalid');
                        input.after(`<div class="invalid-feedback">${data.errors[field][0]}</div>`);
                    });
                }
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao salvar registro');
        });
    });
});
</script>
