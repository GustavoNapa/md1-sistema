@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Detalhes do Cargo: {{ $role->name }}</h5>
                    <div>
                        <a href="{{ route('roles.edit', $role) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <a href="{{ route('roles.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Informações Básicas</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Nome:</strong></td>
                                    <td>{{ $role->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <span class="badge {{ $role->status === 'active' ? 'bg-success' : 'bg-danger' }}">
                                            {{ $role->status === 'active' ? 'Ativo' : 'Inativo' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Usuários com este cargo:</strong></td>
                                    <td>{{ $role->users->count() }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Criado em:</strong></td>
                                    <td>{{ $role->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Atualizado em:</strong></td>
                                    <td>{{ $role->updated_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h6>Permissões</h6>
                            @if($role->permissions->count() > 0)
                                <div class="row">
                                    @foreach($role->permissions as $permission)
                                        <div class="col-md-6 mb-2">
                                            <span class="badge bg-primary">{{ $permission->name }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted">Nenhuma permissão atribuída a este cargo.</p>
                            @endif
                        </div>
                    </div>

                    @if($role->users->count() > 0)
                        <hr>
                        <h6>Usuários com este cargo</h6>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th>Criado em</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($role->users as $user)
                                        <tr>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                                <span class="badge {{ $user->status === 'active' ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $user->status === 'active' ? 'Ativo' : 'Inativo' }}
                                                </span>
                                            </td>
                                            <td>{{ $user->created_at->format('d/m/Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

