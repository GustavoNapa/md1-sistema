@extends('layouts.app')

@section('title', 'Nova Faixa de Faturamento')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Nova Faixa de Faturamento</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('faixa-faturamentos.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="label" class="form-label">Nome da Faixa</label>
                            <input type="text" class="form-control @error('label') is-invalid @enderror" 
                                   id="label" name="label" value="{{ old('label') }}" 
                                   placeholder="Ex: Bronze, Prata, Ouro" maxlength="50" required>
                            @error('label')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="valor_min" class="form-label">Valor Mínimo</label>
                                    <div class="input-group">
                                        <span class="input-group-text">R$</span>
                                        <input type="number" class="form-control @error('valor_min') is-invalid @enderror" 
                                               id="valor_min" name="valor_min" value="{{ old('valor_min') }}" 
                                               step="0.01" min="0" placeholder="0,00" required>
                                        @error('valor_min')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="valor_max" class="form-label">Valor Máximo</label>
                                    <div class="input-group">
                                        <span class="input-group-text">R$</span>
                                        <input type="number" class="form-control @error('valor_max') is-invalid @enderror" 
                                               id="valor_max" name="valor_max" value="{{ old('valor_max') }}" 
                                               step="0.01" min="0" placeholder="0,00" required>
                                        @error('valor_max')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Dica:</strong> O valor máximo deve ser maior que o valor mínimo. 
                            Esta faixa será usada para agrupar inscrições no Kanban por potencial financeiro.
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('faixa-faturamentos.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Voltar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Salvar Faixa
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const valorMinInput = document.getElementById('valor_min');
    const valorMaxInput = document.getElementById('valor_max');
    
    // Validação em tempo real
    valorMaxInput.addEventListener('input', function() {
        const valorMin = parseFloat(valorMinInput.value) || 0;
        const valorMax = parseFloat(valorMaxInput.value) || 0;
        
        if (valorMax <= valorMin && valorMax > 0) {
            valorMaxInput.setCustomValidity('O valor máximo deve ser maior que o valor mínimo');
        } else {
            valorMaxInput.setCustomValidity('');
        }
    });
    
    valorMinInput.addEventListener('input', function() {
        // Trigger validation on valor_max when valor_min changes
        valorMaxInput.dispatchEvent(new Event('input'));
    });
});
</script>
@endsection

