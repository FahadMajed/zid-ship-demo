<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShipmentRequest;
use App\Jobs\ProcessShipmentJob;
use App\Repositories\PackagesRepository;
use App\Repositories\RetailersRepository;
use App\Repositories\ShipmentsRepository;
use Illuminate\Http\Response;



class ShipmentsController extends Controller
{
    protected $shipmentsRepository;
    protected $retailersRepository;
    protected $packageRepository;


    public function __construct(
        ShipmentsRepository $shipmentsRepository,
        RetailersRepository $retailersRepository,
        PackagesRepository $packageRepository
    ) {
        $this->shipmentsRepository = $shipmentsRepository;
        $this->retailersRepository = $retailersRepository;
        $this->packageRepository = $packageRepository;
    }


    public function createBulkShipment(ShipmentRequest $request)
    {
        $shipmentsData = $request->all();

        $response = [
            'shipments' => []
        ];

        foreach ($shipmentsData['shipments'] as $shipmentData) {

            $package = $this->packageRepository->create($shipmentData['package']);

            $retailer = $this->retailersRepository->findByName($request['retailer_name']);

            $shipment = $this->shipmentsRepository->createPendingShipment($shipmentData, $request, $package->id, $retailer->id,);

            $retailerCredentials = $this->retailersRepository->getCredentialsForCourier($retailer->id, $shipment->courier_id);

            ProcessShipmentJob::dispatch($shipment, $retailerCredentials);

            $response['shipments'][] = [
                'id' => $shipment->id,
                'price' => $shipment->price
            ];
        }

        return response()->json($response, Response::HTTP_ACCEPTED);
    }
}
