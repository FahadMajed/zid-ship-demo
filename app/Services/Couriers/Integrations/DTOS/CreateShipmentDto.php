<?php

use App\Models\RetailerCourierCredentials;
use App\Models\Shipment;

class CreateShipmentDto
{
    public $deliveryTypeName;
    public $retailer;
    public $package;
    public $customer;

    public function __construct(Shipment $shipment, RetailerCourierCredentials $retailerCourierCredentials)
    {
        $this->deliveryTypeName = $shipment->deliveryType->name ?? null;
        $this->retailer = [
            'phone' => $shipment->retailer->phone ?? null,
            'city' => $shipment->retailer->city ?? null,
            'email' => $shipment->retailer->email ?? null,
            'country_code' => "SA",
            'name' => $shipment->retailer->name,
            'address' => $shipment->retailer->address ?? null,
            'api_key' => $retailerCourierCredentials->api_key,
            'account_number' => $retailerCourierCredentials->account_id,
        ];

        $this->package = [
            'height' => $shipment->package->height ?? null,
            'width' => $shipment->package->width ?? null,
            'length' => $shipment->package->length ?? null,
            'weight' => $shipment->package->weight ?? null,
            'description' => $shipment->package->description ?? null
        ];
        $this->customer = [
            'phone' => $shipment->customer_phone ?? null,
            'city' => $shipment->customer_city ?? null,
            'email' => $shipment->customer_email ?? null,
            'country_code' => "SA",
            'address' => $shipment->customer_address ?? null
        ];
    }
}
