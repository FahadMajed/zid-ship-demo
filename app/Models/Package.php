<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = [
        'height',
        'width',
        'length',
        'weight',
        'description',
    ];

    public function shipment()
    {
        return $this->hasOne(Shipment::class);
    }
}
