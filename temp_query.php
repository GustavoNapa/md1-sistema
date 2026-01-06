<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Buscar as 2 primeiras inscrições
$inscriptions = DB::table('inscriptions')->limit(2)->get();

echo "=== INSCRIÇÕES NO BANCO DE DADOS ===\n\n";

foreach ($inscriptions as $inscription) {
    echo "INSCRIÇÃO #" . $inscription->id . "\n";
    echo str_repeat("=", 60) . "\n";
    
    foreach ((array)$inscription as $key => $value) {
        echo str_pad($key . ":", 25) . $value . "\n";
    }
    
    // Buscar documentos desta inscrição
    $documents = DB::table('documents')->where('inscription_id', $inscription->id)->get();
    
    echo "\nDOCUMENTOS (" . count($documents) . "):\n";
    echo str_repeat("-", 60) . "\n";
    
    if (count($documents) > 0) {
        foreach ($documents as $doc) {
            echo "  Documento ID: " . $doc->id . "\n";
            echo "    Nome: " . ($doc->nome ?? 'N/A') . "\n";
            echo "    Title: " . ($doc->title ?? 'N/A') . "\n";
            echo "    Sign URL: " . ($doc->sign_url ?? 'N/A') . "\n";
            echo "    File Web View: " . ($doc->file_web_view ?? 'N/A') . "\n";
            echo "    Token: " . ($doc->token ?? 'N/A') . "\n";
            echo "    File Type: " . ($doc->file_type ?? 'N/A') . "\n";
            echo "    File Size: " . ($doc->file_size ?? 'N/A') . " bytes\n";
            echo "    Created At: " . ($doc->created_at ?? 'N/A') . "\n";
            echo "    Updated At: " . ($doc->updated_at ?? 'N/A') . "\n";
            echo "\n";
        }
    } else {
        echo "  Nenhum documento encontrado\n";
    }
    
    echo "\n" . str_repeat("=", 60) . "\n\n";
}

echo "Consulta concluída!\n";
