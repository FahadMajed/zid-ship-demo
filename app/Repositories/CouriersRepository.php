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

    public function getFirstAvailableCourierWithRoute($origin, $destination): array
    {

        $courier = Courier::whereHas('routes', function ($query) use ($origin, $destination) {
            $query->where('origin', $origin)
                ->where('destination', $destination);
        })->whereColumn('max_capacity', '>', 'current_usage')
            ->first();

        $route = $courier->routes->first();

        return [
            'courier' => $courier,
            'route_id' => $route
        ];
    }

    public function incrementUsageFor(Courier $courier): void
    {
        $courier->increment('current_usage');

        $courier->save();
    }

    public function decrementUsageFor(Courier $courier)
    {
        $courier->decrement('current_usage');
        $courier->save();
    }
}
