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
    private $mosecomApi = [
        "typeOfIndications" => "wp-content/themes/moseco/map/elements.php?locale=ru_RU&mapType=air"
    ];

    public function getUrlStationByName($name)
    {
        return $this->domain . $name;
    }

    public function __construct()
    {
        $this->curl = new MosecomRepository();
    }

    public function getTypeOfIndications($isClose = true, $isUseNewUA = false): array
    {
        $response = [];

        $json = $this->curl->get($this->domain . $this->mosecomApi['typeOfIndications'], [] , $isUseNewUA, $isClose);

        $typeOfIndicationsList = json_decode($json,true)[0];

        foreach ($typeOfIndicationsList as $typeOfIndication) {
            $codeName = $typeOfIndication['name'];
            $name = $typeOfIndication['full_name'];

            array_push($response, [
                "codeName" => preg_replace('/( \(\w+\))/u', '', $codeName),
                "name" => trim($name),
            ]);
        }

        return $response;
    }

    public function getStations($isClose = true, $isUseNewUA = false): array
    {
        $response = [];

        $html = $this->curl->get($this->domain . $this->stations['ru'], [] , $isUseNewUA, $isClose);

        $exp = explode("searching-data", $html);

        $pattern = '/<div class=\"row-title\">[\r\n[ ]*]?<a href=\"https:\/\/mosecom.mos.ru\/([-\w]+)\/\">/um';

        $isFind = preg_match_all(
            $pattern,
            $exp[1],
            $matches
        );

        if($isFind) {
            $response = $matches[1];
        }

        return $response;
    }

    public function getStationInfoByName($name, $isClose = true, $isUseNewUA = false): array
    {
        $response = [];

        //Получаем html станции
        $html = $this->getHtmlByStationName($name, $isClose = true, $isUseNewUA = false);

        //Получаем json станции
        $stationJson = $this->getJsonByHtml($html);

        //Получаем название станции
        $stationName = $this->getNameByHtml($html);
        $response['name'] = $stationName;

        //Получаем адресс
        $stationAddress = $this->getAddressByHtml($html);
        $response['address'] = $stationAddress;

        //Проверяем есть ли ошибки и полный текст ошибки при наличии
        $errorInf = $this->tryParseErrorByHtml($html);
        $response['hasError'] = $errorInf['hasError'];

        if($stationJson && $tmpMosecomData = json_decode($stationJson ,true)) {

            if($tmpMosecomData && isset($tmpMosecomData['proportions']) && isset($tmpMosecomData['units'])) {

                if($errorInf['hasError'])
                {
                    $measurementNames = [];

                    foreach ($tmpMosecomData['proportions'] as $timeInterval => $measurementInf)
                    {
                        $measurementNames = array_merge($measurementNames, array_keys($measurementInf));
                    }

                    foreach ($tmpMosecomData['units'] as $timeInterval => $measurementInf)
                    {
                        $measurementNames = array_merge($measurementNames, array_keys($measurementInf));
                    }

                    $measurementNames = array_unique($measurementNames);
                }

                foreach ($tmpMosecomData['proportions']['h'] as $key => $value) {
                    $lastId = count($value['data']) - 1;
                    $lastEl = $value['data'][$lastId];

                    //Чистим от кирилицы и скобок в codeName
                    $key = preg_replace('/( \(\w+\))/u', '', $key);

                    if(!is_null($lastEl[1]))
                    {
                        $response['measurement'][$key]['proportion']['time'] =  round($lastEl[0] / 1000);
                        $response['measurement'][$key]['proportion']['value'] =  round($lastEl[1],3);
                    }
                }

                foreach ($tmpMosecomData['units']['h'] as $key => $value) {
                    $lastId = count($value['data']) - 1;
                    $lastEl = $value['data'][$lastId];

                    //Чистим от кирилицы и скобок в codeName
                    $key = preg_replace('/( \(\w+\))/u', '', $key);

                    if(!is_null($lastEl[1]))
                    {
                        $response['measurement'][$key]['unit']['time'] = round($lastEl[0] / 1000);
                        $response['measurement'][$key]['unit']['value'] = round($lastEl[1], 3);
                    }
                }

                if($errorInf['hasError'])
                {
                    if(isset($response['measurement']))
                    {
                        $diff = array_diff($measurementNames, array_keys($response['measurement']));
                    }
                    else
                    {
                        $diff = $measurementNames;
                    }

                    $response['errorInf'] = [
                        "notFoundMeasurementNames" => $diff,
                        "errorText" => $errorInf['text']
                    ];
                }

            } else {
                dd($stationJson);
            }
        }

        return $response;
    }

    private function getHtmlByStationName($name, $isClose = true, $isUseNewUA = false): string
    {
        return $this->curl->get($this->domain . $name . "/", [], $isUseNewUA, $isClose);
    }

    private function getJsonByHtml($html): string
    {
        $response = "";

        $isFind = preg_match(
            "/AirCharts\.init\((.*?), {\"months\"/m",
            $html,
            $matches
        );

        if($isFind)
        {
            $response = $matches[1];
        }

        return $response;
    }

    private function getAddressByHtml($html): string
    {
        $response = "";

        $isFind = preg_match(
            "/<span class=\"adress\">[\r\n]*([\w ,-\.()\/]+)<\/span>/mu",
            $html,
            $matches
        );

        if($isFind)
        {
            $response = trim($matches[1]);
        }

        return $response;
    }

    private function getNameByHtml($html): string
    {
        $response = "";

        $isFind = preg_match(
            "/h3 class=\"name\">[\r\n]*([\w ,-\.()\/]+)<\/h3>/mu",
            $html,
            $matches
        );

        if($isFind)
        {
            $response = trim($matches[1]);
        }

        return $response;
    }

    private function tryParseErrorByHtml($html): array
    {
        $response = [
            "hasError" => false,
        ];

        $hasError = preg_match(
            "/station-info-message\">[\n ]+<p>(.*?)<\/p>/m",
            $html,
            $matches
        );

        if($hasError)
        {
            $response['hasError'] = true;
            $errorText = $matches[1];

            if(stripos($errorText, "<br />") !== false)
            {
                $exp = explode("<br />", $errorText);
                $errorText = $exp[0];
            }

            $isFindHtml = preg_match_all('/<[^>]*>/m', $errorText, $matches);

            if($isFindHtml)
            {
                $errorText = str_replace($matches[0],"", $errorText);
            }

            $response['text'] = $errorText;
        }

        return $response;
    }
}
