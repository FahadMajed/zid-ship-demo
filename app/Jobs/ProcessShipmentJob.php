<?php

namespace App\Jobs;

use App\Models\RetailerCourierCredentials;
use App\Models\Shipment;
use App\Repositories\ShipmentsRepository;
use App\Services\Couriers\Factory\CourierFactory;
use CreateShipmentDto;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessShipmentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Shipment $shipment;
    protected RetailerCourierCredentials $retailerCourierCredentials;
    protected ShipmentsRepository $shipmentsRepository;

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
}
