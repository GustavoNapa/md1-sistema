<!-- Modal Preceptor -->
<div class="modal fade" id="modalPreceptor" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Novo Registro de Preceptor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form class="form-modal" action="{{ route('preceptor-records.store') }}" method="POST">
                @csrf
                <input type="hidden" name="inscription_id" value="{{ $inscription->id }}">
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nome_preceptor" class="form-label">Nome do Preceptor *</label>
                        <input type="text" class="form-control" name="nome_preceptor" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="crm" class="form-label">CRM</label>
                                <input type="text" class="form-control" name="crm">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="especialidade" class="form-label">Especialidade</label>
                                <input type="text" class="form-control" name="especialidade">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="hospital" class="form-label">Hospital/Instituição</label>
                        <input type="text" class="form-control" name="hospital">
                    </div>
                    
                    <div class="mb-3">
                        <label for="observacoes" class="form-label">Observações</label>
                        <textarea class="form-control" name="observacoes" rows="3"></textarea>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>
