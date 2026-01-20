<!-- Alertas de Status Importantes -->
@if($client->isPaused() || $client->phase)
<div class="row mb-4">
    <div class="col-12">
        @if($client->isPaused())
        <div class="alert alert-warning d-flex align-items-center" role="alert">
            <i class="fas fa-pause-circle fa-2x me-3"></i>
            <div class="flex-grow-1">
                <h6 class="alert-heading mb-1">Cliente em Pausa</h6>
                <p class="mb-0">
                    <strong>Desde:</strong> {{ $client->pause_start_date ? $client->pause_start_date->format('d/m/Y') : '-' }}
                    @if($client->pause_end_date)
                        | <strong>Até:</strong> {{ $client->pause_end_date->format('d/m/Y') }}
                        @php $daysRemaining = $client->getRemainingPauseDays(); @endphp
                        @if($daysRemaining > 0)
                            <span class="badge bg-dark ms-2">{{ $daysRemaining }} dias restantes</span>
                        @else
                            <span class="badge bg-danger ms-2">Vencida</span>
                        @endif
                    @endif
                </p>
            </div>
        </div>
        @endif

        @if($client->phase)
        <div class="alert alert-info d-flex align-items-center" role="alert">
            <i class="fas fa-chart-line fa-2x me-3"></i>
            <div class="flex-grow-1">
                <h6 class="alert-heading mb-1">
                    {{ \App\Models\Client::getPhaseOptions()[$client->phase] ?? $client->phase }}
                </h6>
                <p class="mb-0">
                    @if($client->phase_week)
                        <strong>Progresso:</strong> Semana {{ $client->phase_week }}/27
                        <span class="badge bg-primary ms-2">{{ number_format(($client->phase_week / 27) * 100, 1) }}% concluído</span>
                    @endif
                    @if($client->phase_start_date)
                        | <strong>Desde:</strong> {{ $client->phase_start_date->format('d/m/Y') }}
                    @endif
                </p>
            </div>
        </div>
        @endif
    </div>
</div>
@endif

<div class="row">
    <div class="col-md-6">
        <h5 class="section-title text-primary mb-3">
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
            @if($client->doctoralia)
            <tr>
                <td><strong>Doctoralia:</strong></td>
                <td>
                    <a href="{{ $client->doctoralia }}" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-external-link-alt"></i> Ver Perfil
                    </a>
                </td>
            </tr>
            @endif
            <tr>
                <td><strong>Status:</strong></td>
                <td>
                    @php
                        $statusClass = match($client->status ?? 'active') {
                            'active' => 'bg-success',
                            'paused' => 'bg-warning text-dark',
                            'inactive' => 'bg-danger',
                            default => 'bg-secondary'
                        };
                    @endphp
                    <span class="badge {{ $statusClass }}">
                        {{ $client->status_label }}
                    </span>
                </td>
            </tr>
        </table>
    </div>
    
    <div class="col-md-6">
        <h5 class="section-title text-primary mb-3">
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

<!-- Seção de Acompanhamento e Fase -->
@if($client->phase || $client->isPaused())
<div class="row mt-4">
    <div class="col-12">
        <h5 class="section-title text-primary mb-3">
            <i class="fas fa-chart-line"></i> Acompanhamento e Progresso
        </h5>
    </div>

    @if($client->phase)
    <div class="col-md-6">
        <div class="card border-info mb-3">
            <div class="card-header bg-info text-white">
                <i class="fas fa-layer-group"></i> Fase Atual
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td width="40%"><strong>Fase:</strong></td>
                        <td>
                            <span class="badge bg-info fs-6">
                                {{ \App\Models\Client::getPhaseOptions()[$client->phase] ?? $client->phase }}
                            </span>
                        </td>
                    </tr>
                    @if($client->phase_start_date)
                    <tr>
                        <td><strong>Data de Início:</strong></td>
                        <td>{{ $client->phase_start_date->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Tempo na Fase:</strong></td>
                        <td>
                            @php
                                $diasNaFase = $client->phase_start_date->diffInDays(now());
                                $semanasNaFase = floor($diasNaFase / 7);
                            @endphp
                            {{ $diasNaFase }} dias ({{ $semanasNaFase }} semanas)
                        </td>
                    </tr>
                    @endif
                    @if($client->phase_week)
                    <tr>
                        <td><strong>Progresso:</strong></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-primary me-2">Semana {{ $client->phase_week }}/27</span>
                                <div class="progress flex-grow-1" style="height: 20px;">
                                    @php
                                        $percentual = ($client->phase_week / 27) * 100;
                                    @endphp
                                    <div class="progress-bar" role="progressbar" 
                                         style="width: {{ $percentual }}%" 
                                         aria-valuenow="{{ $percentual }}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                        {{ number_format($percentual, 1) }}%
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>
    @endif

    @if($client->isPaused())
    <div class="col-md-6">
        <div class="card border-warning mb-3">
            <div class="card-header bg-warning text-dark">
                <i class="fas fa-pause-circle"></i> Cliente em Pausa
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    @if($client->pause_start_date)
                    <tr>
                        <td width="40%"><strong>Início da Pausa:</strong></td>
                        <td>{{ $client->pause_start_date->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Tempo em Pausa:</strong></td>
                        <td>
                            @php
                                $diasPausado = $client->pause_start_date->diffInDays(now());
                            @endphp
                            {{ $diasPausado }} dias
                        </td>
                    </tr>
                    @endif
                    @if($client->pause_end_date)
                    <tr>
                        <td><strong>Fim Previsto:</strong></td>
                        <td>{{ $client->pause_end_date->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Status da Pausa:</strong></td>
                        <td>
                            @php
                                $daysRemaining = $client->getRemainingPauseDays();
                            @endphp
                            @if($daysRemaining > 0)
                                <span class="badge bg-warning text-dark">
                                    <i class="fas fa-clock"></i> {{ $daysRemaining }} dias restantes
                                </span>
                            @else
                                <span class="badge bg-danger">
                                    <i class="fas fa-exclamation-triangle"></i> Pausa vencida
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endif
                    @if($client->pause_reason)
                    <tr>
                        <td><strong>Motivo:</strong></td>
                        <td>
                            <div class="alert alert-light mb-0 py-2">
                                <small>{{ $client->pause_reason }}</small>
                            </div>
                        </td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endif

@if($client->whatsappMessages->count() > 0)
<div class="row mt-4">
    <div class="col-12">
        <h5 class="section-title text-primary mb-3">
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

