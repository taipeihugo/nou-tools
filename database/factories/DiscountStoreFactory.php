<?php

namespace Database\Factories;

use App\Enums\DiscountStoreStatus;
use App\Enums\DiscountStoreType;
use App\Models\DiscountStore;
use App\Models\DiscountStoreCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DiscountStore>
 */
class DiscountStoreFactory extends Factory
{
    protected $model = DiscountStore::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'status' => DiscountStoreStatus::Pending,
            'type' => fake()->randomElement(DiscountStoreType::cases()),
            'category_id' => DiscountStoreCategory::factory(),
            'address' => fake()->address(),
            'verification_method' => '學生證',
            'discount_details' => fake()->sentence(),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function online(): static
    {
        return $this->state(fn (): array => ['status' => DiscountStoreStatus::Online]);
    }

    public function pending(): static
    {
        return $this->state(fn (): array => ['status' => DiscountStoreStatus::Pending]);
    }

    public function expired(): static
    {
        return $this->state(fn (): array => ['status' => DiscountStoreStatus::Expired]);
    }

    public function ofTypeOnline(): static
    {
        return $this->state(fn (): array => [
            'type' => DiscountStoreType::Online,
            'address' => fake()->url(),
        ]);
    }

    public function ofTypeLocal(): static
    {
        return $this->state(fn (): array => [
            'type' => DiscountStoreType::Local,
        ]);
    }
}
