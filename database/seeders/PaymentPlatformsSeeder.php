<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentPlatformsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();
        $platforms = [
            ['id' => 1, 'name' => 'Eduzz', 'description' => 'Plataforma Eduzz', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name' => 'Hotmart', 'description' => 'Plataforma Hotmart', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'name' => 'Asaas', 'description' => 'Plataforma Asaas', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'name' => 'GMC Pay', 'description' => 'Plataforma GMC Pay', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'name' => 'pagcorp', 'description' => 'Plataforma pagcorp', 'created_at' => $now, 'updated_at' => $now],
        ];

        foreach ($platforms as $p) {
            DB::table('payment_platforms')->updateOrInsert(['id' => $p['id']], $p);
        }
    }
}
