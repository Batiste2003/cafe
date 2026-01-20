<?php

namespace Database\Factories;

use App\Domain\Store\Enumeration\StoreStatusEnum;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Store>
 */
class StoreFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = Store::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'status' => StoreStatusEnum::DRAFT->value,
            'created_by' => User::factory(),
        ];
    }

    /**
     * Indicate that the store is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => StoreStatusEnum::ACTIVE->value,
        ]);
    }

    /**
     * Indicate that the store is a draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => StoreStatusEnum::DRAFT->value,
        ]);
    }

    /**
     * Indicate that the store is unpublished.
     */
    public function unpublished(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => StoreStatusEnum::UNPUBLISH->value,
        ]);
    }
}
