<?php

namespace App\Services\Shipments;

use App\Exceptions\NoAvailableCourierException;
use App\Exceptions\RetailerNotFoundException;
use App\Jobs\ProcessShipmentJob;
use App\Repositories\CouriersRepository;
use App\Repositories\ShipmentsRepository;
use App\Repositories\RetailersRepository;
use App\Repositories\PackagesRepository;
use App\Repositories\PricingsRepository;
use Illuminate\Support\Facades\Log;

class ShipmentService
{
    protected ShipmentsRepository $shipmentsRepository;
    protected RetailersRepository $retailersRepository;
    protected PackagesRepository $packageRepository;
    protected CouriersRepository $couriersRepository;
    protected PricingsRepository $pricingsRepository;

    public function __construct(
        ShipmentsRepository $shipmentsRepository,
        RetailersRepository $retailersRepository,
        PackagesRepository $packageRepository,
        CouriersRepository $couriersRepository,
        PricingsRepository $pricingsRepository,
    ) {
        $this->shipmentsRepository = $shipmentsRepository;
        $this->retailersRepository = $retailersRepository;
        $this->packageRepository = $packageRepository;
        $this->pricingsRepository = $pricingsRepository;
        $this->couriersRepository = $couriersRepository;
    }

    public function createBulkShipment(array $shipmentsData, string $retailerName): array
    {
        $shipments = [];

        foreach ($shipmentsData as $shipmentData) {
            $shipment = null;
            $error = null;

            try {
                $customer = $shipmentData['customer'];

                $package = $this->packageRepository->create($shipmentData['package']);

                $retailer = $this->retailersRepository->findByName($retailerName);

                $courierResult = $this->couriersRepository->getFirstAvailableCourierWithRoute($customer['city'], $retailer->city);

                $priceResult = $this->pricingsRepository->getPriceForRoute($courierResult['route_id'], $shipmentData['delivery_type']);

                $shipment = $this->shipmentsRepository->createPendingShipment($customer, $package->id, $retailer->id, $courierResult['courier']->id, $courierResult['route_id'], $priceResult['delivery_type_id'], $priceResult['price'],);

                $retailerCredentials = $this->retailersRepository->getCredentialsForCourier($retailer->id, $shipment->courier_id);

                $this->couriersRepository->incrementUsageFor($courierResult['courier']);

                ProcessShipmentJob::dispatch($shipment, $retailerCredentials);
            } catch (RetailerNotFoundException $e) {
                $error = "Retailer not found for shipment data: " . json_encode($shipmentData);
                Log::warning($error);
            } catch (NoAvailableCourierException $e) {
                $error = "No available courier for shipment data: " . json_encode($shipmentData);
                Log::warning($error);
            }

            $shipments[] = [
                'data' => $shipment != null ? ['shipment_id' => $shipment->id, 'status' => $shipment->status] : null,
                'error' => $error
            ];
        }

        return $shipments;
    }
}
