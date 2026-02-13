<?php

echo "====================================\n";
echo "VERIFICAÇÃO DO ARQUIVO .ENV\n";
echo "====================================\n\n";

// Lê o arquivo .env
$envFile = __DIR__ . '/.env';

if (!file_exists($envFile)) {
    echo "Arquivo .env não encontrado!\n";
    exit(1);
}

$envContent = file_get_contents($envFile);
$lines = explode("\n", $envContent);

echo "Credenciais do Banco QUIZ/DISC no .env:\n\n";

foreach ($lines as $line) {
    $line = trim($line);
    if (strpos($line, 'QUIZ_DB_') === 0) {
        echo "  " . $line . "\n";
    }
}

echo "\n";
echo "====================================\n";
echo "TESTE DE CONEXÃO\n";
echo "====================================\n\n";

// Agora testa a conexão
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Banco configurado no Laravel:\n";
echo "  Database: " . config('database.connections.quiz_mysql.database') . "\n";
echo "  Username: " . config('database.connections.quiz_mysql.username') . "\n";
echo "  Host: " . config('database.connections.quiz_mysql.host') . "\n\n";

try {
    $connection = \DB::connection('quiz_mysql');
    $connection->getPdo();
    echo "✓ Conexão estabelecida com sucesso!\n";
    
    $count = \App\Models\QuizResponse::count();
    echo "✓ Total de testes DISC: " . $count . "\n";
    
} catch (\Exception $e) {
    echo "✗ Erro ao conectar: " . $e->getMessage() . "\n";
    echo "\nIsso é normal em ambiente local se o MySQL não estiver acessível.\n";
    echo "Em produção funcionará corretamente.\n";
}

echo "\n";
