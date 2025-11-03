<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();
        $vendors = [
            ['id' => 1, 'name' => 'Anderson', 'email' => null, 'phone' => null, 'active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name' => 'Gustavo Souza', 'email' => 'tecnicowebmedicina@gmail.com', 'phone' => null, 'active' => true, 'created_at' => $now, 'updated_at' => $now],
        ];

        foreach ($vendors as $vendor) {
            DB::table('vendors')->updateOrInsert(['id' => $vendor['id']], $vendor);
        }
    }
}