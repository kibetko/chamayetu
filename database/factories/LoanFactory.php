<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoanFactory extends Factory
{
    public function definition(): array
    {
        return [
            'group_id' => Group::inRandomOrder()->first()?->id,

            'user_id' => User::inRandomOrder()->first()?->id,

            'amount' => fake()->numberBetween(5000, 50000),

            'interest_rate' => 10,

            'duration_days' => 90,

            'reason' => fake()->sentence(),

            'status' => 'approved',
        ];
    }
}