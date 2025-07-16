<div class="row">
    <div class="col-md-6">
        <h5 class="text-primary mb-3">
            <i class="fas fa-user"></i> Informações Pessoais
        </h5>
        <table class="table table-borderless">
            <tr>
                <td width="30%"><strong>Nome:</strong></td>
                <td>{{ $client->name }}</td>
            </tr>
            <tr>
                <td><strong>CPF:</strong></td>
                <td>{{ $client->formatted_cpf ?? $client->cpf }}</td>
            </tr>
            <tr>
                <td><strong>Email Principal:</strong></td>
                <td>{{ $client->email }}</td>
            </tr>
            <tr>
                <td><strong>Telefone Principal:</strong></td>
                <td>{{ $client->formatted_phone ?? $client->phone }}</td>
            </tr>
            <tr>
                <td><strong>Data de Nascimento:</strong></td>
                <td>{{ $client->birth_date ? $client->birth_date->format('d/m/Y') : '-' }}</td>
            </tr>
            @if($client->sexo)
            <tr>
                <td><strong>Sexo:</strong></td>
                <td>{{ $client->sexo_label }}</td>
            </tr>
            @endif
            @if($client->media_faturamento)
            <tr>
                <td><strong>Média de Faturamento:</strong></td>
                <td>{{ $client->formatted_media_faturamento }}</td>
            </tr>
            @endif
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
        <h5 class="text-primary mb-3">
            <i class="fas fa-briefcase"></i> Informações Profissionais
        </h5>
        <table class="table table-borderless">
            <tr>
                <td width="30%"><strong>Especialidade:</strong></td>
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
                        <a href="https://instagram.com/{{ ltrim($client->instagram, '@') }}" target="_blank" class="text-decoration-none">
                            <i class="fab fa-instagram"></i> {{ $client->instagram }}
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
            <tr>
                <td><strong>Última atualização:</strong></td>
                <td>{{ $client->updated_at->format('d/m/Y H:i') }}</td>
            </tr>
        </table>
    </div>
</div>

@if($client->whatsappMessages->count() > 0)
<div class="row mt-4">
    <div class="col-12">
        <h5 class="text-primary mb-3">
            <i class="fab fa-whatsapp"></i> Últimas Mensagens WhatsApp
        </h5>
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
                    @foreach($client->whatsappMessages->sortByDesc('created_at')->take(5) as $message)
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
            @if($client->whatsappMessages->count() > 5)
                <div class="text-center">
                    <small class="text-muted">
                        Mostrando as 5 mensagens mais recentes de {{ $client->whatsappMessages->count() }} total
                    </small>
                </div>
            @endif
        </div>
    </div>
</div>
@endif

