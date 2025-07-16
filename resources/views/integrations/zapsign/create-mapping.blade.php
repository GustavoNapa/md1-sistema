@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-plus me-2"></i>Novo Mapeamento de Template ZapSign
                    </h5>
                    <a href="{{ route('integrations.zapsign.template-mappings') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Voltar
                    </a>
                </div>

                <div class="card-body">
                    <form action="{{ route('integrations.zapsign.template-mappings.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nome do Mapeamento *</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" required>
                                    <div class="form-text">Nome descritivo para identificar este mapeamento</div>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="zapsign_template_id" class="form-label">ID do Template ZapSign *</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control @error('zapsign_template_id') is-invalid @enderror" 
                                               id="zapsign_template_id" name="zapsign_template_id" value="{{ old('zapsign_template_id') }}" required>
                                        <button type="button" class="btn btn-outline-primary" id="load-template-fields">
                                            <i class="fas fa-download me-1"></i>Buscar Campos
                                        </button>
                                    </div>
                                    <div class="form-text">ID do template no ZapSign (ex: 123456)</div>
                                    @error('zapsign_template_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Descrição</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            <div class="form-text">Descrição opcional do que este template é usado</div>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr>

                        <h6 class="mb-3">
                            <i class="fas fa-signature me-2"></i>Configuração de Assinatura Automática
                        </h6>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="auto_sign" name="auto_sign" 
                                               value="1" {{ old('auto_sign') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="auto_sign">
                                            Assinatura Automática
                                        </label>
                                    </div>
                                    <div class="form-text">Assinar automaticamente com dados configurados</div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="signer_name" class="form-label">Nome do Assinante</label>
                                    <input type="text" class="form-control @error('signer_name') is-invalid @enderror" 
                                           id="signer_name" name="signer_name" value="{{ old('signer_name') }}">
                                    <div class="form-text">Nome para assinatura automática</div>
                                    @error('signer_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="signer_email" class="form-label">E-mail do Assinante</label>
                                    <input type="email" class="form-control @error('signer_email') is-invalid @enderror" 
                                           id="signer_email" name="signer_email" value="{{ old('signer_email') }}">
                                    <div class="form-text">E-mail para assinatura automática</div>
                                    @error('signer_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr>

                        <h6 class="mb-3">
                            <i class="fas fa-map me-2"></i>Mapeamento de Campos
                        </h6>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Como funciona:</strong> Mapeie os campos do sistema para as variáveis do template ZapSign. 
                            Os dados serão automaticamente preenchidos quando um documento for criado.
                        </div>

                        <div id="field-mappings">
                            <div class="field-mapping-row mb-3">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="form-label">Variável ZapSign</label>
                                        <input type="text" class="form-control" name="mappings[0][zapsign_variable]" 
                                               placeholder="Ex: NOME_COMPLETO">
                                        <div class="form-text">Nome da variável no template ZapSign</div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Campo do Sistema</label>
                                        <select class="form-select" name="mappings[0][system_field]">
                                            <option value="">Selecione um campo</option>
                                            @if(isset($systemFields) && is_array($systemFields))
                                                @foreach($systemFields as $key => $field)
                                                    <option value="{{ $key }}">{{ $field }}</option>
                                                @endforeach
                                            @else
                                                <option value="" disabled>Erro: Campos do sistema não carregados</option>
                                            @endif
                                        </select>
                                        <div class="form-text">Campo do sistema que será usado</div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Valor Padrão</label>
                                        <input type="text" class="form-control" name="mappings[0][default_value]" 
                                               placeholder="Valor opcional">
                                        <div class="form-text">Usado se campo estiver vazio</div>
                                    </div>
                                    <div class="col-md-1">
                                        <label class="form-label">&nbsp;</label>
                                        <button type="button" class="btn btn-outline-danger btn-sm w-100 remove-mapping" disabled>
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <button type="button" class="btn btn-outline-primary btn-sm" id="add-mapping">
                                <i class="fas fa-plus me-1"></i>Adicionar Mapeamento
                            </button>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('integrations.zapsign.template-mappings') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Salvar Mapeamento
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let mappingIndex = 1;
    
    // Definir campos do sistema disponíveis
    const systemFields = @json($systemFields ?? []);
    
    // Função para gerar options dos campos do sistema
    function generateSystemFieldOptions() {
        let options = '<option value="">Selecione um campo</option>';
        
        if (systemFields && typeof systemFields === 'object') {
            Object.keys(systemFields).forEach(key => {
                options += `<option value="${key}">${systemFields[key]}</option>`;
            });
        } else {
            options += '<option value="" disabled>Erro: Campos do sistema não carregados</option>';
        }
        
        return options;
    }
    
    // Buscar campos do template ZapSign
    document.getElementById('load-template-fields').addEventListener('click', function() {
        const templateId = document.getElementById('zapsign_template_id').value;
        
        if (!templateId) {
            alert('Por favor, insira o ID do template primeiro.');
            return;
        }
        
        const button = this;
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Buscando...';
        button.disabled = true;
        
        fetch(`/integrations/zapsign/templates/${templateId}/fields`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.fields && data.fields.length > 0) {
                // Limpar mapeamentos existentes
                const container = document.getElementById('field-mappings');
                container.innerHTML = '';
                
                // Criar mapeamentos para cada campo encontrado
                data.fields.forEach((field, index) => {
                    const newMapping = createMappingRowWithData(index, field.variable, field.label);
                    container.appendChild(newMapping);
                });
                
                mappingIndex = data.fields.length;
                updateRemoveButtons();
                
                // Mostrar mensagem de sucesso
                showAlert('success', `${data.fields.length} campos encontrados e carregados automaticamente!`);
            } else {
                showAlert('warning', 'Nenhum campo encontrado no template ou template não encontrado.');
            }
        })
        .catch(error => {
            console.error('Erro ao buscar campos:', error);
            showAlert('danger', 'Erro ao buscar campos do template. Verifique o ID e tente novamente.');
        })
        .finally(() => {
            button.innerHTML = originalText;
            button.disabled = false;
        });
    });
    
    // Adicionar novo mapeamento
    document.getElementById('add-mapping').addEventListener('click', function() {
        const container = document.getElementById('field-mappings');
        const newMapping = createMappingRow(mappingIndex);
        container.appendChild(newMapping);
        mappingIndex++;
        updateRemoveButtons();
    });
    
    // Remover mapeamento
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-mapping') || e.target.closest('.remove-mapping')) {
            e.preventDefault();
            const row = e.target.closest('.field-mapping-row');
            row.remove();
            updateRemoveButtons();
        }
    });
    
    // Controle de assinatura automática
    document.getElementById('auto_sign').addEventListener('change', function() {
        const signerName = document.getElementById('signer_name');
        const signerEmail = document.getElementById('signer_email');
        
        if (this.checked) {
            signerName.required = true;
            signerEmail.required = true;
            signerName.parentElement.querySelector('.form-label').innerHTML = 'Nome do Assinante *';
            signerEmail.parentElement.querySelector('.form-label').innerHTML = 'E-mail do Assinante *';
        } else {
            signerName.required = false;
            signerEmail.required = false;
            signerName.parentElement.querySelector('.form-label').innerHTML = 'Nome do Assinante';
            signerEmail.parentElement.querySelector('.form-label').innerHTML = 'E-mail do Assinante';
        }
    });
    
    function createMappingRowWithData(index, variable, label) {
        const div = document.createElement('div');
        div.className = 'field-mapping-row mb-3';
        div.innerHTML = `
            <div class="row">
                <div class="col-md-4">
                    <label class="form-label">Variável ZapSign</label>
                    <input type="text" class="form-control" name="mappings[${index}][zapsign_variable]" 
                           value="${variable}" placeholder="Ex: NOME_COMPLETO">
                    <div class="form-text">Nome da variável no template ZapSign</div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Campo do Sistema</label>
                    <select class="form-select" name="mappings[${index}][system_field]">
                        ${generateSystemFieldOptions()}
                    </select>
                    <div class="form-text">Campo do sistema que será usado</div>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Valor Padrão</label>
                    <input type="text" class="form-control" name="mappings[${index}][default_value]" 
                           placeholder="Valor opcional">
                    <div class="form-text">Usado se campo estiver vazio</div>
                </div>
                <div class="col-md-1">
                    <label class="form-label">&nbsp;</label>
                    <button type="button" class="btn btn-outline-danger btn-sm w-100 remove-mapping">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
        return div;
    }
    
    function showAlert(type, message) {
        // Remover alertas existentes
        const existingAlerts = document.querySelectorAll('.auto-alert');
        existingAlerts.forEach(alert => alert.remove());
        
        // Criar novo alerta
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show auto-alert`;
        alertDiv.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'warning' ? 'exclamation-triangle' : 'exclamation-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        // Inserir antes do formulário
        const form = document.querySelector('form');
        form.parentNode.insertBefore(alertDiv, form);
        
        // Auto-remover após 5 segundos
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }
    
    function createMappingRow(index) {
        const div = document.createElement('div');
        div.className = 'field-mapping-row mb-3';
        div.innerHTML = `
            <div class="row">
                <div class="col-md-4">
                    <label class="form-label">Variável ZapSign</label>
                    <input type="text" class="form-control" name="mappings[${index}][zapsign_variable]" 
                           placeholder="Ex: NOME_COMPLETO">
                    <div class="form-text">Nome da variável no template ZapSign</div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Campo do Sistema</label>
                    <select class="form-select" name="mappings[${index}][system_field]">
                        ${generateSystemFieldOptions()}
                    </select>
                    <div class="form-text">Campo do sistema que será usado</div>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Valor Padrão</label>
                    <input type="text" class="form-control" name="mappings[${index}][default_value]" 
                           placeholder="Valor opcional">
                    <div class="form-text">Usado se campo estiver vazio</div>
                </div>
                <div class="col-md-1">
                    <label class="form-label">&nbsp;</label>
                    <button type="button" class="btn btn-outline-danger btn-sm w-100 remove-mapping">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
        return div;
    }
    
    function updateRemoveButtons() {
        const mappings = document.querySelectorAll('.field-mapping-row');
        mappings.forEach((mapping, index) => {
            const removeBtn = mapping.querySelector('.remove-mapping');
            removeBtn.disabled = mappings.length === 1;
        });
    }
    
    // Inicializar estado dos botões
    updateRemoveButtons();
});
</script>
@endsection

