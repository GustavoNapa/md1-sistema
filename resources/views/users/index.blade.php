@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="page-title">Gestão de Usuários</h1>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userModal">
                    <i class="fas fa-plus"></i> Novo Usuário
                </button>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Lista de Usuários</h5>
                </div>
                <div class="card-body">
                    @if($users->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nome</th>
                                        <th>E-mail</th>
                                        <th>Cargo</th>
                                        <th>Verificado</th>
                                        <th>Criado em</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="users-table-body">
                                    @foreach($users as $user)
                                        <tr>
                                            <td>{{ $user->id }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                                                        <span class="text-white fw-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                                    </div>
                                                    {{ $user->name }}
                                                </div>
                                            </td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                                @if($user->roles->isNotEmpty())
                                                    <span class="badge bg-{{ $user->roles->first() ? 'success' : 'secondary' }}">
                                                        {{ $user->roles->first()->name }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">Sem cargo</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($user->email_verified_at)
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check"></i> Verificado
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning">
                                                        <i class="fas fa-clock"></i> Pendente
                                                    </span>
                                                @endif
                                            </td>
                                            <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    @if(!$user->role)
                                                        <button type="button" class="btn btn-outline-success" 
                                                                onclick="assignRole({{ $user->id }})"
                                                                data-bs-toggle="tooltip" title="Atribuir Cargo">
                                                            <i class="fas fa-user-plus"></i>
                                                        </button>
                                                    @else
                                                        <button type="button" class="btn btn-outline-warning" 
                                                                onclick="removeRole({{ $user->id }})"
                                                                data-bs-toggle="tooltip" title="Remover Cargo">
                                                            <i class="fas fa-user-minus"></i>
                                                        </button>
                                                    @endif
                                                    <button type="button" class="btn btn-outline-primary" 
                                                            onclick="editUser({{ $user->id }})"
                                                            data-bs-toggle="tooltip" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    @if($user->id !== auth()->id())
                                                        <button type="button" class="btn btn-outline-danger" 
                                                                onclick="deleteUser({{ $user->id }})"
                                                                data-bs-toggle="tooltip" title="Excluir">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center">
                            {{ $users->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5>Nenhum usuário cadastrado</h5>
                            <p class="text-muted">Clique no botão "Novo Usuário" para começar.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Criar/Editar Usuário -->
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalLabel">Novo Usuário</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="userForm" action="{{ route('users.store') }}" method="POST">
                @csrf
                <input type="hidden" id="user_id" name="user_id">
                <input type="hidden" id="form_method" name="_method" value="POST">
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nome Completo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Senha <span class="text-danger" id="password-required">*</span></label>
                        <input type="password" class="form-control" id="password" name="password">
                        <div class="form-text">Mínimo de 8 caracteres</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirmar Senha <span class="text-danger" id="password-confirmation-required">*</span></label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                    </div>
                    
                    <div class="mb-3">
                        <label for="role_id" class="form-label">Cargo</label>
                        <select class="form-select" id="role_id" name="role_id">
                            <option value="">Selecione um cargo</option>
                            <!-- Roles will be loaded here -->
                        </select>
                        <div class="form-text">Deixe em branco se não quiser atribuir um cargo agora</div>
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

<!-- Modal para Atribuir Cargo -->
<div class="modal fade" id="assignRoleModal" tabindex="-1" aria-labelledby="assignRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignRoleModalLabel">Atribuir Cargo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="assignRoleForm">
                @csrf
                <input type="hidden" id="assign_user_id" name="user_id">
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="assign_role_id" class="form-label">Selecione o Cargo <span class="text-danger">*</span></label>
                        <select class="form-select" id="assign_role_id" name="role_id" required>
                            <option value="">Selecione um cargo</option>
                            <!-- Roles will be loaded here -->
                        </select>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Atribuir</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.avatar-sm {
    width: 32px;
    height: 32px;
    font-size: 14px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const userForm = document.getElementById('userForm');
    const userModal = new bootstrap.Modal(document.getElementById('userModal'));
    const assignRoleForm = document.getElementById('assignRoleForm');
    const assignRoleModal = new bootstrap.Modal(document.getElementById('assignRoleModal'));

    // Load roles when modals are shown
    document.getElementById('userModal').addEventListener('show.bs.modal', function () {
        loadRoles('role_id');
    });

    document.getElementById('assignRoleModal').addEventListener('show.bs.modal', function () {
        loadRoles('assign_role_id');
    });

    // Handle user form submission
    if (userForm) {
        userForm.addEventListener('submit', function (event) {
            event.preventDefault();

            const submitBtn = userForm.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Salvando...';

            const formData = new FormData(userForm);
            const url = userForm.action;

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
                    userModal.hide();
                    userForm.reset();
                    location.reload();
                    alert(data.message);
                } else {
                    alert('Ocorreu um erro ao salvar o usuário.');
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

    // Handle assign role form submission
    if (assignRoleForm) {
        assignRoleForm.addEventListener('submit', function (event) {
            event.preventDefault();

            const userId = document.getElementById('assign_user_id').value;
            const roleId = document.getElementById('assign_role_id').value;

            fetch(`/users/${userId}/assign-role`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ role_id: roleId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    assignRoleModal.hide();
                    location.reload();
                    alert(data.message);
                } else {
                    alert('Erro ao atribuir cargo.');
                }
            })
            .catch(error => {
                console.error('Erro ao atribuir cargo:', error);
                alert('Erro ao atribuir cargo.');
            });
        });
    }

    // Reset modals when closed
    document.getElementById('userModal').addEventListener('hidden.bs.modal', function () {
        userForm.reset();
        document.getElementById('user_id').value = '';
        document.getElementById('form_method').value = 'POST';
        userForm.action = '{{ route("users.store") }}';
        document.getElementById('userModalLabel').textContent = 'Novo Usuário';
        document.getElementById('password').required = true;
        document.getElementById('password_confirmation').required = true;
        document.getElementById('password-required').style.display = 'inline';
        document.getElementById('password-confirmation-required').style.display = 'inline';
    });
});

function loadRoles(selectId) {
    const select = document.getElementById(selectId);
    select.innerHTML = '<option value="">Carregando...</option>';

    fetch('/roles', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(roles => {
        let options = '<option value=\"\">Selecione um cargo</option>';
        roles.forEach(role => {
            options += `<option value=\"${role.id}\">${role.name}</option>`;
        });
        select.innerHTML = options;
    })
    .catch(() => {
        select.innerHTML = '<option value=\"\">Erro ao carregar cargos</option>';
    });
}

function editUser(id) {
    fetch(`/users/${id}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('user_id').value = data.data.id;
            document.getElementById('name').value = data.data.name;
            document.getElementById('email').value = data.data.email;
            document.getElementById('role_id').value = data.data.role_id || '';
            document.getElementById('form_method').value = 'PUT';
            document.getElementById('userForm').action = `/users/${id}`;
            document.getElementById('userModalLabel').textContent = 'Editar Usuário';
            
            // Make password optional for editing
            document.getElementById('password').required = false;
            document.getElementById('password_confirmation').required = false;
            document.getElementById('password-required').style.display = 'none';
            document.getElementById('password-confirmation-required').style.display = 'none';
            
            const modal = new bootstrap.Modal(document.getElementById('userModal'));
            modal.show();
        }
    })
    .catch(error => {
        console.error('Erro ao carregar usuário:', error);
        alert('Erro ao carregar dados do usuário.');
    });
}

function deleteUser(id) {
    if (confirm('Tem certeza que deseja excluir este usuário? Esta ação não pode ser desfeita.')) {
        fetch(`/users/${id}`, {
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
                alert(data.message || 'Erro ao excluir usuário.');
            }
        })
        .catch(error => {
            console.error('Erro ao excluir usuário:', error);
            alert('Erro ao excluir usuário.');
        });
    }
}

function assignRole(userId) {
    document.getElementById('assign_user_id').value = userId;
    const modal = new bootstrap.Modal(document.getElementById('assignRoleModal'));
    modal.show();
}

function removeRole(userId) {
    if (confirm('Tem certeza que deseja remover o cargo deste usuário?')) {
        fetch(`/users/${userId}/remove-role`, {
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
                alert('Erro ao remover cargo.');
            }
        })
        .catch(error => {
            console.error('Erro ao remover cargo:', error);
            alert('Erro ao remover cargo.');
        });
    }
}
</script>
@endsection

