<?php

namespace App\Http\Controllers;

use App\Services\Mosecom\MosecomParser;
use ArrayIterator;
use CachingIterator;
use Illuminate\Http\Request;

class ParserController extends Controller
{
    private $mosecomParser;

    public function __construct(MosecomParser $mosecomParser)
    {
        $this->mosecomParser = $mosecomParser;
    }

    /**
     * @return array
     */
    public function parse()
    {
//        $response = [];
//
//        $stations = new CachingIterator(new ArrayIterator($this->mosecomParser->getStations()));
//
//        foreach ($stations as $stationName)
//        {
//            $isClose = !$stations->hasNext();
//            $response[$stationName] = $this->mosecomParser->getStationInfoByName($stationName, $isClose);
//        }
//        return $response;
    }
}
