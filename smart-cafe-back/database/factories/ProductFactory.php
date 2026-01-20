<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'description' => fake()->optional()->paragraph(),
            'product_category_id' => ProductCategory::factory(),
            'created_by' => User::factory(),
            'is_active' => true,
            'is_featured' => fake()->boolean(20),
        ];
    }

    /**
     * Configure le modèle après création pour attacher des stores.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Product $product) {
            // Par défaut, attacher un store si aucun n'est attaché
            if ($product->stores()->count() === 0) {
                $store = Store::first() ?? Store::factory()->create();
                $product->stores()->attach($store);
            }
        });
    }

    /**
     * Indique que le produit est inactif.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indique que le produit est mis en avant.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }

    /**
     * Associe le produit à une catégorie spécifique.
     */
    public function forCategory(ProductCategory $category): static
    {
        return $this->state(fn (array $attributes) => [
            'product_category_id' => $category->id,
        ]);
    }

    /**
     * Associe le produit à des stores spécifiques.
     *
     * @param  Store|array<Store>  $stores
     */
    public function forStores(Store|array $stores): static
    {
        $stores = is_array($stores) ? $stores : [$stores];

        return $this->afterCreating(function (Product $product) use ($stores) {
            $storeIds = array_map(fn ($store) => $store->id, $stores);
            $product->stores()->sync($storeIds);
        });
    }

    /**
     * Associe le produit à un créateur spécifique.
     */
    public function createdBy(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'created_by' => $user->id,
        ]);
    }
}
