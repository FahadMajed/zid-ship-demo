<?php

namespace App\Services\Couriers\Integrations;

use App\Services\Couriers\Contracts\Courier;
use Illuminate\Support\Facades\Config;
use CreateShipmentDto;
use Illuminate\Support\Facades\Http;


class FedEx extends Courier
{

    public function __construct($baseUrl,)
    {
        $this->baseUrl = $baseUrl;
    }
    public function createShipment(CreateShipmentDto $dto)
    {

        $endpoint = $this->baseUrl + 'ship/v1/shipments';

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $dto->retailer['api_key'],
            'Content-Type' => 'application/json',
        ])->post($endpoint, [


            'mergeLabelDocOption' => "LABELS_ONLY",
            'requestedShipment' => [
                'shipAction' => "CONFIRM",
                'totalPackagesCount' => 1,
                'shipper' => [
                    'address' => [
                        'streetLines' => $dto->retailer['address'],
                        'city' => $dto->retailer['city'],
                        'countryCode' => $dto->retailer['country_code']
                    ],
                    'contact' => [
                        'emailAddress' => $dto->retailer['email'],
                        'phoneNumber' => $dto->retailer['phone'],
                        'companyName' => $dto->retailer['name']
                    ],

                ],
                'soldTo' => [
                    'address' => [
                        'streetLines' => $dto->customer['address'],
                        'city' => $dto->customer['city'],
                        'countryCode' => $dto->customer['country_code']
                    ],
                    'contact' => [
                        'emailAddress' => $dto->customer['email'],
                        'phoneNumber' => $dto->customer['phone'],
                        'companyName' => $dto->customer['name']
                    ],
                ],
                'recipients' => [
                    [
                        'address' => [
                            'streetLines' => $dto->customer['address'],
                            'city' => $dto->customer['city'],
                            'countryCode' => $dto->customer['country_code']
                        ],
                        'contact' => [
                            'emailAddress' => $dto->customer['email'],
                            'phoneNumber' => $dto->customer['phone'],
                            'companyName' => $dto->customer['name']
                        ],
                    ]
                ],

                'serviceType' => Config::get("fedex.delivery_type_mappings$dto->deliveryTypeName"),
                'packagingType' => 'FEDEX_PAK',
                'pickupType' => 'REGULAR_STOP',
                'shippingChargesPayment' => [
                    'paymentType' => "SENDER",
                    'payor' => [
                        'responsibleParty' => [
                            'address' => [
                                'streetLines' => $dto->retailer['address'],
                                'city' => $dto->retailer['city'],
                                'countryCode' => $dto->retailer['country_code']
                            ],
                            'contact' => [
                                'emailAddress' => $dto->retailer['email'],
                                'phoneNumber' => $dto->retailer['phone'],
                                'companyName' => $dto->retailer['name']
                            ],
                            'accountNumber' => [
                                'value' => $dto->retailer['account_number']
                            ]
                        ]
                    ]
                ],
                'labelSpecification' => [
                    'labelFormat' => "COMMON2D",
                    'labelOrder' => "SHIPPING_LABEL_FIRST",
                    'labelStockType' => "PAPER_4X6",
                    'imageType' => 'PDF'
                ],
                "rateRequestType" => ['ACCOUNT'],
                'preferredCurrency' => "SAR",
            ]



        ]);


        $shipmentDocs = $response['output']['transactionShipments'][0]["shipmentDocuments"];
        return [
            "tracking_number" => $shipmentDocs['trackingNumber'],
            'waybill_url' => $shipmentDocs['url'],
            'label_url' => $shipmentDocs['url'],
        ];
    }

    public function cancelShipment($trackingNumber)
    {
        $endpoint = $this->baseUrl + 'ship/v1/cancel';
        //SIMILAR TO CREATING (FOR FINDING THE CONFIG)

        return ["cancelled" => true, "message" => 'Success'];
    }
    public function trackShipment($tackingNumber)
    {
        $endpoint = $this->baseUrl + 'track/v1/trackingnumbers';

        $response = Http::withHeaders($this->headers)->post($endpoint, [
            'includeDetailedScans' => false,



            'trackingNumberInfo' => [
                'trackingNumber' => $tackingNumber,
            ],


        ]);
        //TODO, DO THE MAPPING
        return $response['output']['completeTrackResults']["trackResults"]["latestStatusDetail"]["description"];
    }
}
