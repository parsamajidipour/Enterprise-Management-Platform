<?php

namespace Database\Factories;

use App\Models\Asset;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkOrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'asset_id'   => Asset::factory(),
            'created_by' => User::factory(),
            'title'      => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'status'     => 'pending_dispatch',
            'priority'   => fake()->randomElement(['low', 'medium', 'high']),
        ];
    }

    public function completed(): static
    {
        return $this->state(['status' => 'completed']);
    }

    public function cancelled(): static
    {
        return $this->state(['status' => 'cancelled']);
    }

    public function syncedToCmms(): static
    {
        return $this->state(['status' => 'synced_to_cmms']);
    }
}
