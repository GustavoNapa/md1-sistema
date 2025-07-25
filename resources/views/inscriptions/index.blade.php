@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="page-title mb-0">Inscrições</h4>
                    <a href="{{ route('inscriptions.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nova Inscrição
                    </a>
                </div>
                <div class="card-body">
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
                                    <th>Cliente</th>
                                    <th>Produto</th>
                                    <th>Turma</th>
                                    <th>Status</th>
                                    <th>Vendedor</th>
                                    <th>Valor</th>
                                    <th>Data Início</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($inscriptions as $inscription)
                                    <tr>
                                        <td>
                                            <strong>{{ $inscription->client->name }}</strong><br>
                                            <small class="text-muted">{{ $inscription->client->email }}</small>
                                        </td>
                                        <td>{{ $inscription->product->name ?? '-' }}</td>
                                        <td>{{ $inscription->class_group ?? '-' }}</td>
                                        <td>
                                            <span class="badge {{ $inscription->status_badge_class }}">
                                                {{ $inscription->status_label }}
                                            </span>
                                        </td>
                                        <td>{{ $inscription->vendor->name ?? '-' }}</td>
                                        <td>{{ $inscription->formatted_amount }}</td>
                                        <td>{{ $inscription->start_date ? $inscription->start_date->format('d/m/Y') : '-' }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group" aria-label="Ações da inscrição">
                                                <a href="{{ route('inscriptions.show', $inscription) }}" 
                                                   class="btn btn-primary" 
                                                   title="Visualizar inscrição"
                                                   data-bs-toggle="tooltip">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('inscriptions.edit', $inscription) }}" 
                                                   class="btn btn-warning" 
                                                   title="Editar inscrição"
                                                   data-bs-toggle="tooltip">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('inscriptions.destroy', $inscription) }}" 
                                                      method="POST" 
                                                      style="display: inline;"
                                                      onsubmit="return confirm('Tem certeza que deseja excluir esta inscrição? Esta ação não pode ser desfeita.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-danger btn-sm" 
                                                            title="Excluir inscrição"
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
                                            Nenhuma inscrição cadastrada.
                                            <a href="{{ route('inscriptions.create') }}">Cadastre a primeira inscrição</a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if(method_exists($inscriptions, 'links'))
                        <div class="d-flex justify-content-center">
                            {{ $inscriptions->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

