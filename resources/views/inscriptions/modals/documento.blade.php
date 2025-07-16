<!-- Modal Documento -->
<div class="modal fade" id="documentoModal" tabindex="-1" aria-labelledby="documentoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="documentoModalLabel">
                    <i class="fas fa-file-alt"></i> <span id="documento-modal-title">Novo Documento</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="documentoForm" enctype="multipart/form-data">
                <div class="modal-body">
                    @csrf
                    <input type="hidden" id="documento_id" name="documento_id">
                    <input type="hidden" id="documento_method" name="_method" value="POST">

                    <!-- Tipo de Documento -->
                    <div class="mb-3">
                        <label class="form-label">Tipo de Documento <span class="text-danger">*</span></label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" id="type_upload" value="upload" checked>
                                    <label class="form-check-label" for="type_upload">
                                        <i class="fas fa-upload text-primary"></i> Upload de Arquivo
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" id="type_link" value="link">
                                    <label class="form-check-label" for="type_link">
                                        <i class="fas fa-link text-success"></i> Link Externo
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Título -->
                    <div class="mb-3">
                        <label for="title" class="form-label">Título do Documento <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" required 
                               placeholder="Ex: Contrato de Prestação de Serviços">
                        <div class="invalid-feedback"></div>
                    </div>

                    <!-- Categoria -->
                    <div class="mb-3">
                        <label for="category" class="form-label">Categoria <span class="text-danger">*</span></label>
                        <select class="form-select" id="category" name="category" required>
                            <option value="">Selecione uma categoria</option>
                            <option value="contrato">Contrato</option>
                            <option value="documento_pessoal">Documento Pessoal</option>
                            <option value="certificado">Certificado</option>
                            <option value="comprovante_pagamento">Comprovante de Pagamento</option>
                            <option value="material_curso">Material do Curso</option>
                            <option value="outros">Outros</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>

                    <!-- Upload de Arquivo -->
                    <div class="mb-3" id="upload_section">
                        <label for="file" class="form-label">Arquivo <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="file" name="file" 
                               accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif,.zip,.rar">
                        <div class="form-text">
                            Formatos aceitos: PDF, DOC, DOCX, JPG, PNG, GIF, ZIP, RAR. Tamanho máximo: 10MB.
                        </div>
                        <div class="invalid-feedback"></div>
                    </div>

                    <!-- Link Externo -->
                    <div class="mb-3 d-none" id="link_section">
                        <label for="external_url" class="form-label">URL do Documento <span class="text-danger">*</span></label>
                        <input type="url" class="form-control" id="external_url" name="external_url" 
                               placeholder="https://drive.google.com/...">
                        <div class="form-text">
                            Cole aqui o link do Google Drive, Dropbox ou outro serviço de armazenamento.
                        </div>
                        <div class="invalid-feedback"></div>
                    </div>

                    <!-- Descrição -->
                    <div class="mb-3">
                        <label for="description" class="form-label">Descrição (Opcional)</label>
                        <textarea class="form-control" id="description" name="description" rows="3" 
                                  placeholder="Adicione uma descrição ou observações sobre este documento"></textarea>
                        <div class="invalid-feedback"></div>
                    </div>

                    <!-- Opções -->
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_required" name="is_required" value="1">
                            <label class="form-check-label" for="is_required">
                                <i class="fas fa-exclamation-triangle text-warning"></i> Documento obrigatório
                            </label>
                        </div>
                    </div>

                    <!-- Preview do arquivo (para edição) -->
                    <div class="mb-3 d-none" id="current_file_section">
                        <label class="form-label">Arquivo Atual</label>
                        <div class="card">
                            <div class="card-body py-2">
                                <div class="d-flex align-items-center">
                                    <i id="current_file_icon" class="fas fa-file me-2"></i>
                                    <span id="current_file_name">arquivo.pdf</span>
                                    <span class="badge bg-light text-dark ms-2" id="current_file_size">1.2 MB</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary" id="documento-submit-btn">
                        <i class="fas fa-save"></i> <span id="documento-submit-text">Salvar Documento</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Controle do tipo de documento
document.addEventListener('DOMContentLoaded', function() {
    const typeUpload = document.getElementById('type_upload');
    const typeLink = document.getElementById('type_link');
    const uploadSection = document.getElementById('upload_section');
    const linkSection = document.getElementById('link_section');
    const fileInput = document.getElementById('file');
    const urlInput = document.getElementById('external_url');

    function toggleDocumentType() {
        if (typeUpload.checked) {
            uploadSection.classList.remove('d-none');
            linkSection.classList.add('d-none');
            fileInput.required = true;
            urlInput.required = false;
            urlInput.value = '';
        } else {
            uploadSection.classList.add('d-none');
            linkSection.classList.remove('d-none');
            fileInput.required = false;
            urlInput.required = true;
            fileInput.value = '';
        }
    }

    typeUpload.addEventListener('change', toggleDocumentType);
    typeLink.addEventListener('change', toggleDocumentType);
});

// Função para abrir modal de novo documento
function abrirModalDocumento() {
    // Reset do formulário
    document.getElementById('documentoForm').reset();
    document.getElementById('documento_id').value = '';
    document.getElementById('documento_method').value = 'POST';
    document.getElementById('documento-modal-title').textContent = 'Novo Documento';
    document.getElementById('documento-submit-text').textContent = 'Salvar Documento';
    
    // Mostrar seção de upload por padrão
    document.getElementById('type_upload').checked = true;
    document.getElementById('upload_section').classList.remove('d-none');
    document.getElementById('link_section').classList.add('d-none');
    document.getElementById('current_file_section').classList.add('d-none');
    document.getElementById('file').required = true;
    document.getElementById('external_url').required = false;
    
    // Limpar erros
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
    
    // Abrir modal
    new bootstrap.Modal(document.getElementById('documentoModal')).show();
}

// Função para editar documento
function editarDocumento(documentoId) {
    // Buscar dados do documento
    fetch(`/inscriptions/{{ $inscription->id }}/documents`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const documento = data.documents.find(d => d.id === documentoId);
                if (documento) {
                    // Preencher formulário
                    document.getElementById('documento_id').value = documento.id;
                    document.getElementById('documento_method').value = 'PUT';
                    document.getElementById('title').value = documento.title;
                    document.getElementById('category').value = documento.category;
                    document.getElementById('description').value = documento.description || '';
                    document.getElementById('is_required').checked = documento.is_required;
                    
                    // Configurar tipo
                    if (documento.type === 'link') {
                        document.getElementById('type_link').checked = true;
                        document.getElementById('external_url').value = documento.external_url || '';
                        document.getElementById('upload_section').classList.add('d-none');
                        document.getElementById('link_section').classList.remove('d-none');
                        document.getElementById('file').required = false;
                        document.getElementById('external_url').required = true;
                    } else {
                        document.getElementById('type_upload').checked = true;
                        document.getElementById('upload_section').classList.remove('d-none');
                        document.getElementById('link_section').classList.add('d-none');
                        document.getElementById('file').required = false; // Não obrigatório na edição
                        document.getElementById('external_url').required = false;
                        
                        // Mostrar arquivo atual
                        if (documento.formatted_file_size) {
                            document.getElementById('current_file_section').classList.remove('d-none');
                            document.getElementById('current_file_icon').className = documento.icon_class;
                            document.getElementById('current_file_name').textContent = documento.title;
                            document.getElementById('current_file_size').textContent = documento.formatted_file_size;
                        }
                    }
                    
                    // Atualizar modal
                    document.getElementById('documento-modal-title').textContent = 'Editar Documento';
                    document.getElementById('documento-submit-text').textContent = 'Atualizar Documento';
                    
                    // Abrir modal
                    new bootstrap.Modal(document.getElementById('documentoModal')).show();
                }
            }
        })
        .catch(error => {
            console.error('Erro ao buscar documento:', error);
            alert('Erro ao carregar dados do documento');
        });
}

// Submit do formulário
document.getElementById('documentoForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const documentoId = document.getElementById('documento_id').value;
    const method = document.getElementById('documento_method').value;
    
    let url = `/inscriptions/{{ $inscription->id }}/documents`;
    if (documentoId) {
        url += `/${documentoId}`;
    }
    
    // Configurar método para PUT se for edição
    if (method === 'PUT') {
        formData.append('_method', 'PUT');
    }
    
    // Desabilitar botão
    const submitBtn = document.getElementById('documento-submit-btn');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Salvando...';
    
    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            bootstrap.Modal.getInstance(document.getElementById('documentoModal')).hide();
            atualizarAbaDocumentos();
        } else {
            if (data.errors) {
                // Mostrar erros de validação
                Object.keys(data.errors).forEach(field => {
                    const input = document.getElementById(field);
                    if (input) {
                        input.classList.add('is-invalid');
                        const feedback = input.parentNode.querySelector('.invalid-feedback');
                        if (feedback) {
                            feedback.textContent = data.errors[field][0];
                        }
                    }
                });
            } else {
                alert(data.message || 'Erro ao salvar documento');
            }
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao salvar documento');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
});

// Função para excluir documento
function excluirDocumento(documentoId) {
    if (!confirm('Tem certeza que deseja excluir este documento? Esta ação não pode ser desfeita.')) {
        return;
    }
    
    fetch(`/inscriptions/{{ $inscription->id }}/documents/${documentoId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            atualizarAbaDocumentos();
        } else {
            alert(data.message || 'Erro ao excluir documento');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao excluir documento');
    });
}

// Função para toggle de verificação
function toggleVerificacao(documentoId) {
    fetch(`/inscriptions/{{ $inscription->id }}/documents/${documentoId}/toggle-verification`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            atualizarAbaDocumentos();
        } else {
            alert(data.message || 'Erro ao alterar verificação');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao alterar verificação');
    });
}

// Função para atualizar a aba de documentos
function atualizarAbaDocumentos() {
    fetch(`/inscriptions/{{ $inscription->id }}/documents`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const documents = data.documents;
                
                // Atualizar badges
                document.getElementById('total-documentos').textContent = documents.length;
                document.getElementById('documentos-verificados').textContent = documents.filter(d => d.is_verified).length;
                document.getElementById('documentos-obrigatorios').textContent = documents.filter(d => d.is_required).length;
                document.getElementById('documentos-contratos').textContent = documents.filter(d => d.category === 'contrato').length;
                
                // Atualizar badge na aba
                const tabBadge = document.querySelector('#documentos-tab .badge');
                if (tabBadge) {
                    tabBadge.textContent = documents.length;
                }
                
                // Atualizar lista de documentos
                const container = document.getElementById('documentos-container');
                if (documents.length > 0) {
                    let html = '<div class="row" id="documentos-grid">';
                    
                    documents.forEach(document => {
                        html += `
                            <div class="col-md-6 mb-3" data-document-id="${document.id}">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="card-title mb-1">
                                                <i class="${document.icon_class}"></i>
                                                ${document.title}
                                            </h6>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    ${document.download_url ? `<li><a class="dropdown-item" href="${document.download_url}" target="_blank">
                                                        <i class="fas fa-download"></i> Download/Abrir
                                                    </a></li>` : ''}
                                                    <li><a class="dropdown-item" href="#" onclick="editarDocumento(${document.id})">
                                                        <i class="fas fa-edit"></i> Editar
                                                    </a></li>
                                                    <li><a class="dropdown-item" href="#" onclick="toggleVerificacao(${document.id})">
                                                        <i class="fas fa-${document.is_verified ? 'times' : 'check'}"></i> 
                                                        ${document.is_verified ? 'Remover Verificação' : 'Marcar como Verificado'}
                                                    </a></li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><a class="dropdown-item text-danger" href="#" onclick="excluirDocumento(${document.id})">
                                                        <i class="fas fa-trash"></i> Excluir
                                                    </a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-2">
                                            <span class="badge bg-primary">${document.category_label}</span>
                                            <span class="badge ${document.status_badge_class}">${document.status_label}</span>
                                            <span class="badge bg-light text-dark">${document.type_label}</span>
                                        </div>
                                        
                                        ${document.description ? `<p class="card-text small text-muted mb-2">${document.description.substring(0, 100)}${document.description.length > 100 ? '...' : ''}</p>` : ''}
                                        
                                        <div class="small text-muted">
                                            ${document.formatted_file_size !== '-' ? `<div><i class="fas fa-hdd"></i> ${document.formatted_file_size}</div>` : ''}
                                            <div><i class="fas fa-calendar"></i> ${document.created_at}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    
                    html += '</div>';
                    container.innerHTML = html;
                } else {
                    container.innerHTML = `
                        <div class="text-center py-5 text-muted" id="documentos-empty">
                            <i class="fas fa-file-alt fa-3x mb-3"></i>
                            <h5>Nenhum documento anexado</h5>
                            <p>Adicione contratos, certificados e outros documentos importantes para esta inscrição.</p>
                            <button class="btn btn-primary" onclick="abrirModalDocumento()">
                                <i class="fas fa-plus"></i> Adicionar Primeiro Documento
                            </button>
                        </div>
                    `;
                }
            }
        })
        .catch(error => {
            console.error('Erro ao atualizar documentos:', error);
        });
}
</script>

