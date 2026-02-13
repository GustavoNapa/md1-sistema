<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Recarrega o .env
$app->useEnvironmentPath(__DIR__);
$app->loadEnvironmentFrom('.env');

echo "====================================\n";
echo "TESTE DE CONEXÕES DE BANCO DE DADOS\n";
echo "====================================\n\n";

// TESTE 1: Conexão Principal do Sistema
echo "1. CONEXÃO PRINCIPAL DO SISTEMA\n";
echo "   Tipo: " . env('DB_CONNECTION', 'não definido') . "\n";

try {
    $defaultConnection = \DB::connection();
    $defaultConnection->getPdo();
    echo "   Status: ✓ Conectado com sucesso!\n";
    
    // Testa listando clientes
    $clientsCount = \DB::table('clients')->count();
    echo "   Clientes na base: " . $clientsCount . "\n";
    
    if ($clientsCount > 0) {
        $firstClient = \DB::table('clients')->first();
        echo "   Exemplo: " . ($firstClient->name ?? 'N/A') . "\n";
    }
    
} catch (\Exception $e) {
    echo "   Status: ✗ ERRO: " . $e->getMessage() . "\n";
}

echo "\n";

// TESTE 2: Conexão Quiz/DISC
echo "2. CONEXÃO QUIZ/DISC\n";
echo "   Host: " . env('QUIZ_DB_HOST', 'não definido') . "\n";
echo "   Database: " . env('QUIZ_DB_DATABASE', 'não definido') . "\n";

try {
    $quizConnection = \DB::connection('quiz_mysql');
    $quizConnection->getPdo();
    echo "   Status: ✓ Conectado com sucesso!\n";
    
    // Testa listando quiz_responses
    $quizCount = \App\Models\QuizResponse::count();
    echo "   Testes DISC na base: " . $quizCount . "\n";
    
    if ($quizCount > 0) {
        $firstQuiz = \App\Models\QuizResponse::orderBy('created_at', 'desc')->first();
        echo "   Último teste: " . ($firstQuiz->name ?? 'N/A') . " (" . $firstQuiz->created_at->format('d/m/Y') . ")\n";
    }
    
} catch (\Exception $e) {
    echo "   Status: ✗ ERRO: " . $e->getMessage() . "\n";
    
    // Verifica se é erro de conexão ou configuração
    if (strpos($e->getMessage(), 'could not find driver') !== false) {
        echo "   Dica: Driver MySQL não instalado\n";
    } elseif (strpos($e->getMessage(), 'Connection refused') !== false || strpos($e->getMessage(), 'recusou') !== false) {
        echo "   Dica: MySQL não está rodando localmente ou não está acessível\n";
        echo "   INFO: Em produção, onde o MySQL está no mesmo servidor, funcionará!\n";
    }
}

echo "\n";

// RESUMO
echo "====================================\n";
echo "RESUMO\n";
echo "====================================\n";

try {
    \DB::connection()->getPdo();
    echo "✓ Conexão principal: OK\n";
} catch (\Exception $e) {
    echo "✗ Conexão principal: FALHOU\n";
}

try {
    \DB::connection('quiz_mysql')->getPdo();
    echo "✓ Conexão Quiz/DISC: OK\n";
} catch (\Exception $e) {
    echo "✗ Conexão Quiz/DISC: FALHOU (normal em ambiente local)\n";
}

echo "\n";
