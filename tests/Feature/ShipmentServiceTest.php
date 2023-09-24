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
                    'name' => "Phahad",
                    'phone' => '599333983',
                    'city' => "riyadh",
                    'address' => "malqa",
                    'email' => "Phahad@majed.com",
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
        foreach ($createdShipments as $index => $shipmentWrapper) {
            $shipmentData = $shipmentWrapper['data'];

            $this->assertDatabaseHas('shipments', [
                'id' => $shipmentData['shipment_id'],
                'courier_id' => $courier->id,
                'price' => 25
            ]);
        }
    }

    /** @test */
    public function it_can_create_multiple_shipments()
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
            // First shipment data
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
            // Second shipment data
            [
                'customer' => [
                    'name' => "Ali",
                    'phone' => '599333984',
                    'city' => "riyadh",
                    'address' => "sulimania",
                    'email' => "Ali@majed.com",
                ],
                'package' => [
                    'height' => 15,
                    'width' => 15,
                    'length' => 15,
                    'weight' => 7,
                    'description' => 'Another test package'
                ],
                'delivery_type' => 'Prime'
            ],
        ];

        // Act
        $createdShipments = $this->shipmentService->createBulkShipment($shipmentsData, $retailerName);

        // Assert
        $this->assertCount(count($shipmentsData), $createdShipments);
        foreach ($createdShipments as $index => $createdShipment) {
            $shipmentData = $shipmentsData[$index];
            $createdData = $createdShipment['data'];

            // Check if the shipment is saved in the database
            $this->assertDatabaseHas('shipments', [
                'id' => $createdData['shipment_id'],
                'courier_id' => $courier->id,
                'price' => 25
            ]);

            $shipment = Shipment::find($createdData['shipment_id'],);

            // Check if the customer details match
            $this->assertEquals($shipmentData['customer']['phone'], $shipment->customer_phone);
            $this->assertEquals($shipmentData['customer']['city'], $shipment->customer_city);
            $this->assertEquals($shipmentData['customer']['address'], $shipment->customer_address);
            $this->assertEquals($shipmentData['customer']['email'], $shipment->customer_email);

            // Fetch the package associated with the shipment
            $package = $shipment->package;

            // Check if the package details match
            $this->assertEquals($shipmentData['package']['height'], $package->height);
            $this->assertEquals($shipmentData['package']['width'], $package->width);
            $this->assertEquals($shipmentData['package']['length'], $package->length);
            $this->assertEquals($shipmentData['package']['weight'], $package->weight);
            $this->assertEquals($shipmentData['package']['description'], $package->description);

            // Check if the delivery type matches
            $deliveryType = $shipment->deliveryType;
            $this->assertEquals($shipmentData['delivery_type'], $deliveryType->name);

            // Check if the shipment status is "Pending"
            $this->assertEquals('Pending', $shipment->status);
        }
    }

    /** @test */
    public function it_cannot_create_a_shipment_without_an_available_courier()
    {
        // Arrange
        $retailerName = 'Test Retailer';

        Retailer::factory()->create(['city' => "riyadh", 'name' => $retailerName]);

        // Note: We're not creating any courier or courier route, so there should be no available courier for the shipment.

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
        $this->assertCount(1, $createdShipments); // We expect one result since we're trying to create one shipment.
        $this->assertNull($createdShipments[0]['data']); // The shipment data should be null since the shipment couldn't be created.
        $this->assertEquals("No available courier for shipment data: " . json_encode($shipmentsData[0]), $createdShipments[0]['error']); // We expect an error message indicating no available courier.
    }

    /** @test */
    public function it_cannot_create_a_shipment_when_courier_capacity_is_exceeded()
    {
        // Arrange
        $retailerName = 'Test Retailer';

        $retailer = Retailer::factory()->create(['city' => "riyadh", 'name' => $retailerName]);

        $courier = Courier::factory()->create(['max_capacity' => 1, 'current_usage' => 1]); // Courier is at full capacity

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
        $this->assertCount(1, $createdShipments); // We expect one result since we're trying to create one shipment.
        $this->assertNull($createdShipments[0]['data']); // The shipment data should be null since the shipment couldn't be created.
        $this->assertEquals("No available courier for shipment data: " . json_encode($shipmentsData[0]), $createdShipments[0]['error']); // We expect an error message indicating no available courier due to capacity being exceeded.
    }

    /** @test */
    public function it_cannot_create_a_shipment_with_missing_retailer()
    {
        // Arrange
        $retailerName = 'Missing Retailer';

        $courier = Courier::factory()->create();

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

        // Assert further that no shipments were created
        $this->assertCount(1, $createdShipments); // We expect one result since we're trying to create one shipment.
        $this->assertNull($createdShipments[0]['data']); // The shipment data should be null since the shipment couldn't be created.
        $this->assertEquals("Retailer not found for shipment data: " . json_encode($shipmentsData[0]), $createdShipments[0]['error']);
    }
}
