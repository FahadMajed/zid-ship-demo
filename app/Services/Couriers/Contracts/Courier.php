<?php

namespace App\Services\Couriers\Contracts;


use CreateShipmentDto;

abstract class Courier
{
    protected $baseUrl;
    protected $headers;

    public abstract function createShipment(CreateShipmentDto $dto);
    public abstract function cancelShipment($dto);
    public abstract function trackShipment($dto);
}
