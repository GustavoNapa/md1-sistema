<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "====================================\n";
echo "ESTRUTURA COMPLETA DA TABELA\n";
echo "====================================\n\n";

try {
    // Pega a estrutura da tabela
    $columns = \DB::connection('quiz_mysql')->select('DESCRIBE quiz_responses');
    
    echo "Colunas da tabela quiz_responses:\n\n";
    foreach ($columns as $column) {
        echo "  - {$column->Field} ({$column->Type})\n";
    }
    
    echo "\n====================================\n";
    echo "DADOS COMPLETOS DE 1 REGISTRO\n";
    echo "====================================\n\n";
    
    // Pega um registro completo
    $response = \App\Models\QuizResponse::orderBy('created_at', 'desc')->first();
    
    if ($response) {
        echo "ID: {$response->id}\n\n";
        
        // Mostra todos os atributos
        $attributes = $response->getAttributes();
        
        foreach ($attributes as $key => $value) {
            echo "Campo: {$key}\n";
            
            if (is_null($value)) {
                echo "  Valor: NULL\n";
            } elseif (in_array($key, ['answers', 'summary']) && $value) {
                echo "  Tipo: JSON\n";
                echo "  Tamanho: " . strlen($value) . " caracteres\n";
                echo "  Preview:\n";
                $decoded = json_decode($value, true);
                echo "  " . print_r($decoded, true) . "\n";
            } elseif ($key === 'report_html' && $value) {
                echo "  Tipo: HTML\n";
                echo "  Tamanho: " . strlen($value) . " caracteres\n";
                echo "  Preview (primeiros 500 chars):\n";
                echo "  " . substr($value, 0, 500) . "...\n";
            } else {
                echo "  Valor: " . (strlen($value) > 200 ? substr($value, 0, 200) . '...' : $value) . "\n";
            }
            echo "  ---\n";
        }
    } else {
        echo "Nenhum registro encontrado.\n";
    }
    
} catch (\Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
}
