<?php

namespace Tests\Feature;

use App\Models\CourierRoute;
use App\Models\RetailerCourierCredentials;

use App\Models\Courier;
use App\Models\DeliveryType;
use App\Models\Retailer;
use App\Models\Package;
use App\Models\Pricing;
use App\Models\Shipment;
use App\Models\RetailerCourierCredential;
use App\Services\Shipments\ShipmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ShipmentServiceTest extends TestCase
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
    public function it_can_create_a_shipment()
    {
        // Arrange
        $retailerName = 'Test Retailer';

        $retailer = Retailer::factory()->create(['city' => "riyadh", 'name' => $retailerName]);

        $courier = Courier::factory()->create();

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

        $shipmentsData = [
            [
                'customer' => [
                    'name' => "Fahad",
                    'phone' => '599333983',
                    'city' => "riyadh",
                    'address' => "malqa",
                    'email' => "Fahad@majed.com",
                ],
                'package' => [
                    'height' => 10,
                    'width' => 10,
                    'length' => 10,
                    'weight' => 5,
                    'description' => 'Test package'
                ],
                'delivery_type' => 'Prime'
            ],

        ];

        // Act
        $createdShipments = $this->shipmentService->createBulkShipment($shipmentsData, $retailerName);

        // Assert
        $this->assertCount(count($shipmentsData), $createdShipments);
        foreach ($createdShipments as $index => $createdShipment) {
            $this->assertDatabaseHas('shipments', [
                'id' => $createdShipment['id'],
                'courier_id' => $courier->id,
                'price' => 25
            ]);
        }
    }
}
