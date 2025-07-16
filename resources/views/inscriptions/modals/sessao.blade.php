<!-- Modal Sessão -->
<div class="modal fade" id="modalSessao" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nova Sessão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form class="form-modal" action="{{ route('sessions.store') }}" method="POST" id="formSessao">
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
                    <button type="submit" class="btn btn-info" id="btnSalvarSessao">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Controle específico do modal de sessão
    $('#formSessao').on('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const form = $(this);
        const formData = new FormData(this);
        const url = form.attr('action');
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
                
                // Atualizar aba de sessões
                atualizarAbaSessoes(data.data);
                
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

function abrirModalSessao() {
    $('#modalSessao').modal('show');
}
</script>
