<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [

        '/shipments/bulk',
        '/shipments/{shipment_id}/events',
        '/shipments/{shipment_id}/track',
        '/shipments/{shipment_id}',

    ];
}
