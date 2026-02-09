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

                        <!-- Área de Agrupamento de Campos -->
                        <div class="grouping-zone mb-3" id="groupingZone">
                            <div class="grouping-header">
                                <i class="fas fa-layer-group"></i>
                                <span>Arraste colunas aqui para agrupar</span>
                            </div>
                            <div class="grouping-items" id="groupingItems">
                                <!-- Grupos ativos aparecerão aqui -->
                            </div>
                        </div>

                        <!-- Área de Ordenação de Campos -->
                        <div class="grouping-zone sorting-zone mb-3" id="sortingZone">
                            <div class="grouping-header">
                                <i class="fas fa-sort-amount-down"></i>
                                <span>Arraste colunas aqui para ordenar (ordenação em cascata)</span>
                            </div>
                            <div class="grouping-items" id="sortingItems">
                                <!-- Ordenações ativas aparecerão aqui -->
                            </div>
                        </div>

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
                                                <th data-column="{{ $key }}" 
                                                    data-label="{{ $label }}"
                                                    class="draggable-column" 
                                                    draggable="true">
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

/* Grouping Zone */
.grouping-zone {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 8px;
    padding: 12px 16px;
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
    transition: all 0.3s ease;
}

.grouping-zone.drag-over {
    background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.5);
    transform: scale(1.02);
}

.grouping-header {
    color: white;
    font-size: 0.85rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 8px;
}

.grouping-header i {
    font-size: 1rem;
}

.grouping-items {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    min-height: 20px;
}

.grouping-items:empty::before {
    content: '';
    display: block;
    width: 100%;
}

.group-item {
    background: rgba(255, 255, 255, 0.95);
    color: #333;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    cursor: move;
    transition: all 0.2s ease;
}

.group-item:hover {
    background: white;
    box-shadow: 0 3px 6px rgba(0,0,0,0.15);
}

.group-item .group-type {
    background: #667eea;
    color: white;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 0.65rem;
}

.group-item .remove-group {
    background: #ff4444;
    color: white;
    border: none;
    border-radius: 50%;
    width: 18px;
    height: 18px;
    font-size: 10px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    transition: all 0.2s ease;
}

.group-item .remove-group:hover {
    background: #cc0000;
    transform: scale(1.1);
}

.grouping-options {
    margin-top: 4px;
    font-size: 0.7rem;
    color: #666;
}

.grouping-options select {
    font-size: 0.7rem;
    padding: 2px 4px;
    border-radius: 3px;
    border: 1px solid #ddd;
    margin-left: 4px;
}

/* Draggable columns */
.draggable-column {
    cursor: move;
    position: relative;
    user-select: none;
}

.draggable-column:hover::after {
    content: '⋮⋮';
    position: absolute;
    left: 4px;
    top: 50%;
    transform: translateY(-50%);
    color: #999;
    font-size: 0.8rem;
    pointer-events: none;
}

.draggable-column.dragging {
    opacity: 0.5;
}

/* Grouped rows */
.group-header-row {
    background: linear-gradient(90deg, #f0f0f0 0%, #f8f8f8 100%);
    font-weight: 600;
    cursor: pointer;
    border-left: 4px solid #667eea !important;
}

.group-header-row:hover {
    background: linear-gradient(90deg, #e8e8e8 0%, #f0f0f0 100%);
}

.group-header-row td {
    padding: 8px !important;
}

.group-header-row[data-level="1"] td {
    padding-left: 28px !important;
}

.group-header-row[data-level="2"] td {
    padding-left: 44px !important;
}

.group-header-row[data-level="3"] td {
    padding-left: 60px !important;
}

.group-toggle {
    display: inline-block;
    width: 16px;
    text-align: center;
    margin-right: 8px;
    transition: transform 0.2s ease;
}

.group-toggle.collapsed {
    transform: rotate(-90deg);
}

.grouped-row {
    display: none;
}

.grouped-row.show {
    display: table-row;
}

.grouped-row td:first-child {
    padding-left: 30px !important;
}

.group-count {
    background: #667eea;
    color: white;
    padding: 2px 8px;
    border-radius: 10px;
    font-size: 0.7rem;
    margin-left: 8px;
}

.sorting-zone {
    background: linear-gradient(90deg, #5a67d8 0%, #9f7aea 100%);
}

.sorting-zone .group-item {
    background: #fff;
    border: 1px dashed rgba(0,0,0,0.08);
}

.sort-direction {
    border: 0;
    background: #e2e8f0;
    color: #1a202c;
    padding: 2px 8px;
    border-radius: 6px;
    font-size: 0.75rem;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    cursor: pointer;
}

.sort-item-index {
    background: #edf2f7;
    color: #2d3748;
    padding: 2px 8px;
    border-radius: 12px;
    margin-right: 6px;
    font-weight: 600;
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

    const groupingStateKey = 'inscriptionsGroupingState';
    const isGroupingAll = {{ ($groupingAll ?? false) ? 'true' : 'false' }};
    const initialSortStack = @json($sortStack ?? []);
    const paginationInfo = {
        hasPages: {{ (method_exists($inscriptions, 'hasPages') && $inscriptions->hasPages()) ? 'true' : 'false' }},
        total: {{ method_exists($inscriptions, 'total') ? $inscriptions->total() : (is_countable($inscriptions) ? count($inscriptions) : 0) }},
        count: {{ is_countable($inscriptions) ? count($inscriptions) : 0 }}
    };
    let isRestoringState = false;

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

    // ========== SISTEMA DE AGRUPAMENTO DE CAMPOS ==========
    
    // Definir tipos de colunas para diferentes opções de agrupamento
    const columnTypes = {
        'client': { type: 'text', label: 'Cliente' },
        'client_email': { type: 'text', label: 'E-mail' },
        'product': { type: 'text', label: 'Produto' },
        'class_group': { type: 'text', label: 'Turma' },
        'status': { type: 'text', label: 'Status' },
        'vendor': { type: 'text', label: 'Vendedor' },
        'valor_total': { type: 'number', label: 'Valor Total' },
        'amount_paid': { type: 'number', label: 'Valor Pago' },
        'payment_method': { type: 'text', label: 'Forma de Pagamento' },
        'start_date': { type: 'date', label: 'Data Início' },
        'calendar_week': { type: 'number', label: 'Semana' },
        'classification': { type: 'text', label: 'Fase' },
        'created_at': { type: 'date', label: 'Criado em' },
        'preceptors_count': { type: 'number', label: 'Preceptores' },
        'payments_count': { type: 'number', label: 'Pagamentos' },
        'sessions_count': { type: 'number', label: 'Sessões' },
        'diagnostics_count': { type: 'number', label: 'Diagnósticos' },
        'achievements_count': { type: 'number', label: 'Conquistas' },
        'followups_count': { type: 'number', label: 'Follow-ups' },
        'documents_count': { type: 'number', label: 'Documentos' },
        'bonuses_count': { type: 'number', label: 'Bônus' },
        'faturamentos_count': { type: 'number', label: 'Faturamentos' },
        'renovacoes_count': { type: 'number', label: 'Renovações' }
    };

    let activeGroups = [];
    let collapsedGroups = new Set();
    let draggedColumn = null;
    let activeSorts = [];
    let sortsTouchedManually = false;

    // Habilitar drag nas colunas
    const columns = document.querySelectorAll('.draggable-column');
    columns.forEach(column => {
        column.addEventListener('dragstart', handleDragStart);
        column.addEventListener('dragend', handleDragEnd);
    });

    // Configurar drop zones
    const groupingZone = document.getElementById('groupingZone');
    const groupingItems = document.getElementById('groupingItems');
    const sortingZone = document.getElementById('sortingZone');
    const sortingItems = document.getElementById('sortingItems');
    
    if (groupingZone) {
        groupingZone.addEventListener('dragover', handleDragOver);
        groupingZone.addEventListener('dragleave', handleDragLeave);
        groupingZone.addEventListener('drop', handleDrop);
    }

    if (sortingZone) {
        sortingZone.addEventListener('dragover', handleDragOverSort);
        sortingZone.addEventListener('dragleave', handleDragLeaveSort);
        sortingZone.addEventListener('drop', handleDropSort);
    }

    function saveGroupingState() {
        try {
            localStorage.setItem(groupingStateKey, JSON.stringify(activeGroups));
        } catch (e) {
            console.warn('Nao foi possivel salvar o estado de agrupamento', e);
        }
    }

    function restoreGroupingState() {
        const raw = localStorage.getItem(groupingStateKey);
        if (!raw) return;
        let parsed = [];
        try {
            parsed = JSON.parse(raw) || [];
        } catch (e) {
            console.warn('Nao foi possivel restaurar agrupamentos salvos', e);
            return;
        }
        if (!Array.isArray(parsed) || parsed.length === 0) return;
        isRestoringState = true;
        parsed.forEach(g => {
            if (!g || !g.key) return;
            const label = (columnTypes[g.key]?.label) || g.label || g.key;
            addGroup(g.key, label, { restore: true, mode: g.mode });
        });
        isRestoringState = false;
        if (!isGroupingAll && paginationInfo.hasPages && activeGroups.length > 0) {
            ensureDefaultSortsFromGroups();
            handleGroupingPaginationSwitch();
        }
    }

    function handleGroupingPaginationSwitch() {
        if (activeGroups.length === 0) return;
        if (isGroupingAll) return;
        if (!paginationInfo.hasPages) return;

        ensureDefaultSortsFromGroups();

        const params = new URLSearchParams(window.location.search);
        params.set('grouping_all', '1');
        params.delete('page');
        saveGroupingState();
        applySortParams(params);
        window.location.search = params.toString();
    }

    function handleGroupingPaginationResetIfNeeded() {
        if (!isGroupingAll) return;
        if (activeGroups.length > 0) return;

        const params = new URLSearchParams(window.location.search);
        params.delete('grouping_all');
        params.delete('page');
        localStorage.removeItem(groupingStateKey);
        if (!sortsTouchedManually) {
            activeSorts = [];
            applySortParams(params);
        }
        window.location.search = params.toString();
    }

    function handleDragStart(e) {
        draggedColumn = {
            key: this.dataset.column,
            label: this.dataset.label
        };
        this.classList.add('dragging');
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/html', this.innerHTML);
    }

    function handleDragEnd(e) {
        this.classList.remove('dragging');
    }

    function handleDragOver(e) {
        if (e.preventDefault) {
            e.preventDefault();
        }
        e.dataTransfer.dropEffect = 'move';
        groupingZone.classList.add('drag-over');
        return false;
    }

    function handleDragLeave(e) {
        groupingZone.classList.remove('drag-over');
    }

    function handleDrop(e) {
        if (e.stopPropagation) {
            e.stopPropagation();
        }
        e.preventDefault();
        
        groupingZone.classList.remove('drag-over');
        
        if (draggedColumn && !activeGroups.find(g => g.key === draggedColumn.key)) {
            addGroup(draggedColumn.key, draggedColumn.label);
        }
        
        return false;
    }

    function handleDragOverSort(e) {
        if (e.preventDefault) {
            e.preventDefault();
        }
        e.dataTransfer.dropEffect = 'move';
        sortingZone.classList.add('drag-over');
        return false;
    }

    function handleDragLeaveSort(e) {
        sortingZone.classList.remove('drag-over');
    }

    function handleDropSort(e) {
        if (e.stopPropagation) {
            e.stopPropagation();
        }
        e.preventDefault();
        sortingZone.classList.remove('drag-over');
        if (draggedColumn) {
            addSort(draggedColumn.key, draggedColumn.label);
        }
        return false;
    }

    function addGroup(columnKey, columnLabel, options = {}) {
        if (activeGroups.find(g => g.key === columnKey)) return;
        const columnType = columnTypes[columnKey] || { type: 'text', label: columnLabel };
        
        const groupItem = document.createElement('div');
        groupItem.className = 'group-item';
        groupItem.dataset.column = columnKey;
        
        let groupingOptions = getGroupingOptions(columnType.type);
        
        groupItem.innerHTML = `
            <span class="group-type">${columnType.type === 'number' ? '#' : (columnType.type === 'date' ? '📅' : 'Abc')}</span>
            <strong>${columnLabel}</strong>
            ${groupingOptions}
            <button class="remove-group" onclick="removeGroup('${columnKey}')" title="Remover agrupamento">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        groupingItems.appendChild(groupItem);
        const select = groupItem.querySelector('.grouping-mode');
        const modeToUse = options.mode || getDefaultGroupMode(columnType.type);
        if (select) {
            select.value = modeToUse;
        }
        activeGroups.push({
            key: columnKey,
            label: columnLabel,
            type: columnType.type,
            mode: modeToUse
        });
        saveGroupingState();
        if (!options.restore) {
            handleGroupingPaginationSwitch();
        }
        applyGrouping();
        ensureDefaultSortsFromGroups();
    }

    function getGroupingOptions(type) {
        if (type === 'number') {
            return `
                <select class="grouping-mode" onchange="updateGroupMode(this)">
                    <option value="exact">Valor exato</option>
                    <option value="range">Por faixa</option>
                    <option value="count">Por quantidade</option>
                </select>
            `;
        } else if (type === 'date') {
            return `
                <select class="grouping-mode" onchange="updateGroupMode(this)">
                    <option value="exact">Data exata</option>
                    <option value="day">Por dia</option>
                    <option value="month">Por mês</option>
                    <option value="year">Por ano</option>
                </select>
            `;
        } else {
            return `
                <select class="grouping-mode" onchange="updateGroupMode(this)">
                    <option value="exact">Texto exato</option>
                    <option value="starts">Iniciado em</option>
                    <option value="ends">Finalizado em</option>
                    <option value="contains">Contém</option>
                </select>
            `;
        }
    }

    function getDefaultGroupMode(type) {
        if (type === 'number') return 'exact';
        if (type === 'date') return 'month';
        return 'exact';
    }

    window.updateGroupMode = function(selectElement) {
        const groupItem = selectElement.closest('.group-item');
        const columnKey = groupItem.dataset.column;
        const group = activeGroups.find(g => g.key === columnKey);
        if (group) {
            group.mode = selectElement.value;
            saveGroupingState();
            applyGrouping();
        }
    };

    window.removeGroup = function(columnKey) {
        activeGroups = activeGroups.filter(g => g.key !== columnKey);
        const groupItem = document.querySelector(`.group-item[data-column="${columnKey}"]`);
        if (groupItem) {
            groupItem.remove();
        }
        saveGroupingState();
        handleGroupingPaginationResetIfNeeded();
        applyGrouping();
        if (!sortsTouchedManually) {
            activeSorts = activeSorts.filter(s => s.key !== columnKey);
            renderSortingItems();
        }
    };

    function applyGrouping() {
        const tableBody = document.querySelector('.table tbody');
        if (!tableBody) return;

        // Remover headers antigos e resetar linhas
        document.querySelectorAll('.group-header-row').forEach(h => h.remove());
        const allRows = Array.from(document.querySelectorAll('.excel-row'));
        allRows.forEach(row => {
            row.classList.remove('grouped-row', 'show');
            row.dataset.groupPath = '';
            row.style.display = '';
        });

        if (activeGroups.length === 0) {
            collapsedGroups.clear();
            return;
        }

        collapsedGroups.clear();

        const groupedStructure = buildGroupStructure(allRows, 0);

        // Render headers and tag rows with group paths
        renderGroupLevel(groupedStructure, 0, '');

        const headerPaths = Array.from(document.querySelectorAll('.group-header-row'))
            .map(h => h.dataset.groupPath)
            .filter(Boolean);
        collapsedGroups = new Set(headerPaths);

        refreshVisibility();
    }

    function buildGroupStructure(rows, level) {
        if (level >= activeGroups.length) return [];
        const group = activeGroups[level];
        const map = new Map();

        rows.forEach(row => {
            const key = getGroupKeyForRow(row, group);
            if (!map.has(key)) {
                map.set(key, []);
            }
            map.get(key).push(row);
        });

        const result = [];
        map.forEach((rowsForGroup, key) => {
            const entry = {
                key,
                rows: rowsForGroup,
                count: rowsForGroup.length
            };
            if (level < activeGroups.length - 1) {
                entry.children = buildGroupStructure(rowsForGroup, level + 1);
            }
            result.push(entry);
        });

        return result;
    }

    function renderGroupLevel(groups, level, parentPath) {
        const displayLevel = level + 1;
        groups.forEach(group => {
            const safeKey = encodeURIComponent(group.key || '(Vazio)');
            const groupPath = parentPath ? `${parentPath}||${safeKey}` : safeKey;
            const firstRow = findFirstRow(group);
            const headerRow = document.createElement('tr');
            headerRow.className = 'group-header-row';
            headerRow.dataset.groupPath = groupPath;
            headerRow.dataset.level = displayLevel;
            headerRow.innerHTML = `
                <td colspan="100%">
                    <span class="group-toggle">▼</span>
                    <strong>${activeGroups[level]?.label ? `${activeGroups[level].label}: ` : ''}${group.key || '(Vazio)'}</strong>
                    <span class="group-count">${group.count}</span>
                </td>
            `;

            firstRow.parentNode.insertBefore(headerRow, firstRow);

            headerRow.addEventListener('click', () => toggleGroup(groupPath, headerRow));

            if (group.children && group.children.length) {
                renderGroupLevel(group.children, level + 1, groupPath);
            } else {
                group.rows.forEach(row => {
                    row.classList.add('grouped-row');
                    row.dataset.groupPath = groupPath;
                });
            }
        });
    }

    function findFirstRow(group) {
        if (group.children && group.children.length) {
            return findFirstRow(group.children[0]);
        }
        return group.rows[0];
    }

    function toggleGroup(groupPath) {
        if (collapsedGroups.has(groupPath)) {
            collapsedGroups.delete(groupPath);
        } else {
            collapsedGroups.add(groupPath);
        }
        refreshVisibility();
    }

    function hasCollapsedAncestor(path, includeSelf = true) {
        if (!path) return false;
        const parts = path.split('||');
        let current = '';
        for (let i = 0; i < parts.length; i++) {
            current = current ? `${current}||${parts[i]}` : parts[i];
            const isSelf = i === parts.length - 1;
            if (!includeSelf && isSelf) continue;
            if (collapsedGroups.has(current)) return true;
        }
        return false;
    }

    function refreshVisibility() {
        const headers = Array.from(document.querySelectorAll('.group-header-row'));
        const rows = Array.from(document.querySelectorAll('.grouped-row'));

        headers.forEach(header => {
            const path = header.dataset.groupPath || '';
            const isCollapsed = collapsedGroups.has(path);
            const hidden = hasCollapsedAncestor(path, false);
            header.style.display = hidden ? 'none' : '';
            const toggle = header.querySelector('.group-toggle');
            if (toggle) {
                toggle.classList.toggle('collapsed', isCollapsed);
            }
        });

        rows.forEach(row => {
            const path = row.dataset.groupPath || '';
            const hidden = hasCollapsedAncestor(path);
            row.classList.toggle('show', !hidden);
        });
    }

    // ====== ORDENACAO EM CASCATA ======

    function renderSortingItems() {
        sortingItems.innerHTML = '';
        activeSorts.forEach((sort, index) => {
            const item = document.createElement('div');
            item.className = 'group-item sort-item';
            item.dataset.column = sort.key;
            item.innerHTML = `
                <span class="sort-item-index">${index + 1}</span>
                <strong>${sort.label}</strong>
                <button class="sort-direction" onclick="toggleSortDirection('${sort.key}')">
                    <i class="fas ${sort.dir === 'desc' ? 'fa-arrow-down' : 'fa-arrow-up'}"></i>
                    ${sort.dir === 'desc' ? 'Desc' : 'Asc'}
                </button>
                <button class="remove-group" onclick="removeSort('${sort.key}')" title="Remover ordenação">
                    <i class="fas fa-times"></i>
                </button>
            `;
            sortingItems.appendChild(item);
        });
    }

    function addSort(columnKey, columnLabel, dir = 'asc', options = {}) {
        if (activeSorts.find(s => s.key === columnKey)) return;
        const label = columnLabel || (columnTypes[columnKey]?.label) || columnKey;
        activeSorts.push({ key: columnKey, label, dir });
        sortsTouchedManually = options.restore ? sortsTouchedManually : true;
        renderSortingItems();
        if (!options.skipReload) {
            syncSortParamsAndReload();
        }
    }

    window.toggleSortDirection = function(columnKey) {
        const sort = activeSorts.find(s => s.key === columnKey);
        if (!sort) return;
        sort.dir = sort.dir === 'desc' ? 'asc' : 'desc';
        sortsTouchedManually = true;
        renderSortingItems();
        syncSortParamsAndReload();
    };

    window.removeSort = function(columnKey) {
        activeSorts = activeSorts.filter(s => s.key !== columnKey);
        renderSortingItems();
        sortsTouchedManually = true;
        syncSortParamsAndReload();
    };

    function ensureDefaultSortsFromGroups() {
        if (sortsTouchedManually) return;
        if (activeGroups.length === 0) return;
        const existingKeys = new Set(activeSorts.map(s => s.key));
        const newSorts = [];
        activeGroups.forEach(g => {
            if (!existingKeys.has(g.key)) {
                newSorts.push({ key: g.key, label: g.label, dir: 'asc' });
            }
        });
        if (newSorts.length === 0 && activeSorts.length > 0) return;
        activeSorts = [...activeGroups.map(g => ({ key: g.key, label: g.label, dir: 'asc' }))];
        renderSortingItems();
    }

    function applySortParams(params) {
        params.delete('sort_stack');
        params.delete('sort_stack[]');
        params.delete('order_by');
        activeSorts.forEach(s => {
            params.append('sort_stack[]', `${s.key}:${s.dir}`);
        });
    }

    function syncSortParamsAndReload() {
        const params = new URLSearchParams(window.location.search);
        applySortParams(params);
        params.delete('page');
        window.location.search = params.toString();
    }

    function getGroupKeyForRow(row, group) {
        const columnIndex = getColumnIndex(group.key);
        if (columnIndex === -1) return '(Desconhecido)';
        
        const cell = row.cells[columnIndex];
        if (!cell) return '(Vazio)';
        
        let value = cell.textContent.trim();
        if (!value) return '(Vazio)';
        
        // Aplicar modo de agrupamento
        if (group.type === 'number') {
            const num = parseFloat(value.replace(/[^\d,.-]/g, '').replace(',', '.'));
            if (isNaN(num)) return '(Vazio)';
            
            if (group.mode === 'range') {
                if (num < 1000) return '< R$ 1.000';
                if (num < 5000) return 'R$ 1.000 - R$ 5.000';
                if (num < 10000) return 'R$ 5.000 - R$ 10.000';
                return '> R$ 10.000';
            } else if (group.mode === 'count') {
                return `Qtd: ${num}`;
            }
            return value;
        } else if (group.type === 'date') {
            if (group.mode === 'month') {
                const match = value.match(/(\d{2})\/(\d{2})\/(\d{4})/);
                if (match) {
                    const months = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
                    return `${months[parseInt(match[2]) - 1]}/${match[3]}`;
                }
            } else if (group.mode === 'year') {
                const match = value.match(/(\d{4})/);
                if (match) return match[1];
            }
            return value;
        } else {
            // Texto
            if (group.mode === 'starts') {
                return value.charAt(0).toUpperCase();
            } else if (group.mode === 'ends') {
                return value.charAt(value.length - 1).toUpperCase();
            } else if (group.mode === 'contains') {
                return value.substring(0, 3).toUpperCase();
            }
            return value;
        }
    }
    
    function getColumnIndex(columnKey) {
        const headers = Array.from(document.querySelectorAll('.table thead th'));
        return headers.findIndex(h => h.dataset.column === columnKey);
    }

    // inicializar ordenações vindas do backend
    if (Array.isArray(initialSortStack) && initialSortStack.length > 0) {
        activeSorts = initialSortStack.map(item => {
            if (typeof item === 'string') {
                const [key, dirRaw] = item.split(':');
                const dir = dirRaw === 'desc' ? 'desc' : 'asc';
                return { key, dir, label: (columnTypes[key]?.label) || key };
            }
            if (item && item.key) {
                return {
                    key: item.key,
                    dir: item.dir === 'desc' ? 'desc' : 'asc',
                    label: (columnTypes[item.key]?.label) || item.label || item.key,
                };
            }
            return null;
        }).filter(Boolean);
        sortsTouchedManually = activeSorts.length > 0;
        renderSortingItems();
    }

    restoreGroupingState();

    // Se não houver ordenação prévia mas houver agrupamento ativo (no front), gerar padrão
    ensureDefaultSortsFromGroups();
    renderSortingItems();
});
</script>
@endsection

