<!-- Modal Conquista -->
<div class="modal fade" id="modalConquista" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nova Conquista</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form class="form-modal" action="{{ route('achievements.store') }}" method="POST">
                @csrf
                <input type="hidden" name="inscription_id" value="{{ $inscription->id }}">
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="title" class="form-label">Título da Conquista *</label>
                        <input type="text" class="form-control" name="title" required placeholder="Ex: Primeira cirurgia realizada">
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Descrição</label>
                        <textarea class="form-control" name="description" rows="3" placeholder="Descreva os detalhes da conquista alcançada..."></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="achieved_at" class="form-label">Data da Conquista</label>
                        <input type="date" class="form-control" name="achieved_at" value="{{ date('Y-m-d') }}">
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>
