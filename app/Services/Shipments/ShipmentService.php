<?php

namespace App\Services\Shipments;

use App\Jobs\ProcessShipmentJob;
use App\Repositories\ShipmentsRepository;
use App\Repositories\RetailersRepository;
use App\Repositories\PackagesRepository;

class ShipmentService
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

    public function createBulkShipment(array $shipmentsData, string $retailerName): array
    {
        $shipments = [];

        foreach ($shipmentsData as $shipmentData) {
            $package = $this->packageRepository->create($shipmentData['package']);
            $retailer = $this->retailersRepository->findByName($retailerName);
            $shipment = $this->shipmentsRepository->createPendingShipment($shipmentData, $package->id, $retailer);
            $retailerCredentials = $this->retailersRepository->getCredentialsForCourier($retailer->id, $shipment->courier_id);
            ProcessShipmentJob::dispatch($shipment, $retailerCredentials);

            $shipments[] = [
                'id' => $shipment->id,
                'price' => $shipment->price
            ];
        }

        return $shipments;
    }
}
