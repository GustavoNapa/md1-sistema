@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Editar Pipeline</h4>
                    <a href="{{ route('pipelines.index') }}" class="btn btn-sm btn-secondary">
                        Cancelar
                    </a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('pipelines.update', $pipeline) }}" id="pipelineForm">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Nome do Pipeline *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $pipeline->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="type" class="form-label">Tipo de Pipeline *</label>
                            <select class="form-select @error('type') is-invalid @enderror" 
                                    id="type" name="type" required>
                                <option value="leads" {{ old('type', $pipeline->type) == 'leads' ? 'selected' : '' }}>Leads</option>
                                <option value="clientes" {{ old('type', $pipeline->type) == 'clientes' ? 'selected' : '' }}>Clientes</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="color" class="form-label">Cor</label>
                            <input type="color" class="form-control form-control-color" 
                                   id="color" name="color" value="{{ old('color', $pipeline->color) }}">
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Pipeline padrão?</label>
                            <select class="form-select" name="is_default">
                                <option value="0" {{ old('is_default', $pipeline->is_default) == '0' ? 'selected' : '' }}>Não</option>
                                <option value="1" {{ old('is_default', $pipeline->is_default) == '1' ? 'selected' : '' }}>Sim</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <label class="form-label mb-0">Etapas do Pipeline</label>
                            </div>

                            <div id="stagesContainer">
                                @foreach($pipeline->stages as $index => $stage)
                                    <div class="stage-row" data-stage-index="{{ $index }}">
                                        <input type="hidden" name="stages[{{ $index }}][id]" value="{{ $stage->id }}">
                                        <div class="row align-items-end">
                                            <div class="col-md-3">
                                                <label class="form-label">Nome da Etapa *</label>
                                                <input type="text" class="form-control" name="stages[{{ $index }}][name]" 
                                                       value="{{ $stage->name }}" required>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">Ordem *</label>
                                                <input type="number" class="form-control" name="stages[{{ $index }}][order]" 
                                                       value="{{ $stage->order }}" min="1" required>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">Cor</label>
                                                <input type="color" class="form-control form-control-color" 
                                                       name="stages[{{ $index }}][color]" value="{{ $stage->color }}">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Tipo</label>
                                                <select class="form-select" name="stages[{{ $index }}][type]" required>
                                                    <option value="normal" {{ $stage->type == 'normal' ? 'selected' : '' }}>Normal</option>
                                                    <option value="ganho" {{ $stage->type == 'ganho' ? 'selected' : '' }}>Ganho</option>
                                                    <option value="perdido" {{ $stage->type == 'perdido' ? 'selected' : '' }}>Perdido</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <button type="button" class="btn btn-danger btn-sm w-100 remove-stage-btn">
                                                    <i class="fas fa-trash"></i> Remover
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="addStageBtn">
                                <i class="fas fa-plus"></i> Adicionar Etapa
                            </button>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('pipelines.index') }}" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Atualizar Pipeline</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.stage-row {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 1rem;
    margin-bottom: 0.75rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let stageCount = {{ $pipeline->stages->count() }};

    function getStageTemplate(index) {
        return `
            <div class="stage-row" data-stage-index="${index}">
                <div class="row align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Nome da Etapa *</label>
                        <input type="text" class="form-control" name="stages[${index}][name]" 
                               placeholder="Nome da etapa" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Ordem *</label>
                        <input type="number" class="form-control" name="stages[${index}][order]" 
                               value="${index + 1}" min="1" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Cor</label>
                        <input type="color" class="form-control form-control-color" 
                               name="stages[${index}][color]" value="#6c757d">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tipo</label>
                        <select class="form-select" name="stages[${index}][type]" required>
                            <option value="normal">Normal</option>
                            <option value="ganho">Ganho</option>
                            <option value="perdido">Perdido</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger btn-sm w-100 remove-stage-btn">
                            <i class="fas fa-trash"></i> Remover
                        </button>
                    </div>
                </div>
            </div>
        `;
    }

    document.getElementById('addStageBtn').addEventListener('click', function() {
        const container = document.getElementById('stagesContainer');
        container.insertAdjacentHTML('beforeend', getStageTemplate(stageCount));
        stageCount++;
    });

    document.getElementById('stagesContainer').addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-stage-btn') || e.target.closest('.remove-stage-btn')) {
            const btn = e.target.closest('.remove-stage-btn');
            const stageRow = btn.closest('.stage-row');
            if (confirm('Tem certeza que deseja remover esta etapa? Todos os leads nesta etapa serão movidos para a primeira etapa.')) {
                stageRow.remove();
            }
        }
    });
});
</script>
@endsection
