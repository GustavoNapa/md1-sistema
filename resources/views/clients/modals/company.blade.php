<!-- Modal Empresa -->
<div class="modal fade" id="modalEmpresa" tabindex="-1" aria-labelledby="modalEmpresaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEmpresaLabel">
                    <i class="fas fa-building"></i> <span id="company-modal-title">Nova Empresa</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEmpresa">
                <div class="modal-body">
                    <input type="hidden" id="company_id" name="company_id">
                    <input type="hidden" id="client_id_company" name="client_id" value="{{ $client->id }}">

                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="company_name" class="form-label">Nome da Empresa *</label>
                                <input type="text" class="form-control" id="company_name" name="name" required
                                       placeholder="Nome da clínica, laboratório, hospital...">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="company_type" class="form-label">Tipo *</label>
                                <select class="form-select" id="company_type" name="type" required>
                                    <option value="">Selecione o tipo</option>
                                    <option value="clinic">Clínica</option>
                                    <option value="laboratory">Laboratório</option>
                                    <option value="hospital">Hospital</option>
                                    <option value="office">Consultório</option>
                                    <option value="other">Outro</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cnpj" class="form-label">CNPJ</label>
                                <input type="text" class="form-control" id="cnpj" name="cnpj"
                                       placeholder="00.000.000/0000-00">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" id="is_main_company" name="is_main">
                                    <label class="form-check-label" for="is_main_company">
                                        Definir como empresa principal
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="company_address" class="form-label">Endereço</label>
                        <input type="text" class="form-control" id="company_address" name="address"
                               placeholder="Rua, número, bairro...">
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="company_city" class="form-label">Cidade</label>
                                <input type="text" class="form-control" id="company_city" name="city"
                                       placeholder="Nome da cidade">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="company_state" class="form-label">Estado</label>
                                <select class="form-select" id="company_state" name="state">
                                    <option value="">UF</option>
                                    <option value="AC">AC</option>
                                    <option value="AL">AL</option>
                                    <option value="AP">AP</option>
                                    <option value="AM">AM</option>
                                    <option value="BA">BA</option>
                                    <option value="CE">CE</option>
                                    <option value="DF">DF</option>
                                    <option value="ES">ES</option>
                                    <option value="GO">GO</option>
                                    <option value="MA">MA</option>
                                    <option value="MT">MT</option>
                                    <option value="MS">MS</option>
                                    <option value="MG">MG</option>
                                    <option value="PA">PA</option>
                                    <option value="PB">PB</option>
                                    <option value="PR">PR</option>
                                    <option value="PE">PE</option>
                                    <option value="PI">PI</option>
                                    <option value="RJ">RJ</option>
                                    <option value="RN">RN</option>
                                    <option value="RS">RS</option>
                                    <option value="RO">RO</option>
                                    <option value="RR">RR</option>
                                    <option value="SC">SC</option>
                                    <option value="SP">SP</option>
                                    <option value="SE">SE</option>
                                    <option value="TO">TO</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="zip_code" class="form-label">CEP</label>
                                <input type="text" class="form-control" id="zip_code" name="zip_code"
                                       placeholder="00000-000">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="company_phone" class="form-label">Telefone</label>
                                <input type="text" class="form-control" id="company_phone" name="phone"
                                       placeholder="(11) 99999-9999">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="company_email" class="form-label">E-mail</label>
                                <input type="email" class="form-control" id="company_email" name="email"
                                       placeholder="contato@empresa.com">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="website" class="form-label">Website</label>
                        <input type="url" class="form-control" id="website" name="website"
                               placeholder="https://www.empresa.com">
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="mb-3">
                        <label for="company_notes" class="form-label">Observações</label>
                        <textarea class="form-control" id="company_notes" name="notes" rows="3" 
                                  placeholder="Observações sobre esta empresa..."></textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btn-salvar-empresa">
                        <i class="fas fa-save"></i> Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script>
    // Variáveis globais para o modal de empresa
    let empresaEditando = null;

    // Máscaras para campos
    $(document).ready(function() {
        $('#cnpj').mask('00.000.000/0000-00', {
            placeholder: '00.000.000/0000-00'
        });
        
        $('#zip_code').mask('00000-000', {
            placeholder: '00000-000'
        });
        
        $('#company_phone').mask('(00) 00000-0000', {
            placeholder: '(11) 99999-9999',
            translation: {
                '0': {pattern: /[0-9]/}
            }
        });
    });

    // Função para abrir modal de edição
    function editarEmpresa(id) {
        // Buscar dados da empresa via AJAX
        fetch(`/client-companies/${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    empresaEditando = id;
                    preencherFormularioEmpresa(data.data);
                    $('#modalEmpresa').modal('show');
                }
            })
            .catch(error => {
                console.error('Erro ao carregar empresa:', error);
                alert('Erro ao carregar dados da empresa');
            });
    }

    // Função para preencher formulário
    function preencherFormularioEmpresa(company) {
        $('#company-modal-title').text('Editar Empresa');
        $('#company_id').val(company.id);
        $('#company_name').val(company.name);
        $('#company_type').val(company.type);
        $('#cnpj').val(company.cnpj || '');
        $('#company_address').val(company.address || '');
        $('#company_city').val(company.city || '');
        $('#company_state').val(company.state || '');
        $('#zip_code').val(company.zip_code || '');
        $('#company_phone').val(company.phone || '');
        $('#company_email').val(company.email || '');
        $('#website').val(company.website || '');
        $('#is_main_company').prop('checked', company.is_main);
        $('#company_notes').val(company.notes || '');
    }

    // Função para limpar formulário
    function limparFormularioEmpresa() {
        $('#company-modal-title').text('Nova Empresa');
        $('#formEmpresa')[0].reset();
        $('#company_id').val('');
        empresaEditando = null;
        
        // Limpar classes de validação
        $('#formEmpresa .form-control, #formEmpresa .form-select').removeClass('is-invalid');
        $('#formEmpresa .invalid-feedback').text('');
    }

    // Event listener para o formulário
    $('#formEmpresa').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const isEditing = empresaEditando !== null;
        const url = isEditing ? `/client-companies/${empresaEditando}` : '/client-companies';
        const method = isEditing ? 'PUT' : 'POST';
        
        // Converter FormData para objeto
        const data = {};
        formData.forEach((value, key) => {
            if (key === 'is_main') {
                data[key] = $('#is_main_company').is(':checked');
            } else {
                data[key] = value;
            }
        });
        
        // Desabilitar botão
        $('#btn-salvar-empresa').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Salvando...');
        
        // Limpar erros anteriores
        $('#formEmpresa .form-control, #formEmpresa .form-select').removeClass('is-invalid');
        $('#formEmpresa .invalid-feedback').text('');
        
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
                $('#modalEmpresa').modal('hide');
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
                        // Mapear nomes dos campos
                        const fieldMap = {
                            'name': 'company_name',
                            'type': 'company_type',
                            'address': 'company_address',
                            'city': 'company_city',
                            'state': 'company_state',
                            'phone': 'company_phone',
                            'email': 'company_email',
                            'notes': 'company_notes'
                        };
                        
                        const inputId = fieldMap[field] || field;
                        input = $('#' + inputId);
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
            alert('Erro ao salvar empresa');
        })
        .finally(() => {
            $('#btn-salvar-empresa').prop('disabled', false).html('<i class="fas fa-save"></i> Salvar');
        });
    });

    // Limpar formulário quando modal for fechado
    $('#modalEmpresa').on('hidden.bs.modal', function() {
        limparFormularioEmpresa();
    });
    </script>
@endpush
