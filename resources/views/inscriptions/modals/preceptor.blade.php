<!-- Modal Preceptor -->
<div class="modal fade" id="modalPreceptor" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPreceptorTitle">Novo Registro de Preceptor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form class="form-modal" action="{{ route('preceptor-records.store') }}" method="POST" id="formPreceptor">
                @csrf
                <input type="hidden" name="_method" value="POST" id="preceptorMethod">
                <input type="hidden" name="inscription_id" value="{{ $inscription->id }}">
                <input type="hidden" name="preceptor_id" id="preceptorId" value="">
                
                <div class="modal-body">
                    <div class="mb-3">
                            <label for="preceptor_user_id" class="form-label">Preceptor (selecionar usuário) </label>
                            <select class="form-select mb-2" id="preceptor_user_id">
                                <option value="">-- Nenhum (digitar manualmente) --</option>
                                @isset($preceptorUsers)
                                    @foreach($preceptorUsers as $u)
                                        <option value="{{ $u->id }}">{{ $u->name }} @if($u->email) - {{ $u->email }} @endif</option>
                                    @endforeach
                                @endisset
                            </select>
                            <label for="nome_preceptor" class="form-label" style="display: none;">Nome do Preceptor *</label>
                            <input type="text" class="form-control" name="nome_preceptor" id="nome_preceptor" style="display: none;">
                            <input type="hidden" name="preceptor_user_id" id="preceptor_user_id_hidden" value="">
                        </div>
                    
                    <div class="mb-3">
                        <label for="historico_preceptor" class="form-label">Histórico do Preceptor</label>
                        <textarea class="form-control" name="historico_preceptor" id="historico_preceptor" rows="3"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="data_preceptor_informado" class="form-label">Data Preceptor Informado</label>
                                <input type="date" class="form-control" name="data_preceptor_informado" id="data_preceptor_informado">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="data_preceptor_contato" class="form-label">Data Preceptor Contato</label>
                                <input type="date" class="form-control" name="data_preceptor_contato" id="data_preceptor_contato">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="nome_secretaria" class="form-label">Nome Secretária</label>
                                <input type="text" class="form-control" name="nome_secretaria" id="nome_secretaria">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="email_clinica" class="form-label">Email Clínica</label>
                                <input type="email" class="form-control" name="email_clinica" id="email_clinica">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="whatsapp_clinica" class="form-label">WhatsApp Clínica</label>
                                <input type="text" class="form-control" name="whatsapp_clinica" id="whatsapp_clinica">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="usm" value="1" id="usm">
                                <label class="form-check-label" for="usm">
                                    USM
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="acesso_vitrine_gmc" value="1" id="acesso_vitrine_gmc">
                                <label class="form-check-label" for="acesso_vitrine_gmc">
                                    Acesso Vitrine GMC
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="medico_celebridade" value="1" id="medico_celebridade">
                                <label class="form-check-label" for="medico_celebridade">
                                    Médico Celebridade
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnSalvarPreceptor">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        $(document).ready(function() {
            // Controle específico do modal de preceptor
            $('#formPreceptor').on('submit', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const form = $(this);
                const formData = new FormData(this);
                const method = $('#preceptorMethod').val();
                const preceptorId = $('#preceptorId').val();
                
                // Determinar URL baseado se é criação ou edição
                let url = form.attr('action');
                if (method === 'PUT' && preceptorId) {
                    url = `/preceptor-records/${preceptorId}`;
                }
                
                const submitBtn = $('#btnSalvarPreceptor');
                const textoOriginal = submitBtn.text();
                
                // Desabilitar botão
                submitBtn.prop('disabled', true).text('Salvando...');
                
                fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Reabilitar botão
                    submitBtn.prop('disabled', false).text(textoOriginal);
                    
                    if (data.success) {
                        // Fechar modal e limpar formulário
                        $('#modalPreceptor').modal('hide');
                        form[0].reset();
                        
                        // Mostrar mensagem de sucesso
                        mostrarMensagemSucesso(data.message);
                        
                        // Recarregar a página para atualizar a lista
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
                    console.error('Erro na requisição:', error);
                    submitBtn.prop('disabled', false).text(textoOriginal);
                    alert('Erro ao salvar registro');
                });
            });
        });

        function abrirModalPreceptor() {
            // Limpar formulário
            $('#formPreceptor')[0].reset();
            $('#preceptorId').val('');
            $('#preceptorMethod').val('POST');
            $('#formPreceptor').attr('action', '{{ route('preceptor-records.store') }}');
            $('#modalPreceptorTitle').text('Novo Registro de Preceptor');
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();
            
            $('#modalPreceptor').modal('show');
        }

        function editarPreceptor(id) {
            // Buscar dados do preceptor
            fetch(`/preceptor-records/${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const preceptor = data.data;
                        
                        // Preencher formulário
                        $('#preceptorId').val(preceptor.id);
                        $('#preceptorMethod').val('PUT');
                        $('#nome_preceptor').val(preceptor.nome_preceptor);
                        // tentar achar usuário correspondente na lista (por nome)
                        try {
                            const match = Array.from(document.querySelectorAll('#preceptor_user_id option')).find(o => o.text.trim().startsWith(preceptor.nome_preceptor));
                            if (match) {
                                $('#preceptor_user_id').val(match.value);
                                $('#preceptor_user_id_hidden').val(match.value);
                            } else {
                                $('#preceptor_user_id').val('');
                                $('#preceptor_user_id_hidden').val('');
                            }
                        } catch (e) {
                            $('#preceptor_user_id').val('');
                            $('#preceptor_user_id_hidden').val('');
                        }
                        $('#historico_preceptor').val(preceptor.historico_preceptor);
                        $('#data_preceptor_informado').val(preceptor.data_preceptor_informado);
                        $('#data_preceptor_contato').val(preceptor.data_preceptor_contato);
                        $('#nome_secretaria').val(preceptor.nome_secretaria);
                        $('#email_clinica').val(preceptor.email_clinica);
                        $('#whatsapp_clinica').val(preceptor.whatsapp_clinica);
                        $('#usm').prop('checked', preceptor.usm);
                        $('#acesso_vitrine_gmc').prop('checked', preceptor.acesso_vitrine_gmc);
                        $('#medico_celebridade').prop('checked', preceptor.medico_celebridade);
                        
                        // Atualizar título e ação do modal
                        $('#modalPreceptorTitle').text('Editar Registro de Preceptor');
                        
                        // Abrir modal
                        $('#modalPreceptor').modal('show');
                    } else {
                        alert('Erro ao carregar dados do preceptor');
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao carregar dados do preceptor');
                });
        }
        // sincronizar select -> input de nome
        $('#preceptor_user_id').on('change', function() {
            const uid = $(this).val();
            if (!uid) {
                $('#preceptor_user_id_hidden').val('');
                return;
            }
            // buscar nome no option e setar no input
            const txt = $(this).find('option:selected').text();
            // remover email se presente
            const name = txt.split(' - ')[0].trim();
            $('#nome_preceptor').val(name);
            $('#preceptor_user_id_hidden').val(uid);
        });
    </script>
@endpush
