<?php

namespace Database\Seeders;


use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PricingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('pricings')->insert([
            'courier_route_id' => 1,
            'delivery_type_id' => 1,
            'price' => fake()->randomFloat(2, 10, 1000),
        ]);
    }
}
