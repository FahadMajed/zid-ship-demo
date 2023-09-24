<?php

namespace App\Repositories;

use App\Exceptions\RetailerNotFoundException;
use App\Models\Retailer;
use App\Models\RetailerCourierCredentials;

class RetailersRepository
{
    public function getCredentialsForCourier($retailerId, $courierId)
    {

        $retailer = Retailer::where('id', $retailerId)->first();

        if (!$retailer) {
            throw new RetailerNotFoundException();
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
