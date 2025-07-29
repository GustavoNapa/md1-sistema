<?php

// Script para testar o webhook com dados de endereço
// Simula o comportamento do ProcessInscriptionWebhook

class MockInscription {
    public $id = 1;
    public $natureza_juridica = 'pessoa fisica';
    public $cpf_cnpj = '12345678901';
    public $valor_total = 20000.00;
    public $forma_pagamento_entrada = 'PIX';
    public $valor_entrada = 10000.00;
    public $data_pagamento_entrada;
    public $forma_pagamento_restante = 'Cartão';
    public $valor_restante = 10000.00;
    public $data_contrato;
    public $status = 'active';
    public $client;

    public function __construct() {
        $this->data_pagamento_entrada = new DateTime('2025-08-01');
        $this->data_contrato = new DateTime('2025-08-15');
        $this->client = new MockClient();
    }
}

class MockClient {
    public $name = 'João Silva';
    public $email = 'joao@example.com';
    public $phone = '11999999999';
    public $addresses;

    public function __construct() {
        $this->addresses = [new MockAddress()];
    }
}

class MockAddress {
    public $endereco = 'Rua das Flores';
    public $numero_casa = '123';
    public $complemento = 'Apto 45';
    public $bairro = 'Centro';
    public $cidade = 'São Paulo';
    public $estado = 'SP';
    public $cep = '01234-567';
}

// Simular o método buildWebhookBody
function buildWebhookBody($inscription) {
    $client = $inscription->client;
    $address = $client->addresses[0];

    return [
        "contact_name" => $client->name,
        "contact_email" => $client->email,
        "contact_phone" => $client->phone,
        "deal_stage" => "Sistema MD1",
        "deal_user" => 'sistema@md1.com',
        "deal_status" => strtoupper($inscription->status),
        "contact_natureza_juridica" => [$inscription->natureza_juridica],
        "contact_cpfcnpj" => $inscription->cpf_cnpj,
        "contact_produto" => number_format($inscription->valor_total, 0, '', ''),
        "contact_forma_pagamento_entr" => $inscription->forma_pagamento_entrada,
        "contact_parcelas_cartao" => $inscription->forma_pagamento_restante,
        "contact_data_contrato" => $inscription->data_contrato->format('d/m/Y'),
        "contact_endereco" => $address->endereco,
        "contact_numero_casa" => $address->numero_casa,
        "contact_complemento" => $address->complemento,
        "contact_bairro" => $address->bairro,
        "contact_cidade" => $address->cidade,
        "contact_estado" => $address->estado,
        "contact_cep" => $address->cep,
        "contact_pagamento_entrada" => (int)$inscription->valor_entrada,
        "contact_pagamento_restante" => (int)$inscription->valor_restante,
        "contact_data_pagamento_entra" => $inscription->data_pagamento_entrada->format('d/m/Y')
    ];
}

// Teste
$inscription = new MockInscription();
$webhookBody = buildWebhookBody($inscription);

echo "=== TESTE DO WEBHOOK BODY ===\n\n";

echo "Campos de Endereço:\n";
echo "contact_endereco: " . ($webhookBody['contact_endereco'] ?? 'NULL') . "\n";
echo "contact_numero_casa: " . ($webhookBody['contact_numero_casa'] ?? 'NULL') . "\n";
echo "contact_complemento: " . ($webhookBody['contact_complemento'] ?? 'NULL') . "\n";
echo "contact_bairro: " . ($webhookBody['contact_bairro'] ?? 'NULL') . "\n";
echo "contact_cidade: " . ($webhookBody['contact_cidade'] ?? 'NULL') . "\n";
echo "contact_estado: " . ($webhookBody['contact_estado'] ?? 'NULL') . "\n";
echo "contact_cep: " . ($webhookBody['contact_cep'] ?? 'NULL') . "\n";

echo "\nTodos os campos:\n";
echo json_encode($webhookBody, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

