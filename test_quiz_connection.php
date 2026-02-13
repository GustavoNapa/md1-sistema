<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Testando conexão com banco Quiz/DISC...\n";
    
    // Testa a conexão
    $connection = \DB::connection('quiz_mysql');
    $connection->getPdo();
    echo "✓ Conexão estabelecida com sucesso!\n\n";
    
    // Conta registros
    $count = \App\Models\QuizResponse::count();
    echo "Total de testes DISC encontrados: " . $count . "\n\n";
    
    // Lista os primeiros 5
    if ($count > 0) {
        echo "Primeiros 5 testes:\n";
        $responses = \App\Models\QuizResponse::orderBy('created_at', 'desc')->take(5)->get();
        foreach ($responses as $response) {
            echo "  - ID: {$response->id} | Nome: {$response->name} | Email: {$response->email} | Data: {$response->created_at}\n";
        }
    } else {
        echo "Nenhum teste DISC encontrado no banco de dados.\n";
    }
    
} catch (\Exception $e) {
    echo "✗ Erro ao conectar: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
