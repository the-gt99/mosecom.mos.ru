<?php


namespace App\Repositories\MosecomRepositories;

use App\Services\CurlClient;


class MosecomRepository
{
    private $curl;

    public function __construct()
    {
        $this->curl = new CurlClient();
    }

    public function get($url, $headers = [], $isUseNewUA = false, $isClose = true): string
    {
        return $this->curl->get($url, $headers, $isUseNewUA, $isClose);
    }
}
