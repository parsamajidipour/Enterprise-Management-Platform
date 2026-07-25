<?php

namespace Database\Factories;

use App\Models\AssetCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssetFactory extends Factory
{
    public function definition(): array
    {
        return [
            'asset_category_id' => AssetCategory::factory(),
            'name'              => fake()->words(3, true),
            'code'              => fake()->unique()->bothify('ASSET-####'),
            'description'       => fake()->sentence(),
            'location'          => fake()->city(),
            'status'            => 'active',
        ];
    }
}
