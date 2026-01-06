<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== VERIFICANDO RELACIONAMENTOS E EXIBIÇÃO ===\n\n";

$inscriptionId = 5;

echo "Testando Inscrição ID: {$inscriptionId}\n";
echo str_repeat("-", 70) . "\n\n";

$inscription = \App\Models\Inscription::with(['documents', 'contractDocuments'])->find($inscriptionId);

if (!$inscription) {
    echo "✗ Inscrição não encontrada!\n";
    exit(1);
}

echo "✓ Inscrição encontrada\n\n";

// Testar aba DOCUMENTOS
echo "1️⃣  ABA 'DOCUMENTOS' (\$inscription->documents)\n";
echo "   Relacionamento: documents()\n";
echo "   Total: " . $inscription->documents->count() . " documento(s)\n";

if ($inscription->documents->count() > 0) {
    echo "   ✓ Documentos encontrados:\n";
    foreach ($inscription->documents as $doc) {
        echo "     - ID: {$doc->id}\n";
        echo "       Nome: {$doc->nome}\n";
        echo "       Title: {$doc->title}\n";
        echo "       Sign URL: " . ($doc->sign_url ?? 'NULL') . "\n";
        echo "       Created At: {$doc->created_at}\n\n";
    }
} else {
    echo "   ✗ Nenhum documento\n\n";
}

// Testar aba CONTRATO
echo "2️⃣  ABA 'CONTRATO' (\$inscription->contractDocuments)\n";
echo "   Relacionamento: contractDocuments()\n";
echo "   Total: " . $inscription->contractDocuments->count() . " documento(s)\n";

if ($inscription->contractDocuments->count() > 0) {
    echo "   ✓ Documentos encontrados:\n";
    foreach ($inscription->contractDocuments as $doc) {
        echo "     - ID: {$doc->id}\n";
        echo "       Nome: {$doc->nome}\n";
        echo "       Title: {$doc->title}\n";
        echo "       File Type: {$doc->file_type}\n";
        echo "       File Web View: " . ($doc->file_web_view ?? 'NULL') . "\n\n";
    }
} else {
    echo "   ✗ Nenhum documento\n\n";
}

// Verificar dados brutos da tabela
echo "3️⃣  CONSULTA DIRETA NA TABELA 'documents'\n";
$documentos = DB::table('documents')->where('inscription_id', $inscriptionId)->get();
echo "   Total na tabela: " . $documentos->count() . " documento(s)\n\n";

foreach ($documentos as $doc) {
    echo "   Documento ID: {$doc->id}\n";
    echo "     Title: {$doc->title}\n";
    echo "     Sign URL: " . ($doc->sign_url ?? 'NULL') . "\n";
    echo "     Token: " . ($doc->token ?? 'NULL') . "\n\n";
}

echo str_repeat("=", 70) . "\n";
echo "CONCLUSÃO:\n";
echo "  - Aba 'Documentos' mostra: {$inscription->documents->count()} item(ns)\n";
echo "  - Aba 'Contrato' mostra: {$inscription->contractDocuments->count()} item(ns)\n";
echo "  - Total real no banco: {$documentos->count()} item(ns)\n";
echo str_repeat("=", 70) . "\n";
