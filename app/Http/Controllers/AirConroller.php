<?php

namespace App\Http\Controllers;

use App\Http\Resources\StationResource;
use App\Repositories\StationsRepository;

class AirConroller extends Controller
{
    public function test()
    {
        $lat = 55.75;
        $lon = 37.6167;
        $station = app(StationsRepository::class)->getNearestStation();
        $windDirection = $station->wind_direction;
        switch ($windDirection) {
            case 'n':
                $min_condition = 0;
                $max_condition = 0;
                break;
            case 'nw':
                $min_condition = 270;
                $max_condition = 360;
                break;
            case 'w':
                $min_condition = 270;
                $max_condition = 270;
                break;
            case 'sw':
                $min_condition = 180;
                $max_condition = 270;
                break;
            case 's':
                $min_condition = 180;
                $max_condition = 180;
                break;
            case 'se':
                $min_condition = 90;
                $max_condition = 180;
                break;
            case 'e':
                $min_condition = 90;
                $max_condition = 90;
                break;
            case 'ne':
                $min_condition = 0;
                $max_condition = 90;
                break;
        }

        // dd($windDirection, $min_condition, $max_condition);
        /** @var StationsRepository */
        $stationsRep = app(StationsRepository::class);
        $stations = $stationsRep->getValidStations($lat, $lon, $min_condition, $max_condition);

        return StationResource::collection($stations);
    }
}
