@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2><i class="fas fa-signature me-2"></i>Integração ZapSign</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('integrations.index') }}">Integrações</a></li>
                            <li class="breadcrumb-item active">ZapSign</li>
                        </ol>
                    </nav>
                </div>
                <a href="{{ route('integrations.zapsign.template-mappings') }}" class="btn btn-primary">
                    <i class="fas fa-list me-2"></i>Gerenciar Templates
                </a>
            </div>

            <!-- Configurações da API -->
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-key me-2"></i>Configurações da API
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('integrations.zapsign.settings') }}" method="POST">
                                @csrf
                                
                                <div class="mb-3">
                                    <label for="api_token" class="form-label">Token da API *</label>
                                    <input type="password" 
                                           class="form-control @error('api_token') is-invalid @enderror" 
                                           id="api_token" 
                                           name="api_token" 
                                           value="{{ old('api_token', $settings['api_token'] ?? '') }}"
                                           placeholder="Insira seu token da API ZapSign">
                                    @error('api_token')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        Você pode encontrar seu token na área de desenvolvedor do ZapSign.
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="sandbox_mode" 
                                               name="sandbox_mode" 
                                               value="1"
                                               {{ old('sandbox_mode', $settings['sandbox_mode'] ?? false) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="sandbox_mode">
                                            Modo Sandbox (Teste)
                                        </label>
                                    </div>
                                    <div class="form-text">
                                        Ative para usar o ambiente de teste do ZapSign.
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="webhook_url" class="form-label">URL do Webhook</label>
                                    <input type="url" 
                                           class="form-control @error('webhook_url') is-invalid @enderror" 
                                           id="webhook_url" 
                                           name="webhook_url" 
                                           value="{{ old('webhook_url', $settings['webhook_url'] ?? '') }}"
                                           placeholder="https://seudominio.com/webhook/zapsign">
                                    @error('webhook_url')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        URL sugerida: <code>{{ url('/webhook/zapsign') }}</code>
                                    </div>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Salvar Configurações
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" id="testConnection">
                                        <i class="fas fa-plug me-2"></i>Testar Conexão
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>Informações
                            </h5>
                        </div>
                        <div class="card-body">
                            <h6>Como configurar:</h6>
                            <ol class="small">
                                <li>Acesse sua conta no <a href="https://app.zapsign.com.br" target="_blank">ZapSign</a></li>
                                <li>Vá em Configurações → API</li>
                                <li>Copie seu token de API</li>
                                <li>Cole o token no campo acima</li>
                                <li>Configure a URL do webhook</li>
                            </ol>

                            <hr>

                            <h6>Recursos disponíveis:</h6>
                            <ul class="small">
                                <li>Criação automática de documentos</li>
                                <li>Mapeamento de campos</li>
                                <li>Assinatura automática</li>
                                <li>Notificações via webhook</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Templates Configurados -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-file-contract me-2"></i>Templates Configurados
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($templateMappings->count() > 0)
                                <div class="list-group list-group-flush">
                                    @foreach($templateMappings->take(5) as $mapping)
                                    <div class="list-group-item px-0 py-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong class="small">{{ $mapping->name }}</strong>
                                                @if($mapping->auto_sign)
                                                    <span class="badge bg-success ms-1">Auto</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @if($templateMappings->count() > 5)
                                    <div class="text-center mt-2">
                                        <small class="text-muted">
                                            e mais {{ $templateMappings->count() - 5 }} template(s)
                                        </small>
                                    </div>
                                @endif
                            @else
                                <p class="text-muted small mb-0">
                                    Nenhum template configurado ainda.
                                </p>
                            @endif
                            
                            <div class="d-grid mt-3">
                                <a href="{{ route('integrations.zapsign.template-mappings.create') }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-plus me-1"></i>Novo Template
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Resultado do Teste -->
<div class="modal fade" id="testResultModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Resultado do Teste</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="testResultBody">
                <!-- Conteúdo será inserido via JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.getElementById('testConnection').addEventListener('click', function() {
    const button = this;
    const originalText = button.innerHTML;
    
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Testando...';
    
    fetch('{{ route("integrations.zapsign.test") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        const modal = new bootstrap.Modal(document.getElementById('testResultModal'));
        const body = document.getElementById('testResultBody');
        
        if (data.success) {
            body.innerHTML = `
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>${data.message}
                    ${data.templates_count !== undefined ? `<br><small>Templates encontrados: ${data.templates_count}</small>` : ''}
                </div>
            `;
        } else {
            body.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>${data.message}
                </div>
            `;
        }
        
        modal.show();
    })
    .catch(error => {
        const modal = new bootstrap.Modal(document.getElementById('testResultModal'));
        const body = document.getElementById('testResultBody');
        
        body.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>Erro na conexão: ${error.message}
            </div>
        `;
        
        modal.show();
    })
    .finally(() => {
        button.disabled = false;
        button.innerHTML = originalText;
    });
});
</script>
@endsection

