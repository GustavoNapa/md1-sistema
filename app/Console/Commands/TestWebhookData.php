<?php

namespace App\Console\Commands;

use App\Models\Inscription;
use App\Models\Client;
use App\Models\Address;
use App\Jobs\SendInscriptionWebhook;
use Illuminate\Console\Command;

class TestWebhookData extends Command
{
    protected $signature = 'webhook:test-data {inscription_id?}';
    protected $description = 'Testa os dados do webhook para uma inscrição específica';

    public function handle()
    {
        $inscriptionId = $this->argument('inscription_id');
        
        if (!$inscriptionId) {
            // Buscar a primeira inscrição disponível
            $inscription = Inscription::with(['client.addresses'])->first();
            if (!$inscription) {
                $this->error('Nenhuma inscrição encontrada no sistema.');
                return 1;
            }
        } else {
            $inscription = Inscription::with(['client.addresses'])->find($inscriptionId);
            if (!$inscription) {
                $this->error("Inscrição com ID {$inscriptionId} não encontrada.");
                return 1;
            }
        }

        $this->info("Testando dados da inscrição ID: {$inscription->id}");
        $this->info("Cliente: {$inscription->client->name}");
        
        // Verificar endereços
        $addresses = $inscription->client->addresses;
        $this->info("Endereços encontrados: " . $addresses->count());
        
        if ($addresses->count() > 0) {
            $address = $addresses->first();
            $this->table(
                ['Campo', 'Valor'],
                [
                    ['CEP', $address->cep],
                    ['Endereço', $address->endereco],
                    ['Número', $address->numero_casa],
                    ['Complemento', $address->complemento],
                    ['Bairro', $address->bairro],
                    ['Cidade', $address->cidade],
                    ['Estado', $address->estado],
                ]
            );
        } else {
            $this->warn('Nenhum endereço encontrado para este cliente.');
        }

        // Testar o payload do webhook
        $this->info("\n--- Simulando payload do webhook ---");
        
        // Simular o job de webhook
        $job = new SendInscriptionWebhook($inscription, 'inscricao.created');
        
        // Usar reflection para acessar o método privado buildPayload
        $reflection = new \ReflectionClass($job);
        $method = $reflection->getMethod('buildPayload');
        $method->setAccessible(true);
        
        $payload = $method->invoke($job);
        
        // Mostrar apenas os dados de endereço do body
        $this->info("Dados de endereço no payload:");
        $this->table(
            ['Campo', 'Valor'],
            [
                ['contact_endereco', $payload['body']['contact_endereco'] ?? 'null'],
                ['contact_numero_casa', $payload['body']['contact_numero_casa'] ?? 'null'],
                ['contact_complemento', $payload['body']['contact_complemento'] ?? 'null'],
                ['contact_bairro', $payload['body']['contact_bairro'] ?? 'null'],
                ['contact_cidade', $payload['body']['contact_cidade'] ?? 'null'],
                ['contact_estado', $payload['body']['contact_estado'] ?? 'null'],
                ['contact_cep', $payload['body']['contact_cep'] ?? 'null'],
            ]
        );

        return 0;
    }
}
