<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\Contribution;
use App\Models\Loan;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        User::factory(20)->create();

        Group::factory(3)->create();

        $groups = Group::all();
        $users = User::all();

        foreach ($groups as $group) {

            $selectedUsers = $users->random(10);

            foreach ($selectedUsers as $user) {

                GroupMember::create([
                    'group_id' => $group->id,
                    'user_id' => $user->id,
                    'role' => 'member',
                    'status' => 'active',
                    'joined_at' => now(),
                ]);
            }
        }

        Contribution::factory(50)->create();

        Loan::factory(15)->create();
    }
}