<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class RetailerCourierCredentials extends Pivot
{

    use HasFactory;
    protected $table = 'retailer_courier_credentials';

    public $incrementing = true;

    protected $fillable = [
        'retailer_id',
        'courier_id',
        'api_key',
        'account_id'
    ];
}
