<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class GroupFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->company(),

            'unique_code' => strtoupper(fake()->bothify('GRP###')),

           

            'description' => fake()->sentence(),

            'created_by' => User::inRandomOrder()->first()?->id,

            'active' => true,
        ];
    }
}