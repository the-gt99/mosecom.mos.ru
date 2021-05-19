<?php

namespace App\Repositories;

use App\Models\Records;
use App\Models\Stations;
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

    public function getValidStations($lat, $lon, $min_condition, $max_condition, string $type = null): Collection
    {
        $query = Stations::query()
            ->distanceSphere('point', new Point($lat, $lon), 10000)
            ->whereHas('records' , function ($query) {
                return $query
                    ->where('measurement_at', '>=' , date('Y-m-d H:i:s', strtotime('now -1 hour')))
                    ->whereNull('error_id');
            });
        if ($type) {
            $query->where('type', $type);
        }

        if ($min_condition != $max_condition) {
            $query
                ->where(\DB::raw("IF(ST_X(`point`) <= $lon,  degrees(ATAN(ST_X(`point`) - $lon, ST_Y(`point`) - $lat)) + 360,  degrees(ATAN(ST_X(`point`) - $lon, ST_Y(`point`) - $lat)))"), '>', $min_condition)
                ->where(\DB::raw("IF(ST_X(`point`) <= $lon,  degrees(ATAN(ST_X(`point`) - $lon, ST_Y(`point`) - $lat)) + 360,  degrees(ATAN(ST_X(`point`) - $lon, ST_Y(`point`) - $lat)))"), '<', $max_condition);
        } else {
            $query->where(\DB::raw("IF(ST_X(`point`) <= $lon,  degrees(ATAN(ST_X(`point`) - $lon, ST_Y(`point`) - $lat)) + 360,  degrees(ATAN(ST_X(`point`) - $lon, ST_Y(`point`) - $lat)))"), '=', $min_condition);
        }

        $statuionsIds = $query->get()->pluck('id')->toArray();

        $recordsModel = new Records();
        $subQueryForRecordsMeasurementMax = Records::query()
            ->selectRaw('station_id, max(measurement_at) as day')
            ->whereNull('error_id')
            ->groupBy('station_id');
        $recordsIds = Records::query()
            ->whereIn($recordsModel->qualifyColumn('station_id'), $statuionsIds)
            ->withExpression('max_station', $subQueryForRecordsMeasurementMax)
            ->join('max_station', 'max_station.station_id' ,'=', $recordsModel->qualifyColumn('station_id'))
            ->where('records.measurement_at', '=', \DB::raw('`max_station`.`day`'))->get()->pluck('id')->toArray();

        $stations = Stations::whereIn('id', $statuionsIds)->with(['records' => function ($query) use ($recordsIds) {
            return $query->whereIn('id', $recordsIds);
        }])->get();

        return $stations;
    }
}
