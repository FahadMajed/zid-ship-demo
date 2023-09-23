<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Retailer extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'address',
        'phone',
        'city',
        'email'
    ];

    public function couriers()
    {
        return $this->belongsToMany(Courier::class, 'retailer_courier_credentials')
            ->withPivot('api_key', 'account_id');
    }
}
