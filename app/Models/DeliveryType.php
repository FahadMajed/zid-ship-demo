<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryType extends Model
{

    use HasFactory;
    protected $fillable = [
        'name',
    ];

    public function pricings()
    {
        return $this->hasMany(Pricing::class);
    }

    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }
}
