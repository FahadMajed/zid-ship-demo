<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RetailerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('retailers')->insert([
            'name' => fake()->unique()->company(),
            'address' => fake()->address,
            'phone' => fake()->phoneNumber,
            'city' => fake()->city,
            'email' => fake()->unique()->safeEmail,
        ]);
    }
}
