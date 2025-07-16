@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Documentos - {{ $inscription->client->name }}</h4>
                    <div>
                        <span class="badge bg-info">{{ $inscription->product }}</span>
                        @if($inscription->class_group)
                            <span class="badge bg-secondary">{{ $inscription->class_group }}</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <!-- Upload Form -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Upload de Documento</h5>
                                </div>
                                <div class="card-body">
                                    <form id="uploadForm" enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="collection" class="form-label">Tipo de Documento *</label>
                                                    <select class="form-select" id="collection" name="collection" required>
                                                        <option value="">Selecione o tipo</option>
                                                        <option value="documents">Documentos Gerais</option>
                                                        <option value="contracts">Contratos</option>
                                                        <option value="certificates">Certificados</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="name" class="form-label">Nome do Documento</label>
                                                    <input type="text" class="form-control" id="name" name="name" 
                                                           placeholder="Nome personalizado (opcional)">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="file" class="form-label">Arquivo *</label>
                                                    <input type="file" class="form-control" id="file" name="file" 
                                                           accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" required>
                                                    <div class="form-text">Máximo 10MB. Formatos: PDF, JPG, PNG, DOC, DOCX</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <a href="{{ route('inscriptions.show', $inscription) }}" class="btn btn-secondary">
                                                <i class="fas fa-arrow-left"></i> Voltar
                                            </a>
                                            <button type="submit" class="btn btn-primary" id="uploadBtn">
                                                <i class="fas fa-upload"></i> Fazer Upload
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Documents List -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Documentos Anexados</h5>
                                </div>
                                <div class="card-body">
                                    <div id="documentsContainer">
                                        <div class="text-center">
                                            <div class="spinner-border" role="status">
                                                <span class="visually-hidden">Carregando...</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmação -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Tem certeza que deseja excluir este documento?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Excluir</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const uploadForm = document.getElementById('uploadForm');
    const uploadBtn = document.getElementById('uploadBtn');
    const documentsContainer = document.getElementById('documentsContainer');
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    let documentToDelete = null;

    // Load documents on page load
    loadDocuments();

    // Handle form submission
    uploadForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(uploadForm);
        uploadBtn.disabled = true;
        uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';

        fetch('{{ route("documents.upload", $inscription) }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
                uploadForm.reset();
                loadDocuments();
            } else {
                showAlert('danger', data.message);
            }
        })
        .catch(error => {
            showAlert('danger', 'Erro ao fazer upload do arquivo.');
            console.error('Error:', error);
        })
        .finally(() => {
            uploadBtn.disabled = false;
            uploadBtn.innerHTML = '<i class="fas fa-upload"></i> Fazer Upload';
        });
    });

    // Load documents
    function loadDocuments() {
        fetch('{{ route("documents.index", $inscription) }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderDocuments(data.documents);
            }
        })
        .catch(error => {
            console.error('Error loading documents:', error);
            documentsContainer.innerHTML = '<div class="alert alert-danger">Erro ao carregar documentos.</div>';
        });
    }

    // Render documents
    function renderDocuments(documents) {
        if (documents.length === 0) {
            documentsContainer.innerHTML = '<div class="text-muted text-center">Nenhum documento anexado.</div>';
            return;
        }

        const groupedDocs = documents.reduce((groups, doc) => {
            const collection = doc.collection;
            if (!groups[collection]) {
                groups[collection] = [];
            }
            groups[collection].push(doc);
            return groups;
        }, {});

        const collectionNames = {
            'documents': 'Documentos Gerais',
            'contracts': 'Contratos',
            'certificates': 'Certificados'
        };

        let html = '';
        for (const [collection, docs] of Object.entries(groupedDocs)) {
            html += `
                <div class="mb-4">
                    <h6 class="text-primary">${collectionNames[collection] || collection}</h6>
                    <div class="row">
            `;
            
            docs.forEach(doc => {
                const icon = getFileIcon(doc.mime_type);
                html += `
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="${icon} fa-2x text-primary"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="card-title mb-1">${doc.name}</h6>
                                        <small class="text-muted">${doc.size_formatted} • ${doc.created_at}</small>
                                    </div>
                                    <div class="btn-group btn-group-sm" role="group" aria-label="Ações do documento">
                                        <a href="${doc.download_url}" 
                                           class="btn btn-primary" 
                                           title="Download do documento"
                                           data-bs-toggle="tooltip">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        <button class="btn btn-danger" 
                                                onclick="confirmDeleteDocument(${doc.id})" 
                                                title="Excluir documento"
                                                data-bs-toggle="tooltip">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            html += `
                    </div>
                </div>
            `;
        }

        documentsContainer.innerHTML = html;
    }

    // Get file icon based on mime type
    function getFileIcon(mimeType) {
        if (mimeType.includes('pdf')) return 'fas fa-file-pdf';
        if (mimeType.includes('image')) return 'fas fa-file-image';
        if (mimeType.includes('word')) return 'fas fa-file-word';
        return 'fas fa-file';
    }

    // Confirm delete document
    window.confirmDeleteDocument = function(documentId) {
        documentToDelete = documentId;
        deleteModal.show();
    };

    // Handle delete confirmation
    document.getElementById('confirmDelete').addEventListener('click', function() {
        if (documentToDelete) {
            fetch(`{{ route("documents.destroy", [$inscription, "DOCUMENT_ID"]) }}`.replace('DOCUMENT_ID', documentToDelete), {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                    loadDocuments();
                } else {
                    showAlert('danger', data.message);
                }
            })
            .catch(error => {
                showAlert('danger', 'Erro ao excluir documento.');
                console.error('Error:', error);
            })
            .finally(() => {
                deleteModal.hide();
                documentToDelete = null;
            });
        }
    });

    // Show alert
    function showAlert(type, message) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        const alertContainer = document.querySelector('.card-body');
        alertContainer.insertAdjacentHTML('afterbegin', alertHtml);
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            const alert = alertContainer.querySelector('.alert');
            if (alert) {
                alert.remove();
            }
        }, 5000);
    }
});
</script>
@endsection

