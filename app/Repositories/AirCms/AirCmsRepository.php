<?php

namespace App\Repositories\AirCms;

use Illuminate\Support\Facades\Http;

class AirCmsRepository
{
    protected $host = 'http://doiot.ru/php';

    protected $apikey;

    protected $current;

    const ROUTES = [
        'devices' => '/guiapi.php?devices',
        'records' => '/guiapi.php?T=0',
        'records_by_coords' => '/guiapi.v2.php?bbox=<bbox1>,<bbox2>&current=<current>&api_key=<apikey>'
    ];


    public function __construct()
    {

    }

    public function getDevices()
    {
        return Http::get($this->host.self::ROUTES['devices'])->json();
    }

    public function getRecords()
    {
        return Http::get($this->host.self::ROUTES['records'])->json();
    }

    public function getRecordsByCoords($botLeftX, $botLeftY, $topRightX, $topRightY)
    {
        $route = str_replace(
            '<current>',
            $this->getCurrent(),
            str_replace('<apikey>', $this->getApiKey(), self::ROUTES['records_by_coords'])
        );

        $route = str_replace(
            '<bbox2>',
            "$topRightX,$topRightY",
            str_replace('<bbox1>', "$botLeftX,$botLeftY", $route)
        );

        return Http::get($this->host.$route)->json();
    }

    private function getApiKey()
    {
        if (!$this->apikey) {
            $this->apikey = config('client.aircms.api_key');
        }

        return $this->apikey;
    }

    private function getCurrent()
    {
        if (!$this->current) {
            $this->current = config('client.aircms.current');
        }

        return $this->current;
    }
}
