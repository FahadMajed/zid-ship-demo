<?php

return [
    'fedex' => [
        'base_url' => env('FEDEX_BASE_URL', 'https://api.fedex.com/'),
        'status_path' => 'output.shipmentStatus',
        'status_mappings' => [
            'picked up' => 'In Transit',
            'on truck for delivery' => 'Out for Delivery',
            'shipped' => 'Shipped',
            'delivered' => 'Delivered',
        ],
        'delivery_type_mappings' => [
            'PRIORITY_OVERNIGHT' => "Prime",
            'STANDARD_OVERNIGHT' => "Fast",
            'FEDEX_2_DAY' => "Usual"
        ]
    ],
    // ... mappings for other couriers

];
