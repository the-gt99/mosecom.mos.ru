<?php


namespace App\Services\Mosecom;


use ArrayIterator;
use CachingIterator;

use App\Models\Stations;
use App\Models\TypeOfIndication;
use App\Models\Records;

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
            ->get()
        ;

        return $tmp;
    }

    public function save($stations, $lang = "ru")
    {
        foreach ($stations as $stationName => $stationInf)
        {
            //Создаем тип станции если еще не создан
            $station = Stations::firstOrCreate(
                [
                    'name' => $stationName,
                    'address' => $this->mosecomParser->getUrl($lang)
                ]
            );

            foreach ($stationInf as $indicationName => $indicationInf)
            {
                //Создаем тип измерения если еще не создан
                $typeOfIndication = TypeOfIndication::firstOrCreate(
                    ['name' => $indicationName]
                );

                Records::firstOrCreate(
                    [
                        'station_id' => $station->id,
                        'indication_id' => $typeOfIndication->id,
                        'proportion' => $indicationInf['proportion']['value'],
                        'measurement_at' => date("Y-m-d H:i:s",$indicationInf['proportion']['time']),
                        'unit' => $indicationInf['unit']['value']
                    ]
                );
            }
        }
    }
}
