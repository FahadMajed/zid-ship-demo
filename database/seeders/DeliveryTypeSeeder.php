<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DeliveryTypeSeeder extends Seeder
{
    public function run()
    {
        DB::table('delivery_types')->insert([
            ['name' => 'Prime'],
            ['name' => 'Fast'],
            ['name' => 'Usual'],
        ]);
    }
}
