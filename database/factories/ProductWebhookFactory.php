<?php

namespace Database\Factories;

use App\Models\ProductWebhook;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductWebhookFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProductWebhook::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'product_id' => Product::factory(),
            'webhook_url' => $this->faker->url,
            'webhook_token' => $this->faker->uuid,
            'webhook_trigger_status' => $this->faker->randomElement(['active', 'pending', 'completed']),
        ];
    }
}

