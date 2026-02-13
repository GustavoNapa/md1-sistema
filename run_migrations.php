<?php

/**
 * Script temporário para executar migrations em produção
 * IMPORTANTE: Remova este arquivo após usar!
 * 
 * Acesse via: https://seu-dominio.com/run_migrations.php
 */

// Carrega o autoloader do Laravel
require __DIR__.'/vendor/autoload.php';

// Carrega o aplicativo Laravel
$app = require_once __DIR__.'/bootstrap/app.php';

// Cria o kernel
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

// Executa o comando de migration
$status = $kernel->call('migrate', [
    '--force' => true, // Necessário para rodar em produção
]);

echo "<h1>Migrations Executadas</h1>";
echo "<p>Status: " . ($status === 0 ? 'Sucesso' : 'Erro') . "</p>";
echo "<pre>";
echo "Código de saída: " . $status;
echo "</pre>";

echo "<h2>IMPORTANTE:</h2>";
echo "<p style='color: red; font-weight: bold;'>REMOVA ESTE ARQUIVO IMEDIATAMENTE APÓS O USO POR SEGURANÇA!</p>";
