@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="page-title mb-0">Detalhes do Teste DISC</h4>
                    <a href="{{ route('quiz-responses.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Informações do Respondente</h5>
                            <table class="table table-sm">
                                <tbody>
                                    <tr>
                                        <th width="40%">Nome:</th>
                                        <td>{{ $quizResponse->name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Email:</th>
                                        <td>{{ $quizResponse->email ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Data do Teste:</th>
                                        <td>{{ $quizResponse->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tempo de Resposta:</th>
                                        <td>
                                            @if($quizResponse->response_time_minutes)
                                                <strong>{{ $quizResponse->response_time_minutes }}</strong> minutos
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <h5>Resultados do Perfil DISC</h5>
                            @if($quizResponse->summary)
                                <table class="table table-sm">
                                    <tbody>
                                        <tr>
                                            <th width="40%">Perfil Dominante:</th>
                                            <td>
                                                <span class="badge bg-primary fs-6">
                                                    {{ $quizResponse->dominant_profile }}
                                                </span>
                                            </td>
                                        </tr>
                                        @if(isset($quizResponse->summary['ordenacao']))
                                            <tr>
                                                <th>Ordenação:</th>
                                                <td>{{ implode(' > ', $quizResponse->summary['ordenacao']) }}</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            @else
                                <p class="text-muted">Informações de sumário não disponíveis.</p>
                            @endif
                        </div>
                    </div>

                    @if($quizResponse->percentages)
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5>Percentuais por Perfil</h5>
                                <div class="row" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px;">
                                    @foreach($quizResponse->percentages as $profile => $percentage)
                                        <div style="display: flex; flex-direction: column;">
                                            <div class="card text-center">
                                                <div class="card-body" style="padding: 20px 10px;">
                                                    <h3 class="text-primary" style="margin: 0 0 10px 0; font-size: 28px; font-weight: bold;">{{ substr($profile, 0, 1) }}</h3>
                                                    <h2 style="margin: 0; font-size: 24px;">{{ number_format($percentage, 0) }}%</h2>
                                                    <div class="progress" style="height: 20px; margin-top: 10px;">
                                                        <div class="progress-bar" role="progressbar" style="width: {{ $percentage }}%;" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($quizResponse->summary && isset($quizResponse->summary['contagens']))
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5>Contagens de Respostas</h5>
                                <table class="table table-sm table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Perfil</th>
                                            <th>Contagem</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($quizResponse->summary['contagens'] as $profile => $count)
                                            <tr>
                                                <td><strong>{{ $profile }}</strong></td>
                                                <td>{{ $count }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    @if($quizResponse->answers)
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5>Respostas Detalhadas</h5>
                                <div class="accordion" id="answersAccordion">
                                    @foreach($quizResponse->answers as $index => $answer)
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="heading{{ $index }}">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}" aria-expanded="false" aria-controls="collapse{{ $index }}">
                                                    Questão {{ $index + 1 }}: {{ isset($answer['answer']) ? $answer['answer'] : (is_string($answer) ? $answer : 'Resposta ' . ($index + 1)) }}
                                                </button>
                                            </h2>
                                            <div id="collapse{{ $index }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $index }}" data-bs-parent="#answersAccordion">
                                                <div class="accordion-body">
                                                    <pre>{{ json_encode($answer, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($quizResponse->report_html)
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5>Relatório</h5>
                                <div class="d-flex gap-2 mb-3">
                                    <button type="button" class="btn btn-primary" onclick="openReportModal()">
                                        <i class="fas fa-eye"></i> Ver Relatório
                                    </button>
                                    <button type="button" class="btn btn-success" onclick="downloadReportPDF()">
                                        <i class="fas fa-download"></i> Baixar Relatório
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal do Relatório DISC -->
<div id="reportModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; padding: 20px;">
    <div style="background: white; border-radius: 10px; overflow: hidden; display: flex; flex-direction: column; max-width: 95%; max-height: 95vh; margin: auto; box-shadow: 0 10px 40px rgba(0,0,0,0.3);">
        <!-- Header -->
        <div style="padding: 20px; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center; background: #f8f9fa;">
            <h5 style="margin: 0; color: #0f172a; font-weight: 600; font-size: 1.25rem;">Relatório Comportamental DISC</h5>
            <button onclick="closeReportModal()" style="background: none; border: none; font-size: 28px; cursor: pointer; color: #666; padding: 0; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; transition: color 0.2s;">&times;</button>
        </div>
        
        <!-- Content com iframe -->
        <div style="flex: 1; overflow: hidden;">
            <iframe id="reportIframe" style="width: 100%; height: 100%; border: none;"></iframe>
        </div>
        
        <!-- Footer -->
        <div style="padding: 20px; border-top: 1px solid #e5e7eb; display: flex; gap: 10px; justify-content: flex-end; background: #f8f9fa;">
            <button onclick="closeReportModal()" style="background: #6c757d; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-weight: 600; transition: background 0.2s;" onmouseover="this.style.background='#5a6268'" onmouseout="this.style.background='#6c757d'">Fechar</button>
            <button onclick="downloadReportPDF()" id="downloadPdfBtn" style="background: #0f172a; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-weight: 600; transition: background 0.2s;" onmouseover="this.style.background='#1a2332'" onmouseout="this.style.background='#0f172a'">Salvar como PDF</button>
        </div>
    </div>
</div>

@section('scripts')
<script>
// Armazenar o HTML do relatório e as informações do teste
const reportHtmlContent = `{!! addslashes($quizResponse->report_html ?? '') !!}`;
const quizResponseId = {{ $quizResponse->id }};

// Abrir modal do relatório
function openReportModal() {
    const modal = document.getElementById('reportModal');
    const iframe = document.getElementById('reportIframe');
    
    // Carregar HTML no iframe usando srcdoc
    iframe.srcdoc = reportHtmlContent;
    
    // Mostrar modal
    modal.style.display = 'flex';
}

// Fechar modal do relatório
function closeReportModal() {
    const modal = document.getElementById('reportModal');
    modal.style.display = 'none';
}

// Baixar relatório como PDF
function downloadReportPDF() {
    const btn = document.getElementById('downloadPdfBtn');
    const originalText = btn.innerText;
    
    btn.disabled = true;
    btn.innerText = 'Gerando PDF...';
    
    try {
        // Redirecionar para a rota de download do servidor
        window.location.href = `/quiz-responses/${quizResponseId}/download`;
        
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
    const modal = document.getElementById('reportModal');
    
    if (modal) {
        modal.addEventListener('click', function(event) {
            if (event.target === modal) {
                closeReportModal();
            }
        });
    }
});

// Fechar modal ao pressionar ESC
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeReportModal();
    }
});
</script>
@endsection

@endsection
