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
        $dataJson = $matches[1];

        $hasError = preg_match(
            "/station-info-message\">[\n ]+<p>(.*?)<\/p>/m",
            $html,
            $matches
        );
        $errorText = $matches[1];

        $errorIndications = $this->parseError($errorText);
        //todo выдать на response

        if($isFind && $tmpMosecomData = json_decode( $dataJson,true)) {

            if($tmpMosecomData && isset($tmpMosecomData['proportions']) && isset($tmpMosecomData['units'])) {
                $response = [];

                foreach ($tmpMosecomData['proportions']['h'] as $key => $value) {
                    $lastId = count($value['data']) - 1;
                    $lastEl = $value['data'][$lastId];


                    $response[$key]['isError'] = false;

                    if(is_null($lastEl[1]))
                    {
                        $response[$key]['isError'] = true;
                        $response[$key]['errorTime'] = round($lastEl[0] / 1000);
                        continue;
                    }

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

    private function parseError($str)
    {
        $exp = explode(",",$str);

        array_walk($exp, function(&$item, $key) {
            if($key == 0)
            {
                $expTmp = explode(" ",$item);
                $item = $expTmp[count($expTmp) - 1];
            }

            $item = trim($item);

            $item = str_replace([",","."],"",$item);

        });

        return $exp;
    }

}
