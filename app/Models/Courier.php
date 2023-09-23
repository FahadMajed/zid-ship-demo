<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Courier extends Model
{
    protected $fillable = [
        'name', 'max_capacity', 'supports_cancellation', 'current_usage'
    ];

    public function routes()
    {
        return $this->hasMany(CourierRoute::class);
    }

    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }

    public function retailers()
    {
        return $this->belongsToMany(Retailer::class, 'retailer_courier_credentials')
            ->withPivot('api_key', 'account_id');
    }
}
