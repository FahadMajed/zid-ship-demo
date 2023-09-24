<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Shipment>
 */
class ShipmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'courier_id' => 1,
            'courier_route_id' => 1,
            'delivery_type_id' => 1,
            'status' => 'Pending',
            'price' => 1,
            'package_id' => 1,
            'retailer_id' => 1,
            'order_id' => 1,
            'customer_phone' => fake()->unique()->name(),
            'customer_name' => fake()->unique()->name(),
            'customer_city' => fake()->unique()->name(),
            'customer_email' => fake()->unique()->name(),
            'customer_address' => fake()->unique()->name(),
        ];
    }
}
