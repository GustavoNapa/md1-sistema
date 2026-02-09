<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use App\Models\Client;
use App\Models\Inscription;
use App\Models\Vendor;
use App\Models\Product;

class DevTestingSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('pt_BR');

        DB::beginTransaction();
        try {
            // Ensure there are some vendors
            $vendors = Vendor::count() ? Vendor::all() : collect();
            if ($vendors->isEmpty()) {
                for ($i = 1; $i <= 5; $i++) {
                    $vendors->push(Vendor::create([
                        'name' => "Vendedor {$i}",
                        'email' => "vendor{$i}@example.com",
                        'active' => true,
                    ]));
                }
            }

            // Ensure there are some products
            $products = Product::count() ? Product::all() : collect();
            if ($products->isEmpty()) {
                for ($i = 1; $i <= 6; $i++) {
                    $products->push(Product::create([
                        'name' => "Produto {$i}",
                        'is_active' => true,
                        'price' => rand(500, 5000) / 1,
                    ]));
                }
            }

            $vendorIds = $vendors->pluck('id')->toArray();
            $productIds = $products->pluck('id')->toArray();

            // Create many clients
            $clients = [];
            for ($i = 0; $i < 50; $i++) {
                // Gerar CPF simples (11 dígitos) sem depender de providers extras
                $cpf = preg_replace('/\D/', '', $faker->numerify('###########'));

                $clients[] = Client::create([
                    'name' => $faker->name,
                    'cpf' => $cpf,
                    'email' => $faker->unique()->safeEmail,
                    'sexo' => $faker->randomElement(['masculino','feminino','nao_informado']),
                    'birth_date' => $faker->date('Y-m-d', '-25 years'),
                    'phone' => $faker->phoneNumber,
                    'active' => true,
                ]);
            }

            $statuses = ['active', 'paused', 'cancelled', 'completed'];
            $classGroups = ['Turma A', 'Turma B', 'Turma C', null];
            $classifications = ['Start','Fase 01','Fase 02','Fase 03','Renovação'];

            foreach ($clients as $client) {
                $insCount = rand(1,3);
                for ($j=0;$j<$insCount;$j++) {
                    $valor = $faker->randomFloat(2, 500, 10000);
                    $amountPaid = $valor * ($faker->randomFloat(2, 0, 1));
                    Inscription::create([
                        'client_id' => $client->id,
                        'vendor_id' => $faker->randomElement($vendorIds) ?? null,
                        'product_id' => $faker->randomElement($productIds) ?? null,
                        'class_group' => $faker->randomElement($classGroups),
                        'status' => $faker->randomElement($statuses),
                        'classification' => $faker->randomElement($classifications),
                        'has_medboss' => $faker->boolean(20),
                        'crmb_number' => $faker->bothify('CRM-#####'),
                        'start_date' => $faker->dateTimeBetween('-60 days', '+60 days')->format('Y-m-d'),
                        'calendar_week' => $faker->numberBetween(1,52),
                        'amount_paid' => $amountPaid,
                        'payment_method' => $faker->randomElement(['pix','credit_card','bank_transfer','boleto']),
                        'valor_total' => $valor,
                        'valor_entrada' => $valor * 0.2,
                        'valor_restante' => $valor - ($valor * 0.2),
                        'created_at' => $faker->dateTimeBetween('-120 days', 'now'),
                        'updated_at' => now(),
                    ]);
                }
            }

            DB::commit();
            $this->command->info('DevTestingSeeder: Clients and inscriptions created.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('DevTestingSeeder failed: ' . $e->getMessage());
            throw $e;
        }
    }
}
