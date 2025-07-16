<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="section-title text-primary mb-0">
        <i class="fas fa-phone"></i> Telefones do Cliente
    </h5>
    <button type="button" class="btn btn-primary" onclick="abrirModalTelefone()">
        <i class="fas fa-plus"></i> Novo Telefone
    </button>
</div>

@if($client->phones->count() > 0)
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Telefone</th>
                    <th>Tipo</th>
                    <th>WhatsApp</th>
                    <th>Status</th>
                    <th>Observações</th>
                    <th width="150">Ações</th>
                </tr>
            </thead>
            <tbody id="phones-table-body">
                @foreach($client->phones->sortByDesc('is_primary') as $phone)
                    <tr>
                        <td>
                            <strong>{{ $phone->formatted_phone }}</strong>
                            @if($phone->is_primary)
                                <span class="badge bg-primary ms-1">Principal</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ $phone->type_label }}</span>
                        </td>
                        <td>
                            @if($phone->is_whatsapp)
                                <span class="badge bg-success">
                                    <i class="fab fa-whatsapp"></i> Sim
                                </span>
                            @else
                                <span class="badge bg-secondary">Não</span>
                            @endif
                        </td>
                        <td>
                            @foreach($phone->status_labels as $label)
                                <span class="badge bg-info me-1">{{ $label }}</span>
                            @endforeach
                            @if(empty($phone->status_labels))
                                <span class="badge bg-secondary">Secundário</span>
                            @endif
                        </td>
                        <td>
                            <small>{{ $phone->notes ? Str::limit($phone->notes, 30) : '-' }}</small>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                @if(!$phone->is_primary)
                                    <button type="button" class="btn btn-outline-primary" 
                                            onclick="definirComoPrincipal('phone', {{ $phone->id }})"
                                            title="Definir como principal">
                                        <i class="fas fa-star"></i>
                                    </button>
                                @endif
                                
                                @if($phone->is_whatsapp)
                                    <a href="https://wa.me/55{{ preg_replace('/[^0-9]/', '', $phone->phone) }}" 
                                       target="_blank" class="btn btn-outline-success" title="Abrir WhatsApp">
                                        <i class="fab fa-whatsapp"></i>
                                    </a>
                                @endif
                                
                                <button type="button" class="btn btn-outline-warning" 
                                        onclick="editarTelefone({{ $phone->id }})"
                                        title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                
                                @if(!$phone->is_primary || $client->phones->count() > 1)
                                    <button type="button" class="btn btn-outline-danger" 
                                            onclick="excluirRegistro('phone', {{ $phone->id }})"
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
        <i class="fas fa-phone fa-3x text-muted mb-3"></i>
        <h6 class="text-muted">Nenhum telefone cadastrado</h6>
        <p class="text-muted">Clique em "Novo Telefone" para adicionar o primeiro telefone.</p>
    </div>
@endif

<div class="mt-3">
    <div class="row">
        <div class="col-md-6">
            <div class="card bg-light">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="fas fa-info-circle text-info"></i> Tipos de Telefone
                    </h6>
                    <ul class="list-unstyled mb-0">
                        <li><small><strong>Celular:</strong> Telefone móvel pessoal</small></li>
                        <li><small><strong>Fixo:</strong> Telefone fixo residencial</small></li>
                        <li><small><strong>Trabalho:</strong> Telefone comercial/profissional</small></li>
                        <li><small><strong>Outro:</strong> Outros tipos de contato</small></li>
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
                        <li><small><strong>Total:</strong> {{ $client->phones->count() }} telefone(s)</small></li>
                        <li><small><strong>WhatsApp:</strong> {{ $client->phones->where('is_whatsapp', true)->count() }} telefone(s)</small></li>
                        <li><small><strong>Celulares:</strong> {{ $client->phones->where('type', 'mobile')->count() }} telefone(s)</small></li>
                        <li><small><strong>Fixos:</strong> {{ $client->phones->where('type', 'landline')->count() }} telefone(s)</small></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

