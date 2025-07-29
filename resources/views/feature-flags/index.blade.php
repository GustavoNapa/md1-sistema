@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="fas fa-toggle-on me-2"></i>Gerenciamento de Funcionalidades
                </h1>
                <div>
                    <button class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#helpModal">
                        <i class="fas fa-question-circle"></i> Ajuda
                    </button>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Estatísticas -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Total de Funcionalidades</h6>
                                    <h3 class="mb-0">{{ $groupedFeatures->flatten(1)->count() }}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-cogs fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Funcionalidades Ativas</h6>
                                    <h3 class="mb-0">{{ $groupedFeatures->flatten(1)->where('enabled', true)->count() }}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-toggle-on fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Funcionalidades Inativas</h6>
                                    <h3 class="mb-0">{{ $groupedFeatures->flatten(1)->where('enabled', false)->count() }}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-toggle-off fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Categorias</h6>
                                    <h3 class="mb-0">{{ $groupedFeatures->count() }}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-layer-group fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Feature Flags por Categoria -->
            @foreach($groupedFeatures as $category => $features)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-folder me-2"></i>{{ $category }}
                            <span class="badge bg-secondary ms-2">{{ count($features) }} funcionalidades</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($features as $feature)
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card h-100 {{ $feature['enabled'] ? 'border-success' : 'border-secondary' }}">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="card-title mb-0">{{ $feature['name'] }}</h6>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input feature-toggle" 
                                                           type="checkbox" 
                                                           id="toggle_{{ $feature['key'] }}"
                                                           data-feature="{{ $feature['key'] }}"
                                                           {{ $feature['enabled'] ? 'checked' : '' }}>
                                                </div>
                                            </div>
                                            <p class="card-text text-muted small">{{ $feature['description'] }}</p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="badge {{ $feature['enabled'] ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ $feature['enabled'] ? 'Ativa' : 'Inativa' }}
                                                </span>
                                                @if($feature['scope_count'] > 0)
                                                    <small class="text-muted">
                                                        <i class="fas fa-users me-1"></i>{{ $feature['scope_count'] }} escopos
                                                    </small>
                                                @endif
                                            </div>
                                            <div class="mt-2">
                                                <a href="{{ route('feature-flags.show', $feature['key']) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i> Detalhes
                                                </a>
                                                <a href="{{ route('feature-flags.edit', $feature['key']) }}" 
                                                   class="btn btn-sm btn-outline-secondary">
                                                    <i class="fas fa-edit"></i> Editar
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Modal de Ajuda -->
<div class="modal fade" id="helpModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-question-circle me-2"></i>Ajuda - Gerenciamento de Funcionalidades
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h6><i class="fas fa-info-circle me-2"></i>O que são Feature Flags?</h6>
                <p>Feature Flags (ou Feature Toggles) são uma técnica que permite habilitar ou desabilitar funcionalidades do sistema sem precisar fazer deploy de código. Isso permite:</p>
                <ul>
                    <li>Testar novas funcionalidades com um grupo específico de usuários</li>
                    <li>Desabilitar rapidamente uma funcionalidade com problemas</li>
                    <li>Fazer rollout gradual de novas funcionalidades</li>
                    <li>Personalizar a experiência do usuário</li>
                </ul>

                <h6 class="mt-4"><i class="fas fa-toggle-on me-2"></i>Como usar?</h6>
                <ul>
                    <li><strong>Toggle:</strong> Use o botão de switch para ativar/desativar rapidamente uma funcionalidade</li>
                    <li><strong>Detalhes:</strong> Veja informações detalhadas sobre a funcionalidade e seus escopos</li>
                    <li><strong>Editar:</strong> Configure opções avançadas da funcionalidade</li>
                </ul>

                <h6 class="mt-4"><i class="fas fa-exclamation-triangle me-2"></i>Cuidados</h6>
                <div class="alert alert-warning">
                    <ul class="mb-0">
                        <li>Algumas funcionalidades podem afetar outras partes do sistema</li>
                        <li>Teste sempre em ambiente de desenvolvimento antes de aplicar em produção</li>
                        <li>Mantenha um log das alterações realizadas</li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle de feature flags via AJAX
    document.querySelectorAll('.feature-toggle').forEach(function(toggle) {
        toggle.addEventListener('change', function() {
            const featureKey = this.dataset.feature;
            const enabled = this.checked;
            
            // Mostrar loading
            this.disabled = true;
            
            fetch(`/feature-flags/${featureKey}/toggle`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ enabled: enabled })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Atualizar UI
                    const card = this.closest('.card');
                    const badge = card.querySelector('.badge');
                    
                    if (data.enabled) {
                        card.classList.remove('border-secondary');
                        card.classList.add('border-success');
                        badge.classList.remove('bg-secondary');
                        badge.classList.add('bg-success');
                        badge.textContent = 'Ativa';
                    } else {
                        card.classList.remove('border-success');
                        card.classList.add('border-secondary');
                        badge.classList.remove('bg-success');
                        badge.classList.add('bg-secondary');
                        badge.textContent = 'Inativa';
                    }
                    
                    // Mostrar notificação de sucesso
                    showNotification(data.message, 'success');
                } else {
                    // Reverter o toggle em caso de erro
                    this.checked = !enabled;
                    showNotification(data.message || 'Erro ao atualizar funcionalidade', 'error');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                this.checked = !enabled;
                showNotification('Erro de conexão', 'error');
            })
            .finally(() => {
                this.disabled = false;
            });
        });
    });
    
    function showNotification(message, type) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
        
        const alert = document.createElement('div');
        alert.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
        alert.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        alert.innerHTML = `
            <i class="fas ${icon} me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(alert);
        
        // Auto-remover após 5 segundos
        setTimeout(() => {
            if (alert.parentNode) {
                alert.remove();
            }
        }, 5000);
    }
});
</script>
@endpush

@push('styles')
<style>
.feature-toggle {
    transform: scale(1.2);
}

.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.badge {
    transition: all 0.3s ease;
}

.form-check-input:disabled {
    opacity: 0.5;
}
</style>
@endpush

