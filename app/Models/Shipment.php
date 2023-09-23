<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    protected $fillable = [
        'courier_id',
        'courier_route_id',
        'delivery_type_id',
        'waybill_url',
        'tracking_number',
        'status',
        'timestamp',
        'retailer_id',
        'package_id',
        'customer_phone',
        'customer_city',
        'customer_email',
        'customer_address',
        'price',
    ];

    public function courier()
    {
        return $this->belongsTo(Courier::class);
    }

    public function courierRoute()
    {
        return $this->belongsTo(CourierRoute::class);
    }

    public function deliveryType()
    {
        return $this->belongsTo(DeliveryType::class);
    }

    public function retailer()
    {
        return $this->belongsTo(Retailer::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
