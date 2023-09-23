<?php

namespace App\Repositories;

use App\Models\Shipment;
use App\Models\Courier;
use App\Models\DeliveryType;
use App\Models\Pricing;


class ShipmentsRepository
{
    public function createPendingShipment($data, $request, $packageId, $retailerId): Shipment
    {

        $courier = Courier::whereHas('courier_routes', function ($query) use ($data) {
            $query->where('origin', $data['origin'])
                ->where('destination', $data['destination']);
        })->whereColumn('max_capacity', '>', 'current_usage')
            ->first();

        $deliveryType = DeliveryType::where('delivery_type', $data['delivery_type']);

        $price = Pricing::where('courier_route_id', $courier->id)
            ->where('delivery_type_id', $deliveryType->id)
            ->first();

        $courier->increment('current_usage');

        $courier->save();

        $shipment = Shipment::create([
            'courier_id' => $courier->id,
            'courier_route_id' => $courier->id,
            'delivery_type_id' => $data['delivery_type_id'],
            'status' => 'Pending',
            'price' => $price->price,
            'package_id' => $packageId,
            'retailer_id' => $retailerId
        ]);

        $shipment->load('courier', 'courier_route', 'delivery_type', 'retailer', 'package');

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
