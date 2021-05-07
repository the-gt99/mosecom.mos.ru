<?php


namespace App\Services\Mosecom;
use App\Repositories\MosecomRepositories\MosecomRepository;

class MosecomParser
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
        $response = [];

        $html = $this->curl->get($this->domain . $name . "/", [], $isUseNewUA, $isClose);

        $isFind = preg_match(
            "/AirCharts\.init\((.*?), {\"months\"/m",
            $html,
            $matches
        );

        if($isFind && $tmpMosecomData = json_decode($matches[1] ,true)) {

            //comment я проверил если сделать так json_decode("asdasdad") не будет ексепшен!
            if(
                json_last_error() == JSON_ERROR_NONE &&
                isset($tmpMosecomData['proportions']) &&
                isset($tmpMosecomData['units'])
            )
            {
                $response = [
                    "proportions" => [],
                    "units" => []
                ];

                foreach ($tmpMosecomData['proportions']['h'] as $key => $value)
                    $response['proportions'][$key] =  round($value['data'][count($value['data']) - 1][1],3);

                foreach ($tmpMosecomData['units']['h'] as $key => $value)
                    $response['units'][$key] =  round($value['data'][count($value['data']) - 1][1],3);
            }
            else
            {
                dd($matches[1]);
            }
        }

        return $response;
    }

}
