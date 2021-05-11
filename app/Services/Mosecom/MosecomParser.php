<?php


namespace App\Services\Mosecom;


use App\Repositories\MosecomRepositories\MosecomRepository;

class MosecomParser
{
    private $curl = null;
    private $domain = "https://mosecom.mos.ru/";
    private $stations = [
        "ru" => "stations/",
        "en" => "measuring-stations/" //TODO: WTF?
    ];

    public function getUrl($lang = "ru")
    {
        return $this->domain . $this->stations[$lang];
    }

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

            if($tmpMosecomData && isset($tmpMosecomData['proportions']) && isset($tmpMosecomData['units'])) {
                $response = [];

                foreach ($tmpMosecomData['proportions']['h'] as $key => $value) {
                    $lastId = count($value['data']) - 1;
                    $lastEl = $value['data'][$lastId];

                    $response[$key]['proportion']['time'] =  round($lastEl[0] / 1000);
                    $response[$key]['proportion']['value'] =  round($lastEl[1],3);

                }

                foreach ($tmpMosecomData['units']['h'] as $key => $value) {
                    $lastId = count($value['data']) - 1;
                    $lastEl = $value['data'][$lastId];

                    $response[$key]['unit']['time'] =  round($lastEl[0] / 1000);
                    $response[$key]['unit']['value'] =  round($lastEl[1],3);

                }
            } else {
                dd($matches[1]);
            }
        }

        return $response;
    }

}
