<!-- Modal Conquista -->
<div class="modal fade" id="modalConquista" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nova Conquista</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form class="form-modal" action="{{ route('achievements.store') }}" method="POST" id="formConquista">
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
                    
                    <div class="mb-3">
                        <label for="achievement_type_id" class="form-label">Tipo de Conquista</label>
                        <select class="form-select" name="achievement_type_id">
                            <option value="">Selecione o tipo</option>
                            @foreach($achievementTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success" id="btnSalvarConquista">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            // Controle específico do modal de conquista
            $('#formConquista').on('submit', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const form = $(this);
                const formData = new FormData(this);
                const url = form.attr('action');
                const submitBtn = $('#btnSalvarConquista');
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
                        $('#modalConquista').modal('hide');
                        form[0].reset();
                        
                        // Atualizar aba de conquistas
                        atualizarAbaConquistas(data.data);
                        
                        // Mostrar mensagem de sucesso
                        mostrarMensagemSucesso(data.message);
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

        function abrirModalConquista() {
            $('#modalConquista').modal('show');
        }
    </script>
@endpush
