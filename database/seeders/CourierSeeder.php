<?php

namespace Database\Seeders;


use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

// database/seeds/CourierSeeder.php
class CourierSeeder extends Seeder
{
    public function run()
    {
        DB::table('couriers')->insert([
            'name' => fake()->unique()->company(),
            'max_capacity' => fake()->randomNumber(3),
            'supports_cancellation' => fake()->boolean,
            'current_usage' => fake()->randomNumber(2),
        ]);
    }
}
