@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="page-title mb-0">Inscrições</h4>
                    <div class="d-flex gap-3 align-items-center">
                        <!-- Seletor de Visualização -->
                        <div class="d-flex align-items-center">
                            <label for="viewMode" class="form-label me-2 mb-0">Visualização:</label>
                            <select id="viewMode" class="form-select form-select-sm" style="width: auto;">
                                <option value="table">Tabela</option>
                                <option value="kanban-status">Kanban por Status</option>
                                <option value="kanban-faixa">Kanban por Faixa de Faturamento</option>
                                <option value="kanban-semana">Kanban por Semana</option>
                                <option value="kanban-fase">Kanban por Fase</option>
                            </select>
                        </div>
                        <a href="{{ route('inscriptions.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nova Inscrição
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Visualização em Tabela (padrão) -->
                    <div id="tableView" class="view-container">
                        <form method="GET" action="{{ route('inscriptions.index') }}" class="row g-2 mb-3">
                            <div class="col-md-3">
                                <input type="text" name="client_name" value="{{ request('client_name') }}" class="form-control" placeholder="Nome do cliente">
                            </div>
                            <div class="col-md-2">
                                <select name="vendor_id" class="form-select">
                                    <option value="">Todos os vendedores</option>
                                    @foreach($vendors ?? [] as $vendor)
                                        <option value="{{ $vendor->id }}" @if(request('vendor_id') == $vendor->id) selected @endif>{{ $vendor->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="product_id" class="form-select">
                                    <option value="">Todos os produtos</option>
                                    @foreach($products ?? [] as $product)
                                        <option value="{{ $product->id }}" @if(request('product_id') == $product->id) selected @endif>{{ $product->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="status" class="form-select">
                                    <option value="">Todos os status</option>
                                    @foreach($statusOptions ?? [] as $key => $label)
                                        <option value="{{ $key }}" @if(request('status') == $key) selected @endif>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="text" name="class_group" value="{{ request('class_group') }}" class="form-control" placeholder="Turma">
                            </div>

                            <div class="col-md-6 mt-2">
                                <div class="input-group">
                                    <span class="input-group-text">De</span>
                                        <input type="text" name="start_date_from" value="{{ old('start_date_from', $displayStartFrom ?? request('start_date_from')) }}" class="form-control date-br" placeholder="dd/mm/yyyy">
                                    <span class="input-group-text">Até</span>
                                        <input type="text" name="start_date_to" value="{{ old('start_date_to', $displayStartTo ?? request('start_date_to')) }}" class="form-control date-br" placeholder="dd/mm/yyyy">
                                </div>
                            </div>

                            <div class="col-md-3 mt-2">
                                <select name="order_by" class="form-select">
                                    <option value="created_at_desc" @if(request('order_by', 'created_at_desc')=='created_at_desc') selected @endif>Criação (mais recentes)</option>
                                    <option value="created_at_asc" @if(request('order_by')=='created_at_asc') selected @endif>Criação (mais antigas)</option>
                                    <option value="date_desc" @if(request('order_by')=='date_desc') selected @endif>Data Início (mais recentes)</option>
                                    <option value="date_asc" @if(request('order_by')=='date_asc') selected @endif>Data Início (mais antigas)</option>
                                    <option value="value_desc" @if(request('order_by')=='value_desc') selected @endif>Valor (maior)</option>
                                    <option value="value_asc" @if(request('order_by')=='value_asc') selected @endif>Valor (menor)</option>
                                    <option value="name_asc" @if(request('order_by')=='name_asc') selected @endif>Nome (A-Z)</option>
                                    <option value="name_desc" @if(request('order_by')=='name_desc') selected @endif>Nome (Z-A)</option>
                                </select>
                            </div>

                            <div class="col-md-2 mt-2">
                                <button type="submit" class="btn btn-outline-primary">Filtrar</button>
                                <a href="{{ route('inscriptions.index') }}" class="btn btn-outline-secondary ms-1">Limpar</a>
                            </div>
                        </form>

                        <form method="POST" action="{{ route('inscriptions.columns') }}" class="mb-3">
                            @csrf
                            <div class="d-flex flex-wrap align-items-center gap-3">
                                <div class="fw-semibold">Colunas visíveis:</div>
                                @foreach($availableColumns as $key => $label)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="{{ $key }}" id="col_{{ $key }}" name="columns[]" @if(in_array($key, $visibleColumns)) checked @endif>
                                        <label class="form-check-label" for="col_{{ $key }}">{{ $label }}</label>
                                    </div>
                                @endforeach
                                <button type="submit" class="btn btn-sm btn-outline-primary">Salvar colunas</button>
                            </div>
                        </form>

                        @php
                            $sortMap = [
                                'client' => ['asc' => 'name_asc', 'desc' => 'name_desc'],
                                'client_email' => null,
                                'product' => ['asc' => 'product_asc', 'desc' => 'product_desc'],
                                'class_group' => ['asc' => 'class_group_asc', 'desc' => 'class_group_desc'],
                                'status' => ['asc' => 'status_asc', 'desc' => 'status_desc'],
                                'vendor' => ['asc' => 'vendor_asc', 'desc' => 'vendor_desc'],
                                'valor_total' => ['asc' => 'value_asc', 'desc' => 'value_desc'],
                                'amount_paid' => ['asc' => 'amount_paid_asc', 'desc' => 'amount_paid_desc'],
                                'payment_method' => null,
                                'start_date' => ['asc' => 'date_asc', 'desc' => 'date_desc'],
                                'calendar_week' => null,
                                'classification' => null,
                                'created_at' => ['asc' => 'created_at_asc', 'desc' => 'created_at_desc'],
                                'preceptors_count' => null,
                                'payments_count' => null,
                                'sessions_count' => null,
                                'diagnostics_count' => null,
                                'achievements_count' => null,
                                'followups_count' => null,
                                'documents_count' => null,
                                'bonuses_count' => null,
                                'faturamentos_count' => null,
                                'renovacoes_count' => null,
                            ];
                            $currentOrder = request('order_by', 'created_at_desc');
                        @endphp
                        <div class="table-responsive" style="overflow-x: auto;">
                            <table class="table table-striped" style="min-width: 1400px;">
                                <thead>
                                    <tr>
                                        @foreach($availableColumns as $key => $label)
                                            @if(in_array($key, $visibleColumns))
                                                @php
                                                    $sortConfig = $sortMap[$key] ?? null;
                                                    $isActive = $sortConfig && in_array($currentOrder, $sortConfig);
                                                    $nextOrder = $sortConfig
                                                        ? (($isActive && $currentOrder === $sortConfig['asc']) ? $sortConfig['desc'] : $sortConfig['asc'])
                                                        : null;
                                                    $icon = 'fa-sort';
                                                    if ($isActive) {
                                                        $icon = str_ends_with($currentOrder, '_asc') ? 'fa-arrow-up' : 'fa-arrow-down';
                                                    }
                                                @endphp
                                                <th>
                                                    @if($sortConfig)
                                                        <a href="{{ request()->fullUrlWithQuery(['order_by' => $nextOrder]) }}" class="text-decoration-none text-reset d-flex align-items-center gap-1">
                                                            <span>{{ $label }}</span>
                                                            <i class="fas {{ $icon }}"></i>
                                                        </a>
                                                    @else
                                                        {{ $label }}
                                                    @endif
                                                </th>
                                            @endif
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($inscriptions as $inscription)
                                        @php
                                            $tooltipContent = 
                                                "<div class='text-start'>" .
                                                "<strong>" . e($inscription->client->name) . "</strong><br>" .
                                                "<small>ID: #" . $inscription->id . "</small><br>" .
                                                "<hr class='my-1'>" .
                                                "Email: " . e($inscription->client->email ?? '-') . "<br>" .
                                                "Produto: " . e($inscription->product->name ?? '-') . "<br>" .
                                                "Vendedor: " . e($inscription->vendor->name ?? '-') . "<br>" .
                                                "Fase: " . e($inscription->classification ?? '-') . "<br>" .
                                                "<hr class='my-1'>" .
                                                "<em>Clique para visualizar</em>" .
                                                "</div>";
                                        @endphp
                                        <tr class="excel-row" 
                                            onclick="window.location.href='{{ route('inscriptions.show', $inscription) }}'" 
                                            data-bs-toggle="tooltip" 
                                            data-bs-html="true" 
                                            data-bs-placement="right"
                                            title="{!! $tooltipContent !!}"
                                            style="cursor: pointer;">
                                            @foreach($availableColumns as $key => $label)
                                                @if(in_array($key, $visibleColumns))
                                                    <td>
                                                        @switch($key)
                                                            @case('client')
                                                                <strong>{{ $inscription->client->name }}</strong>
                                                                @break
                                                            @case('client_email')
                                                                <small class="text-muted">{{ $inscription->client->email ?? '-' }}</small>
                                                                @break
                                                            @case('product')
                                                                {{ $inscription->product->name ?? '-' }}
                                                                @break
                                                            @case('class_group')
                                                                {{ $inscription->class_group ?? '-' }}
                                                                @break
                                                            @case('status')
                                                                <span class="badge {{ $inscription->status_badge_class }}">{{ $inscription->status_label }}</span>
                                                                @break
                                                            @case('vendor')
                                                                {{ $inscription->vendor->name ?? '-' }}
                                                                @break
                                                            @case('valor_total')
                                                                R$ {{ number_format($inscription->valor_total ?? 0, 2, ',', '.') }}
                                                                @break
                                                            @case('amount_paid')
                                                                R$ {{ number_format($inscription->amount_paid ?? 0, 2, ',', '.') }}
                                                                @break
                                                            @case('payment_method')
                                                                {{ $inscription->payment_method ?? '-' }}
                                                                @break
                                                            @case('start_date')
                                                                {{ $inscription->start_date ? $inscription->start_date->format('d/m/Y') : '-' }}
                                                                @break
                                                            @case('calendar_week')
                                                                {{ $inscription->calendar_week ?? '-' }}
                                                                @break
                                                            @case('classification')
                                                                {{ $inscription->classification ?? '-' }}
                                                                @break
                                                            @case('created_at')
                                                                {{ $inscription->created_at ? $inscription->created_at->format('d/m/Y H:i') : '-' }}
                                                                @break
                                                            @case('preceptors_count')
                                                                @php
                                                                    $preceptorTooltip = '<div class="text-start"><strong>Preceptores</strong><br>';
                                                                    if ($inscription->preceptorRecords->count() > 0) {
                                                                        foreach ($inscription->preceptorRecords->take(3) as $p) {
                                                                            $preceptorTooltip .= '<small>• ' . e($p->nome_preceptor ?? 'N/A') . '</small><br>';
                                                                        }
                                                                        if ($inscription->preceptor_records_count > 3) {
                                                                            $preceptorTooltip .= '<small>... +' . ($inscription->preceptor_records_count - 3) . ' mais</small>';
                                                                        }
                                                                    } else {
                                                                        $preceptorTooltip .= '<small>Nenhum registro</small>';
                                                                    }
                                                                    $preceptorTooltip .= '</div>';
                                                                @endphp
                                                                <span class="badge bg-secondary" data-bs-toggle="tooltip" data-bs-html="true" title="{!! $preceptorTooltip !!}">
                                                                    {{ $inscription->preceptor_records_count ?? 0 }}
                                                                </span>
                                                                @break
                                                            @case('payments_count')
                                                                @php
                                                                    $paymentTooltip = '<div class="text-start"><strong>Pagamentos</strong><br>';
                                                                    if ($inscription->payments->count() > 0) {
                                                                        foreach ($inscription->payments->take(3) as $p) {
                                                                            $paymentTooltip .= '<small>• R$ ' . number_format($p->valor ?? 0, 2, ',', '.') . ' - ' . ($p->data_pagamento ? $p->data_pagamento->format('d/m/Y') : 'N/A') . '</small><br>';
                                                                        }
                                                                        if ($inscription->payments_count > 3) {
                                                                            $paymentTooltip .= '<small>... +' . ($inscription->payments_count - 3) . ' mais</small>';
                                                                        }
                                                                    } else {
                                                                        $paymentTooltip .= '<small>Nenhum registro</small>';
                                                                    }
                                                                    $paymentTooltip .= '</div>';
                                                                @endphp
                                                                <span class="badge bg-secondary" data-bs-toggle="tooltip" data-bs-html="true" title="{!! $paymentTooltip !!}">
                                                                    {{ $inscription->payments_count ?? 0 }}
                                                                </span>
                                                                @break
                                                            @case('sessions_count')
                                                                @php
                                                                    $sessionTooltip = '<div class="text-start"><strong>Sessões</strong><br>';
                                                                    if ($inscription->sessions->count() > 0) {
                                                                        foreach ($inscription->sessions->take(3) as $s) {
                                                                            $sessionTooltip .= '<small>• #' . $s->numero_sessao . ' - ' . ($s->status ?? 'N/A') . '</small><br>';
                                                                        }
                                                                        if ($inscription->sessions_count > 3) {
                                                                            $sessionTooltip .= '<small>... +' . ($inscription->sessions_count - 3) . ' mais</small>';
                                                                        }
                                                                    } else {
                                                                        $sessionTooltip .= '<small>Nenhum registro</small>';
                                                                    }
                                                                    $sessionTooltip .= '</div>';
                                                                @endphp
                                                                <span class="badge bg-secondary" data-bs-toggle="tooltip" data-bs-html="true" title="{!! $sessionTooltip !!}">
                                                                    {{ $inscription->sessions_count ?? 0 }}
                                                                </span>
                                                                @break
                                                            @case('diagnostics_count')
                                                                @php
                                                                    $diagTooltip = '<div class="text-start"><strong>Diagnósticos</strong><br>';
                                                                    if ($inscription->diagnostics->count() > 0) {
                                                                        foreach ($inscription->diagnostics->take(3) as $d) {
                                                                            $diagTooltip .= '<small>• ' . ($d->created_at ? $d->created_at->format('d/m/Y') : 'N/A') . '</small><br>';
                                                                        }
                                                                        if ($inscription->diagnostics_count > 3) {
                                                                            $diagTooltip .= '<small>... +' . ($inscription->diagnostics_count - 3) . ' mais</small>';
                                                                        }
                                                                    } else {
                                                                        $diagTooltip .= '<small>Nenhum registro</small>';
                                                                    }
                                                                    $diagTooltip .= '</div>';
                                                                @endphp
                                                                <span class="badge bg-secondary" data-bs-toggle="tooltip" data-bs-html="true" title="{!! $diagTooltip !!}">
                                                                    {{ $inscription->diagnostics_count ?? 0 }}
                                                                </span>
                                                                @break
                                                            @case('achievements_count')
                                                                @php
                                                                    $achievTooltip = '<div class="text-start"><strong>Conquistas</strong><br>';
                                                                    if ($inscription->achievements->count() > 0) {
                                                                        foreach ($inscription->achievements->take(3) as $a) {
                                                                            $achievTooltip .= '<small>• ' . e($a->title ?? 'N/A') . '</small><br>';
                                                                        }
                                                                        if ($inscription->achievements_count > 3) {
                                                                            $achievTooltip .= '<small>... +' . ($inscription->achievements_count - 3) . ' mais</small>';
                                                                        }
                                                                    } else {
                                                                        $achievTooltip .= '<small>Nenhum registro</small>';
                                                                    }
                                                                    $achievTooltip .= '</div>';
                                                                @endphp
                                                                <span class="badge bg-secondary" data-bs-toggle="tooltip" data-bs-html="true" title="{!! $achievTooltip !!}">
                                                                    {{ $inscription->achievements_count ?? 0 }}
                                                                </span>
                                                                @break
                                                            @case('followups_count')
                                                                @php
                                                                    $followTooltip = '<div class="text-start"><strong>Follow-ups</strong><br>';
                                                                    if ($inscription->followUps->count() > 0) {
                                                                        foreach ($inscription->followUps->take(3) as $f) {
                                                                            $followTooltip .= '<small>• ' . ($f->date ? $f->date->format('d/m/Y') : 'N/A') . '</small><br>';
                                                                        }
                                                                        if ($inscription->follow_ups_count > 3) {
                                                                            $followTooltip .= '<small>... +' . ($inscription->follow_ups_count - 3) . ' mais</small>';
                                                                        }
                                                                    } else {
                                                                        $followTooltip .= '<small>Nenhum registro</small>';
                                                                    }
                                                                    $followTooltip .= '</div>';
                                                                @endphp
                                                                <span class="badge bg-secondary" data-bs-toggle="tooltip" data-bs-html="true" title="{!! $followTooltip !!}">
                                                                    {{ $inscription->follow_ups_count ?? 0 }}
                                                                </span>
                                                                @break
                                                            @case('documents_count')
                                                                @php
                                                                    $docTooltip = '<div class="text-start"><strong>Documentos</strong><br>';
                                                                    if ($inscription->documents->count() > 0) {
                                                                        foreach ($inscription->documents->take(3) as $doc) {
                                                                            $docTooltip .= '<small>• ' . e($doc->tipo ?? 'N/A') . '</small><br>';
                                                                        }
                                                                        if ($inscription->documents_count > 3) {
                                                                            $docTooltip .= '<small>... +' . ($inscription->documents_count - 3) . ' mais</small>';
                                                                        }
                                                                    } else {
                                                                        $docTooltip .= '<small>Nenhum registro</small>';
                                                                    }
                                                                    $docTooltip .= '</div>';
                                                                @endphp
                                                                <span class="badge bg-secondary" data-bs-toggle="tooltip" data-bs-html="true" title="{!! $docTooltip !!}">
                                                                    {{ $inscription->documents_count ?? 0 }}
                                                                </span>
                                                                @break
                                                            @case('bonuses_count')
                                                                @php
                                                                    $bonusTooltip = '<div class="text-start"><strong>Bônus</strong><br>';
                                                                    if ($inscription->bonuses->count() > 0) {
                                                                        foreach ($inscription->bonuses->take(3) as $b) {
                                                                            $bonusTooltip .= '<small>• ' . e($b->description ?? 'N/A') . '</small><br>';
                                                                        }
                                                                        if ($inscription->bonuses_count > 3) {
                                                                            $bonusTooltip .= '<small>... +' . ($inscription->bonuses_count - 3) . ' mais</small>';
                                                                        }
                                                                    } else {
                                                                        $bonusTooltip .= '<small>Nenhum registro</small>';
                                                                    }
                                                                    $bonusTooltip .= '</div>';
                                                                @endphp
                                                                <span class="badge bg-secondary" data-bs-toggle="tooltip" data-bs-html="true" title="{!! $bonusTooltip !!}">
                                                                    {{ $inscription->bonuses_count ?? 0 }}
                                                                </span>
                                                                @break
                                                            @case('faturamentos_count')
                                                                @php
                                                                    $fatTooltip = '<div class="text-start"><strong>Faturamentos</strong><br>';
                                                                    if ($inscription->faturamentos->count() > 0) {
                                                                        foreach ($inscription->faturamentos->take(3) as $fat) {
                                                                            $fatTooltip .= '<small>• R$ ' . number_format($fat->valor ?? 0, 2, ',', '.') . '</small><br>';
                                                                        }
                                                                        if ($inscription->faturamentos_count > 3) {
                                                                            $fatTooltip .= '<small>... +' . ($inscription->faturamentos_count - 3) . ' mais</small>';
                                                                        }
                                                                    } else {
                                                                        $fatTooltip .= '<small>Nenhum registro</small>';
                                                                    }
                                                                    $fatTooltip .= '</div>';
                                                                @endphp
                                                                <span class="badge bg-secondary" data-bs-toggle="tooltip" data-bs-html="true" title="{!! $fatTooltip !!}">
                                                                    {{ $inscription->faturamentos_count ?? 0 }}
                                                                </span>
                                                                @break
                                                            @case('renovacoes_count')
                                                                @php
                                                                    $renTooltip = '<div class="text-start"><strong>Renovações</strong><br>';
                                                                    if ($inscription->renovacoes->count() > 0) {
                                                                        foreach ($inscription->renovacoes->take(3) as $ren) {
                                                                            $renTooltip .= '<small>• ' . ($ren->data_renovacao ? $ren->data_renovacao->format('d/m/Y') : 'N/A') . '</small><br>';
                                                                        }
                                                                        if ($inscription->renovacoes_count > 3) {
                                                                            $renTooltip .= '<small>... +' . ($inscription->renovacoes_count - 3) . ' mais</small>';
                                                                        }
                                                                    } else {
                                                                        $renTooltip .= '<small>Nenhum registro</small>';
                                                                    }
                                                                    $renTooltip .= '</div>';
                                                                @endphp
                                                                <span class="badge bg-secondary" data-bs-toggle="tooltip" data-bs-html="true" title="{!! $renTooltip !!}">
                                                                    {{ $inscription->renovacoes_count ?? 0 }}
                                                                </span>
                                                                @break
                                                            @default
                                                                -
                                                        @endswitch
                                                    </td>
                                                @endif
                                            @endforeach
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ count($visibleColumns) }}" class="text-center text-muted">
                                                Nenhuma inscrição cadastrada.
                                                <a href="{{ route('inscriptions.create') }}">Cadastre a primeira inscrição</a>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if(method_exists($inscriptions, 'links'))
                            <div class="d-flex justify-content-center">
                                {{ $inscriptions->links() }}
                            </div>
                        @endif
                    </div>

                    <!-- Visualização Kanban -->
                    <div id="kanbanView" class="view-container" style="display: none;">
                        <div id="kanbanBoard" class="kanban-board">
                            <!-- As colunas serão geradas dinamicamente via JavaScript -->
                        </div>
                    </div>

                    <!-- Loading Spinner -->
                    <div id="loadingSpinner" class="text-center" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Carregando...</span>
                        </div>
                        <p class="mt-2">Carregando dados do Kanban...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.kanban-board {
    display: flex;
    gap: 20px;
    overflow-x: auto;
    padding: 20px 0;
    min-height: 500px;
}

.kanban-column {
    min-width: 300px;
    background-color: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    border: 1px solid #dee2e6;
}

.kanban-column-header {
    background-color: #e9ecef;
    padding: 10px 15px;
    border-radius: 6px;
    margin-bottom: 15px;
    font-weight: 600;
    text-align: center;
    border: 1px solid #ced4da;
}

.kanban-card {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    padding: 12px;
    margin-bottom: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    cursor: pointer;
    transition: all 0.2s ease;
}

.kanban-card:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    transform: translateY(-2px);
}

.kanban-card-title {
    font-weight: 600;
    margin-bottom: 8px;
    color: #495057;
}

.kanban-card-info {
    font-size: 0.875rem;
    color: #6c757d;
    margin-bottom: 4px;
}

.kanban-card-actions {
    margin-top: 10px;
    padding-top: 10px;
    border-top: 1px solid #e9ecef;
}

.view-container {
    transition: opacity 0.3s ease;
}

.gap-3 {
    gap: 1rem !important;
}

/* Excel-style table */
.table.table-striped {
    font-size: 0.75rem;
    margin-bottom: 0;
    table-layout: auto;
}

.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.table-responsive::-webkit-scrollbar {
    height: 8px;
}

.table-responsive::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.table-responsive::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

.table-responsive::-webkit-scrollbar-thumb:hover {
    background: #555;
}

.table thead th {
    background-color: #e8e8e8;
    border: 1px solid #c0c0c0;
    padding: 4px 8px;
    font-weight: 600;
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    white-space: nowrap;
    vertical-align: middle;
}

.table thead th a {
    font-size: 0.7rem;
}

.table tbody td {
    border: 1px solid #d0d0d0;
    padding: 3px 8px;
    vertical-align: middle;
    line-height: 1.3;
}

.table-striped tbody tr:nth-of-type(odd) {
    background-color: #f9f9f9;
}

.table-striped tbody tr:nth-of-type(even) {
    background-color: #ffffff;
}

.excel-row:hover {
    background-color: #e3f2fd !important;
    transition: background-color 0.15s ease;
}

.table tbody tr.excel-row {
    transition: all 0.1s ease;
}

.badge {
    font-size: 0.65rem;
    padding: 2px 6px;
    font-weight: 500;
}

/* Status colors - subtle Excel-like */
.badge.bg-success {
    background-color: #c6efce !important;
    color: #006100 !important;
}

.badge.bg-warning {
    background-color: #ffeb9c !important;
    color: #9c5700 !important;
}

.badge.bg-danger {
    background-color: #ffc7ce !important;
    color: #9c0006 !important;
}

.badge.bg-info {
    background-color: #bdd7ee !important;
    color: #0c5288 !important;
}

.badge.bg-secondary {
    background-color: #e0e0e0 !important;
    color: #505050 !important;
}

/* Compact form controls */
.form-control, .form-select {
    font-size: 0.8rem;
    padding: 0.25rem 0.5rem;
}

.btn-sm {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

/* Tooltips */
.tooltip-inner {
    max-width: 350px;
    text-align: left;
}
</style>

@endsection

@section('scripts')
    <!-- flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <!-- flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <!-- flatpickr locale PT (Português/Brasil) -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/pt.js"></script>
    <!-- Inputmask for date typing mask -->
    <script src="https://cdn.jsdelivr.net/npm/inputmask@5.0.8/dist/inputmask.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const viewModeSelect = document.getElementById('viewMode');
    const tableView = document.getElementById('tableView');
    const kanbanView = document.getElementById('kanbanView');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const kanbanBoard = document.getElementById('kanbanBoard');

    viewModeSelect.addEventListener('change', function() {
        const selectedMode = this.value;
        
        if (selectedMode === 'table') {
            showTableView();
        } else {
            showKanbanView(selectedMode);
        }
    });

    function showTableView() {
        tableView.style.display = 'block';
        kanbanView.style.display = 'none';
        loadingSpinner.style.display = 'none';
    }

    function showKanbanView(mode) {
        tableView.style.display = 'none';
        kanbanView.style.display = 'block';
        loadingSpinner.style.display = 'block';

        // Mapear modo para group_by
        let groupBy = '';
        switch(mode) {
            case 'kanban-status':
                groupBy = 'status';
                break;
            case 'kanban-faixa':
                groupBy = 'faixa_faturamento';
                break;
            case 'kanban-semana':
                groupBy = 'calendar_week';
                break;
            case 'kanban-fase':
                groupBy = 'classification';
                break;
        }

        // Buscar dados via API
        fetch(`/api/inscriptions/kanban?group_by=${groupBy}`)
            .then(response => response.json())
            .then(data => {
                loadingSpinner.style.display = 'none';
                generateKanbanBoard(data.grouped);
            })
            .catch(error => {
                console.error('Erro ao carregar dados do Kanban:', error);
                loadingSpinner.style.display = 'none';
                kanbanBoard.innerHTML = '<div class="alert alert-danger">Erro ao carregar dados do Kanban</div>';
            });
    }

    function generateKanbanBoard(grouped) {
        kanbanBoard.innerHTML = '';
        
        // Criar colunas para cada grupo
        Object.entries(grouped).forEach(([key, group]) => {
            const column = createColumn(key, group.label);
            kanbanBoard.appendChild(column);

            // Adicionar cards das inscrições
            group.items.forEach(inscription => {
                const card = createInscriptionCard(inscription);
                column.appendChild(card);
                updateColumnCount(column); // Mover para depois de adicionar o card
            });
        });
    }

    function createColumn(key, title) {
        const column = document.createElement('div');
        column.className = 'kanban-column';
        column.setAttribute('data-column', key);
        
        const header = document.createElement('div');
        header.className = 'kanban-column-header';
        header.innerHTML = `
            <div>${title}</div>
            <small class="text-muted">0 itens</small>
        `;
        
        column.appendChild(header);
        return column;
    }

    function createInscriptionCard(inscription) {
        const card = document.createElement('div');
        card.className = 'kanban-card';
        card.setAttribute('data-inscription-id', inscription.id);
        
        card.innerHTML = `
            <div class="kanban-card-title">${inscription.client.name}</div>
            <div class="kanban-card-info">
                <strong>Produto:</strong> ${inscription.product.name || '-'}
            </div>
            <div class="kanban-card-info">
                <strong>Valor:</strong> ${inscription.formatted_amount || 'R$ 0,00'}
            </div>
            <div class="kanban-card-info">
                <strong>Status:</strong> 
                <span class="badge ${inscription.status_badge_class}">${inscription.status_label}</span>
            </div>
            ${inscription.class_group ? `<div class="kanban-card-info"><strong>Turma:</strong> ${inscription.class_group}</div>` : ''}
            ${inscription.vendor.name ? `<div class="kanban-card-info"><strong>Vendedor:</strong> ${inscription.vendor.name}</div>` : ''}
            <div class="kanban-card-actions">
                <a href="/inscriptions/${inscription.id}" class="btn btn-sm btn-primary">
                    <i class="fas fa-eye"></i> Ver
                </a>
                <a href="/inscriptions/${inscription.id}/edit" class="btn btn-sm btn-warning">
                    <i class="fas fa-edit"></i> Editar
                </a>
            </div>
        `;
        
        return card;
    }

    function updateColumnCount(column) {
        const cards = column.querySelectorAll('.kanban-card');
        const countElement = column.querySelector('.kanban-column-header small');
        if (countElement) {
            countElement.textContent = `${cards.length} ${cards.length === 1 ? 'item' : 'itens'}`;
        }
    }

    // Inicializar flatpickr nos campos de data com formato brasileiro
    if (window.flatpickr) {
        flatpickr('.date-br', {
            dateFormat: 'd/m/Y',
            allowInput: true,
            locale: 'pt'
        });
    }
    // Inicializar máscara de entrada para dd/mm/YYYY
    if (window.Inputmask) {
        var elements = document.querySelectorAll('.date-br');
        Inputmask({'mask': '99/99/9999', 'placeholder': 'dd/mm/aaaa', 'clearIncomplete': false}).mask(elements);
    }

    // Inicializar tooltips do Bootstrap para linhas da tabela
    if (typeof bootstrap !== 'undefined') {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl, {
                html: true,
                trigger: 'hover focus'
            });
        });
    }

});
</script>
@endsection

