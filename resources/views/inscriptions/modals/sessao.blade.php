<!-- Modal Sessão -->
<div class="modal fade" id="modalSessao" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalSessaoTitle">Nova Sessão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form class="form-modal" action="{{ route('sessions.store') }}" method="POST" id="formSessao">
                @csrf
                <input type="hidden" name="_method" value="POST" id="sessaoMethod">
                <input type="hidden" name="inscription_id" value="{{ $inscription->id }}">
                <input type="hidden" name="sessao_id" id="sessaoId" value="">
                
                <div class="modal-body">
                    <!-- Informações Básicas -->
                    <h6 class="mb-3 text-primary">Informações Básicas</h6>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="numero_sessao" class="form-label">Fase *</label>
                                <input type="number" class="form-control" name="numero_sessao" id="numero_sessao" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="preceptor_record_id" class="form-label">Preceptor *</label>
                                <select class="form-select" name="preceptor_record_id" id="preceptor_record_id" required>
                                    <option value="">Selecione</option>
                                    @foreach($inscription->preceptorRecords as $preceptor)
                                        <option value="{{ $preceptor->id }}">{{ $preceptor->nome_preceptor }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="semana_mes" class="form-label">Semana do Mês *</label>
                                <select class="form-select" name="semana_mes" id="semana_mes" required>
                                    <option value="">Selecione</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="fase" class="form-label">Qual fase o médico se encontra? *</label>
                                <select class="form-select" name="fase" id="fase" required>
                                    <option value="">Selecione</option>
                                    <option value="Start">Start</option>
                                    <option value="Fase 01">Fase 01</option>
                                    <option value="Fase 02">Fase 02</option>
                                    <option value="Fase 03">Fase 03</option>
                                    <option value="Fase 04">Fase 04</option>
                                    <option value="Fase 05">Fase 05</option>
                                    <option value="Fase 06">Fase 06</option>
                                    <option value="Renovação">Renovação</option>
                                    <option value="MedBoss">MedBoss</option>
                                    <option value="Retorno de Pausa">Retorno de Pausa</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="tipo" class="form-label">Tipo</label>
                                <select class="form-select" name="tipo" id="tipo">
                                    <option value="">Selecione</option>
                                    <option value="diagnostico">Diagnóstico</option>
                                    <option value="chamada_start">Chamada Start</option>
                                    <option value="onboarding">Onboarding</option>
                                    <option value="mentoria">Mentoria</option>
                                    <option value="follow_up">Follow-up</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" name="status" id="status">
                                    <option value="agendada">Agendada</option>
                                    <option value="realizada">Realizada</option>
                                    <option value="cancelada">Cancelada</option>
                                    <option value="reagendada">Reagendada</option>
                                    <option value="no_show">No show</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="data_agendada" class="form-label">Data Agendada</label>
                                <input type="datetime-local" class="form-control" name="data_agendada" id="data_agendada">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="data_realizada" class="form-label">Data Realizada</label>
                                <input type="datetime-local" class="form-control" name="data_realizada" id="data_realizada">
                            </div>
                        </div>
                    </div>

                    <!-- Confirmação -->
                    <hr class="my-4">
                    <h6 class="mb-3 text-primary">Confirmação e Comparecimento</h6>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="confirmou_24h" class="form-label">Mandou Mensagem confirmando 24hrs antes? *</label>
                                <select class="form-select" name="confirmou_24h" id="confirmou_24h" required>
                                    <option value="">Selecione</option>
                                    <option value="1">Sim</option>
                                    <option value="0">Não</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="medico_confirmou" class="form-label">O Médico confirmou? *</label>
                                <select class="form-select" name="medico_confirmou" id="medico_confirmou" required>
                                    <option value="">Selecione</option>
                                    <option value="confirmou">Confirmou</option>
                                    <option value="desmarcou">Desmarcou</option>
                                    <option value="nao_respondeu">Não Respondeu</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="medico_compareceu" class="form-label">O médico compareceu? *</label>
                                <select class="form-select" name="medico_compareceu" id="medico_compareceu" required>
                                    <option value="">Selecione</option>
                                    <option value="1">Compareceu</option>
                                    <option value="0">Não Compareceu</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3" id="campo_motivo_desmarcou" style="display: none;">
                        <label for="motivo_desmarcou" class="form-label">Algum motivo ou só desmarcou mesmo?</label>
                        <textarea class="form-control" name="motivo_desmarcou" id="motivo_desmarcou" rows="2"></textarea>
                    </div>

                    <!-- No Show -->
                    <div id="campos_no_show" style="display: none;">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status_reagendamento" class="form-label">Caso tenha dado No Show, ele remarcou?</label>
                                    <select class="form-select" name="status_reagendamento" id="status_reagendamento">
                                        <option value="">Selecione</option>
                                        <option value="reagendado">Reagendado</option>
                                        <option value="em_processo">Em Processo</option>
                                        <option value="sem_comunicacao">Sem Comunicação</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3" id="campo_data_remarcada" style="display: none;">
                                    <label for="data_remarcada" class="form-label">Remarcou para qual data?</label>
                                    <input type="date" class="form-control" name="data_remarcada" id="data_remarcada">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Observações -->
                    <hr class="my-4">
                    <h6 class="mb-3 text-primary">Observações e Resultados</h6>
                    <div class="mb-3">
                        <label for="observacoes" class="form-label">Observações</label>
                        <textarea class="form-control" name="observacoes" id="observacoes" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="resultado" class="form-label">Resultado da Sessão</label>
                        <textarea class="form-control" name="resultado" id="resultado" rows="3"></textarea>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-info" id="btnSalvarSessao">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            // Controle de exibição de campos condicionais
            $('#medico_confirmou').on('change', function() {
                if ($(this).val() === 'desmarcou') {
                    $('#campo_motivo_desmarcou').show();
                } else {
                    $('#campo_motivo_desmarcou').hide();
                }
            });

            $('#medico_compareceu').on('change', function() {
                if ($(this).val() === '0') {
                    $('#campos_no_show').show();
                } else {
                    $('#campos_no_show').hide();
                }
            });

            $('#status').on('change', function() {
                if ($(this).val() === 'no_show') {
                    $('#medico_compareceu').val('0').trigger('change');
                }
            });

            $('#status_reagendamento').on('change', function() {
                if ($(this).val() === 'reagendado') {
                    $('#campo_data_remarcada').show();
                } else {
                    $('#campo_data_remarcada').hide();
                }
            });

            // Controle específico do modal de sessão
            $('#formSessao').on('submit', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const form = $(this);
                const formData = new FormData(this);
                const method = $('#sessaoMethod').val();
                const sessaoId = $('#sessaoId').val();
                
                // Determinar URL baseado se é criação ou edição
                let url = form.attr('action');
                if (method === 'PUT' && sessaoId) {
                    url = `/sessions/${sessaoId}`;
                }
                
                const submitBtn = $('#btnSalvarSessao');
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
                        $('#modalSessao').modal('hide');
                        form[0].reset();
                        
                        // Ocultar campos condicionais
                        $('#campo_motivo_desmarcou').hide();
                        $('#campos_no_show').hide();
                        $('#campo_data_remarcada').hide();
                        
                        // Mostrar mensagem de sucesso
                        mostrarMensagemSucesso(data.message);
                        
                        // Recarregar página
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

        function abrirModalSessao() {
            // Limpar formulário
            $('#formSessao')[0].reset();
            $('#sessaoId').val('');
            $('#sessaoMethod').val('POST');
            $('#formSessao').attr('action', '{{ route('sessions.store') }}');
            $('#modalSessaoTitle').text('Nova Sessão');
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();
            $('#campo_motivo_desmarcou').hide();
            $('#campos_no_show').hide();
            $('#campo_data_remarcada').hide();
            // pré-preencher fase como quantidade de sessões existentes + 1
            try {
                $('#numero_sessao').val({{ $inscription->sessions->count() + 1 }});
            } catch (e) {
                // se não houver $inscription no contexto, ignore
            }

            $('#modalSessao').modal('show');
        }

        function editarSessao(id) {
            // Buscar dados da sessão
            fetch(`/sessions/${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const sessao = data.data;
                        
                        // Preencher formulário
                        $('#sessaoId').val(sessao.id);
                        $('#sessaoMethod').val('PUT');
                        $('#numero_sessao').val(sessao.numero_sessao);
                        $('#preceptor_record_id').val(sessao.preceptor_record_id);
                        $('#semana_mes').val(sessao.semana_mes);
                        $('#fase').val(sessao.fase);
                        $('#tipo').val(sessao.tipo);
                        $('#status').val(sessao.status);
                        
                        // Formatar datas para datetime-local
                        if (sessao.data_agendada) {
                            const dataAgendada = new Date(sessao.data_agendada);
                            $('#data_agendada').val(dataAgendada.toISOString().slice(0, 16));
                        }
                        if (sessao.data_realizada) {
                            const dataRealizada = new Date(sessao.data_realizada);
                            $('#data_realizada').val(dataRealizada.toISOString().slice(0, 16));
                        }
                        
                        // Campos de confirmação
                        $('#confirmou_24h').val(sessao.confirmou_24h ? '1' : '0');
                        $('#medico_confirmou').val(sessao.medico_confirmou);
                        $('#medico_compareceu').val(sessao.medico_compareceu ? '1' : '0');
                        
                        // Motivo desmarcou
                        if (sessao.medico_confirmou === 'desmarcou') {
                            $('#campo_motivo_desmarcou').show();
                            $('#motivo_desmarcou').val(sessao.motivo_desmarcou);
                        }
                        
                        // No show
                        if (sessao.status === 'no_show' || sessao.medico_compareceu === 0 || sessao.medico_compareceu === false) {
                            $('#campos_no_show').show();
                            $('#status_reagendamento').val(sessao.status_reagendamento);
                            
                            if (sessao.status_reagendamento === 'reagendado') {
                                $('#campo_data_remarcada').show();
                                $('#data_remarcada').val(sessao.data_remarcada);
                            }
                        }
                        
                        // Observações
                        $('#observacoes').val(sessao.observacoes);
                        $('#resultado').val(sessao.resultado);
                        
                        // Atualizar título do modal
                        $('#modalSessaoTitle').text('Editar Sessão');
                        
                        // Abrir modal
                        $('#modalSessao').modal('show');
                    } else {
                        alert('Erro ao carregar dados da sessão');
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao carregar dados da sessão');
                });
        }
    </script>
@endpush
