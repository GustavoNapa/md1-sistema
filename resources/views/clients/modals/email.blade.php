<!-- Modal E-mail -->
<div class="modal fade" id="modalEmail" tabindex="-1" aria-labelledby="modalEmailLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEmailLabel">
                    <i class="fas fa-envelope"></i> <span id="email-modal-title">Novo E-mail</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEmail">
                <div class="modal-body">
                    <input type="hidden" id="email_id" name="email_id">
                    <input type="hidden" id="client_id" name="client_id" value="{{ $client->id }}">

                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail *</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="mb-3">
                        <label for="type" class="form-label">Tipo *</label>
                        <select class="form-select" id="type" name="type" required>
                            <option value="">Selecione o tipo</option>
                            <option value="personal">Pessoal</option>
                            <option value="work">Trabalho</option>
                            <option value="other">Outro</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_primary" name="is_primary">
                            <label class="form-check-label" for="is_primary">
                                Definir como e-mail principal
                            </label>
                        </div>
                        <small class="form-text text-muted">
                            O e-mail principal será usado nas informações básicas do cliente
                        </small>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_verified" name="is_verified">
                            <label class="form-check-label" for="is_verified">
                                E-mail verificado
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Observações</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" 
                                  placeholder="Observações sobre este e-mail..."></textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btn-salvar-email">
                        <i class="fas fa-save"></i> Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script>
    // Variáveis globais para o modal de e-mail
    let emailEditando = null;

    // Função para abrir modal de edição
    function editarEmail(id) {
        // Buscar dados do e-mail via AJAX
        fetch(`/client-emails/${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    emailEditando = id;
                    preencherFormularioEmail(data.data);
                    $('#modalEmail').modal('show');
                }
            })
            .catch(error => {
                console.error('Erro ao carregar e-mail:', error);
                alert('Erro ao carregar dados do e-mail');
            });
    }

    // Função para preencher formulário
    function preencherFormularioEmail(email) {
        $('#email-modal-title').text('Editar E-mail');
        $('#email_id').val(email.id);
        $('#email').val(email.email);
        $('#type').val(email.type);
        $('#is_primary').prop('checked', email.is_primary);
        $('#is_verified').prop('checked', email.is_verified);
        $('#notes').val(email.notes || '');
    }

    // Função para limpar formulário
    function limparFormularioEmail() {
        $('#email-modal-title').text('Novo E-mail');
        $('#formEmail')[0].reset();
        $('#email_id').val('');
        emailEditando = null;
        
        // Limpar classes de validação
        $('#formEmail .form-control, #formEmail .form-select').removeClass('is-invalid');
        $('#formEmail .invalid-feedback').text('');
    }

    // Event listener para o formulário
    $('#formEmail').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const isEditing = emailEditando !== null;
        const url = isEditing ? `/client-emails/${emailEditando}` : '/client-emails';
        const method = isEditing ? 'PUT' : 'POST';
        
        // Converter FormData para objeto
        const data = {};
        formData.forEach((value, key) => {
            if (key === 'is_primary' || key === 'is_verified') {
                data[key] = $('#' + key).is(':checked');
            } else {
                data[key] = value;
            }
        });
        
        // Desabilitar botão
        $('#btn-salvar-email').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Salvando...');
        
        // Limpar erros anteriores
        $('#formEmail .form-control, #formEmail .form-select').removeClass('is-invalid');
        $('#formEmail .invalid-feedback').text('');
        
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
                $('#modalEmail').modal('hide');
                mostrarMensagemSucesso(data.message);
                
                // Recarregar a página para atualizar a lista
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                // Mostrar erros de validação
                if (data.errors) {
                    Object.keys(data.errors).forEach(field => {
                        const input = $('#' + field);
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
            alert('Erro ao salvar e-mail');
        })
        .finally(() => {
            $('#btn-salvar-email').prop('disabled', false).html('<i class="fas fa-save"></i> Salvar');
        });
    });

    // Limpar formulário quando modal for fechado
    $('#modalEmail').on('hidden.bs.modal', function() {
        limparFormularioEmail();
    });
    </script>
@endpush
