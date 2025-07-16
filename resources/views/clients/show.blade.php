@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Detalhes do Cliente</h4>
                    <div class="btn-group" role="group">
                        <a href="{{ route('clients.edit', $client) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <a href="{{ route('clients.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="text-primary">Informações Pessoais</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Nome:</strong></td>
                                    <td>{{ $client->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>CPF:</strong></td>
                                    <td>{{ $client->formatted_cpf ?? $client->cpf }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>{{ $client->email }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Telefone:</strong></td>
                                    <td>{{ $client->formatted_phone ?? $client->phone }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Data de Nascimento:</strong></td>
                                    <td>{{ $client->birth_date ? $client->birth_date->format('d/m/Y') : '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <span class="badge {{ $client->active ? 'bg-success' : 'bg-danger' }}">
                                            {{ $client->status_label }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5 class="text-primary">Informações Profissionais</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Especialidade:</strong></td>
                                    <td>{{ $client->specialty ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Cidade de Atendimento:</strong></td>
                                    <td>{{ $client->service_city ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Estado:</strong></td>
                                    <td>{{ $client->state ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Região:</strong></td>
                                    <td>{{ $client->region ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Instagram:</strong></td>
                                    <td>
                                        @if($client->instagram)
                                            <a href="https://instagram.com/{{ ltrim($client->instagram, '@') }}" target="_blank">
                                                {{ $client->instagram }}
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Cadastrado em:</strong></td>
                                    <td>{{ $client->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inscrições do Cliente -->
            @if($client->inscriptions->count() > 0)
            <div class="card mt-4">
                <div class="card-header">
                    <h5>Inscrições ({{ $client->inscriptions->count() }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th>Status</th>
                                    <th>Vendedor</th>
                                    <th>Valor Pago</th>
                                    <th>Início</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($client->inscriptions as $inscription)
                                    <tr>
                                        <td>
                                            @if($inscription->product)
                                                {{ $inscription->product->name }}
                                            @else
                                                {{ $inscription->product ?? '-' }}
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ 
                                                $inscription->status === 'active' ? 'success' :
                                                ($inscription->status === 'paused' ? 'warning' :
                                                ($inscription->status === 'cancelled' ? 'danger' : 'info'))
                                            }}">
                                                {{ ucfirst($inscription->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $inscription->vendor->name ?? '-' }}</td>
                                        <td>{{ $inscription->formatted_amount }}</td>
                                        <td>{{ $inscription->start_date ? $inscription->start_date->format('d/m/Y') : '-' }}</td>
                                        <td>
                                            <a href="{{ route('inscriptions.show', $inscription) }}" 
                                               class="btn btn-sm btn-primary" 
                                               title="Ver inscrição">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Mensagens WhatsApp -->
            @if($client->whatsappMessages->count() > 0)
            <div class="card mt-4">
                <div class="card-header">
                    <h5>Mensagens WhatsApp ({{ $client->whatsappMessages->count() }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Telefone</th>
                                    <th>Tipo</th>
                                    <th>Status</th>
                                    <th>Enviado em</th>
                                    <th>Mensagem</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($client->whatsappMessages->sortByDesc('created_at') as $message)
                                    <tr>
                                        <td>{{ $message->formatted_phone ?? $message->phone }}</td>
                                        <td>
                                            <span class="badge bg-{{ $message->type === 'sent' ? 'primary' : 'info' }}">
                                                {{ $message->type_label }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ 
                                                $message->status === 'sent' ? 'success' :
                                                ($message->status === 'pending' ? 'warning' :
                                                ($message->status === 'failed' ? 'danger' : 'info'))
                                            }}">
                                                {{ $message->status_label }}
                                            </span>
                                        </td>
                                        <td>{{ $message->sent_at ? $message->sent_at->format('d/m/Y H:i') : '-' }}</td>
                                        <td>
                                            <small>{{ Str::limit($message->message, 50) }}</small>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
