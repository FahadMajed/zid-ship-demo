<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RetailerCourierCredentialsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('retailer_courier_credentials')->insert([
            'retailer_id' => 1,
            'courier_id' => 1,
            'api_key' => fake()->uuid,
            'account_id' => fake()->bankAccountNumber,
        ]);
    }
}
