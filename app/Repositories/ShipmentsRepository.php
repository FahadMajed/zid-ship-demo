<?php

namespace App\Repositories;

use App\Models\Shipment;


class ShipmentsRepository
{
    public function createPendingShipment($customer, $packageId, $retailerId, $courierId, $deliveryTypeId, $routeId, $price, $orderId): Shipment
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
            'order_id' => $orderId
        ]);

        $shipment->load('courier', 'courierRoute', 'deliveryType', 'retailer', 'package');

        return $shipment;
    }

    public function confirm($shipment, $trackingNumber, $waybillUrl, $labelUrl)
    {
        $shipment->status = 'Confirmed';
        $shipment->tracking_number = $trackingNumber;
        $shipment->waybill_url = $waybillUrl;
        $shipment->label_url = $labelUrl;

        $shipment->save();
    }



    public function getWaybill($id)
    {
        $shipment = $this->getShipment($id);

        return $shipment->waybill_url;
    }


    public function getStatus($id)
    {
        $shipment = $this->getShipment($id);

        return $shipment->status;
    }

    public function updateShipmentStatus($shipmentId, $updatedStatus)
    {
        Shipment::query()->where('id', $shipmentId)->update(['status' => $updatedStatus]);
    }



    public function markAsFailed(Shipment $shipment)
    {
        $shipment->status = 'Failed';
        $shipment->save();
    }

    public function getShipment($id)
    {
        $shipment = Shipment::findOrFail($id);

        return $shipment;
    }
}
