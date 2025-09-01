<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="section-title text-primary mb-0">
        <i class="fas fa-building"></i> Empresas do Cliente
    </h5>
    <button type="button" class="btn btn-primary" onclick="abrirModalEmpresa()">
        <i class="fas fa-plus"></i> Nova Empresa
    </button>
</div>

@if($client->companies->count() > 0)
    <div class="row">
        @foreach($client->companies->sortByDesc('is_main') as $company)
            <div class="col-md-6 mb-3">
                <div class="card {{ $company->is_main ? 'border-primary' : '' }}">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">{{ $company->name }}</h6>
                            <small class="text-muted">{{ $company->type_label }}</small>
                        </div>
                        <div>
                            @if($company->is_main)
                                <span class="badge bg-primary">Principal</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                @if($company->cnpj)
                                    <p class="mb-1">
                                        <strong>CNPJ:</strong> {{ $company->formatted_cnpj }}
                                    </p>
                                @endif
                                
                                @if($company->full_address)
                                    <p class="mb-1">
                                        <strong>Endereço:</strong> {{ $company->full_address }}
                                    </p>
                                @endif
                                
                                @if($company->phone)
                                    <p class="mb-1">
                                        <strong>Telefone:</strong> {{ $company->formatted_phone }}
                                    </p>
                                @endif
                                
                                @if($company->email)
                                    <p class="mb-1">
                                        <strong>E-mail:</strong> 
                                        <a href="mailto:{{ $company->email }}">{{ $company->email }}</a>
                                    </p>
                                @endif
                                
                                @if($company->website)
                                    <p class="mb-1">
                                        <strong>Website:</strong> 
                                        <a href="{{ $company->website }}" target="_blank">{{ $company->website }}</a>
                                    </p>
                                @endif
                                
                                @if($company->notes)
                                    <p class="mb-1">
                                        <strong>Observações:</strong> {{ $company->notes }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="btn-group btn-group-sm w-100" role="group">
                            @if(!$company->is_main)
                                <button type="button" class="btn btn-outline-primary" 
                                        onclick="definirComoPrincipal('company', {{ $company->id }})"
                                        title="Definir como principal">
                                    <i class="fas fa-star"></i> Principal
                                </button>
                            @endif
                            
                            <button type="button" class="btn btn-outline-warning" 
                                    onclick="editarEmpresa({{ $company->id }})"
                                    title="Editar">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            
                            <button type="button" class="btn btn-outline-danger" 
                                    onclick="excluirRegistro('company', {{ $company->id }})"
                                    title="Excluir">
                                <i class="fas fa-trash"></i> Excluir
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="text-center py-4">
        <i class="fas fa-building fa-3x text-muted mb-3"></i>
        <h6 class="text-muted">Nenhuma empresa cadastrada</h6>
        <p class="text-muted">Clique em "Nova Empresa" para adicionar a primeira empresa.</p>
    </div>
@endif

<div class="mt-3">
    <div class="row">
        <div class="col-md-6">
            <div class="card bg-light">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="fas fa-info-circle text-info"></i> Tipos de Empresa
                    </h6>
                    <ul class="list-unstyled mb-0">
                        <li><small><strong>Clínica:</strong> Clínica médica ou odontológica</small></li>
                        <li><small><strong>Laboratório:</strong> Laboratório de análises</small></li>
                        <li><small><strong>Hospital:</strong> Hospital ou centro médico</small></li>
                        <li><small><strong>Consultório:</strong> Consultório particular</small></li>
                        <li><small><strong>Outro:</strong> Outros tipos de estabelecimento</small></li>
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
                        <li><small><strong>Total:</strong> {{ $client->companies->count() }} empresa(s)</small></li>
                        <li><small><strong>Clínicas:</strong> {{ $client->companies->where('type', 'clinic')->count() }} empresa(s)</small></li>
                        <li><small><strong>Laboratórios:</strong> {{ $client->companies->where('type', 'laboratory')->count() }} empresa(s)</small></li>
                        <li><small><strong>Hospitais:</strong> {{ $client->companies->where('type', 'hospital')->count() }} empresa(s)</small></li>
                        <li><small><strong>Consultórios:</strong> {{ $client->companies->where('type', 'office')->count() }} empresa(s)</small></li>
                        <li><small><strong>Outros:</strong> {{ $client->companies->where('type', 'other')->count() }} empresa(s)</small></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

