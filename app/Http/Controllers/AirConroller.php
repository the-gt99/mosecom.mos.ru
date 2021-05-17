<?php

namespace App\Http\Controllers;

use App\Http\Resources\StationResource;
use App\Jobs\AirCmsDevicesSaveJob;
use App\Jobs\AirCmsRecordsSaveJob;
use App\Models\Errors;
use App\Models\Records;
use App\Models\Stations;
use App\Models\TypeOfIndication;
use App\Repositories\AirCms\AirCmsRepository;
use App\Repositories\StationsRepository;
use App\Services\AirCms\AirCmsAdapter;
use App\Services\Mosecom\MosecomService;
use Illuminate\Http\Resources\Json\JsonResource;

class AirConroller extends Controller
{
    public function test()
    {
        $c = app(StationsRepository::class)->getStationsByCoords();
        return StationResource::collection($c);
    }
}
