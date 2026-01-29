<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = \App\Models\User::all();

        for($i = 0; $i < 200; $i++) {
            $user = $users->random();

            \App\Models\Event::create([
                'user_id'    => $user->id,
                'name'       => fake()->sentence(3),
                'start_time' => fake()->dateTimeBetween('now', '+1 month'),
                'end_time'   => fake()->dateTimeBetween('+1 month', '+2 months'),
            ]);
        }
    }
}
