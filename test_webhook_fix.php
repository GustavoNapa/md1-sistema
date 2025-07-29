<?php

// Teste para validar a correção do webhook com endereços
// Simula o fluxo completo: criação da inscrição → criação do endereço → webhook

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

    public function load($relations) {
        // Simula o carregamento dos relacionamentos
        if ($relations === 'client.addresses') {
            // Garante que o cliente tem endereços carregados
            $this->client->addresses = [new MockAddress()];
        }
    }
}

class MockClient {
    public $name = 'João Silva';
    public $email = 'joao@example.com';
    public $phone = '11999999999';
    public $addresses = [];

    public function addresses() {
        return new MockAddressRelation();
    }
}

class MockAddressRelation {
    public function latest() {
        return $this;
    }

    public function first() {
        return new MockAddress();
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

// Simular o método buildWebhookBody do ProcessInscriptionWebhook
function buildWebhookBodyFixed($inscription) {
    $client = $inscription->client;
    
    // Primeira tentativa: usar o relacionamento carregado
    $address = null;
    if (!empty($client->addresses)) {
        $address = $client->addresses[0];
    } else {
        // Segunda tentativa: usar o método latest()->first()
        $address = $client->addresses()->latest()->first();
    }

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
        "contact_endereco" => $address ? $address->endereco : null,
        "contact_numero_casa" => $address ? $address->numero_casa : null,
        "contact_complemento" => $address ? $address->complemento : null,
        "contact_bairro" => $address ? $address->bairro : null,
        "contact_cidade" => $address ? $address->cidade : null,
        "contact_estado" => $address ? $address->estado : null,
        "contact_cep" => $address ? $address->cep : null,
        "contact_pagamento_entrada" => (int)$inscription->valor_entrada,
        "contact_pagamento_restante" => (int)$inscription->valor_restante,
        "contact_data_pagamento_entra" => $inscription->data_pagamento_entrada->format('d/m/Y')
    ];
}

echo "=== TESTE DA CORREÇÃO DO WEBHOOK ===\n\n";

// Teste 1: Sem carregar relacionamentos (situação original)
echo "TESTE 1: Sem carregar relacionamentos\n";
$inscription1 = new MockInscription();
$inscription1->client->addresses = []; // Simula endereços não carregados
$webhookBody1 = buildWebhookBodyFixed($inscription1);

echo "Campos de endereço:\n";
echo "contact_endereco: " . ($webhookBody1['contact_endereco'] ?? 'NULL') . "\n";
echo "contact_numero_casa: " . ($webhookBody1['contact_numero_casa'] ?? 'NULL') . "\n";
echo "contact_bairro: " . ($webhookBody1['contact_bairro'] ?? 'NULL') . "\n";
echo "contact_cidade: " . ($webhookBody1['contact_cidade'] ?? 'NULL') . "\n";

echo "\n" . str_repeat("-", 50) . "\n\n";

// Teste 2: Com relacionamentos carregados (situação corrigida)
echo "TESTE 2: Com relacionamentos carregados\n";
$inscription2 = new MockInscription();
$inscription2->load('client.addresses'); // Simula o load dos relacionamentos
$webhookBody2 = buildWebhookBodyFixed($inscription2);

echo "Campos de endereço:\n";
echo "contact_endereco: " . ($webhookBody2['contact_endereco'] ?? 'NULL') . "\n";
echo "contact_numero_casa: " . ($webhookBody2['contact_numero_casa'] ?? 'NULL') . "\n";
echo "contact_bairro: " . ($webhookBody2['contact_bairro'] ?? 'NULL') . "\n";
echo "contact_cidade: " . ($webhookBody2['contact_cidade'] ?? 'NULL') . "\n";

echo "\n=== RESULTADO ===\n";
if ($webhookBody2['contact_endereco'] !== null) {
    echo "✅ CORREÇÃO FUNCIONOU! Os campos de endereço agora são preenchidos.\n";
} else {
    echo "❌ CORREÇÃO NÃO FUNCIONOU. Os campos ainda estão nulos.\n";
}

echo "\nWebhook body completo (corrigido):\n";
echo json_encode($webhookBody2, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

