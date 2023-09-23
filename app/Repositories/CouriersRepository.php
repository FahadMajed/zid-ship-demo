<?php

namespace App\Repositories;

use App\Models\Courier;
use App\Models\Package;

class CouriersRepository
{
    public function resetShipmentsUsage()
    {
        Courier::query()->update(['current_usage' => 0]);
    }
}
