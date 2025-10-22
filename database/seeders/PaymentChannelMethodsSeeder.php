<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentChannelMethodsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        $methods = [];

        // Substitui a geração hardcoded por leitura dos canais existentes
        $channels = DB::table('payment_channels')->select('id', 'name')->get();

        foreach ($channels as $channel) {
            $channelId = $channel->id;
            $name = $channel->name;

            // Sempre adiciona "A vista" (1 parcela)
            $methods[] = [
                'payment_channel_id' => $channelId,
                'name' => 'A vista',
                'installments' => 1,
                'active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            // Se for canal de cartão, adiciona 1x..12x
            if (stripos($name, 'cartão') !== false || stripos($name, 'cartao') !== false || stripos($name, 'crédito') !== false || stripos($name, 'credito') !== false) {
                for ($i = 1; $i <= 12; $i++) {
                    $methods[] = [
                        'payment_channel_id' => $channelId,
                        'name' => "{$i}x",
                        'installments' => $i,
                        'active' => true,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }

            // Para outros canais (Pix, Boleto, etc.) mantemos apenas "A vista" por padrão.
        }

        foreach ($methods as $m) {
            DB::table('payment_channel_methods')->updateOrInsert(
                ['payment_channel_id' => $m['payment_channel_id'], 'name' => $m['name']],
                $m
            );
        }
    }
}
