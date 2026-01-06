<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== VERIFICANDO CLIENTES EXISTENTES ===\n\n";

$clients = DB::table('clients')->limit(5)->get();

if ($clients->count() > 0) {
    echo "Clientes encontrados:\n";
    foreach ($clients as $client) {
        echo "  ID: {$client->id} - {$client->name} - CPF: {$client->cpf}\n";
    }
} else {
    echo "Nenhum cliente encontrado no banco de dados.\n";
}

echo "\n";
