@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header do Cliente -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">{{ $client->name }}</h4>
                        <small class="text-muted">{{ $client->email }} • {{ $client->formatted_cpf }}</small>
                    </div>
                    <div class="btn-group" role="group">
                        <a href="{{ route('clients.edit', $client) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <a href="{{ route('clients.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Navegação por Abas -->
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="clientTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="info-tab" data-bs-toggle="tab" 
                                    data-bs-target="#info" type="button" role="tab">
                                <i class="fas fa-user"></i> Informações Básicas
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="emails-tab" data-bs-toggle="tab" 
                                    data-bs-target="#emails" type="button" role="tab">
                                <i class="fas fa-envelope"></i> E-mails 
                                <span class="badge bg-primary ms-1">{{ $client->emails->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="phones-tab" data-bs-toggle="tab" 
                                    data-bs-target="#phones" type="button" role="tab">
                                <i class="fas fa-phone"></i> Telefones 
                                <span class="badge bg-primary ms-1">{{ $client->phones->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="companies-tab" data-bs-toggle="tab" 
                                    data-bs-target="#companies" type="button" role="tab">
                                <i class="fas fa-building"></i> Empresas 
                                <span class="badge bg-primary ms-1">{{ $client->companies->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="inscriptions-tab" data-bs-toggle="tab" 
                                    data-bs-target="#inscriptions" type="button" role="tab">
                                <i class="fas fa-graduation-cap"></i> Inscrições 
                                <span class="badge bg-primary ms-1">{{ $client->inscriptions->count() }}</span>
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="card-body">
                    <div class="tab-content" id="clientTabsContent">
                        <!-- Aba Informações Básicas -->
                        <div class="tab-pane fade show active" id="info" role="tabpanel">
                            @include('clients.tabs.info')
                        </div>

                        <!-- Aba E-mails -->
                        <div class="tab-pane fade" id="emails" role="tabpanel">
                            @include('clients.tabs.emails')
                        </div>

                        <!-- Aba Telefones -->
                        <div class="tab-pane fade" id="phones" role="tabpanel">
                            @include('clients.tabs.phones')
                        </div>

                        <!-- Aba Empresas -->
                        <div class="tab-pane fade" id="companies" role="tabpanel">
                            @include('clients.tabs.companies')
                        </div>

                        <!-- Aba Inscrições -->
                        <div class="tab-pane fade" id="inscriptions" role="tabpanel">
                            @include('clients.tabs.inscriptions')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Incluir Modais -->
@include('clients.modals.email')
@include('clients.modals.phone')
@include('clients.modals.company')
@endsection

@section('scripts')
<script>
// Funções globais para abrir modais
function abrirModalEmail() {
    $('#modalEmail').modal('show');
}

function abrirModalTelefone() {
    $('#modalTelefone').modal('show');
}

function abrirModalEmpresa() {
    $('#modalEmpresa').modal('show');
}

// Função global para excluir registros
function excluirRegistro(tipo, id) {
    if (confirm('Tem certeza que deseja excluir este registro?')) {
        const routes = {
            'email': `/client-emails/${id}`,
            'phone': `/client-phones/${id}`,
            'company': `/client-companies/${id}`
        };

        fetch(routes[tipo], {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload(); // Simplificado por enquanto
            } else {
                alert('Erro ao excluir registro: ' + (data.message || 'Erro desconhecido'));
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao excluir registro');
        });
    }
}

// Função para definir como principal
function definirComoPrincipal(tipo, id) {
    const routes = {
        'email': `/client-emails/${id}/set-primary`,
        'phone': `/client-phones/${id}/set-primary`,
        'company': `/client-companies/${id}/set-main`
    };

    fetch(routes[tipo], {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload(); // Simplificado por enquanto
        } else {
            alert('Erro: ' + (data.message || 'Erro desconhecido'));
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao definir como principal');
    });
}

// Funções utilitárias globais
function mostrarMensagemSucesso(mensagem) {
    const alert = $(`
        <div class="alert alert-success alert-dismissible fade show position-fixed" 
             style="top: 20px; right: 20px; z-index: 9999;">
            ${mensagem}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `);
    
    $('body').append(alert);
    
    setTimeout(() => {
        alert.alert('close');
    }, 3000);
}
</script>
@endsection
