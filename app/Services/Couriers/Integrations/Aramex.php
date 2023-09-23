<?php

namespace App\Services\Couriers\Integrations;

use App\Services\Couriers\Contracts\Courier;


class Aramex extends Courier
{

    public function __construct($baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }
    public function createShipment($shipmentRequest)
    {
    }
    public function cancelShipment($shipmentRequest)
    {
    }
    public function trackShipment($shipmentRequest)
    {
    }
}
