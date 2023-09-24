<?php

namespace App\Repositories;

use App\Models\Shipment;
use App\Models\Courier;
use App\Models\DeliveryType;
use App\Models\Pricing;
use App\Models\Retailer;

class ShipmentsRepository
{
    public function createPendingShipment($customer, $packageId, $retailerId, $courierId, $deliveryTypeId, $routeId, $price): Shipment
    {
        $shipment = Shipment::create([
            'courier_id' => $courierId,
            'courier_route_id' => $routeId,
            'delivery_type_id' => $deliveryTypeId,
            'status' => 'Pending',
            'price' => $price,
            'package_id' => $packageId,
            'retailer_id' => $retailerId,
            'customer_phone' => $customer['phone'],
            'customer_name' => $customer['name'],
            'customer_city' => $customer['city'],
            'customer_email' => $customer['email'],
            'customer_address' => $customer['address'],
        ]);

        $shipment->load('courier', 'courierRoute', 'deliveryType', 'retailer', 'package');

        return $shipment;
    }

    public function confirm($shipment, $trackingNumber, $waybillUrl)
    {
        $shipment->status = 'Confirmed';
        $shipment->tracking_number = $trackingNumber;
        $shipment->waybill_url = $waybillUrl;

        $shipment->save();
    }

    public function getShipment($id)
    {
        $shipment = Shipment::findOrFail($id);

        return $shipment;
    }

    public function getWaybill($id)
    {
        $shipment = $this->getShipment($id);
        return $shipment->waybill_url;
    }

    public function markAsFailed(Shipment $shipment)
    {
        $shipment->status = 'Failed';
        $shipment->save();
    }
}
