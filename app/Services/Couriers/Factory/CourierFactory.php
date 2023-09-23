<?php

namespace App\Services\Couriers\Factory;

use App\Services\Couriers\Integrations\Aramex;
use App\Services\Couriers\Integrations\FedEx;

class CourierFactory
{
    public static function create($courierName)
    {
        switch ($courierName) {
            case 'aramex':
                return new Aramex(config('couriers.aramex.base_url'));
            case 'fedex':
                return new FedEx(config('couriers.fedex.base_url'));

            default:
                throw new \Exception("Courier not supported");
        }
    }
}
