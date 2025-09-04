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
                    <form method="GET" action="{{ route('roles.index') }}" class="row g-2 mb-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="text" id="roles-search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Buscar cargos por nome">
                                <button class="btn btn-outline-primary" type="submit">Buscar</button>
                                <button class="btn btn-outline-danger" type="button" id="clear-roles-search">Limpar</button>
                            </div>
                        </div>
                    </form>
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
                                                <span class="badge bg-secondary" data-bs-toggle="tooltip" title="{{ $role->users->pluck('name')->implode(', ') }}">
                                                    {{ $role->users->count() }} usuários
                                                </span>
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
                            <div class="row mb-2">
                                <div class="col-md-8">
                                    <input type="text" id="permissions-search" class="form-control form-control-sm" placeholder="Buscar permissões">
                                </div>
                                <div class="col-md-4 text-end">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="clear-permissions-search">Limpar</button>
                                </div>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="selectAllPermissions" onclick="toggleAllPermissions()">
                                <label class="form-check-label" for="selectAllPermissions">Selecionar todos</label>
                            </div>
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

// Keep a global set of selected permission ids to preserve selection across filtering
let rolePermissionsSelected = new Set();

function loadPermissions(selectedIds) {
    const permissionsList = document.getElementById('permissions-list');
    permissionsList.innerHTML = '';
    const permissions = @json($permissions);
    // Merge incoming selectedIds into the global set (used when editing a role)
    if (Array.isArray(selectedIds) && selectedIds.length > 0) {
        selectedIds.forEach(id => rolePermissionsSelected.add(Number(id)));
    }

    const searchTerm = (document.getElementById('permissions-search') && document.getElementById('permissions-search').value || '').toLowerCase().trim();

    // Ordena as permissões para que as já selecionadas (ativadas para o cargo) apareçam primeiro
    permissions.sort(function(a, b) {
        const aSelected = rolePermissionsSelected.has(Number(a.id)) ? 0 : 1;
        const bSelected = rolePermissionsSelected.has(Number(b.id)) ? 0 : 1;
        if (aSelected !== bSelected) return aSelected - bSelected;
        return a.name.localeCompare(b.name);
    });

    // Se houver termo de busca, filtra a lista
    const filtered = searchTerm ? permissions.filter(p => p.name.toLowerCase().includes(searchTerm)) : permissions;

    filtered.forEach(function(permission) {
        const div = document.createElement('div');
        div.className = 'form-check';
        const input = document.createElement('input');
        input.className = 'form-check-input';
        input.type = 'checkbox';
        input.name = 'permissions[]';
        input.value = permission.id;
        input.id = 'permission' + permission.id;
        if (rolePermissionsSelected.has(Number(permission.id))) {
            input.checked = true;
        }
        // update global set when user toggles a checkbox
        input.addEventListener('change', function() {
            const idNum = Number(permission.id);
            if (this.checked) rolePermissionsSelected.add(idNum);
            else rolePermissionsSelected.delete(idNum);
            // update selectAll checkbox state
            updateSelectAllState(searchTerm, permissions);
        });
        const label = document.createElement('label');
        label.className = 'form-check-label';
        label.htmlFor = input.id;
        label.textContent = permission.name;
        div.appendChild(input);
        div.appendChild(label);
        permissionsList.appendChild(div);
    });
    // Marca ou desmarca o 'Selecionar todos'
    updateSelectAllState(searchTerm, permissions);
}

function updateSelectAllState(searchTerm, permissions) {
    const selectAll = document.getElementById('selectAllPermissions');
    if (!selectAll) return;
    const visible = searchTerm ? permissions.filter(p => p.name.toLowerCase().includes(searchTerm)) : permissions;
    const visibleCount = visible.length;
    const visibleSelected = visible.filter(p => rolePermissionsSelected.has(Number(p.id))).length;
    selectAll.checked = visibleCount > 0 && visibleSelected === visibleCount;
}

function editRole(id) {
    fetch(`/roles/${id}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('role_id').value = data.data.id;
                document.getElementById('name').value = data.data.name;
                document.getElementById('status').value = data.data.status ? '1' : '0';
                document.getElementById('form_method').value = 'PUT';
                document.getElementById('roleForm').action = `/roles/${id}`;
                document.getElementById('roleModalLabel').textContent = 'Editar Cargo';
                // Aguarda o modal abrir para marcar permissões
                const modal = new bootstrap.Modal(document.getElementById('roleModal'));
                modal.show();
                setTimeout(function() {
                    loadPermissions(data.data.permissions);
                }, 300);
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

function toggleAllPermissions() {
    const checked = document.getElementById('selectAllPermissions').checked;
    document.querySelectorAll('#permissions-list input[type=checkbox]').forEach(function(cb) {
        cb.checked = checked;
    });
}

// Wire clear search buttons and permission search input
document.addEventListener('DOMContentLoaded', function() {
    const clearRoles = document.getElementById('clear-roles-search');
    if (clearRoles) {
        clearRoles.addEventListener('click', function() {
            const rolesSearch = document.getElementById('roles-search');
            if (rolesSearch) rolesSearch.value = '';
            // submit the parent form to clear filters
            const form = rolesSearch.closest('form');
            if (form) form.submit();
        });
    }

    const permissionsSearch = document.getElementById('permissions-search');
    if (permissionsSearch) {
        permissionsSearch.addEventListener('input', function() {
            // reload permissions with current selections preserved
            // gather currently checked permission ids
            const checked = Array.from(document.querySelectorAll('#permissions-list input[type=checkbox]:checked')).map(i => parseInt(i.value));
            loadPermissions(checked);
        });
    }

    const clearPermissions = document.getElementById('clear-permissions-search');
    if (clearPermissions) {
        clearPermissions.addEventListener('click', function() {
            const ps = document.getElementById('permissions-search');
            if (ps) ps.value = '';
            const checked = Array.from(document.querySelectorAll('#permissions-list input[type=checkbox]:checked')).map(i => parseInt(i.value));
            loadPermissions(checked);
        });
    }
});
</script>
@endsection

