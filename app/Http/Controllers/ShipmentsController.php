<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShipmentRequest;


use App\Services\Shipments\ShipmentService;
use Illuminate\Http\Response;

class ShipmentsController extends Controller
{
    protected $shipmentService;

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
}
