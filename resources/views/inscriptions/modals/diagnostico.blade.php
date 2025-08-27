<!-- Modal Diagnóstico -->
<div class="modal fade" id="modalDiagnostico" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Novo Diagnóstico</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form class="form-modal" action="{{ route('diagnostics.store') }}" method="POST" id="formDiagnostico">
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
                    <button type="submit" class="btn btn-warning" id="btnSalvarDiagnostico">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            // Controle específico do modal de diagnóstico
            $('#formDiagnostico').on('submit', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const form = $(this);
                const formData = new FormData(this);
                const url = form.attr('action');
                const submitBtn = $('#btnSalvarDiagnostico');
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
                        $('#modalDiagnostico').modal('hide');
                        form[0].reset();
                        
                        // Atualizar aba de diagnósticos
                        atualizarAbaDiagnosticos(data.data);
                        
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

        function abrirModalDiagnostico() {
            $('#modalDiagnostico').modal('show');
        }
    </script>
@endpush
