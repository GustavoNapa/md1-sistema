<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "====================================\n";
echo "VERIFICAÇÃO DOS DADOS QUIZ_RESPONSES\n";
echo "====================================\n\n";

try {
    $responses = \App\Models\QuizResponse::orderBy('created_at', 'desc')->take(5)->get();
    
    echo "Total de registros: " . \App\Models\QuizResponse::count() . "\n\n";
    
    echo "Primeiros 5 registros:\n\n";
    
    foreach ($responses as $response) {
        echo "ID: {$response->id}\n";
        echo "  Nome: " . ($response->name ?: 'NULL') . "\n";
        echo "  Email: " . ($response->email ?: 'NULL') . "\n";
        echo "  report_html: " . ($response->report_html ? 'SIM (' . strlen($response->report_html) . ' bytes)' : 'NÃO') . "\n";
        echo "  report_filename: " . ($response->report_filename ?: 'NULL') . "\n";
        echo "  summary: " . ($response->summary ? 'SIM' : 'NÃO') . "\n";
        echo "  answers: " . ($response->answers ? 'SIM (' . count($response->answers) . ' respostas)' : 'NÃO') . "\n";
        echo "  created_at: " . $response->created_at . "\n";
        echo "  ---\n";
    }
    
} catch (\Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
