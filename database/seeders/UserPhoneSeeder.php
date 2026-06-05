<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserPhoneSeeder extends Seeder
{
    public function run(): void
    {
        User::whereNull('phone_no')
            ->get()
            ->each(function ($user) {

                $user->update([
                    'phone_no' => fake()->numerify('07########')
                ]);

            });
    }
}