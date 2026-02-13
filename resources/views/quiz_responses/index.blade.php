@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="page-title mb-0">Testes DISC</h4>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('quiz-responses.index') }}" class="row g-2 mb-3">
                        <div class="col-md-4">
                            <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Buscar por nome ou email">
                        </div>
                        <div class="col-md-3">
                            <select name="profile" class="form-select">
                                <option value="">Todos os perfis</option>
                                <option value="D" @if(request('profile')=='D') selected @endif>Dominância (D)</option>
                                <option value="I" @if(request('profile')=='I') selected @endif>Influência (I)</option>
                                <option value="S" @if(request('profile')=='S') selected @endif>Estabilidade (S)</option>
                                <option value="C" @if(request('profile')=='C') selected @endif>Conformidade (C)</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="order_by" class="form-select">
                                <option value="created_at_desc" @if(request('order_by')=='created_at_desc') selected @endif>Mais recentes</option>
                                <option value="created_at_asc" @if(request('order_by')=='created_at_asc') selected @endif>Mais antigos</option>
                                <option value="name_asc" @if(request('order_by')=='name_asc') selected @endif>Nome (A-Z)</option>
                                <option value="name_desc" @if(request('order_by')=='name_desc') selected @endif>Nome (Z-A)</option>
                            </select>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-outline-primary">Pesquisar</button>
                            <a href="{{ route('quiz-responses.index') }}" class="btn btn-outline-secondary ms-1">Limpar</a>
                        </div>
                    </form>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Email</th>
                                    <th>Perfil Dominante</th>
                                    <th>Percentuais</th>
                                    <th>Tempo (min)</th>
                                    <th>Data</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($quizResponses as $response)
                                    <tr>
                                        <td>{{ $response->name ?? '-' }}</td>
                                        <td>{{ $response->email ?? '-' }}</td>
                                        <td>
                                            @if($response->dominant_profile)
                                                <span class="badge bg-primary">
                                                    {{ $response->dominant_profile }}
                                                </span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if($response->percentages)
                                                <small>
                                                    @foreach($response->percentages as $key => $percentage)
                                                        <span class="badge bg-secondary me-2">
                                                            {{ substr($key, 0, 1) }}: {{ number_format($percentage, 0) }}%
                                                        </span>
                                                    @endforeach
                                                </small>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $response->response_time_minutes ?? '-' }}</td>
                                        <td>{{ $response->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            @if($response->report_html)
                                                <button onclick="openReportModalFromList({{ $response->id }}, `{!! addslashes($response->report_html) !!}`)" class="btn btn-sm btn-primary" title="Ver relatório">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            @endif
                                            <a href="{{ route('quiz-responses.download', $response) }}" class="btn btn-sm btn-success" title="Baixar relatório">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <a href="{{ route('quiz-responses.show', $response) }}" class="btn btn-sm btn-info" title="Ver detalhes">
                                                <i class="fas fa-list-ul"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Nenhum teste encontrado.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center">
                        {{ $quizResponses->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal do Relatório DISC -->
<div id="reportModalList" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; padding: 20px;">
    <div style="background: white; border-radius: 10px; overflow: hidden; display: flex; flex-direction: column; max-width: 95%; max-height: 95vh; margin: auto; box-shadow: 0 10px 40px rgba(0,0,0,0.3);">
        <!-- Header -->
        <div style="padding: 20px; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center; background: #f8f9fa;">
            <h5 style="margin: 0; color: #0f172a; font-weight: 600; font-size: 1.25rem;">Relatório Comportamental DISC</h5>
            <button onclick="closeReportModalList()" style="background: none; border: none; font-size: 28px; cursor: pointer; color: #666; padding: 0; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; transition: color 0.2s;">&times;</button>
        </div>
        
        <!-- Content com iframe -->
        <div style="flex: 1; overflow: hidden;">
            <iframe id="reportIframeList" style="width: 100%; height: 100%; border: none;"></iframe>
        </div>
        
        <!-- Footer -->
        <div style="padding: 20px; border-top: 1px solid #e5e7eb; display: flex; gap: 10px; justify-content: flex-end; background: #f8f9fa;">
            <button onclick="closeReportModalList()" style="background: #6c757d; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-weight: 600; transition: background 0.2s;" onmouseover="this.style.background='#5a6268'" onmouseout="this.style.background='#6c757d'">Fechar</button>
            <button onclick="downloadReportPDFFromList()" id="downloadPdfBtnList" style="background: #0f172a; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-weight: 600; transition: background 0.2s;" onmouseover="this.style.background='#1a2332'" onmouseout="this.style.background='#0f172a'">Salvar como PDF</button>
        </div>
    </div>
</div>

@section('scripts')
<script>
let currentReportIdList = null;

// Abrir modal do relatório a partir da listagem
function openReportModalFromList(responseId, htmlContent) {
    const modal = document.getElementById('reportModalList');
    const iframe = document.getElementById('reportIframeList');
    
    // Armazenar o ID para usar no download
    currentReportIdList = responseId;
    
    // Carregar HTML no iframe usando srcdoc
    iframe.srcdoc = htmlContent;
    
    // Mostrar modal
    modal.style.display = 'flex';
}

// Fechar modal do relatório
function closeReportModalList() {
    const modal = document.getElementById('reportModalList');
    modal.style.display = 'none';
}

// Baixar relatório como PDF da listagem
function downloadReportPDFFromList() {
    if (!currentReportIdList) {
        alert('Erro: ID do relatório não encontrado');
        return;
    }
    
    const btn = document.getElementById('downloadPdfBtnList');
    const originalText = btn.innerText;
    
    btn.disabled = true;
    btn.innerText = 'Gerando PDF...';
    
    try {
        // Redirecionar para a rota de download do servidor
        window.location.href = `/quiz-responses/${currentReportIdList}/download`;
        
        // Restaurar o botão após um tempo
        setTimeout(() => {
            btn.disabled = false;
            btn.innerText = originalText;
        }, 1500);
    } catch (error) {
        console.error('Erro ao gerar PDF:', error);
        alert('Erro ao gerar o PDF');
        btn.disabled = false;
        btn.innerText = originalText;
    }
}

// Fechar modal ao clicar no overlay
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('reportModalList');
    
    if (modal) {
        modal.addEventListener('click', function(event) {
            if (event.target === modal) {
                closeReportModalList();
            }
        });
    }
});

// Fechar modal ao pressionar ESC
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeReportModalList();
    }
});
</script>
@endsection

@endsection
