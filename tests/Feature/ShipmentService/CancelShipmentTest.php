<?php

namespace Tests\Feature\ShipmentService;

use App\Models\CourierRoute;
use App\Models\RetailerCourierCredentials;

use App\Models\Courier;
use App\Models\DeliveryType;
use App\Models\Package;
use App\Models\Retailer;
use App\Models\Pricing;
use App\Models\Shipment;
use App\Services\Couriers\Factory\CourierFactory;
use App\Services\Shipments\ShipmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use App\Repositories\CouriersRepository;
use App\Repositories\RetailersRepository;
use App\Repositories\ShipmentsRepository;
use App\Repositories\PackagesRepository;
use App\Repositories\PricingsRepository;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class CancelShipmentTest extends TestCase
{
    use RefreshDatabase;
    protected ShipmentService $shipmentService;
    protected function setUp(): void
    {
        parent::setUp();
        $this->shipmentService = app(ShipmentService::class);
        Queue::fake();
    }

    /** @test */

    public function it_can_cancel_a_shipment_with_a_supported_courier()
    {
        // Arrange
        $courier = Courier::factory()->create(['name' => 'MockCourier', 'supports_cancellation' => true]);
        $retailerName = 'Test Retailer';

        $retailer = Retailer::factory()->create(['city' => "riyadh", 'name' => $retailerName]);

        RetailerCourierCredentials::factory()->create([
            'retailer_id' => $retailer->id,
            'courier_id' => $courier->id
        ]);

        CourierRoute::factory()->create([
            'courier_id' => $courier->id,
            'origin' => $retailer->city,
            'destination' => $retailer->city
        ]);

        DeliveryType::factory()->create([
            'name' => "Prime",
        ]);

        Pricing::factory()->create([
            'courier_route_id' => 1,
            'delivery_type_id' => 1,
            'price' => 25
        ]);

        Package::factory()->create([]);

        $shipment = Shipment::factory()->create();

        $mockCourier = $this->createMock(\App\Services\Couriers\Contracts\Courier::class);
        $mockCourier->method('cancelShipment')->willReturn(['cancelled' => true]);

        $mockCourierFactory = $this->createMock(CourierFactory::class);
        $mockCourierFactory->method('create')->willReturn($mockCourier);

        $this->shipmentService = new ShipmentService(
            app(ShipmentsRepository::class),
            app(RetailersRepository::class),
            app(PackagesRepository::class),
            app(CouriersRepository::class),
            app(PricingsRepository::class),
            $mockCourierFactory  // Inject the mocked CourierFactory
        );

        // Act
        $this->shipmentService->cancelShipment($shipment->id);

        // Assert
        $cancelledShipment = Shipment::find($shipment->id);
        $this->assertEquals('Cancelled', $cancelledShipment->status);
    }

    public function it_cannot_cancel_a_shipment_with_a_courier_that_does_not_support_cancellations()
    {
        // Arrange
        $courier = Courier::factory()->create(['name' => 'NoCancelCourier', 'supports_cancellation' => false]);
        $retailerName = 'Test Retailer';

        $retailer = Retailer::factory()->create(['city' => "riyadh", 'name' => $retailerName]);

        RetailerCourierCredentials::factory()->create([
            'retailer_id' => $retailer->id,
            'courier_id' => $courier->id
        ]);

        CourierRoute::factory()->create([
            'courier_id' => $courier->id,
            'origin' => $retailer->city,
            'destination' => $retailer->city
        ]);

        DeliveryType::factory()->create(['name' => "Prime"]);
        Pricing::factory()->create(['courier_route_id' => 1, 'delivery_type_id' => 1, 'price' => 25]);
        Package::factory()->create([]);
        $shipment = Shipment::factory()->create();

        // Assert
        $this->expectException(CourierDisallowedCancellation::class);

        // Act
        $this->shipmentService->cancelShipment($shipment->id);
    }
}
