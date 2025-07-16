@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="page-title">Gestão de Cargos</h1>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#roleModal">
                    <i class="fas fa-plus"></i> Novo Cargo
                </button>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Lista de Cargos</h5>
                </div>
                <div class="card-body">
                    @if($roles->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nome</th>
                                        <th>Status</th>
                                        <th>Permissões</th>
                                        <th>Usuários</th>
                                        <th>Criado em</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="roles-table-body">
                                    @foreach($roles as $role)
                                        <tr>
                                            <td>{{ $role->id }}</td>
                                            <td>{{ $role->name }}</td>
                                            <td>
                                                @if($role->status)
                                                    <span class="badge bg-success">Ativo</span>
                                                @else
                                                    <span class="badge bg-danger">Inativo</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $role->permissions->count() }} permissões</span>
                                                @if($role->permissions->count() > 0)
                                                    <button type="button" class="btn btn-sm btn-outline-info ms-1" 
                                                            data-bs-toggle="tooltip" 
                                                            title="{{ $role->permissions->pluck('name')->implode(', ') }}">
                                                        <i class="fas fa-info-circle"></i>
                                                    </button>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $role->users->count() }} usuários</span>
                                            </td>
                                            <td>{{ $role->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button type="button" class="btn btn-outline-warning" 
                                                            onclick="toggleRoleStatus({{ $role->id }})"
                                                            data-bs-toggle="tooltip" 
                                                            title="{{ $role->status ? 'Desativar' : 'Ativar' }}">
                                                        <i class="fas fa-{{ $role->status ? 'toggle-on' : 'toggle-off' }}"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-primary" 
                                                            onclick="editRole({{ $role->id }})"
                                                            data-bs-toggle="tooltip" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-danger" 
                                                            onclick="deleteRole({{ $role->id }})"
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
                            {{ $roles->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-user-tag fa-3x text-muted mb-3"></i>
                            <h5>Nenhum cargo cadastrado</h5>
                            <p class="text-muted">Clique no botão "Novo Cargo" para começar.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Criar/Editar Cargo -->
<div class="modal fade" id="roleModal" tabindex="-1" aria-labelledby="roleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="roleModalLabel">Novo Cargo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="roleForm" action="{{ route('roles.store') }}" method="POST">
                @csrf
                <input type="hidden" id="role_id" name="role_id">
                <input type="hidden" id="form_method" name="_method" value="POST">
                
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nome do Cargo <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" required>
                                <div class="form-text">Nome descritivo do cargo (ex: "Administrador", "Vendedor")</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="1">Ativo</option>
                                    <option value="0">Inativo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Permissões</label>
                        <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                            <div id="permissions-list">
                                <!-- Permissions will be loaded here -->
                                <div class="text-center">
                                    <div class="spinner-border spinner-border-sm" role="status">
                                        <span class="visually-hidden">Carregando...</span>
                                    </div>
                                    <span class="ms-2">Carregando permissões...</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-text">Selecione as permissões que este cargo terá acesso</div>
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
    const roleForm = document.getElementById('roleForm');
    const roleModal = new bootstrap.Modal(document.getElementById('roleModal'));

    // Load permissions when modal is shown
    document.getElementById('roleModal').addEventListener('show.bs.modal', function () {
        loadPermissions();
    });

    // Handle form submission
    if (roleForm) {
        roleForm.addEventListener('submit', function (event) {
            event.preventDefault();

            const submitBtn = roleForm.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Salvando...';

            const formData = new FormData(roleForm);
            const url = roleForm.action;

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
                    roleModal.hide();
                    roleForm.reset();
                    location.reload();
                    alert(data.message);
                } else {
                    alert('Ocorreu um erro ao salvar o cargo.');
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
    document.getElementById('roleModal').addEventListener('hidden.bs.modal', function () {
        roleForm.reset();
        document.getElementById('role_id').value = '';
        document.getElementById('form_method').value = 'POST';
        roleForm.action = '{{ route("roles.store") }}';
        document.getElementById('roleModalLabel').textContent = 'Novo Cargo';
    });
});

function loadPermissions() {
    fetch('/permissions')
        .then(response => response.text())
        .then(html => {
            // This is a simplified version - in a real app, you'd have an API endpoint
            // For now, we'll create a basic permissions list
            const permissionsList = document.getElementById('permissions-list');
            permissionsList.innerHTML = `
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="permissions[]" value="1" id="permission1">
                    <label class="form-check-label" for="permission1">
                        Gerenciar Usuários
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="permissions[]" value="2" id="permission2">
                    <label class="form-check-label" for="permission2">
                        Gerenciar Clientes
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="permissions[]" value="3" id="permission3">
                    <label class="form-check-label" for="permission3">
                        Gerenciar Produtos
                    </label>
                </div>
            `;
        })
        .catch(error => {
            console.error('Erro ao carregar permissões:', error);
            document.getElementById('permissions-list').innerHTML = '<div class="text-danger">Erro ao carregar permissões</div>';
        });
}

function editRole(id) {
    fetch(`/roles/${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('role_id').value = data.data.id;
                document.getElementById('name').value = data.data.name;
                document.getElementById('status').value = data.data.status ? '1' : '0';
                document.getElementById('form_method').value = 'PUT';
                document.getElementById('roleForm').action = `/roles/${id}`;
                document.getElementById('roleModalLabel').textContent = 'Editar Cargo';
                
                const modal = new bootstrap.Modal(document.getElementById('roleModal'));
                modal.show();
            }
        })
        .catch(error => {
            console.error('Erro ao carregar cargo:', error);
            alert('Erro ao carregar dados do cargo.');
        });
}

function deleteRole(id) {
    if (confirm('Tem certeza que deseja excluir este cargo? Esta ação não pode ser desfeita.')) {
        fetch(`/roles/${id}`, {
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
                alert(data.message || 'Erro ao excluir cargo.');
            }
        })
        .catch(error => {
            console.error('Erro ao excluir cargo:', error);
            alert('Erro ao excluir cargo.');
        });
    }
}

function toggleRoleStatus(id) {
    fetch(`/roles/${id}/toggle-status`, {
        method: 'POST',
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
            alert('Erro ao alterar status do cargo.');
        }
    })
    .catch(error => {
        console.error('Erro ao alterar status:', error);
        alert('Erro ao alterar status do cargo.');
    });
}
</script>
@endsection

