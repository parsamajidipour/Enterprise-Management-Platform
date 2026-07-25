<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TechnicianProfileFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'       => User::factory(),
            'employee_code' => fake()->unique()->bothify('TECH-####'),
            'skills'        => ['electrical'],
            'phone'         => fake()->e164PhoneNumber(),
            'default_area'  => fake()->city(),
            'is_active'     => true,
        ];
    }
}
