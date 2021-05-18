<?php


namespace App\Services\Mosecom;


use ArrayIterator;
use CachingIterator;

use App\Models\Stations;
use App\Models\TypeOfIndication;
use App\Models\Records;
use App\Models\Errors;
use Grimzy\LaravelMysqlSpatial\Types\Point;

class MosecomService
{
    private $mosecomParser;

    public function __construct(MosecomParser $mosecomParser)
    {
        $this->mosecomParser = $mosecomParser;
    }

    /**
     * @param int $inPackCount
     *
     * @return array
     */
    public function getStationsPacks(int $inPackCount = 5)
    {
        $response = [];

        $stations = $this->mosecomParser->getStations();

        $response = array_chunk($stations, $inPackCount);

        return $response;
    }

    /**
     * @param string|null $name
     *
     * @return array
     */
    public function parse(string $name = null): array
    {
        $response = [];

        if ($name) {
            $response[$name] = $this->mosecomParser->getStationInfoByName($name, true);
        } else {
            $stations = new CachingIterator(new ArrayIterator($this->mosecomParser->getStations()));

            foreach ($stations as $stationName)
            {
                $isClose = !$stations->hasNext();
                $response[$stationName] = $this->mosecomParser->getStationInfoByName($stationName, $isClose);
            }
        }
        return $response;
    }

    /**
     * @param array $stationNames
     *
     * @return array
     */
    public function getStationsInfoByNames(array $stationNames = []): array
    {
        $response = [];

        $stationNames = new CachingIterator(new ArrayIterator($stationNames));

        foreach ($stationNames as $stationName)
        {
            $isClose = !$stationNames->hasNext();
            $response[$stationName] = $this->mosecomParser->getStationInfoByName($stationName, $isClose);
        }

        return $response;
    }

    public function getRecordByDate($date)
    {
        $unix = strtotime($date);
        $dateStart = date("Y-m-d 00:00:00", $unix);
        $dateEnd = date("Y-m-d 23:59:59", $unix);

        $tmp = Records::query()
            ->where("measurement_at", ">=", $dateStart)
            ->where("measurement_at", "<=", $dateEnd)
            ->groupBy('type')
            ->get();

        dd($tmp);

        return $tmp;
    }

    public function trySaveStationsInf($stations, $lang = "ru")
    {

        foreach ($stations as $stationName => $stationInf)
        {
            //Создаем тип станции если еще не создан

            $station = Stations::query()->where('type_primaty_key', $stationName)->first();
            if (!$station) {
                $station = new Stations([
                    'type_primaty_key' => $stationName,
                    'address' => $stationInf['address'],
                    'name' => $stationInf['name'],
                    'type' => MosecomAdapter::NAME
                ]);
                $station->point = new Point(0,0);
                $station->save();
            }

            //Если есть ошибки измерений создаем их и записи по ним
            if($stationInf['hasError'])
            {
                foreach ($stationInf['errorInf']['notFoundMeasurementNames'] as $indicationName)
                {
                    //Создаем тип измерения если еще не создан, что старнно
                    $typeOfIndication = TypeOfIndication::firstOrCreate(
                        ['code_name' => $indicationName],
                        ['name' => null]
                    );

                    $error = Errors::firstOrCreate([
                        'message' => $stationInf['errorInf']['errorText']
                    ]);

                    Records::firstOrCreate(
                        [
                            'station_id' => $station->id,
                            'indication_id' => $typeOfIndication->id,
                            'proportion' => null,
                            'measurement_at' => date("Y-m-d H:i:s",time()),
                            'unit' => null,
                            'error_id' => $error->id
                        ]
                    );
                }
            }

            //Если есть не ошибочные измерения то создаем их
            if(isset($stationInf['measurement']))
            {
                foreach ($stationInf['measurement'] as $indicationName => $indicationInf)
                {
                    //Создаем тип измерения если еще не создан
                    $typeOfIndication = TypeOfIndication::firstOrCreate(
                        ['code_name' => $indicationName],
                        ['name' => null]
                    );

                    $unixTimestamp = (int)$indicationInf['proportion']['time'] - 3*60*60;

                    Records::firstOrCreate(
                        [
                            'station_id' => $station->id,
                            'indication_id' => $typeOfIndication->id,
                            'proportion' => $indicationInf['proportion']['value'],
                            'measurement_at' => date("Y-m-d H:i:s", $unixTimestamp),
                            'unit' => $indicationInf['unit']['value']
                        ]
                    );
                }
            }
        }

        return true;
    }
}
