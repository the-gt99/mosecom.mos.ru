<?php

namespace App\Repositories\AirCms;

use Illuminate\Support\Facades\Http;

class AirCmsRepository
{
    protected $host = 'http://doiot.ru';


    public function __construct()
    {

    }

    public function getDevices()
    {
        return Http::get($this->host.'/php/guiapi.php?devices')->json();
    }

    public function getRecords()
    {
        return Http::get($this->host.'/php/guiapi.php?T=0')->json();
    }
}
