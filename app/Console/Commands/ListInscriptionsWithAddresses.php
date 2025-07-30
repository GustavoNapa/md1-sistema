<?php

namespace App\Console\Commands;

use App\Models\Inscription;
use Illuminate\Console\Command;

class ListInscriptionsWithAddresses extends Command
{
    protected $signature = 'inscriptions:list-addresses';
    protected $description = 'Lista inscrições com seus endereços';

    public function handle()
    {
        $inscriptions = Inscription::with(['client.addresses'])
            ->orderBy('id', 'desc')
            ->take(10)
            ->get();

        if ($inscriptions->isEmpty()) {
            $this->error('Nenhuma inscrição encontrada.');
            return 1;
        }

        $this->info('Últimas 10 inscrições com informações de endereço:');
        
        $data = [];
        foreach ($inscriptions as $inscription) {
            $client = $inscription->client;
            $address = $client->addresses()->latest()->first();
            
            $data[] = [
                'ID Inscr.', $inscription->id,
                'Cliente', $client->name,
                'Status', $inscription->status,
                'Qtd Endereços', $client->addresses->count(),
                'Último Endereço', $address ? $address->endereco . ', ' . $address->cidade : 'Nenhum'
            ];
        }

        $this->table(
            ['ID Inscr.', 'Cliente', 'Status', 'Qtd Endereços', 'Último Endereço'],
            $data
        );

        return 0;
    }
}
