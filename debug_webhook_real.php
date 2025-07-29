<?php

require_once 'vendor/autoload.php';

// Carregar configuração do Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Inscription;
use App\Models\Client;
use App\Models\Address;

echo "=== DEBUG WEBHOOK ENDEREÇOS ===\n\n";

// Verificar se existem inscrições
$inscriptionCount = Inscription::count();
echo "Total de inscrições: $inscriptionCount\n";

if ($inscriptionCount > 0) {
    $inscription = Inscription::with('client.addresses')->first();
    echo "Primeira inscrição ID: " . $inscription->id . "\n";
    echo "Cliente: " . $inscription->client->name . "\n";
    echo "Endereços do cliente: " . $inscription->client->addresses->count() . "\n";
    
    if ($inscription->client->addresses->count() > 0) {
        $address = $inscription->client->addresses->first();
        echo "\nPrimeiro endereço:\n";
        echo "- Endereço: " . ($address->endereco ?? 'NULL') . "\n";
        echo "- Número: " . ($address->numero_casa ?? 'NULL') . "\n";
        echo "- Bairro: " . ($address->bairro ?? 'NULL') . "\n";
        echo "- Cidade: " . ($address->cidade ?? 'NULL') . "\n";
        echo "- Estado: " . ($address->estado ?? 'NULL') . "\n";
        echo "- CEP: " . ($address->cep ?? 'NULL') . "\n";
    } else {
        echo "\nNenhum endereço encontrado para este cliente!\n";
        
        // Verificar se existem endereços na tabela
        $totalAddresses = Address::count();
        echo "Total de endereços na tabela addresses: $totalAddresses\n";
        
        if ($totalAddresses > 0) {
            echo "\nEndereços existentes:\n";
            $addresses = Address::with('client')->get();
            foreach ($addresses as $addr) {
                echo "- Cliente: " . $addr->client->name . " | Endereço: " . $addr->endereco . "\n";
            }
        }
    }
} else {
    echo "Nenhuma inscrição encontrada.\n";
    
    // Verificar clientes
    $clientCount = Client::count();
    echo "Total de clientes: $clientCount\n";
    
    if ($clientCount > 0) {
        $client = Client::with('addresses')->first();
        echo "Primeiro cliente: " . $client->name . "\n";
        echo "Endereços do primeiro cliente: " . $client->addresses->count() . "\n";
    }
}

// Verificar estrutura da tabela addresses
echo "\n=== ESTRUTURA DA TABELA ADDRESSES ===\n";
try {
    $addresses = Address::all();
    echo "Total de registros na tabela addresses: " . $addresses->count() . "\n";
    
    if ($addresses->count() > 0) {
        $firstAddress = $addresses->first();
        echo "Primeiro endereço:\n";
        echo json_encode($firstAddress->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
} catch (Exception $e) {
    echo "Erro ao acessar tabela addresses: " . $e->getMessage() . "\n";
}

