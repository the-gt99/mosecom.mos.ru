<?php

namespace App\Repositories;

use App\Models\Stations;

class StationsRepositor
{
    /**
     * @param User $user
     * @param DefiningPointImage $definingPointImage
     * @return Builder
     */
    public function getStationsByCoords($lat, $lon)
    {

        // return Stations::query()->distance('location')
    }
}
