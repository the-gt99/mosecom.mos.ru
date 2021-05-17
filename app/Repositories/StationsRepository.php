<?php

namespace App\Repositories;

use App\Models\Stations;
use DateTime;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Database\Eloquent\Builder;


class StationsRepository
{
    /**
     * @param User $user
     * @param DefiningPointImage $definingPointImage
     * @return Builder
     */
    public function getStationsByCoords($lat = 55.75, $lon = 37.6167)
    {

        return Stations::query()
            ->distanceSphere('point', new Point($lat, $lon), 10000)
            ->with(['records' => function ($query) {
                return $query->where('measurement_at', '>=' , date('Y-m-d H:i:s', strtotime('now -1 hour')));
            }])
            ->whereHas('records' , function ($query) {
                return $query->where('measurement_at', '>=' , date('Y-m-d H:i:s', strtotime('now -1 hour')));
            })
            ->get();
    }
}
