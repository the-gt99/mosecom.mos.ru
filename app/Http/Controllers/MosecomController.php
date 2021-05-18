<?php

namespace App\Http\Controllers;

use App\Http\Resources\StationResource;
use App\Models\Stations;
use App\Services\Mosecom\MosecomService;

class MosecomController extends Controller
{
    /** @var MosecomService $mosecomService */
    private $mosecomService;

    public function __construct(MosecomService $mosecomService)
    {
        $this->mosecomService = $mosecomService;
    }

    /**
     * @param string $name
     *
     * @return array
     */
    public function parse(string $name = null)
    {
        $response = $this->mosecomService->parseTypeOfIndicationInfo();
        $this->mosecomService->saveTypeOfIndications($response);

        $response1 = $this->mosecomService->parseStationInfo($name);
        $this->mosecomService->saveStationsInf($response1);

        return $response1;
    }

    public function getRecordByDate(string $date)
    {
        return StationResource::collection(Stations::all());
    }
}
