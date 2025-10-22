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
            ['id' => 1, 'name' => 'Pix', 'description' => 'Canal de pagamento via Pix', 'active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'name' => 'Cartão de Crédito', 'description' => 'Canal de pagamento via cartão de crédito', 'active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'name' => 'Boleto Bancário', 'description' => 'Canal de pagamento via boleto bancário', 'active' => true, 'created_at' => $now, 'updated_at' => $now]
        ];

        foreach ($channels as $ch) {
            DB::table('payment_channels')->updateOrInsert(['id' => $ch['id']], $ch);
        }
    }
}
