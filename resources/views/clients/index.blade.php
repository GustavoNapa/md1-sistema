@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="page-title mb-0">Clientes</h4>
                    <a href="{{ route('clients.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Novo Cliente
                    </a>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('clients.index') }}" class="row g-2 mb-3">
                        <div class="col-md-4">
                            <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Buscar por nome, CPF, email ou telefone">
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-select">
                                <option value="">Todos os status</option>
                                <option value="active" @if(request('status')=='active') selected @endif>Ativo</option>
                                <option value="inactive" @if(request('status')=='inactive') selected @endif>Inativo</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="specialty" class="form-select">
                                <option value="">Todas as especialidades</option>
                                @foreach($specialties ?? [] as $sp)
                                    <option value="{{ $sp }}" @if(request('specialty') == $sp) selected @endif>{{ $sp }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="order_by" class="form-select">
                                <option value="name_asc" @if(request('order_by')=='name_asc') selected @endif>Nome (A-Z)</option>
                                <option value="name_desc" @if(request('order_by')=='name_desc') selected @endif>Nome (Z-A)</option>
                                <option value="inscriptions_desc" @if(request('order_by')=='inscriptions_desc') selected @endif>Inscrições (maior)</option>
                                <option value="inscriptions_asc" @if(request('order_by')=='inscriptions_asc') selected @endif>Inscrições (menor)</option>
                            </select>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-outline-primary">Pesquisar</button>
                            <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary ms-1">Limpar</a>
                        </div>
                    </form>
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>CPF</th>
                                    <th>Email</th>
                                    <th>Telefone</th>
                                    <th>Especialidade</th>
                                    <th>Status</th>
                                    <th>Inscrições</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($clients as $client)
                                    <tr>
                                        <td>{{ $client->name }}</td>
                                        <td>{{ $client->formatted_cpf }}</td>
                                        <td>{{ $client->email }}</td>
                                        <td>{{ $client->phone ? $client->formatted_phone : '-' }}</td>
                                        <td>{{ $client->specialty ?? '-' }}</td>
                                        <td>
                                            <span class="badge {{ $client->active ? 'bg-success' : 'bg-danger' }}">
                                                {{ $client->status_label }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ $client->inscriptions->count() }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group" aria-label="Ações do cliente">
                                                <a href="{{ route('clients.show', $client) }}" 
                                                   class="btn btn-primary" 
                                                   title="Visualizar cliente"
                                                   data-bs-toggle="tooltip">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('clients.edit', $client) }}" 
                                                   class="btn btn-warning" 
                                                   title="Editar cliente"
                                                   data-bs-toggle="tooltip">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('clients.destroy', $client) }}" 
                                                      method="POST" 
                                                      style="display: inline;"
                                                      onsubmit="return confirm('Tem certeza que deseja excluir este cliente? Esta ação não pode ser desfeita.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-danger btn-sm" 
                                                            title="Excluir cliente"
                                                            data-bs-toggle="tooltip">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">
                                            Nenhum cliente cadastrado.
                                            <a href="{{ route('clients.create') }}">Cadastre o primeiro cliente</a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if(method_exists($clients, 'links'))
                        <div class="d-flex justify-content-center">
                            {{ $clients->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

