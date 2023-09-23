<?php

namespace App\Services\Couriers\Integrations;

use App\Http\Requests\ShipmentRequest;
use App\Models\Shipment;
use App\Services\Couriers\Contracts\Courier;
use CreateShipmentDto;
use Illuminate\Support\Facades\Http;


class FedEx extends Courier
{

    public function __construct($baseUrl,)
    {
        $this->baseUrl = $baseUrl;
    }
    public function createShipment(\CreateShipmentDto $dto)
    {
        //   SERVICE TYPE:      PRIORITY_OVERNIGHT
        // FedEx Standard Overnight®
        // 	STANDARD_OVERNIGHT
        // FedEx 2Day®
        // 	FEDEX_2_DAY

        //PACKAGE TYPE: YOUR_PACKAGING

        //PICK UP TYPE: REGULAR_STOP

        //PAYMENT TYPE: {
        //    SENDER, payor.responsibleParty.accountNumber
        //  }

        //LABEL: {
        //labelFormatType: "COMMON2D"
        //LABEL STOCK TYPE: PAPER_4x6;
        //IMAGE TYPE: PDF
        //
        //}

        //rateRequestType: {
        //    ["ACCOUNT"]
        //}

        //preferredCurrency: SAR

        // totalPackageCount receipts.length
        //labelResponseOptions: URL_ONLY
        //accountNumber: NUMBER
        //shipAction: CONFIRM

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
                //TODO DELIVERY TYPE MAPPING
                'serviceType' => "STANDARD_OVERNIGHT",
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
        //TODO, DO THE MAPPING
        return $response['output']['completeTrackResults']["trackResults"]["latestStatusDetail"]["description"];
    }

    public function cancelShipment($shipmentRequest)
    {
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
