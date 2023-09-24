<?php

namespace App\Repositories;

use App\Models\DeliveryType;
use App\Models\Pricing;

class PricingsRepository
{
    public function getPriceForRoute($routeId, $deliveryType)
    {
        $deliveryType = DeliveryType::where('name', $deliveryType)->first();

        $price = Pricing::where('courier_route_id', $routeId)
            ->where('delivery_type_id', $deliveryType->id)
            ->first();

        return [
            'price' => $price->price,
            'delivery_type_id' => $deliveryType->id,
        ];
    }
}
