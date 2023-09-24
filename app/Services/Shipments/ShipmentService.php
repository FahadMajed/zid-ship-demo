<?php

namespace App\Services\Shipments;

use App\Exceptions\CourierDisallowedCancellation;
use App\Exceptions\NoAvailableCourierException;
use App\Exceptions\RetailerNotFoundException;
use App\Jobs\ProcessShipmentJob;
use App\Repositories\CouriersRepository;
use App\Repositories\ShipmentsRepository;
use App\Repositories\RetailersRepository;
use App\Repositories\PackagesRepository;
use App\Repositories\PricingsRepository;
use App\Services\Couriers\Factory\CourierFactory;
use GuzzleHttp\Promise\CancellationException;
use Illuminate\Support\Facades\Log;

class ShipmentService
{
    protected ShipmentsRepository $shipmentsRepository;
    protected RetailersRepository $retailersRepository;
    protected PackagesRepository $packageRepository;
    protected CouriersRepository $couriersRepository;
    protected PricingsRepository $pricingsRepository;

    protected CourierFactory $courierFactory;

    public function __construct(
        ShipmentsRepository $shipmentsRepository,
        RetailersRepository $retailersRepository,
        PackagesRepository $packageRepository,
        CouriersRepository $couriersRepository,
        PricingsRepository $pricingsRepository,
        CourierFactory $courierFactory,
    ) {
        $this->shipmentsRepository = $shipmentsRepository;
        $this->retailersRepository = $retailersRepository;
        $this->packageRepository = $packageRepository;
        $this->pricingsRepository = $pricingsRepository;
        $this->couriersRepository = $couriersRepository;
        $this->courierFactory = $courierFactory;
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

                $courierResult = $this->couriersRepository->getFirstAvailableCourierWithRoute($retailer->city, $customer['city']);

                $priceResult = $this->pricingsRepository->getPriceForRoute($courierResult['route_id'], $shipmentData['delivery_type']);

                $shipment = $this->shipmentsRepository->createPendingShipment($customer, $package->id, $retailer->id, $courierResult['courier']->id, $courierResult['route_id'], $priceResult['delivery_type_id'], $priceResult['price'], $shipmentData['order_id']);

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

    public function  updateShipmentStatus($shipmentId, $updatedStatus, $orderId): void
    {
        //TODO PUBLISH THE EVENT USING A MESSAGE BROKER USING THE ORDER ID AND THE STATUS

        $this->shipmentsRepository->updateShipmentStatus($shipmentId, $updatedStatus);
    }

    public function getStatusFor($shipmentId)
    {
        return $this->shipmentsRepository->getStatus($shipmentId);
    }

    public function getShipment($shipmentId)
    {
        return $this->shipmentsRepository->getShipment($shipmentId);
    }

    public function cancelShipment($shipmentId)
    {
        $shipment = $this->shipmentsRepository->getShipment($shipmentId);

        $courierSupportsCancellations = $this->couriersRepository->courierSupportsCancellations($shipment->courier_id);

        if ($courierSupportsCancellations == false) {
            throw new CourierDisallowedCancellation();
        }

        $courier = $this->courierFactory->create($shipment->courier->name);

        $result =  $courier->cancelShipment($shipment->tracking_number);

        if ($result['cancelled'] == true) {
            //TODO PUBLISH THE EVENT USING A MESSAGE BROKER USING THE ORDER ID AND THE STATUS
            $this->shipmentsRepository->updateShipmentStatus($shipmentId, 'Cancelled');
        } else {
            throw new CancellationException($result['message']);
        }
    }
}
