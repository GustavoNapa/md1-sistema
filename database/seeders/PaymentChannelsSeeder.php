<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentChannelsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();
        $channels = [
            ['name' => 'Pagcorp', 'description' => 'Plataforma Pagcorp', 'active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Conta Bancária', 'description' => 'Pagamento via conta bancária (TED/PIX/Transferência)', 'active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Outro', 'description' => 'Outro canal de pagamento', 'active' => true, 'created_at' => $now, 'updated_at' => $now],
        ];

        foreach ($channels as $ch) {
            DB::table('payment_channels')->updateOrInsert(['name' => $ch['name']], $ch);
        }
    }
}
