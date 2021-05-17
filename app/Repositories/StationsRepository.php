<?php

namespace App\Repositories;

use App\Models\Stations;
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
            ->has('records', '>=', 4)
            ->with(['records' => function ($query) {
                return $query->orderBy('measurement_at', 'DESC')
                    ->groupBy('indication_id')
                    ->groupBy('station_id')
                    ;
            }])
            ->get();
    }
}
