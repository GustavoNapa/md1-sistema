<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="section-title text-primary mb-0">
        <i class="fas fa-graduation-cap"></i> Inscrições do Cliente
    </h5>
    <a href="{{ route('inscriptions.create', ['client_id' => $client->id]) }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Nova Inscrição
    </a>
</div>

@if($client->inscriptions->count() > 0)
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Produto/Curso</th>
                    <th>Status</th>
                    <th>Vendedor</th>
                    <th>Valor Pago</th>
                    <th>Data de Início</th>
                    <th>Progresso</th>
                    <th width="120">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($client->inscriptions->sortByDesc('created_at') as $inscription)
                    <tr>
                        <td>
                            <div>
                                <strong>
                                    @if($inscription->product)
                                        {{ $inscription->product->name }}
                                    @else
                                        {{ $inscription->product ?? 'Produto não informado' }}
                                    @endif
                                </strong>
                                @if($inscription->problemas_desafios)
                                    <br><small class="text-muted">{{ Str::limit($inscription->problemas_desafios, 50) }}</small>
                                @endif
                            </div>
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
                            @php
                                $totalItems = $inscription->preceptorRecords->count() + 
                                             $inscription->payments->count() + 
                                             $inscription->sessions->count() + 
                                             $inscription->diagnostics->count() + 
                                             $inscription->achievements->count() + 
                                             $inscription->followUps->count();
                                $progress = $totalItems > 0 ? min(100, ($totalItems / 10) * 100) : 0;
                            @endphp
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar" role="progressbar" 
                                     style="width: {{ $progress }}%" 
                                     aria-valuenow="{{ $progress }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                    {{ number_format($progress, 0) }}%
                                </div>
                            </div>
                            <small class="text-muted">{{ $totalItems }} atividades</small>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('inscriptions.show', $inscription) }}" 
                                   class="btn btn-outline-primary" 
                                   title="Ver detalhes">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('inscriptions.edit', $inscription) }}" 
                                   class="btn btn-outline-warning" 
                                   title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Resumo das Inscrições -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-success">{{ $client->inscriptions->where('status', 'active')->count() }}</h5>
                    <p class="card-text">Ativas</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-warning">{{ $client->inscriptions->where('status', 'paused')->count() }}</h5>
                    <p class="card-text">Pausadas</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-danger">{{ $client->inscriptions->where('status', 'cancelled')->count() }}</h5>
                    <p class="card-text">Canceladas</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-primary">R$ {{ number_format($client->inscriptions->sum('amount'), 2, ',', '.') }}</h5>
                    <p class="card-text">Total Investido</p>
                </div>
            </div>
        </div>
    </div>
@else
    <div class="text-center py-4">
        <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
        <h6 class="text-muted">Nenhuma inscrição encontrada</h6>
        <p class="text-muted">Este cliente ainda não possui inscrições em cursos ou treinamentos.</p>
        <a href="{{ route('inscriptions.create', ['client_id' => $client->id]) }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Criar Primeira Inscrição
        </a>
    </div>
@endif

@if($client->inscriptions->count() > 0)
<div class="mt-3">
    <div class="card bg-light">
        <div class="card-body">
            <h6 class="card-title">
                <i class="fas fa-chart-line text-info"></i> Histórico de Evolução
            </h6>
            <p class="mb-0">
                <small>
                    Cliente desde <strong>{{ $client->created_at->format('d/m/Y') }}</strong> • 
                    Primeira inscrição em <strong>{{ $client->inscriptions->min('start_date')?->format('d/m/Y') ?? 'Data não informada' }}</strong> • 
                    Última atividade em <strong>{{ $client->inscriptions->max('updated_at')?->format('d/m/Y') ?? 'Data não informada' }}</strong>
                </small>
            </p>
        </div>
    </div>
</div>
@endif

