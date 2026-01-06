<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== ATUALIZANDO SIGN_URL DO DOCUMENTO ===\n\n";

// Buscar o documento da inscrição #1
$document = DB::table('documents')->where('inscription_id', 1)->first();

if ($document) {
    echo "Documento encontrado:\n";
    echo "  ID: " . $document->id . "\n";
    echo "  Nome: " . $document->nome . "\n";
    echo "  Sign URL atual: " . ($document->sign_url ?? 'NULL') . "\n\n";
    
    // Atualizar o sign_url usando o token específico
    $updated = DB::table('documents')
        ->where('token', '58a55a29-ab08-4ebf-ac63-7c4c620bcf6c')
        ->update([
            'sign_url' => 'https://app.zapsign.com.br/verificar/827c7996-3055-4974-b6c9-8e362d022daa',
            'updated_at' => now()
        ]);
    
    if ($updated) {
        echo "✓ Sign URL atualizada com sucesso!\n\n";
        
        // Verificar a atualização
        $documentoAtualizado = DB::table('documents')->where('id', $document->id)->first();
        echo "Dados atualizados:\n";
        echo "  ID: " . $documentoAtualizado->id . "\n";
        echo "  Nome: " . $documentoAtualizado->nome . "\n";
        echo "  Title: " . $documentoAtualizado->title . "\n";
        echo "  Sign URL: " . $documentoAtualizado->sign_url . "\n";
        echo "  File Web View: " . $documentoAtualizado->file_web_view . "\n";
        echo "  Updated At: " . $documentoAtualizado->updated_at . "\n";
    } else {
        echo "✗ Erro ao atualizar o documento\n";
    }
} else {
    echo "✗ Nenhum documento encontrado para a inscrição #1\n";
}

echo "\nConcluído!\n";
