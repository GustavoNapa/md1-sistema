<!-- Modal Preceptor -->
<div class="modal fade" id="modalPreceptor" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Novo Registro de Preceptor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form class="form-modal" action="{{ route('preceptor-records.store') }}" method="POST" id="formPreceptor">
                @csrf
                <input type="hidden" name="inscription_id" value="{{ $inscription->id }}">
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nome_preceptor" class="form-label">Nome do Preceptor *</label>
                        <input type="text" class="form-control" name="nome_preceptor" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="historico_preceptor" class="form-label">Histórico do Preceptor</label>
                        <textarea class="form-control" name="historico_preceptor" rows="3"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="data_preceptor_informado" class="form-label">Data Preceptor Informado</label>
                                <input type="date" class="form-control" name="data_preceptor_informado">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="data_preceptor_contato" class="form-label">Data Preceptor Contato</label>
                                <input type="date" class="form-control" name="data_preceptor_contato">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="nome_secretaria" class="form-label">Nome Secretária</label>
                                <input type="text" class="form-control" name="nome_secretaria">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="email_clinica" class="form-label">Email Clínica</label>
                                <input type="email" class="form-control" name="email_clinica">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="whatsapp_clinica" class="form-label">WhatsApp Clínica</label>
                                <input type="text" class="form-control" name="whatsapp_clinica">
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

<script>
$(document).ready(function() {
    // Controle específico do modal de preceptor
    $('#formPreceptor').on('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const form = $(this);
        const formData = new FormData(this);
        const url = form.attr('action');
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
                
                // Atualizar aba de preceptores
                atualizarAbaPreceptores(data.data);
                
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

function abrirModalPreceptor() {
    $('#modalPreceptor').modal('show');
}
</script>
