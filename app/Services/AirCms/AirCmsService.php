<?php

namespace App\Services\AirCms;

use App\Models\Records;
use App\Models\Stations;
use App\Models\TypeOfIndication;
use App\Repositories\AirCms\AirCmsRepository;
use App\Services\GeographyHelper;
use App\Services\Mosecom\MosecomAdapter;
use Grimzy\LaravelMysqlSpatial\Types\Point;

class AirCmsService
{
    /** @var AirCmsRepository */
    protected $aircmsRepository;

    public function __construct(AirCmsRepository $airCmsRepository)
    {
        $this->aircmsRepository = $airCmsRepository;
    }

    public function getWindDirectuion($lat, $lon)
    {
        $stations = $this->aircmsRepository->getRecordsByCoords($lat - 0.06, $lon - 0.06, $lat + 0.06, $lon + 0.06);
        $vars = array_count_values(array_column($stations, 'wd'));

        $max = -1;
        foreach ($vars as $key=>$item) {
            if ($max < $item) {
                $max = $item;
                $wind = $key;
            }
        }

        return $wind;
    }

    public function getValidCurrentRecords($lat, $lon)
    {
        $stations = $this->aircmsRepository->getRecordsByCoords($lat - 0.06, $lon - 0.06, $lat + 0.06, $lon + 0.06);
        $validStations = $this->filterStationsData($stations, $lat, $lon);

        return $validStations;
    }

    public function saveStations(array $stationsData = [])
    {
        foreach ($stationsData as $stationData) {
            $station = new Stations([
                'type_primaty_key' => $stationData['did'],
                'type' => MosecomAdapter::NAME
            ]);
            $station->point = new Point($stationData['lat'], $stationData['lon']);
            $station->save();
        }
    }

    public function saveRecords(array $data = [])
    {
        $pm10 = TypeOfIndication::query()->where('code_name', 'sds_p1')->first();
        $pm2_5 = TypeOfIndication::query()->where('code_name', 'sds_p2')->first();
        foreach ($data as $stationData) {
            $station = Stations::query()
                ->where('type_primaty_key', $stationData['did'])
                ->where('type', AirCmsAdapter::NAME)
                ->first();
            if ($station) {
                $station->update(['wind_direction' => $stationData['wd']]);
            } else {
                $station = new Stations([
                    'type_primaty_key' => $stationData['did'],
                    'type' => AirCmsAdapter::NAME
                ]);
                $station->point = new Point($stationData['lat'], $stationData['lon']);
                $station->save();
            }

            if (Records::query()->where('station_id', $station->getKey())->where('measurement_at', date('Y-m-d H:m:s', $stationData['ts']))->first()) {
                continue;
            }

            if ($pm10) {
                $recordsToInsert[] = [
                    'unit' => $stationData['pm10'],
                    'measurement_at' => date('Y-m-d H:m:s', $stationData['ts']),
                    'station_id' => $station->getKey(),
                    'indication_id' => $pm10->getKey()
                ];
            }

            if ($pm2_5) {
                $recordsToInsert[] = [
                    'unit' => $stationData['pm2_5'],
                    'measurement_at' => date('Y-m-d H:m:s', $stationData['ts']),
                    'station_id' => $station->getKey(),
                    'indication_id' => $pm2_5->getKey()
                ];
            }
        }

        isset($recordsToInsert) ? Records::insert($recordsToInsert) : '';
    }

    public function filterStationsData($stations, $lat, $lon, $isAll = false)
    {
        return array_map(function ($station) use ($lat, $lon, $isAll) {
            $stationLon= $station['lon'];
            $stationLat= $station['lat'];
            $degrees = GeographyHelper::getDegrees($stationLon - $lon, $stationLat - $lat);
            $compassAngle = GeographyHelper::getAngleByCompassPoint($station['wd']);

            if (
                $isAll
                && GeographyHelper::getPolar($stationLon - $lon, $stationLat - $lat) === $station['wd']
            ) {
                return $station;
            } elseif (
                GeographyHelper::getPolar($stationLon - $lon, $stationLat - $lat) === $station['wd']
                &&  $degrees < $compassAngle + GeographyHelper::getCompassError()
                &&  $degrees > $compassAngle - GeographyHelper::getCompassError()
            ) {
                return $station;
            }
        }, $stations);
    }
}
