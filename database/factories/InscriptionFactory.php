<?php

namespace Database\Factories;

use App\Models\Inscription;
use App\Models\Client;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class InscriptionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Inscription::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'client_id' => Client::factory(),
            'product_id' => Product::factory(),
            'status' => $this->faker->randomElement(['active', 'paused', 'cancelled', 'completed']),
            'class_group' => $this->faker->word,
            'classification' => $this->faker->word,
            'has_medboss' => $this->faker->boolean,
            'crmb_number' => $this->faker->word,
            'start_date' => $this->faker->date(),
            'original_end_date' => $this->faker->date(),
            'actual_end_date' => $this->faker->date(),
            'platform_release_date' => $this->faker->date(),
            'calendar_week' => $this->faker->numberBetween(1, 52),
            'current_week' => $this->faker->numberBetween(1, 52),
            'amount_paid' => $this->faker->randomFloat(2, 100, 10000),
            'payment_method' => $this->faker->randomElement(['credit_card', 'pix', 'bank_transfer']),
            'commercial_notes' => $this->faker->sentence,
            'general_notes' => $this->faker->sentence,
            'contrato_assinado' => $this->faker->boolean,
            'contrato_na_pasta' => $this->faker->boolean,
        ];
    }
}


