<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== SIMULAÇÃO DE CRIAÇÃO DE INSCRIÇÃO ===\n\n";

// Dados do cliente (do n8n)
$clientCpf = "12633656633";
$clientName = "Teste Anderson";
$clientEmail = "andersonadr58@gmail.com";
$clientPhone = "(31) 98528-3897";

echo "1. Buscando ou criando cliente no banco de dados...\n";
$client = DB::table('clients')->where('cpf', $clientCpf)->first();

if (!$client) {
    echo "  Cliente não encontrado. Criando novo cliente...\n";
    $clientId = DB::table('clients')->insertGetId([
        'name' => $clientName,
        'cpf' => $clientCpf,
        'email' => $clientEmail,
        'phone' => $clientPhone,
        'active' => 1,
        'created_at' => '2025-11-13 13:10:34',
        'updated_at' => '2025-11-13 13:10:34',
    ]);
    $client = DB::table('clients')->where('id', $clientId)->first();
    echo "  ✓ Cliente criado com ID: {$clientId}\n";
} else {
    echo "  ✓ Cliente já existe\n";
}

echo "✓ Cliente encontrado:\n";
echo "  ID: {$client->id}\n";
echo "  Nome: {$client->name}\n";
echo "  CPF: {$client->cpf}\n";
echo "  Email: {$client->email}\n";
echo "  Phone: {$client->phone}\n\n";

// Criar nova inscrição
echo "\n2. Criando nova inscrição...\n";

$inscriptionId = DB::table('inscriptions')->insertGetId([
    'client_id' => $client->id,
    'vendor_id' => 1, // Ajuste conforme necessário
    'product_id' => 1, // Ajuste conforme necessário
    'status' => 'active',
    'natureza_juridica' => 'pessoa fisica',
    'cpf_cnpj' => $client->cpf,
    'valor_total' => 10000,
    'created_at' => now(),
    'updated_at' => now(),
]);

echo "✓ Inscrição criada com sucesso!\n";
echo "  Inscription ID: {$inscriptionId}\n\n";

// Dados do documento (do n8n)
echo "3. Inserindo documento na inscrição...\n";

$documentData = [
    'inscription_id' => $inscriptionId,
    'title' => 'Contrato CRM Black 2025',
    'nome' => $client->name, // Nome do signatário
    'file_type' => 'application/pdf',
    'file_size' => 0, // Pode ajustar depois
    'sign_url' => 'https://app.zapsign.com.br/verificar/b7acc209-bf6c-4dfd-9ba5-e497016d7e2e',
    'token' => 'b7acc209-bf6c-4dfd-9ba5-e497016d7e2e',
    'created_at' => '2026-01-06 20:35:39',
    'updated_at' => '2026-01-06 20:35:39',
];

$documentId = DB::table('documents')->insertGetId($documentData);

echo "✓ Documento inserido com sucesso!\n";
echo "  Document ID: {$documentId}\n\n";

// Verificar os dados inseridos
echo "4. Verificando dados inseridos...\n\n";

$inscription = DB::table('inscriptions')->where('id', $inscriptionId)->first();
echo "INSCRIÇÃO:\n";
echo "  ID: {$inscription->id}\n";
echo "  Client ID: {$inscription->client_id}\n";
echo "  Status: {$inscription->status}\n";
echo "  Criado em: {$inscription->created_at}\n\n";

$document = DB::table('documents')->where('id', $documentId)->first();
echo "DOCUMENTO:\n";
echo "  ID: {$document->id}\n";
echo "  Inscription ID: {$document->inscription_id}\n";
echo "  Nome: {$document->nome}\n";
echo "  Title: {$document->title}\n";
echo "  Sign URL: {$document->sign_url}\n";
echo "  Token: {$document->token}\n";
echo "  File Type: {$document->file_type}\n";
echo "  Criado em: {$document->created_at}\n\n";

// Testar o relacionamento usando Eloquent
echo "5. Testando relacionamento Eloquent...\n";

$inscriptionModel = \App\Models\Inscription::with('documents')->find($inscriptionId);

echo "✓ Inscrição carregada via Eloquent\n";
echo "  Total de documentos: " . $inscriptionModel->documents->count() . "\n";

if ($inscriptionModel->documents->count() > 0) {
    echo "  ✓ Documentos encontrados!\n";
    foreach ($inscriptionModel->documents as $doc) {
        echo "    - {$doc->title} (ID: {$doc->id})\n";
        echo "      Sign URL: {$doc->sign_url}\n";
    }
} else {
    echo "  ✗ PROBLEMA: Nenhum documento encontrado via relacionamento!\n";
    echo "  DIAGNÓSTICO: O relacionamento documents() pode estar incorreto.\n";
}

echo "\n" . str_repeat("=", 70) . "\n";
echo "RESUMO:\n";
echo "  Inscrição ID: {$inscriptionId}\n";
echo "  Cliente: {$client->name} (ID: {$client->id})\n";
echo "  Documento ID: {$documentId}\n";
echo "  URL para acessar: http://127.0.0.1:8000/inscriptions/{$inscriptionId}\n";
echo str_repeat("=", 70) . "\n\n";

echo "✓ Simulação concluída com sucesso!\n";
echo "\nAcesse a URL acima no navegador e vá até a aba 'Documentos'\n";
echo "para verificar se o documento aparece corretamente.\n";
