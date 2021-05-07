<?php


namespace App\Services\Mosecom;


use ArrayIterator;
use CachingIterator;

class MosecomService
{
    private $mosecomParser;

    public function __construct(MosecomParser $mosecomParser)
    {
        $this->mosecomParser = $mosecomParser;
    }

    //Получаем разбитые на пачки станции
    public function getStationsBrokenIntoPacks($count = 5)
    {
        $response = [];

        $stations = $this->mosecomParser->getStations();

        for($i=0; $i<count($stations); $i++)
        {
            $stationsTmp = [];

            for($j=0; $j<$count; $j++)
            {
                array_push($stationsTmp, $stations[$i]);
            }

            array_push($response, $stationsTmp);
        }

        return $response;
    }

    public function parse()
    {
        $response = [];

        $stations = new CachingIterator(new ArrayIterator($this->mosecomParser->getStations()));

        foreach ($stations as $stationName)
        {
            $isClose = !$stations->hasNext();
            $response[$stationName] = $this->mosecomParser->getStationInfoByName($stationName, $isClose);
        }
        return $response;
    }

    //получаем инфу по пачке имен
    public function getStationsInfoByNames($stationNames)
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
}
