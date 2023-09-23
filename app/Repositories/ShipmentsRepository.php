<?php

namespace App\Repositories;

use App\Models\Shipment;
use App\Models\Courier;
use App\Models\DeliveryType;
use App\Models\Pricing;
use App\Models\Retailer;

class ShipmentsRepository
{
    public function createPendingShipment($shipmentData, $packageId, Retailer $retailer): Shipment
    {
        $customer = $shipmentData['customer'];

        $courier = Courier::whereHas('routes', function ($query) use ($customer, $retailer) {
            $query->where('origin', $retailer->city)
                ->where('destination', $customer['city']);
        })->whereColumn('max_capacity', '>', 'current_usage')
            ->first();

        $courierRoute = $courier->routes()->where('origin', $retailer->city)
            ->where('destination', $customer['city'])
            ->first();

        $deliveryType = DeliveryType::where('name', $shipmentData['delivery_type'])->first();



        $price = Pricing::where('courier_route_id', $courierRoute->id)
            ->where('delivery_type_id', $deliveryType->id)
            ->first();

        $courier->increment('current_usage');

        $courier->save();

        $shipment = Shipment::create([
            'courier_id' => $courier->id,
            'courier_route_id' => $courierRoute->id,
            'delivery_type_id' => $deliveryType->id,
            'status' => 'Pending',
            'price' => $price->price,
            'package_id' => $packageId,
            'retailer_id' => $retailer->id,
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
}
