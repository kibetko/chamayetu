<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContributionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'group_id' => Group::inRandomOrder()->first()?->id,

            'user_id' => User::inRandomOrder()->first()?->id,

            'amount' => fake()->numberBetween(1000, 10000),

            'month' => now()->month,

            'year' => now()->year,

            'status' => 'paid',

            'paid_at' => now(),
        ];
    }
}