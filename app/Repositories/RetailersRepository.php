<?php

namespace App\Repositories;

use App\Models\Retailer;
use App\Models\RetailerCourierCredentials;

class RetailersRepository
{
    public function getCredentialsForCourier($retailerId, $courierId)
    {

        $retailer = Retailer::where('id', $retailerId)->first();

        if (!$retailer) {
            return null;
        }

        return RetailerCourierCredentials::where('retailer_id', $retailer->id)
            ->where('courier_id', $courierId)
            ->first();
    }

    public function findByName($retailerName): Retailer
    {

        $retailer = Retailer::where('name', $retailerName)->first();
        return $retailer;
    }
}
