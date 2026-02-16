<?php

namespace App\Http\Controllers;

use App\Models\QuizResponse;
use App\Services\DISCReportGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class QuizResponseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of quiz responses.
     */
    public function index(Request $request)
    {
        $query = QuizResponse::query();

        // Busca por nome ou email
        if ($search = $request->input('q')) {
            $query->where(function($q) use ($search) {
                // Buscar por email
                $q->where('email', 'like', "%{$search}%");
                
                // Buscar por nome se existir dados
                if ($q->getConnection()->getSchemaBuilder()->hasColumn('quiz_responses', 'name')) {
                    $q->orWhere('name', 'like', "%{$search}%");
                }
            });
        }

        // Filtro por perfil dominante
        if ($profile = $request->input('profile')) {
            $query->whereRaw("JSON_EXTRACT(summary, '$.ordered[0].profile') = ?", [$profile]);
        }

        // Ordenação
        $orderBy = $request->input('order_by', 'created_at_desc');
        
        switch ($orderBy) {
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'email_asc':
                $query->orderBy('email', 'asc');
                break;
            case 'email_desc':
                $query->orderBy('email', 'desc');
                break;
            case 'created_at_asc':
                $query->orderBy('created_at', 'asc');
                break;
            case 'created_at_desc':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $quizResponses = $query->paginate(15);

        return view('quiz_responses.index', compact('quizResponses'));
    }

    /**
     * Display the specified quiz response.
     */
    public function show(QuizResponse $quizResponse)
    {
        return view('quiz_responses.show', compact('quizResponse'));
    }

    /**
     * View the HTML report.
     */
    public function viewReport(QuizResponse $quizResponse)
    {
        // Se tiver report_html salvo, usa ele
        if ($quizResponse->report_html) {
            return response($quizResponse->report_html)
                ->header('Content-Type', 'text/html; charset=UTF-8');
        }
        
        // Caso contrário, gera relatório dinamicamente
        $html = $this->generateReportHtml($quizResponse);
        
        return response($html)
            ->header('Content-Type', 'text/html; charset=UTF-8');
    }

    /**
     * Download the PDF report.
     */
    public function downloadReport(QuizResponse $quizResponse)
    {
        $generator = new DISCReportGenerator();

        // Preparar dados do summary
        $summary = $quizResponse->summary ?? [];
        
        // Converter para formato esperado pelo generator se necessário
        if (!isset($summary['counts']) || !isset($summary['perc'])) {
            // Se não tiver o formato completo, tenta extrair do que tiver
            $summary = [
                'counts' => $summary['counts'] ?? [],
                'perc' => $summary['perc'] ?? [],
                'ordered' => $summary['ordered'] ?? [],
            ];
        }

        // Gerar relatório
        $report = $generator->generate($summary);
        
        // Gerar HTML
        $html = $generator->generateHTML(
            $report,
            $quizResponse->name ?? 'Respondente',
            $quizResponse->email ?? null,
            $quizResponse->response_time_minutes ?? null
        );

        // Converter para PDF
        $pdf = Pdf::loadHTML($html)
            ->setPaper('a4', 'portrait')
            ->setOption('margin-top', 0)
            ->setOption('margin-bottom', 0)
            ->setOption('margin-left', 0)
            ->setOption('margin-right', 0);

        $filename = 'relatorio-disc-' . ($quizResponse->name ? str_slug($quizResponse->name) : $quizResponse->id) . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Download PDF from external DISC system (Sistema A)
     */
    public function downloadReportFromExternal(Request $request)
    {
        // Validação
        $request->validate([
            'id' => 'required|integer',
        ]);

        $externalId = $request->get('id');

        try {
            // Conectar ao banco externo e puxar dados
            $externalData = DB::connection('disc_external')
                ->table('quiz_responses')
                ->where('id', $externalId)
                ->select('id', 'name', 'email', 'summary', 'response_time_minutes')
                ->first();

            if (!$externalData) {
                return back()->with('error', 'Registro não encontrado no banco externo');
            }

            // Converter summary de JSON para array se necessário
            $summary = is_string($externalData->summary) 
                ? json_decode($externalData->summary, true) 
                : (array)$externalData->summary;

            // Gerar PDF
            $generator = new DISCReportGenerator();
            $report = $generator->generate($summary);
            
            $html = $generator->generateHTML(
                $report,
                $externalData->name ?? 'Respondente',
                $externalData->email ?? null,
                $externalData->response_time_minutes ?? null
            );

            $pdf = Pdf::loadHTML($html)
                ->setPaper('a4', 'portrait')
                ->setOption('margin-top', 0)
                ->setOption('margin-bottom', 0)
                ->setOption('margin-left', 0)
                ->setOption('margin-right', 0);

            $filename = 'relatorio-disc-' . ($externalData->name ? str_slug($externalData->name) : $externalId) . '.pdf';

            return $pdf->download($filename);
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao gerar relatório: ' . $e->getMessage());
        }
    }
    
    /**
     * Generate HTML report from summary data
     */
    private function generateReportHtml(QuizResponse $quizResponse)
    {
        $summary = $quizResponse->summary;
        $answers = $quizResponse->answers;
        
        // Perfis e descrições
        $profileDescriptions = [
            'Dominância' => [
                'letter' => 'D',
                'title' => 'Dominância',
                'description' => 'Pessoas com alto perfil D são diretas, decididas e focadas em resultados. São competitivas, gostam de desafios e preferem ter controle sobre situações.',
                'strengths' => 'Liderança, tomada de decisão rápida, foco em objetivos, coragem para enfrentar desafios',
                'challenges' => 'Pode ser impaciente, direto demais, ter dificuldade em ouvir outros pontos de vista'
            ],
            'Influência' => [
                'letter' => 'I',
                'title' => 'Influência',
                'description' => 'Pessoas com alto perfil I são comunicativas, entusiastas e sociáveis. Adoram interagir com pessoas, são otimistas e inspiram outros.',
                'strengths' => 'Comunicação, persuasão, entusiasmo, criatividade, capacidade de motivar equipes',
                'challenges' => 'Pode ser impulsivo, desorganizado, ter dificuldade em focar em detalhes'
            ],
            'Estabilidade' => [
                'letter' => 'S',
                'title' => 'Estabilidade',
                'description' => 'Pessoas com alto perfil S são pacientes, leais e consistentes. Valorizam segurança, harmonia no ambiente e são excelentes ouvintes.',
                'strengths' => 'Paciência, trabalho em equipe, consistência, empatia, capacidade de criar ambiente harmonioso',
                'challenges' => 'Pode resistir a mudanças, ter dificuldade em dizer não, evitar conflitos necessários'
            ],
            'Conformidade' => [
                'letter' => 'C',
                'title' => 'Conformidade',
                'description' => 'Pessoas com alto perfil C são analíticas, precisas e focadas em qualidade. Valorizam dados, processos e padrões de excelência.',
                'strengths' => 'Atenção aos detalhes, pensamento analítico, qualidade, organização sistemática',
                'challenges' => 'Pode ser muito crítico, perfeccionista ao extremo, lento em tomar decisões'
            ]
        ];
        
        // Extrai dados do summary
        $percentages = isset($summary['perc']) ? $summary['perc'] : [];
        $ordered = isset($summary['ordered']) ? $summary['ordered'] : [];
        $counts = isset($summary['counts']) ? $summary['counts'] : [];
        
        // Perfil dominante
        $dominantProfile = '';
        $dominantPerc = 0;
        if (!empty($ordered) && isset($ordered[0])) {
            $dominantProfile = $ordered[0]['profile'];
            $dominantPerc = $ordered[0]['perc'];
        }
        
        $html = '<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório DISC - ' . ($quizResponse->email ?? 'Usuário') . '</title>
    <style>
        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        h1 {
            color: #2d3748;
            border-bottom: 4px solid #667eea;
            padding-bottom: 15px;
            margin-bottom: 30px;
            font-size: 36px;
        }
        h2 {
            color: #4a5568;
            margin-top: 40px;
            font-size: 24px;
            border-left: 5px solid #667eea;
            padding-left: 15px;
        }
        .info-box {
            background: #f7fafc;
            padding: 25px;
            border-radius: 10px;
            margin: 20px 0;
            border-left: 5px solid #667eea;
        }
        .info-box p {
            margin: 10px 0;
            font-size: 16px;
            color: #2d3748;
        }
        .info-box strong {
            color: #4a5568;
        }
        .dominant-profile {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            text-align: center;
            border-radius: 15px;
            margin: 30px 0;
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
        }
        .dominant-profile h2 {
            margin: 0;
            font-size: 48px;
            border: none;
            padding: 0;
            color: white;
        }
        .dominant-profile p {
            margin: 15px 0 0 0;
            font-size: 20px;
            opacity: 0.95;
        }
        .profile-bar-container {
            margin: 25px 0;
        }
        .profile-label {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2d3748;
            font-size: 16px;
        }
        .bar-wrapper {
            background: #e2e8f0;
            height: 40px;
            border-radius: 20px;
            overflow: hidden;
            position: relative;
        }
        .bar {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding-right: 15px;
            color: white;
            font-weight: bold;
            font-size: 16px;
            transition: width 1s ease;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
        }
        .bar-dominancia { background: linear-gradient(90deg, #f56565 0%, #e53e3e 100%); }
        .bar-influencia { background: linear-gradient(90deg, #48bb78 0%, #38a169 100%); }
        .bar-estabilidade { background: linear-gradient(90deg, #4299e1 0%, #3182ce 100%); }
        .bar-conformidade { background: linear-gradient(90deg, #ed8936 0%, #dd6b20 100%); }
        .profile-description {
            background: #fff;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 25px;
            margin: 20px 0;
        }
        .profile-description h3 {
            margin-top: 0;
            color: #1a202c;
            font-size: 22px;
        }
        .profile-description p {
            line-height: 1.8;
            color: #4a5568;
            margin: 12px 0;
        }
        .profile-description ul {
            margin: 10px 0;
            padding-left: 20px;
        }
        .profile-description li {
            margin: 8px 0;
            color: #4a5568;
            line-height: 1.6;
        }
        .section-title {
            background: #edf2f7;
            padding: 12px 20px;
            border-radius: 8px;
            margin: 15px 0 10px 0;
            font-weight: 600;
            color: #2d3748;
        }
        .footer {
            margin-top: 50px;
            padding-top: 30px;
            border-top: 2px solid #e2e8f0;
            color: #718096;
            font-size: 14px;
            text-align: center;
        }
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .container {
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎯 Relatório do Teste DISC</h1>
        
        <div class="info-box">
            <p><strong>📧 Email:</strong> ' . ($quizResponse->email ?? '-') . '</p>
            <p><strong>📅 Data do Teste:</strong> ' . $quizResponse->created_at->format('d/m/Y \à\s H:i') . '</p>
            <p><strong>⏱️ Tempo de Resposta:</strong> ' . ($quizResponse->response_time_minutes ?? '-') . ' minutos</p>
            <p><strong>📊 Total de Questões:</strong> ' . count($answers) . '</p>
        </div>';
        
        // Perfil dominante
        if ($dominantProfile && isset($profileDescriptions[$dominantProfile])) {
            $profileInfo = $profileDescriptions[$dominantProfile];
            $html .= '
        <div class="dominant-profile">
            <h2>Seu Perfil Dominante: ' . $profileInfo['letter'] . '</h2>
            <p>' . $profileInfo['title'] . ' (' . number_format($dominantPerc, 0) . '%)</p>
        </div>';
        }
        
        // Gráfico de barras
        if (!empty($percentages)) {
            $html .= '
        <h2>📊 Distribuição dos Perfis</h2>
        <p style="color: #4a5568; margin-bottom: 25px;">Veja como seus traços de personalidade se distribuem entre os quatro perfis DISC:</p>';
            
            $barClasses = [
                'Dominância' => 'bar-dominancia',
                'Influência' => 'bar-influencia',
                'Estabilidade' => 'bar-estabilidade',
                'Conformidade' => 'bar-conformidade'
            ];
            
            foreach ($ordered as $item) {
                $profile = $item['profile'];
                $perc = $item['perc'];
                $count = $item['count'];
                $letter = isset($profileDescriptions[$profile]) ? $profileDescriptions[$profile]['letter'] : '';
                $barClass = isset($barClasses[$profile]) ? $barClasses[$profile] : 'bar-dominancia';
                
                $html .= '
            <div class="profile-bar-container">
                <div class="profile-label">
                    <span>' . $letter . ' - ' . $profile . '</span>
                    <span>' . $count . ' respostas (' . number_format($perc, 1) . '%)</span>
                </div>
                <div class="bar-wrapper">
                    <div class="bar ' . $barClass . '" style="width: ' . $perc . '%;">' . number_format($perc, 0) . '%</div>
                </div>
            </div>';
            }
        }
        
        // Descrições detalhadas
        if (!empty($ordered)) {
            $html .= '
        <h2>📖 Entenda Seus Perfis</h2>
        <p style="color: #4a5568; margin-bottom: 25px;">Descrição detalhada de cada perfil, começando pelo seu dominante:</p>';
            
            foreach ($ordered as $item) {
                $profile = $item['profile'];
                if (isset($profileDescriptions[$profile])) {
                    $info = $profileDescriptions[$profile];
                    $perc = $item['perc'];
                    
                    $html .= '
        <div class="profile-description">
            <h3>' . $info['letter'] . ' - ' . $info['title'] . ' (' . number_format($perc, 0) . '%)</h3>
            <p><strong>Descrição:</strong> ' . $info['description'] . '</p>
            <div class="section-title">✨ Pontos Fortes</div>
            <p>' . $info['strengths'] . '</p>
            <div class="section-title">⚠️ Desafios</div>
            <p>' . $info['challenges'] . '</p>
        </div>';
                }
            }
        }
        
        $html .= '
        <div class="footer">
            <p><strong>O que é o DISC?</strong></p>
            <p>O DISC é uma ferramenta de avaliação comportamental que identifica padrões de personalidade através de quatro perfis principais: Dominância, Influência, Estabilidade e Conformidade.</p>
            <p style="margin-top: 20px;">Relatório gerado em ' . date('d/m/Y \à\s H:i') . '</p>
        </div>
    </div>
</body>
</html>';
        
        return $html;
    }
}
