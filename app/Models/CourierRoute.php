<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourierRoute extends Model
{

    use HasFactory;
    protected $fillable = [
        'courier_id',
        'origin',
        'destination',
    ];

    public function courier()
    {
        return $this->belongsTo(Courier::class);
    }

    public function pricings()
    {
        return $this->hasMany(Pricing::class);
    }

    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }
}
