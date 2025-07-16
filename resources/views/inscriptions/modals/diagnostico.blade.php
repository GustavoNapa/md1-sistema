<!-- Modal Diagnóstico -->
<div class="modal fade" id="modalDiagnostico" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Novo Diagnóstico</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form class="form-modal" action="{{ route('diagnostics.store') }}" method="POST">
                @csrf
                <input type="hidden" name="inscription_id" value="{{ $inscription->id }}">
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="diagnosis" class="form-label">Diagnóstico *</label>
                        <input type="text" class="form-control" name="diagnosis" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="date" class="form-label">Data do Diagnóstico</label>
                        <input type="date" class="form-control" name="date" value="{{ date('Y-m-d') }}">
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Observações e Detalhes</label>
                        <textarea class="form-control" name="notes" rows="4" placeholder="Descreva detalhes do diagnóstico, sintomas, exames realizados, etc."></textarea>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>
