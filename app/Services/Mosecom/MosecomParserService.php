<?php


namespace App\Services\Mosecom;
use App\Libraries\MosecomRepository;

class MosecomParserService
{
    private $curl = null;
    private $domain = "https://mosecom.mos.ru/";
    private $stations = [
        "ru" => "stations/",
        "en" => "measuring-stations/"
    ];

    public function __construct()
    {
        $this->curl = new MosecomRepository();
    }

    public function getStations($isClose = true, $isUseNewUA = false)
    {
        $response = [];

        $html = $this->curl->get($this->domain . $this->stations['ru'], [] , $isUseNewUA, $isClose);

        $isFind = preg_match_all(
            "/<div class=\"row-title\">[\r\n[ ]*]?<a href=\"https:\/\/mosecom.mos.ru\/([-\w]+)\/\">/m",
            $html,
            $matches
        );

        if($isFind)
            $response = $matches[1];

        return $response;
    }

    public function getStationInfoByName($name, $isClose = true, $isUseNewUA = false)
    {
        $html = $this->curl->get($this->domain . $name . "/", [], $isUseNewUA, $isClose);

        $isFind = preg_match(
            "/AirCharts\.init\((.*?), {\"months\"/m",
            $html,
            $matches
        );

        if($isFind)
            $response = $matches;

        return $response;
    }


}
