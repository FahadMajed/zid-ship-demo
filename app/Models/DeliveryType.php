<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryType extends Model
{
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
