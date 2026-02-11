<!-- Modal Sessão -->
<div class="modal fade" id="modalSessao" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalSessaoTitle">Nova Sessão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form class="form-modal" action="{{ route('sessions.store') }}" method="POST" id="formSessao" data-next-session="{{ $inscription->sessions->count() + 1 }}">
                @csrf
                <input type="hidden" name="_method" value="POST" id="sessaoMethod">
                <input type="hidden" name="inscription_id" value="{{ $inscription->id }}">
                <input type="hidden" name="sessao_id" id="sessaoId" value="">
                
                <div class="modal-body">
                    <!-- Informações Básicas -->
                    <h6 class="mb-3 text-primary">Informações Básicas</h6>
                    <div class="row">
                        <div class="col col-md-6">
                            <div class="mb-3">
                                <label for="numero_sessao" class="form-label">Fase *</label>
                                <input type="number" class="form-control" name="numero_sessao" id="numero_sessao" required>
                            </div>
                        </div>
                        <div class="col col-md-6">
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
                    </div>
                    <div class="row">
                        <div class="col col-md-6">
                            <div class="mb-3">
                                <label for="semana_mes" class="form-label">Semana do mês em que a preceptoria foi realizada *</label>
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

                        <div class="col col-md-6">
                            <div class="mb-3">
                                <label for="fase" class="form-label">Qual fase foi trabalhada com o médico na chamada? *</label>
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
                    </div>

                    

                        
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tipo" class="form-label">Tipo de sessão *</label>
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
                        <div class="col-md-6">
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
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="confirmou_24h" id="confirmou_24h" value="1">
                                <label class="form-check-label" for="confirmou_24h">
                                    Confirmou 24hrs antes? *
                                </label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="medico_confirmou" id="medico_confirmou" value="confirmou" data-value-desmarcou="desmarcou">
                                <label class="form-check-label" for="medico_confirmou">
                                    Médico confirmou? *
                                </label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="medico_compareceu" id="medico_compareceu" value="1" data-value-unchecked="0">
                                <label class="form-check-label" for="medico_compareceu">
                                    Médico compareceu? *
                                </label>
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

                    <!-- Implementação e Desenvolvimento -->
                    <hr class="my-4">
                    <h6 class="mb-3 text-primary">Implementação e Desenvolvimento</h6>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="implementacao_fase" class="form-label">Quanto o Médico implementou da Fase tratada na chamada?</label>
                                <select class="form-select" name="implementacao_fase" id="implementacao_fase">
                                    <option value="">Selecione...</option>
                                    <option value="0-25">0% - 25%</option>
                                    <option value="25-50">25% - 50%</option>
                                    <option value="50-75">50% - 75%</option>
                                    <option value="75-100">75% - 100%</option>
                                    <option value="100">100% - Concluído</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="impacto_faturamento" class="form-label">Impacto no faturamento da Fase no mês da preceptoria?</label>
                                <select class="form-select" name="impacto_faturamento" id="impacto_faturamento">
                                    <option value="">Selecione...</option>
                                    <option value="sem_impacto">Sem impacto</option>
                                    <option value="baixo">Baixo</option>
                                    <option value="medio">Médio</option>
                                    <option value="alto">Alto</option>
                                    <option value="muito_alto">Muito Alto</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="dificuldades_travas" class="form-label">Qual a dificuldade ou trava apresentada na implementação pelo médico?</label>
                        <textarea class="form-control" name="dificuldades_travas" id="dificuldades_travas" rows="3" placeholder="Descreva as dificuldades encontradas"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="desenvolvimento_ultima_preceptoria" class="form-label">Qual foi o desenvolvimento desde a última preceptoria?</label>
                        <textarea class="form-control" name="desenvolvimento_ultima_preceptoria" id="desenvolvimento_ultima_preceptoria" rows="3" placeholder="Descreva o desenvolvimento observado"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="avancos_importantes" class="form-label">Quais foram os avanços mais importantes na mentoria?</label>
                        <textarea class="form-control" name="avancos_importantes" id="avancos_importantes" rows="3" placeholder="Descreva os principais avanços"></textarea>
                    </div>

                    <!-- Depoimentos e Indicações -->
                    <hr class="my-4">
                    <h6 class="mb-3 text-primary">Depoimentos e Indicações</h6>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="momento_depoimento" class="form-label">Houve algum momento que vale a pena recorte para depoimento?</label>
                                <textarea class="form-control" name="momento_depoimento" id="momento_depoimento" rows="3" placeholder="Descreva o momento relevante para depoimento"></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check form-switch mt-4">
                                    <input class="form-check-input" type="checkbox" name="conseguiu_indicacao" id="conseguiu_indicacao" value="1">
                                    <label class="form-check-label" for="conseguiu_indicacao">
                                        Conseguimos indicação nessa preceptoria?
                                    </label>
                                </div>
                                <div id="campo_detalhes_indicacao" style="display: none;" class="mt-3">
                                    <label for="detalhes_indicacao" class="form-label">Detalhes da indicação</label>
                                    <textarea class="form-control" name="detalhes_indicacao" id="detalhes_indicacao" rows="2" placeholder="Nome do indicado, contato, etc."></textarea>
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

                    <!-- Faturamento do Médico -->
                    <hr class="my-4">
                    <h6 class="mb-3 text-primary">Faturamento do Médico (Relatório)</h6>
                    <p class="text-muted small mb-3">Registre o faturamento da empresa do médico para relatório de lucro e margem</p>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="faturamento_mes_ano" class="form-label">Mês/Ano</label>
                                <input type="month" class="form-control" name="faturamento_mes_ano" id="faturamento_mes_ano">
                                <small class="text-muted">Preenche automaticamente com a data da sessão</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="faturamento_valor" class="form-label">Valor Faturado</label>
                                <input type="number" step="0.01" class="form-control" name="faturamento_valor" id="faturamento_valor" placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="faturamento_data_vencimento" class="form-label">Data de Vencimento</label>
                                <input type="date" class="form-control" name="faturamento_data_vencimento" id="faturamento_data_vencimento">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="faturamento_status" class="form-label">Status</label>
                                <select class="form-select" name="faturamento_status" id="faturamento_status">
                                    <option value="">Selecione...</option>
                                    <option value="pendente">Pendente</option>
                                    <option value="pago">Pago</option>
                                    <option value="vencido">Vencido</option>
                                    <option value="cancelado">Cancelado</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="faturamento_observacoes" class="form-label">Observações</label>
                                <textarea class="form-control" name="faturamento_observacoes" id="faturamento_observacoes" rows="2" placeholder="Observações sobre o faturamento"></textarea>
                            </div>
                        </div>
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
            // Variável para rastrear o estado real de medico_confirmou (para suportar ambos checkbox e select)
            let medicoConfirmouValue = '';
            let medicoCompareceuValue = '';

            // Controle de checkbox para "Médico confirmou?"
            $('#medico_confirmou').on('change', function() {
                if (this.checked) {
                    medicoConfirmouValue = 'confirmou';
                    $('#campo_motivo_desmarcou').hide();
                } else {
                    // Se não marcado, mostrar opção de desmarcamento
                    medicoConfirmouValue = 'desmarcou';
                    $('#campo_motivo_desmarcou').show();
                }
            });

            // Controle de checkbox para "Médico compareceu?"
            $('#medico_compareceu').on('change', function() {
                if (this.checked) {
                    medicoCompareceuValue = '1';
                    $('#campos_no_show').hide();
                } else {
                    medicoCompareceuValue = '0';
                    $('#campos_no_show').show();
                }
            });

            // Controle para status
            $('#status').on('change', function() {
                if ($(this).val() === 'no_show') {
                    // Ao selecionar no_show, desmarcar comparecimento
                    $('#medico_compareceu').prop('checked', false).trigger('change');
                }
            });

            // Controle de reagendamento
            $('#status_reagendamento').on('change', function() {
                if ($(this).val() === 'reagendado') {
                    $('#campo_data_remarcada').show();
                } else {
                    $('#campo_data_remarcada').hide();
                }
            });

            // Pré-preencher Mês/Ano do faturamento quando data realizada for alterada
            $('#data_realizada').on('change', function() {
                const dataRealizada = $(this).val();
                if (dataRealizada) {
                    // Converter de datetime-local (YYYY-MM-DDTHH:MM) para month (YYYY-MM)
                    const mesAno = dataRealizada.substring(0, 7);
                    $('#faturamento_mes_ano').val(mesAno);
                }
            });

            // Controle do campo de indicação
            $('#conseguiu_indicacao').on('change', function() {
                if (this.checked) {
                    $('#campo_detalhes_indicacao').show();
                } else {
                    $('#campo_detalhes_indicacao').hide();
                    $('#detalhes_indicacao').val('');
                }
            });

            // Controle específico do modal de sessão
            $('#formSessao').on('submit', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const form = $(this);
                const formData = new FormData(this);
                
                // Ajustar valores dos checkboxes para valores esperados
                formData.set('medico_confirmou', medicoConfirmouValue || $('#medico_confirmou').is(':checked') ? 'confirmou' : 'desmarcou');
                formData.set('medico_compareceu', medicoCompareceuValue || ($('#medico_compareceu').is(':checked') ? '1' : '0'));
                
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
            
            // Resetar variáveis de estado
            medicoConfirmouValue = '';
            medicoCompareceuValue = '';
            
            // Limpar campos de faturamento
            $('#faturamento_mes_ano').val('');
            $('#faturamento_valor').val('');
            $('#faturamento_data_vencimento').val('');
            $('#faturamento_status').val('');
            $('#faturamento_observacoes').val('');
            
            // Limpar novos campos de implementação
            $('#implementacao_fase').val('');
            $('#impacto_faturamento').val('');
            $('#dificuldades_travas').val('');
            $('#desenvolvimento_ultima_preceptoria').val('');
            $('#avancos_importantes').val('');
            $('#momento_depoimento').val('');
            $('#conseguiu_indicacao').prop('checked', false);
            $('#detalhes_indicacao').val('');
            $('#campo_detalhes_indicacao').hide();
            
            // Pré-preencher fase com o próximo número de sessão
            const nextSession = $('#formSessao').data('next-session');
            if (nextSession) {
                $('#numero_sessao').val(nextSession);
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
                        $('#confirmou_24h').prop('checked', sessao.confirmou_24h ? true : false);
                        
                        // Médico confirmou - atualizar checkbox
                        medicoConfirmouValue = sessao.medico_confirmou;
                        $('#medico_confirmou').prop('checked', sessao.medico_confirmou === 'confirmou' ? true : false);
                        
                        // Médico compareceu - atualizar checkbox
                        medicoCompareceuValue = sessao.medico_compareceu ? '1' : '0';
                        $('#medico_compareceu').prop('checked', sessao.medico_compareceu ? true : false);
                        
                        // Motivo desmarcou
                        if (sessao.medico_confirmou === 'desmarcou') {
                            $('#campo_motivo_desmarcou').show();
                            $('#motivo_desmarcou').val(sessao.motivo_desmarcou);
                        } else {
                            $('#campo_motivo_desmarcou').hide();
                        }
                        
                        // No show
                        if (sessao.status === 'no_show' || sessao.medico_compareceu === 0 || sessao.medico_compareceu === false) {
                            $('#campos_no_show').show();
                            $('#status_reagendamento').val(sessao.status_reagendamento);
                            
                            if (sessao.status_reagendamento === 'reagendado') {
                                $('#campo_data_remarcada').show();
                                $('#data_remarcada').val(sessao.data_remarcada);
                            } else {
                                $('#campo_data_remarcada').hide();
                            }
                        } else {
                            $('#campos_no_show').hide();
                        }
                        
                        // Observações
                        $('#observacoes').val(sessao.observacoes);
                        $('#resultado').val(sessao.resultado);
                        
                        // Campos de Faturamento (se existirem)
                        if (sessao.faturamento) {
                            $('#faturamento_mes_ano').val(sessao.faturamento.mes_ano || '');
                            $('#faturamento_valor').val(sessao.faturamento.valor || '');
                            $('#faturamento_data_vencimento').val(sessao.faturamento.data_vencimento || '');
                            $('#faturamento_status').val(sessao.faturamento.status || '');
                            $('#faturamento_observacoes').val(sessao.faturamento.observacoes || '');
                        } else {
                            // Limpar campos se não houver faturamento
                            $('#faturamento_mes_ano').val('');
                            $('#faturamento_valor').val('');
                            $('#faturamento_data_vencimento').val('');
                            $('#faturamento_status').val('');
                            $('#faturamento_observacoes').val('');
                        }
                        
                        // Novos campos de implementação e desenvolvimento
                        $('#implementacao_fase').val(sessao.implementacao_fase || '');
                        $('#impacto_faturamento').val(sessao.impacto_faturamento || '');
                        $('#dificuldades_travas').val(sessao.dificuldades_travas || '');
                        $('#desenvolvimento_ultima_preceptoria').val(sessao.desenvolvimento_ultima_preceptoria || '');
                        $('#avancos_importantes').val(sessao.avancos_importantes || '');
                        $('#momento_depoimento').val(sessao.momento_depoimento || '');
                        
                        // Campo de indicação
                        $('#conseguiu_indicacao').prop('checked', sessao.conseguiu_indicacao ? true : false);
                        if (sessao.conseguiu_indicacao) {
                            $('#campo_detalhes_indicacao').show();
                            $('#detalhes_indicacao').val(sessao.detalhes_indicacao || '');
                        } else {
                            $('#campo_detalhes_indicacao').hide();
                            $('#detalhes_indicacao').val('');
                        }
                        
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
