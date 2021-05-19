<?php

namespace App\Http\Controllers;

use App\Http\Resources\StationResource;
use App\Repositories\StationsRepository;
use App\Services\AirCms\AirCmsService;
use App\Services\GeographyHelper;
use Illuminate\Http\Request;

class AirConroller extends Controller
{
    public function getCurrent(Request $request)
    {
        $lat = $request->get('lat') ? (float)$request->get('lat') : 55.750143;
        $lon = $request->get('lon') ? (float)$request->get('lon') : 37.620066;

        /** @var AirCmsService */
        $airCmsSercvice = app(AirCmsService::class);
        $windDirection = $airCmsSercvice->getWindDirectuion($lat, $lon);
        list($minCondition, $maxCondition) = GeographyHelper::getAngleConditions($windDirection);
        /** @var StationsRepository */
        $stationsRep = app(StationsRepository::class);
        $stations = $stationsRep->getValidStations($lat, $lon, $minCondition, $maxCondition);

        return StationResource::collection($stations);
    }
}
