<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="section-title text-primary mb-0">
        <i class="fas fa-envelope"></i> E-mails do Cliente
    </h5>
    <button type="button" class="btn btn-primary" onclick="abrirModalEmail()">
        <i class="fas fa-plus"></i> Novo E-mail
    </button>
</div>

@if($client->emails->count() > 0)
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>E-mail</th>
                    <th>Tipo</th>
                    <th>Status</th>
                    <th>Verificado</th>
                    <th>Observações</th>
                    <th width="150">Ações</th>
                </tr>
            </thead>
            <tbody id="emails-table-body">
                @foreach($client->emails->sortByDesc('is_primary') as $email)
                    <tr>
                        <td>
                            <strong>{{ $email->email }}</strong>
                            @if($email->is_primary)
                                <span class="badge bg-primary ms-1">Principal</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ $email->type_label }}</span>
                        </td>
                        <td>
                            <span class="badge {{ $email->status_badge_class }}">
                                {{ $email->status_label }}
                            </span>
                        </td>
                        <td>
                            @if($email->is_verified)
                                <i class="fas fa-check-circle text-success" title="Verificado"></i>
                            @else
                                <i class="fas fa-times-circle text-warning" title="Não verificado"></i>
                            @endif
                        </td>
                        <td>
                            <small>{{ $email->notes ? Str::limit($email->notes, 30) : '-' }}</small>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                @if(!$email->is_primary)
                                    <button type="button" class="btn btn-outline-primary" 
                                            onclick="definirComoPrincipal('email', {{ $email->id }})"
                                            title="Definir como principal">
                                        <i class="fas fa-star"></i>
                                    </button>
                                @endif
                                
                                <button type="button" class="btn btn-outline-warning" 
                                        onclick="editarEmail({{ $email->id }})"
                                        title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                
                                @if(!$email->is_primary || $client->emails->count() > 1)
                                    <button type="button" class="btn btn-outline-danger" 
                                            onclick="excluirRegistro('email', {{ $email->id }})"
                                            title="Excluir">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="text-center py-4">
        <i class="fas fa-envelope fa-3x text-muted mb-3"></i>
        <h6 class="text-muted">Nenhum e-mail cadastrado</h6>
        <p class="text-muted">Clique em "Novo E-mail" para adicionar o primeiro e-mail.</p>
    </div>
@endif

<div class="mt-3">
    <div class="row">
        <div class="col-md-6">
            <div class="card bg-light">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="fas fa-info-circle text-info"></i> Informações
                    </h6>
                    <ul class="list-unstyled mb-0">
                        <li><small><strong>Principal:</strong> E-mail usado nas informações básicas</small></li>
                        <li><small><strong>Pessoal:</strong> E-mail pessoal do cliente</small></li>
                        <li><small><strong>Trabalho:</strong> E-mail profissional</small></li>
                        <li><small><strong>Outro:</strong> E-mails alternativos</small></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card bg-light">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="fas fa-chart-bar text-success"></i> Estatísticas
                    </h6>
                    <ul class="list-unstyled mb-0">
                        <li><small><strong>Total:</strong> {{ $client->emails->count() }} e-mail(s)</small></li>
                        <li><small><strong>Verificados:</strong> {{ $client->emails->where('is_verified', true)->count() }} e-mail(s)</small></li>
                        <li><small><strong>Pessoais:</strong> {{ $client->emails->where('type', 'personal')->count() }} e-mail(s)</small></li>
                        <li><small><strong>Trabalho:</strong> {{ $client->emails->where('type', 'work')->count() }} e-mail(s)</small></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

