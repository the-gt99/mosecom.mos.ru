<?php

namespace App\Repositories;

use App\Models\Records;
use App\Models\Stations;
use DateTime;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class StationsRepository
{
    /**
     * @param User $user
     * @param DefiningPointImage $definingPointImage
     * @return Builder
     */
    public function getStationsByCoords($lat = 55.75, $lon = 37.6167): Collection
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

    public function getNearestStation($lat = 55.75, $lon = 37.6167): ?Stations
    {
        return Stations::orderByDistanceSphere('point', new Point($lat, $lon))->whereNotNull('wind_direction')->first();
    }

    public function getValidStations($lat = 55.75, $lon = 37.6167, $min_condition, $max_condition): Collection
    {
        $subQueryForRecordsMeasurementMax = Records::query()
            ->selectRaw('station_id, max(measurement_at) as date')
            ->groupBy('station_id');

        $query = Stations::query()
            ->distanceSphere('point', new Point($lat, $lon), 10000)
            ->with(['records' => function ($query) use ($subQueryForRecordsMeasurementMax) {
                return $query
                    ->withExpression('max_station', $subQueryForRecordsMeasurementMax)
                    ->where('measurement_at', '>=' , date('Y-m-d H:i:s', strtotime('now -1 hour')))
                    ->join('max_station', 'max_station.station_id' ,'=', 'records.station_id')
                    ;
            }])
            ->whereHas('records' , function ($query) {
                return $query->where('measurement_at', '>=' , date('Y-m-d H:i:s', strtotime('now -1 hour')));
            });
        if ($min_condition != $max_condition) {
            $query
                ->where(\DB::raw("IF(ST_X(`point`) <= $lon,  degrees(ATAN(ST_X(`point`) - $lon, ST_Y(`point`) - $lat)) + 360,  degrees(ATAN(ST_X(`point`) - $lon, ST_Y(`point`) - $lat)))"), '>', $min_condition)
                ->where(\DB::raw("IF(ST_X(`point`) <= $lon,  degrees(ATAN(ST_X(`point`) - $lon, ST_Y(`point`) - $lat)) + 360,  degrees(ATAN(ST_X(`point`) - $lon, ST_Y(`point`) - $lat)))"), '<', $max_condition);
        } else {
            $query->where(\DB::raw("IF(ST_X(`point`) <= $lon,  degrees(ATAN(ST_X(`point`) - $lon, ST_Y(`point`) - $lat)) + 360,  degrees(ATAN(ST_X(`point`) - $lon, ST_Y(`point`) - $lat)))"), '=', $min_condition);
        }

        return $query->get();
    }
}
