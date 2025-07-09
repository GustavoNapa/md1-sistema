@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Clientes</h2>
            <a href="{{ route('clients.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Novo Cliente
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                @if($clients->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>CPF</th>
                                    <th>Email</th>
                                    <th>Especialidade</th>
                                    <th>Cidade/UF</th>
                                    <th>Inscrições</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($clients as $client)
                                    <tr>
                                        <td>
                                            <strong>{{ $client->nome }}</strong>
                                        </td>
                                        <td>{{ $client->cpf }}</td>
                                        <td>{{ $client->email }}</td>
                                        <td>{{ $client->especialidade ?? '-' }}</td>
                                        <td>
                                            {{ $client->cidade_atendimento }}
                                            @if($client->uf)
                                                / {{ $client->uf }}
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ $client->inscriptions->count() }} inscrição(ões)
                                            </span>
                                        </td>
                                        <td>
                                            @if($client->ativo)
                                                <span class="badge bg-success">Ativo</span>
                                            @else
                                                <span class="badge bg-secondary">Inativo</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('clients.show', $client) }}" class="btn btn-sm btn-outline-primary">
                                                    Ver
                                                </a>
                                                <a href="{{ route('clients.edit', $client) }}" class="btn btn-sm btn-outline-secondary">
                                                    Editar
                                                </a>
                                                <form action="{{ route('clients.destroy', $client) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir este cliente?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        Excluir
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center">
                        {{ $clients->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <h5>Nenhum cliente cadastrado</h5>
                        <p class="text-muted">Comece criando seu primeiro cliente.</p>
                        <a href="{{ route('clients.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Criar Primeiro Cliente
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

