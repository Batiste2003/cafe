<?php

namespace Database\Factories;

use App\Models\ProductOption;
use App\Models\ProductOptionValue;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductOptionValue>
 */
class ProductOptionValueFactory extends Factory
{
    protected $model = ProductOptionValue::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_option_id' => ProductOption::factory(),
            'value' => fake()->word(),
            'price_add_cent_ht' => fake()->randomElement([0, 0, 0, 50, 100, 150]), // Souvent gratuit
        ];
    }

    /**
     * Indique que la valeur n'a pas de supplément de prix.
     */
    public function free(): static
    {
        return $this->state(fn (array $attributes) => [
            'price_add_cent_ht' => 0,
        ]);
    }

    /**
     * Définit un supplément de prix en euros.
     */
    public function priceAddEuros(float $euros): static
    {
        return $this->state(fn (array $attributes) => [
            'price_add_cent_ht' => (int) ($euros * 100),
        ]);
    }

    /**
     * Associe la valeur à une option spécifique.
     */
    public function forOption(ProductOption $option): static
    {
        return $this->state(fn (array $attributes) => [
            'product_option_id' => $option->id,
        ]);
    }
}
