<?php

namespace App\Repositories;

use App\Models\Package;

class PackagesRepository
{
    public function create($packageData): Package
    {

        return Package::create([
            'height' => $packageData['height'],
            'width' => $packageData['width'],
            'length' => $packageData['length'],
            'weight' => $packageData['weight'],
            'description' => $packageData['description'] ?? null
        ]);
    }
}
