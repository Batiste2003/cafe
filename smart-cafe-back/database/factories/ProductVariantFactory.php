<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductVariant>
 */
class ProductVariantFactory extends Factory
{
    protected $model = ProductVariant::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'sku' => fake()->unique()->regexify('[A-Z]{3}-[0-9]{4}'),
            'price_cent_ht' => fake()->numberBetween(100, 5000), // 1€ à 50€
            'cost_price_cent_ht' => fake()->optional()->numberBetween(50, 3000),
            'is_default' => false,
        ];
    }

    /**
     * Indique que le variant est le variant par défaut.
     */
    public function default(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_default' => true,
        ]);
    }


    /**
     * Associe le variant à un produit spécifique.
     */
    public function forProduct(Product $product): static
    {
        return $this->state(fn (array $attributes) => [
            'product_id' => $product->id,
        ]);
    }

    /**
     * Définit un prix spécifique en euros.
     */
    public function priceEuros(float $euros): static
    {
        return $this->state(fn (array $attributes) => [
            'price_cent_ht' => (int) ($euros * 100),
        ]);
    }
}
