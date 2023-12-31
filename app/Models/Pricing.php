<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pricing extends Model
{

    use HasFactory;
    protected $fillable = [
        'courier_route_id',
        'delivery_type_id',
        'price',
    ];

    public function courierRoute()
    {
        return $this->belongsTo(CourierRoute::class);
    }

    public function deliveryType()
    {
        return $this->belongsTo(DeliveryType::class);
    }
}
