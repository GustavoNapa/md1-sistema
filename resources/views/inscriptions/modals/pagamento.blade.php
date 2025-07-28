<!-- Modal Pagamento -->
<div class="modal fade" id="modalPagamento" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Novo Pagamento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form class="form-modal" action="{{ route('payments.store') }}" method="POST" id="formPagamento">
                @csrf
                <input type="hidden" name="inscription_id" value="{{ $inscription->id }}">
                
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="valor" class="form-label">Valor *</label>
                                <input type="number" class="form-control" name="valor" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="data_pagamento" class="form-label">Data do Pagamento</label>
                                <input type="date" class="form-control" name="data_pagamento">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="metodo_pagamento" class="form-label">Método de Pagamento</label>
                                <select class="form-select" name="metodo_pagamento">
                                    <option value="">Selecione</option>
                                    <option value="cartao_credito">Cartão de Crédito</option>
                                    <option value="cartao_debito">Cartão de Débito</option>
                                    <option value="pix">PIX</option>
                                    <option value="boleto">Boleto</option>
                                    <option value="transferencia">Transferência</option>
                                    <option value="dinheiro">Dinheiro</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <option value="pendente">Pendente</option>
                                    <option value="pago">Pago</option>
                                    <option value="cancelado">Cancelado</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    

                    
                    <div class="mb-3">
                        <label for="observacoes" class="form-label">Observações</label>
                        <textarea class="form-control" name="observacoes" rows="3"></textarea>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success" id="btnSalvarPagamento">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Controle específico do modal de pagamento
    $('#formPagamento').on('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const form = $(this);
        const formData = new FormData(this);
        const url = form.attr('action');
        const submitBtn = $('#btnSalvarPagamento');
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
                $('#modalPagamento').modal('hide');
                form[0].reset();
                
                // Atualizar aba de pagamentos
                atualizarAbaPagamentos(data.data);
                
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

function abrirModalPagamento() {
    $('#modalPagamento').modal('show');
}
</script>
