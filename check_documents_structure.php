<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== ESTRUTURA DA TABELA DOCUMENTS ===\n\n";

// Obter a estrutura da tabela
$columns = DB::select("PRAGMA table_info(documents)");

echo "Colunas existentes:\n";
echo str_repeat("-", 60) . "\n";
foreach ($columns as $column) {
    echo sprintf("%-20s | %-15s | Null: %s\n", 
        $column->name, 
        $column->type, 
        $column->notnull ? 'NO' : 'YES'
    );
}

echo "\n" . str_repeat("=", 60) . "\n";
