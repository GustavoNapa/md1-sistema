<!-- Modal Sessão -->
<div class="modal fade" id="modalSessao" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nova Sessão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form class="form-modal" action="{{ route('sessions.store') }}" method="POST">
                @csrf
                <input type="hidden" name="inscription_id" value="{{ $inscription->id }}">
                
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="numero_sessao" class="form-label">Número da Sessão *</label>
                                <input type="number" class="form-control" name="numero_sessao" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="fase" class="form-label">Fase</label>
                                <input type="text" class="form-control" name="fase" placeholder="Fase 01">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="tipo" class="form-label">Tipo</label>
                                <select class="form-select" name="tipo">
                                    <option value="">Selecione</option>
                                    <option value="diagnostico">Diagnóstico</option>
                                    <option value="chamada_start">Chamada Start</option>
                                    <option value="onboarding">Onboarding</option>
                                    <option value="mentoria">Mentoria</option>
                                    <option value="follow_up">Follow-up</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="data_agendada" class="form-label">Data Agendada</label>
                                <input type="datetime-local" class="form-control" name="data_agendada">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="data_realizada" class="form-label">Data Realizada</label>
                                <input type="datetime-local" class="form-control" name="data_realizada">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option value="agendada">Agendada</option>
                            <option value="realizada">Realizada</option>
                            <option value="cancelada">Cancelada</option>
                            <option value="reagendada">Reagendada</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="observacoes" class="form-label">Observações</label>
                        <textarea class="form-control" name="observacoes" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="resultado" class="form-label">Resultado da Sessão</label>
                        <textarea class="form-control" name="resultado" rows="3"></textarea>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-info">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>
