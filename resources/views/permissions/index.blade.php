@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="page-title">Gestão de Permissões</h1>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#permissionModal">
                    <i class="fas fa-plus"></i> Nova Permissão
                </button>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Lista de Permissões</h5>
                </div>
                <div class="card-body">
                    @if($permissions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nome</th>
                                        <th>Slug</th>
                                        <th>Cargos Associados</th>
                                        <th>Criado em</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="permissions-table-body">
                                    @foreach($permissions as $permission)
                                        <tr>
                                            <td>{{ $permission->id }}</td>
                                            <td>{{ $permission->name }}</td>
                                            <td><code>{{ $permission->slug }}</code></td>
                                            <td>
                                                @if($permission->roles->count() > 0)
                                                    @foreach($permission->roles as $role)
                                                        <span class="badge bg-secondary me-1">{{ $role->name }}</span>
                                                    @endforeach
                                                @else
                                                    <span class="text-muted">Nenhum cargo</span>
                                                @endif
                                            </td>
                                            <td>{{ $permission->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button type="button" class="btn btn-outline-primary" 
                                                            onclick="editPermission({{ $permission->id }})"
                                                            data-bs-toggle="tooltip" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-danger" 
                                                            onclick="deletePermission({{ $permission->id }})"
                                                            data-bs-toggle="tooltip" title="Excluir">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center">
                            {{ $permissions->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-shield-alt fa-3x text-muted mb-3"></i>
                            <h5>Nenhuma permissão cadastrada</h5>
                            <p class="text-muted">Clique no botão "Nova Permissão" para começar.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Criar/Editar Permissão -->
<div class="modal fade" id="permissionModal" tabindex="-1" aria-labelledby="permissionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="permissionModalLabel">Nova Permissão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="permissionForm" action="{{ route('permissions.store') }}" method="POST">
                @csrf
                <input type="hidden" id="permission_id" name="permission_id">
                <input type="hidden" id="form_method" name="_method" value="POST">
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nome da Permissão <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required>
                        <div class="form-text">Nome descritivo da permissão (ex: "Gerenciar Usuários")</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="slug" class="form-label">Slug <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="slug" name="slug" required>
                        <div class="form-text">Identificador único da permissão (ex: "manage-users")</div>
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const permissionForm = document.getElementById('permissionForm');
    const permissionModal = new bootstrap.Modal(document.getElementById('permissionModal'));
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');

    // Auto-generate slug from name
    nameInput.addEventListener('input', function() {
        const slug = this.value
            .toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .trim();
        slugInput.value = slug;
    });

    // Handle form submission
    if (permissionForm) {
        permissionForm.addEventListener('submit', function (event) {
            event.preventDefault();

            const submitBtn = permissionForm.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Salvando...';

            const formData = new FormData(permissionForm);
            const url = permissionForm.action;

            fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': formData.get('_token')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    permissionModal.hide();
                    permissionForm.reset();
                    location.reload(); // Reload to show updated data
                    alert(data.message);
                } else {
                    alert('Ocorreu um erro ao salvar a permissão.');
                }
            })
            .catch(error => {
                console.error('Erro na requisição AJAX:', error);
                alert('Não foi possível conectar ao servidor.');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            });
        });
    }

    // Reset modal when closed
    document.getElementById('permissionModal').addEventListener('hidden.bs.modal', function () {
        permissionForm.reset();
        document.getElementById('permission_id').value = '';
        document.getElementById('form_method').value = 'POST';
        permissionForm.action = '{{ route("permissions.store") }}';
        document.getElementById('permissionModalLabel').textContent = 'Nova Permissão';
    });
});

function editPermission(id) {
    // This would fetch permission data and populate the form
    // For now, we'll implement a basic version
    fetch(`/permissions/${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('permission_id').value = data.data.id;
                document.getElementById('name').value = data.data.name;
                document.getElementById('slug').value = data.data.slug;
                document.getElementById('form_method').value = 'PUT';
                document.getElementById('permissionForm').action = `/permissions/${id}`;
                document.getElementById('permissionModalLabel').textContent = 'Editar Permissão';
                
                const modal = new bootstrap.Modal(document.getElementById('permissionModal'));
                modal.show();
            }
        })
        .catch(error => {
            console.error('Erro ao carregar permissão:', error);
            alert('Erro ao carregar dados da permissão.');
        });
}

function deletePermission(id) {
    if (confirm('Tem certeza que deseja excluir esta permissão? Esta ação não pode ser desfeita.')) {
        fetch(`/permissions/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
                alert(data.message);
            } else {
                alert(data.message || 'Erro ao excluir permissão.');
            }
        })
        .catch(error => {
            console.error('Erro ao excluir permissão:', error);
            alert('Erro ao excluir permissão.');
        });
    }
}
</script>
@endsection

