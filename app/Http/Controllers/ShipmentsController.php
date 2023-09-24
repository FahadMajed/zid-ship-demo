<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShipmentEventRequest;
use App\Http\Requests\ShipmentRequest;


use App\Services\Shipments\ShipmentService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class ShipmentsController extends Controller
{
    protected ShipmentService $shipmentService;

    public function __construct(ShipmentService $shipmentService)
    {
        $this->shipmentService = $shipmentService;
    }

    public function createBulkShipment(ShipmentRequest $request)
    {

        $shipmentsData = $request->all()['shipments'];
        $retailerName = $request['retailer_name'];

        $results = $this->shipmentService->createBulkShipment($shipmentsData, $retailerName);

        return response()->json(['shipments' => $results], Response::HTTP_ACCEPTED);
    }


    /**
     * Handles the events coming through the webhook of the couriers, 
     * and updates the shipment status synchronously
     */
    public function handleShipmentEvents(ShipmentEventRequest $request)
    {

        $shipmentId = $request->route('shipment_id');
        $orderId = $request->route('order_id');

        $unifiedStatus = $request->attributes->get('unified_status');

        $this->shipmentService->updateShipmentStatus($shipmentId, $unifiedStatus, $orderId);



        return response()->json(['status' => 'success'], Response::HTTP_OK);
    }

    public function getShipmentStatus(Request $request)
    {

        $shipmentId = $request->route('shipment_id');

        $status = $this->shipmentService->getStatusFor($shipmentId);

        return response()->json(['status' => $status], Response::HTTP_OK);
    }

    public function getShipment(Request $request)
    {

        $shipmentId = $request->route('shipment_id');

        $shipment = $this->shipmentService->getShipment($shipmentId,);

        return response()->json(['waybill_url' => $shipment->waybill_url, 'label_url' => $shipment->label_url], Response::HTTP_OK);
    }
}
