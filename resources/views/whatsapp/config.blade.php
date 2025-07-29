@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4><i class="fab fa-whatsapp me-2"></i>Configuração WhatsApp</h4>
                    <a href="{{ route('whatsapp.index') }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-comments me-1"></i>Ir para Chat
                    </a>
                </div>
                <div class="card-body">
                    <!-- Status da Conexão -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="alert alert-info" id="status-alert">
                                <i class="fas fa-info-circle me-2"></i>
                                <span id="status-text">Verificando status da conexão...</span>
                            </div>
                        </div>
                    </div>

                    <!-- Botões de Ação -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <button type="button" class="btn btn-success w-100" id="btn-connect">
                                <i class="fas fa-play me-2"></i>Conectar WhatsApp
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button type="button" class="btn btn-danger w-100" id="btn-disconnect">
                                <i class="fas fa-stop me-2"></i>Desconectar WhatsApp
                            </button>
                        </div>
                    </div>

                    <!-- QR Code -->
                    <div class="row" id="qr-code-section" style="display: none;">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-qrcode me-2"></i>QR Code para Conexão
                                    </h5>
                                </div>
                                <div class="card-body text-center">
                                    <div id="qr-code-container">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Carregando QR Code...</span>
                                        </div>
                                        <p class="mt-2">Aguarde, gerando QR Code...</p>
                                    </div>
                                    <img id="qr-code-image" src="" alt="QR Code" class="img-fluid" style="display: none; max-width: 300px;">
                                    <div class="mt-3">
                                        <p class="text-muted">
                                            <i class="fas fa-mobile-alt me-2"></i>
                                            Abra o WhatsApp no seu celular e escaneie este QR Code
                                        </p>
                                        <small class="text-muted">
                                            WhatsApp > Menu (⋮) > Dispositivos conectados > Conectar um dispositivo
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Informações da Configuração -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-cog me-2"></i>Informações da Configuração
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Nome da Instância:</strong><br>
                                            <code>{{ config('services.evolution.instance_name') }}</code>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>URL da API:</strong><br>
                                            <code>{{ config('services.evolution.base_url') }}</code>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-md-12">
                                            <strong>Webhook URL:</strong><br>
                                            <code>{{ url('/api/webhook/whatsapp') }}</code>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnConnect = document.getElementById('btn-connect');
    const btnDisconnect = document.getElementById('btn-disconnect');
    const statusAlert = document.getElementById('status-alert');
    const statusText = document.getElementById('status-text');
    const qrCodeSection = document.getElementById('qr-code-section');
    const qrCodeContainer = document.getElementById('qr-code-container');
    const qrCodeImage = document.getElementById('qr-code-image');
    
    let qrCheckInterval = null;

    // Verificar status inicial
    checkStatus();

    // Conectar WhatsApp
    btnConnect.addEventListener('click', function() {
        btnConnect.disabled = true;
        btnConnect.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Conectando...';
        
        fetch('{{ route("whatsapp.connect") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateStatus('info', data.message);
                showQrCodeSection();
                startQrCodePolling();
            } else {
                updateStatus('danger', data.message);
            }
        })
        .catch(error => {
            updateStatus('danger', 'Erro de conexão: ' + error.message);
        })
        .finally(() => {
            btnConnect.disabled = false;
            btnConnect.innerHTML = '<i class="fas fa-play me-2"></i>Conectar WhatsApp';
        });
    });

    // Desconectar WhatsApp
    btnDisconnect.addEventListener('click', function() {
        if (!confirm('Tem certeza que deseja desconectar o WhatsApp?')) {
            return;
        }
        
        btnDisconnect.disabled = true;
        btnDisconnect.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Desconectando...';
        
        fetch('{{ route("whatsapp.disconnect") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateStatus('success', data.message);
                hideQrCodeSection();
                stopQrCodePolling();
            } else {
                updateStatus('danger', data.message);
            }
        })
        .catch(error => {
            updateStatus('danger', 'Erro de conexão: ' + error.message);
        })
        .finally(() => {
            btnDisconnect.disabled = false;
            btnDisconnect.innerHTML = '<i class="fas fa-stop me-2"></i>Desconectar WhatsApp';
        });
    });

    function checkStatus() {
        fetch('{{ route("whatsapp.qr-code") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.status === 'open') {
                    updateStatus('success', 'WhatsApp conectado e funcionando!');
                    hideQrCodeSection();
                } else if (data.qrcode) {
                    updateStatus('warning', 'QR Code disponível. Escaneie para conectar.');
                    showQrCode(data.qrcode);
                } else {
                    updateStatus('secondary', 'WhatsApp desconectado. Clique em "Conectar" para iniciar.');
                }
            } else {
                updateStatus('secondary', 'WhatsApp desconectado. Clique em "Conectar" para iniciar.');
            }
        })
        .catch(error => {
            updateStatus('secondary', 'Status desconhecido. Clique em "Conectar" para iniciar.');
        });
    }

    function startQrCodePolling() {
        qrCheckInterval = setInterval(() => {
            fetch('{{ route("whatsapp.qr-code") }}')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.status === 'open') {
                        updateStatus('success', 'WhatsApp conectado com sucesso!');
                        hideQrCodeSection();
                        stopQrCodePolling();
                    } else if (data.qrcode) {
                        showQrCode(data.qrcode);
                    }
                }
            })
            .catch(error => {
                console.error('Erro ao verificar QR Code:', error);
            });
        }, 3000); // Verificar a cada 3 segundos
    }

    function stopQrCodePolling() {
        if (qrCheckInterval) {
            clearInterval(qrCheckInterval);
            qrCheckInterval = null;
        }
    }

    function showQrCodeSection() {
        qrCodeSection.style.display = 'block';
        qrCodeContainer.style.display = 'block';
        qrCodeImage.style.display = 'none';
    }

    function hideQrCodeSection() {
        qrCodeSection.style.display = 'none';
    }

    function showQrCode(base64) {
        qrCodeContainer.style.display = 'none';
        qrCodeImage.src = base64;
        qrCodeImage.style.display = 'block';
    }

    function updateStatus(type, message) {
        statusAlert.className = `alert alert-${type}`;
        statusText.textContent = message;
    }

    // Limpar polling ao sair da página
    window.addEventListener('beforeunload', function() {
        stopQrCodePolling();
    });
});
</script>
@endsection

