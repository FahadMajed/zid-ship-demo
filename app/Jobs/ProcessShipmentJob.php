<?php

namespace App\Jobs;

use App\Models\RetailerCourierCredentials;
use App\Models\Shipment;
use App\Repositories\CouriersRepository;
use App\Repositories\ShipmentsRepository;
use App\Services\Couriers\Factory\CourierFactory;
use CreateShipmentDto;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessShipmentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $tries = 3;
    protected Shipment $shipment;
    protected RetailerCourierCredentials $retailerCourierCredentials;

    public function __construct($shipment,  $retailerCourierCredentials)
    {
        $this->shipment = $shipment;
        $this->retailerCourierCredentials = $retailerCourierCredentials;
    }

    public function handle(ShipmentsRepository $shipmentsRepository)
    {

        $courier = CourierFactory::create($this->shipment->courier->name);

        $shipmentDto = new CreateShipmentDto($this->shipment, $this->retailerCourierCredentials);

        $courierResponse = $courier->createShipment($shipmentDto);

        $shipmentsRepository->confirm(
            $this->shipment,
            $courierResponse['tracking_number'],
            $courierResponse['waybill_url'],
        );
    }

    public function failed(Exception $exception, CouriersRepository $couriersRepository, ShipmentsRepository $shipmentsRepository)
    {
        // Handle the job failure, add logs and ...
        $couriersRepository->decrementUsageFor($this->shipment->courier);
        $shipmentsRepository->markAsFailed($this->shipment);
    }
}
