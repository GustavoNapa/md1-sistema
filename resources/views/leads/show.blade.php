@extends('layouts.app')

@section('content')
<style>
.lead-header {
    background: white;
    border-bottom: 1px solid #dee2e6;
    padding: 1.5rem 0;
    margin-bottom: 0;
}

.lead-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    font-weight: 600;
}

.lead-actions {
    display: flex;
    gap: 0.5rem;
}

.action-btn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: 1px solid #dee2e6;
    background: white;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
}

.action-btn:hover {
    background: #f8f9fa;
    border-color: #0d6efd;
    color: #0d6efd;
}

.lead-tabs {
    background: white;
    border-bottom: 1px solid #dee2e6;
}

.lead-tabs .nav-link {
    border: none;
    border-bottom: 3px solid transparent;
    color: #6c757d;
    padding: 1rem 1.5rem;
    font-weight: 500;
}

.lead-tabs .nav-link.active {
    border-bottom-color: #0d6efd;
    color: #0d6efd;
    background: transparent;
}

.lead-tabs .nav-link:hover {
    border-bottom-color: #0d6efd;
    color: #0d6efd;
}

.pipeline-progress {
    padding: 2rem 0;
    background: white;
}

.stage-step {
    position: relative;
    text-align: center;
    flex: 1;
}

.stage-number {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: #e9ecef;
    color: #6c757d;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 0.5rem;
    font-weight: 600;
    position: relative;
    z-index: 2;
}

.stage-step.active .stage-number {
    background: #0d6efd;
    color: white;
}

.stage-step.completed .stage-number {
    background: #6610f2;
    color: white;
}

.stage-line {
    position: absolute;
    top: 25px;
    left: 50%;
    right: -50%;
    height: 3px;
    background: #e9ecef;
    z-index: 1;
}

.stage-step.completed .stage-line {
    background: #6610f2;
}

.stage-step:last-child .stage-line {
    display: none;
}

.info-label {
    color: #6c757d;
    font-size: 0.875rem;
    margin-bottom: 0.25rem;
}

.info-value {
    color: #212529;
    font-weight: 500;
}

.timeline-item {
    position: relative;
    padding-left: 2rem;
    padding-bottom: 1.5rem;
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: 7px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item:last-child::before {
    display: none;
}

.timeline-dot {
    position: absolute;
    left: 0;
    top: 0;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: #0d6efd;
    border: 3px solid white;
    box-shadow: 0 0 0 1px #dee2e6;
}

.empty-state-timeline {
    text-align: center;
    padding: 3rem 1rem;
    color: #6c757d;
}
</style>

<div class="container-fluid p-0">
    <!-- Header do Lead -->
    <div class="lead-header">
        <div class="container-fluid px-4">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('leads.index', ['pipeline_id' => $lead->pipeline_id]) }}">
                            {{ $lead->pipeline->name }}
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('leads.index', ['pipeline_id' => $lead->pipeline_id]) }}">Leads</a>
                    </li>
                    <li class="breadcrumb-item active">{{ $lead->name }}</li>
                </ol>
            </nav>

            <!-- Informações principais -->
            <div class="d-flex align-items-start justify-content-between">
                <div class="d-flex align-items-start gap-3">
                    <div class="lead-avatar">
                        {{ strtoupper(substr($lead->name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <h3 class="mb-0">{{ $lead->name }}</h3>
                            @if($lead->user)
                                <span class="badge bg-secondary">
                                    <i class="fas fa-user me-1"></i>{{ $lead->user->name }}
                                </span>
                            @endif
                        </div>
                        <div class="d-flex gap-2 mb-2">
                            <span class="badge" style="background-color: {{ $lead->pipeline->color }}">
                                {{ $lead->pipeline->name }}
                            </span>
                            <span class="badge" style="background-color: {{ $lead->stage->color }}">
                                {{ $lead->stage->name }}
                            </span>
                            <span class="badge bg-info">{{ $lead->origin_label }}</span>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('leads.edit', $lead) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                        <a href="{{ route('leads.index', ['pipeline_id' => $lead->pipeline_id]) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="lead-tabs">
        <div class="container-fluid px-4">
            <ul class="nav nav-tabs border-0" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#contato">
                        <i class="fas fa-address-card me-2"></i>Contato
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#historico">
                        <i class="fas fa-history me-2"></i>Histórico
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Conteúdo das Tabs -->
    <div class="container-fluid px-4 py-4">
        <div class="tab-content">
            <!-- Aba Contato -->
            <div class="tab-pane fade show active" id="contato">
                <!-- Cabeçalho com botões -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Campos de contato</h5>
                    <div>
                        <button type="button" class="btn btn-sm btn-outline-secondary me-2" id="toggleEmptyFields">
                            <i class="fas fa-eye-slash"></i> Ocultar Campos vazios
                        </button>
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#manageFieldsModal">
                            <i class="fas fa-cog"></i> Gerenciar campos
                        </button>
                    </div>
                </div>

                <!-- Grupos de campos -->
                @foreach($fieldGroups as $group)
                    <div class="card mb-3 field-group-card">
                        <div class="card-header bg-light">
                            <button class="btn btn-link text-decoration-none text-dark w-100 text-start d-flex justify-content-between align-items-center p-0" 
                                    type="button" 
                                    data-bs-toggle="collapse" 
                                    data-bs-target="#group-{{ $group->id }}">
                                <span><i class="fas fa-chevron-down me-2"></i> {{ $group->name }}</span>
                            </button>
                        </div>
                        <div class="collapse show" id="group-{{ $group->id }}">
                            <div class="card-body">
                                <div class="row">
                                    @foreach($group->customFields as $field)
                                        @php
                                            $isEmpty = false;
                                            if ($field->is_system) {
                                                if ($field->identifier === 'name') {
                                                    $isEmpty = empty($lead->name);
                                                } elseif ($field->identifier === 'email') {
                                                    $isEmpty = empty($lead->email);
                                                } elseif ($field->identifier === 'phone') {
                                                    $isEmpty = empty($lead->phone);
                                                }
                                            } else {
                                                $isEmpty = empty($field->value);
                                            }
                                        @endphp
                                        <div class="col-md-6 mb-3 field-item {{ $isEmpty ? 'empty-field' : '' }}">
                                            <div class="info-label">{{ $field->name }}</div>
                                            <div class="info-value">
                                                @if($field->is_system)
                                                    @if($field->identifier === 'name')
                                                        {{ $lead->name }}
                                                    @elseif($field->identifier === 'email')
                                                        {{ $lead->email ?? 'Não informado' }}
                                                    @elseif($field->identifier === 'phone')
                                                        {{ $lead->formatted_phone }}
                                                        @if($lead->is_whatsapp)
                                                            <i class="fab fa-whatsapp text-success ms-1"></i>
                                                        @endif
                                                    @endif
                                                @else
                                                    @if($field->type === 'monetary')
                                                        {{ $field->value ? 'R$ ' . number_format((float)$field->value, 2, ',', '.') : 'Clique aqui para adicionar' }}
                                                    @else
                                                        {{ $field->value ?? 'Clique aqui para adicionar' }}
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                @if($lead->notes)
                    <div class="card">
                        <div class="card-body">
                            <h6 class="mb-3">Observações</h6>
                            <p class="text-muted mb-0">{{ $lead->notes }}</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Aba Histórico -->
            <div class="tab-pane fade" id="historico">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Histórico de Atividades</h5>
                        
                        @if($lead->histories->isEmpty())
                            <div class="empty-state-timeline">
                                <i class="fas fa-history fa-3x mb-3"></i>
                                <p class="text-muted">Nenhuma atividade registrada ainda.</p>
                            </div>
                        @else
                            <div class="timeline">
                                @foreach($lead->histories as $history)
                                    <div class="timeline-item">
                                        <div class="timeline-dot bg-{{ $history->color }}"></div>
                                        <div>
                                            <div class="d-flex justify-content-between align-items-start mb-1">
                                                <div>
                                                    <i class="{{ $history->icon }} me-2 text-{{ $history->color }}"></i>
                                                    <strong>{{ $history->description }}</strong>
                                                </div>
                                                <small class="text-muted">{{ $history->created_at->diffForHumans() }}</small>
                                            </div>
                                            
                                            @if($history->changes)
                                                <div class="small text-muted mb-2">
                                                    @foreach($history->changes as $key => $value)
                                                        @if(is_array($value) && isset($value['old']) && isset($value['new']))
                                                            <div class="mb-1">
                                                                <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                                                <span class="text-decoration-line-through">{{ $value['old'] }}</span>
                                                                <i class="fas fa-arrow-right mx-1"></i>
                                                                <span class="text-success">{{ $value['new'] }}</span>
                                                            </div>
                                                        @else
                                                            <div class="mb-1">
                                                                <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @endif
                                            
                                            <div class="small">
                                                @if($history->user)
                                                    <span class="text-muted">
                                                        <i class="fas fa-user me-1"></i>{{ $history->user->name }}
                                                    </span>
                                                    <span class="text-muted mx-2">•</span>
                                                @endif
                                                <small class="text-muted">{{ $history->created_at->format('d/m/Y H:i') }}</small>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Gerenciar Campos -->
<div class="modal fade" id="manageFieldsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Gerenciamento de campos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Tabs -->
                <ul class="nav nav-tabs mb-3" id="fieldTypeTabs">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#fieldContato">Contato</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#fieldEmpresa">Empresa</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#fieldNegocio">Negócio</a>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- Tab Contato -->
                    <div class="tab-pane fade show active" id="fieldContato">
                        <div class="d-flex justify-content-between mb-3">
                            <div></div>
                            <div>
                                <button type="button" class="btn btn-sm btn-outline-primary me-2" onclick="openAddGroupModal('contato')">
                                    <i class="fas fa-plus"></i> Adicionar grupo
                                </button>
                                <button type="button" class="btn btn-sm btn-primary" onclick="openAddFieldModal('contato')">
                                    <i class="fas fa-plus"></i> Adicionar campo
                                </button>
                            </div>
                        </div>

                        <div id="contatoFieldGroups">
                            <!-- Carregado dinamicamente -->
                        </div>
                    </div>

                    <!-- Tab Empresa -->
                    <div class="tab-pane fade" id="fieldEmpresa">
                        <p class="text-muted">Em desenvolvimento...</p>
                    </div>

                    <!-- Tab Negócio -->
                    <div class="tab-pane fade" id="fieldNegocio">
                        <p class="text-muted">Em desenvolvimento...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Adicionar Grupo -->
<div class="modal fade" id="addGroupModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Adicionar grupo de campos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addGroupForm">
                <div class="modal-body">
                    <input type="hidden" id="groupType" name="type">
                    <div class="mb-3">
                        <label for="groupName" class="form-label">Nome do Grupo</label>
                        <input type="text" class="form-control" id="groupName" name="name" 
                               placeholder="Digite para criar um novo grupo" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Adicionar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Adicionar Campo -->
<div class="modal fade" id="addFieldModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Adicionar campo customizado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addFieldForm">
                <div class="modal-body">
                    <input type="hidden" id="fieldTypeInput" name="type">
                    
                    <div class="mb-3">
                        <label class="form-label">Criação de um novo campo</label>
                        <p class="small text-muted">Estou criando um campo para:</p>
                        <div class="btn-group w-100 mb-3" role="group">
                            <input type="radio" class="btn-check" name="fieldFor" id="fieldForContato" value="contato" checked>
                            <label class="btn btn-outline-primary" for="fieldForContato">
                                <i class="fas fa-user"></i> Contato
                            </label>
                            
                            <input type="radio" class="btn-check" name="fieldFor" id="fieldForEmpresa" value="empresa">
                            <label class="btn btn-outline-primary" for="fieldForEmpresa">
                                <i class="fas fa-building"></i> Empresa
                            </label>
                            
                            <input type="radio" class="btn-check" name="fieldFor" id="fieldForNegocio" value="negocio">
                            <label class="btn btn-outline-primary" for="fieldForNegocio">
                                <i class="fas fa-briefcase"></i> Negócio
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="fieldName" class="form-label">Nome do campo</label>
                        <input type="text" class="form-control" id="fieldName" name="name" 
                               placeholder="Teste" required>
                    </div>

                    <div class="mb-3">
                        <label for="fieldGroup" class="form-label">Grupo</label>
                        <div class="input-group">
                            <select class="form-select" id="fieldGroup" name="field_group_id" required>
                                <option value="">HighMed</option>
                            </select>
                            <button type="button" class="btn btn-outline-secondary" onclick="openAddGroupModal(document.querySelector('input[name=fieldFor]:checked').value)">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tipo do campo</label>
                        <div class="row g-2">
                            <div class="col-4">
                                <input type="radio" class="btn-check" name="fieldType" id="fieldTypeText" value="text" checked>
                                <label class="btn btn-outline-primary w-100" for="fieldTypeText">
                                    <i class="fas fa-font d-block mb-2"></i>
                                    Texto
                                </label>
                            </div>
                            <div class="col-4">
                                <input type="radio" class="btn-check" name="fieldType" id="fieldTypeNumber" value="number">
                                <label class="btn btn-outline-primary w-100" for="fieldTypeNumber">
                                    <i class="fas fa-hashtag d-block mb-2"></i>
                                    Número
                                </label>
                            </div>
                            <div class="col-4">
                                <input type="radio" class="btn-check" name="fieldType" id="fieldTypeMonetary" value="monetary">
                                <label class="btn btn-outline-primary w-100" for="fieldTypeMonetary">
                                    <i class="fas fa-dollar-sign d-block mb-2"></i>
                                    Monetário
                                </label>
                            </div>
                            <div class="col-4">
                                <input type="radio" class="btn-check" name="fieldType" id="fieldTypeRichText" value="rich_text">
                                <label class="btn btn-outline-primary w-100" for="fieldTypeRichText">
                                    <i class="fas fa-align-left d-block mb-2"></i>
                                    Texto Rico
                                </label>
                            </div>
                            <div class="col-4">
                                <input type="radio" class="btn-check" name="fieldType" id="fieldTypePhone" value="phone">
                                <label class="btn btn-outline-primary w-100" for="fieldTypePhone">
                                    <i class="fas fa-phone d-block mb-2"></i>
                                    Telefone
                                </label>
                            </div>
                            <div class="col-4">
                                <input type="radio" class="btn-check" name="fieldType" id="fieldTypeSelect" value="select">
                                <label class="btn btn-outline-primary w-100" for="fieldTypeSelect">
                                    <i class="fas fa-check-square d-block mb-2"></i>
                                    Seleção
                                </label>
                            </div>
                            <div class="col-4">
                                <input type="radio" class="btn-check" name="fieldType" id="fieldTypeMultiSelect" value="multi_select">
                                <label class="btn btn-outline-primary w-100" for="fieldTypeMultiSelect">
                                    <i class="fas fa-tasks d-block mb-2"></i>
                                    Múltipla seleção
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Adicionar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let currentFieldType = 'contato';

// Toggle campos vazios
document.getElementById('toggleEmptyFields')?.addEventListener('click', function() {
    const emptyFields = document.querySelectorAll('.empty-field');
    const isHidden = emptyFields[0]?.style.display === 'none';
    
    emptyFields.forEach(field => {
        field.style.display = isHidden ? '' : 'none';
    });
    
    const icon = this.querySelector('i');
    const text = isHidden ? 'Ocultar Campos vazios' : 'Mostrar Campos vazios';
    icon.className = isHidden ? 'fas fa-eye-slash' : 'fas fa-eye';
    this.innerHTML = `<i class="${icon.className}"></i> ${text}`;
});

// Carregar grupos quando o modal é aberto
document.getElementById('manageFieldsModal')?.addEventListener('shown.bs.modal', function() {
    loadFieldGroups('contato');
});

// Carregar grupos de campos
function loadFieldGroups(type) {
    currentFieldType = type;
    fetch(`/custom-fields/${type}`)
        .then(response => response.json())
        .then(groups => {
            const container = document.getElementById('contatoFieldGroups');
            container.innerHTML = '';
            
            groups.forEach(group => {
                const groupHtml = `
                    <div class="card mb-3">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <div>
                                <button class="btn btn-link text-decoration-none text-dark p-0" type="button" 
                                        data-bs-toggle="collapse" data-bs-target="#manage-group-${group.id}">
                                    <i class="fas fa-chevron-down me-2"></i> ${group.name}
                                </button>
                            </div>
                            ${!group.is_system ? `
                                <button type="button" class="btn btn-sm btn-danger" onclick="deleteGroup(${group.id})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            ` : ''}
                        </div>
                        <div class="collapse show" id="manage-group-${group.id}">
                            <div class="card-body">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Nome do campo</th>
                                            <th>Tipo</th>
                                            <th>Identificador</th>
                                            <th style="width: 200px;">Usar como variável</th>
                                            <th style="width: 50px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${group.custom_fields.map(field => `
                                            <tr>
                                                <td>${field.name}</td>
                                                <td>${getFieldTypeLabel(field.type)}</td>
                                                <td><code>${field.identifier}</code></td>
                                                <td>
                                                    <input type="checkbox" class="form-check-input" ${field.is_system ? 'disabled' : ''}>
                                                </td>
                                                <td>
                                                    ${!field.is_system ? `
                                                        <button type="button" class="btn btn-sm btn-link text-danger" onclick="deleteField(${field.id})">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    ` : ''}
                                                </td>
                                            </tr>
                                        `).join('')}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                `;
                container.innerHTML += groupHtml;
            });
            
            // Atualizar select de grupos no modal de adicionar campo
            updateGroupSelect(groups);
        })
        .catch(error => {
            console.error('Erro ao carregar grupos:', error);
            alert('Erro ao carregar grupos de campos');
        });
}

function getFieldTypeLabel(type) {
    const labels = {
        'text': 'Texto',
        'number': 'Número',
        'monetary': 'Monetário',
        'rich_text': 'Texto Rico',
        'phone': 'Telefone',
        'select': 'Seleção',
        'multi_select': 'Múltipla seleção'
    };
    return labels[type] || type;
}

function updateGroupSelect(groups) {
    const select = document.getElementById('fieldGroup');
    select.innerHTML = '<option value="">Selecione um grupo</option>';
    groups.forEach(group => {
        const option = document.createElement('option');
        option.value = group.id;
        option.textContent = group.name;
        select.appendChild(option);
    });
}

// Abrir modal de adicionar grupo
function openAddGroupModal(type) {
    currentFieldType = type;
    document.getElementById('groupType').value = type;
    document.getElementById('groupName').value = '';
    new bootstrap.Modal(document.getElementById('addGroupModal')).show();
}

// Abrir modal de adicionar campo
function openAddFieldModal(type) {
    currentFieldType = type;
    document.getElementById('fieldTypeInput').value = type;
    document.getElementById('fieldName').value = '';
    document.getElementById('fieldGroup').value = '';
    
    // Carregar grupos do tipo selecionado
    fetch(`/custom-fields/${type}`)
        .then(response => response.json())
        .then(groups => {
            updateGroupSelect(groups);
        });
    
    new bootstrap.Modal(document.getElementById('addFieldModal')).show();
}

// Submeter formulário de adicionar grupo
document.getElementById('addGroupForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = {
        name: document.getElementById('groupName').value,
        type: document.getElementById('groupType').value
    };
    
    fetch('/custom-fields/groups', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('addGroupModal')).hide();
            loadFieldGroups(currentFieldType);
            location.reload();
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao criar grupo');
    });
});

// Submeter formulário de adicionar campo
document.getElementById('addFieldForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = {
        field_group_id: document.getElementById('fieldGroup').value,
        name: document.getElementById('fieldName').value,
        type: document.querySelector('input[name="fieldType"]:checked').value
    };
    
    fetch('/custom-fields/fields', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('addFieldModal')).hide();
            loadFieldGroups(currentFieldType);
            location.reload();
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao criar campo');
    });
});

// Deletar grupo
function deleteGroup(groupId) {
    if (!confirm('Tem certeza que deseja excluir este grupo? Todos os campos e valores serão perdidos.')) {
        return;
    }
    
    fetch(`/custom-fields/groups/${groupId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadFieldGroups(currentFieldType);
            location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao excluir grupo');
    });
}

// Deletar campo
function deleteField(fieldId) {
    if (!confirm('Tem certeza que deseja excluir este campo? Todos os valores serão perdidos.')) {
        return;
    }
    
    fetch(`/custom-fields/fields/${fieldId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadFieldGroups(currentFieldType);
            location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao excluir campo');
    });
}
</script>
@endsection
