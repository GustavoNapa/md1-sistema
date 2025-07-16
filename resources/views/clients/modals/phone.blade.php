<!-- Modal Telefone -->
<div class="modal fade" id="modalTelefone" tabindex="-1" aria-labelledby="modalTelefoneLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTelefoneLabel">
                    <i class="fas fa-phone"></i> <span id="phone-modal-title">Novo Telefone</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formTelefone">
                <div class="modal-body">
                    <input type="hidden" id="phone_id" name="phone_id">
                    <input type="hidden" id="client_id_phone" name="client_id" value="{{ $client->id }}">

                    <div class="mb-3">
                        <label for="phone" class="form-label">Telefone *</label>
                        <input type="text" class="form-control" id="phone" name="phone" required
                               placeholder="(11) 99999-9999">
                        <div class="form-text">Formato: (11) 99999-9999 ou (11) 9999-9999</div>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="mb-3">
                        <label for="phone_type" class="form-label">Tipo *</label>
                        <select class="form-select" id="phone_type" name="type" required>
                            <option value="">Selecione o tipo</option>
                            <option value="mobile">Celular</option>
                            <option value="landline">Fixo</option>
                            <option value="work">Trabalho</option>
                            <option value="other">Outro</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_whatsapp" name="is_whatsapp">
                            <label class="form-check-label" for="is_whatsapp">
                                <i class="fab fa-whatsapp text-success"></i> Este número tem WhatsApp
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_primary_phone" name="is_primary">
                            <label class="form-check-label" for="is_primary_phone">
                                Definir como telefone principal
                            </label>
                        </div>
                        <small class="form-text text-muted">
                            O telefone principal será usado nas informações básicas do cliente
                        </small>
                    </div>

                    <div class="mb-3">
                        <label for="phone_notes" class="form-label">Observações</label>
                        <textarea class="form-control" id="phone_notes" name="notes" rows="3" 
                                  placeholder="Observações sobre este telefone..."></textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btn-salvar-telefone">
                        <i class="fas fa-save"></i> Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Variáveis globais para o modal de telefone
let telefoneEditando = null;

// Máscara para telefone
$(document).ready(function() {
    $('#phone').mask('(00) 00000-0000', {
        placeholder: '(11) 99999-9999',
        translation: {
            '0': {pattern: /[0-9]/}
        }
    });
});

// Função para abrir modal de edição
function editarTelefone(id) {
    // Buscar dados do telefone via AJAX
    fetch(`/client-phones/${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                telefoneEditando = id;
                preencherFormularioTelefone(data.data);
                $('#modalTelefone').modal('show');
            }
        })
        .catch(error => {
            console.error('Erro ao carregar telefone:', error);
            alert('Erro ao carregar dados do telefone');
        });
}

// Função para preencher formulário
function preencherFormularioTelefone(phone) {
    $('#phone-modal-title').text('Editar Telefone');
    $('#phone_id').val(phone.id);
    $('#phone').val(phone.phone);
    $('#phone_type').val(phone.type);
    $('#is_whatsapp').prop('checked', phone.is_whatsapp);
    $('#is_primary_phone').prop('checked', phone.is_primary);
    $('#phone_notes').val(phone.notes || '');
}

// Função para limpar formulário
function limparFormularioTelefone() {
    $('#phone-modal-title').text('Novo Telefone');
    $('#formTelefone')[0].reset();
    $('#phone_id').val('');
    telefoneEditando = null;
    
    // Limpar classes de validação
    $('#formTelefone .form-control, #formTelefone .form-select').removeClass('is-invalid');
    $('#formTelefone .invalid-feedback').text('');
}

// Event listener para o formulário
$('#formTelefone').on('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const isEditing = telefoneEditando !== null;
    const url = isEditing ? `/client-phones/${telefoneEditando}` : '/client-phones';
    const method = isEditing ? 'PUT' : 'POST';
    
    // Converter FormData para objeto
    const data = {};
    formData.forEach((value, key) => {
        if (key === 'is_whatsapp' || key === 'is_primary') {
            data[key] = $('#' + (key === 'is_primary' ? 'is_primary_phone' : key)).is(':checked');
        } else {
            data[key] = value;
        }
    });
    
    // Desabilitar botão
    $('#btn-salvar-telefone').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Salvando...');
    
    // Limpar erros anteriores
    $('#formTelefone .form-control, #formTelefone .form-select').removeClass('is-invalid');
    $('#formTelefone .invalid-feedback').text('');
    
    fetch(url, {
        method: method,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            $('#modalTelefone').modal('hide');
            mostrarMensagemSucesso(data.message);
            
            // Recarregar a página para atualizar a lista
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            // Mostrar erros de validação
            if (data.errors) {
                Object.keys(data.errors).forEach(field => {
                    let input;
                    if (field === 'type') {
                        input = $('#phone_type');
                    } else if (field === 'notes') {
                        input = $('#phone_notes');
                    } else {
                        input = $('#' + field);
                    }
                    input.addClass('is-invalid');
                    input.siblings('.invalid-feedback').text(data.errors[field][0]);
                });
            } else {
                alert('Erro: ' + (data.message || 'Erro desconhecido'));
            }
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao salvar telefone');
    })
    .finally(() => {
        $('#btn-salvar-telefone').prop('disabled', false).html('<i class="fas fa-save"></i> Salvar');
    });
});

// Limpar formulário quando modal for fechado
$('#modalTelefone').on('hidden.bs.modal', function() {
    limparFormularioTelefone();
});
</script>

