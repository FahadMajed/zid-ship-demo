<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourierRouteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('courier_routes')->insert([
            'courier_id' => 2, // Assuming you have a courier with ID 1
            'origin' => fake()->city,
            'destination' => fake()->city,
        ]);
    }
}
