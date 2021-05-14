<?php

namespace App\Http\Controllers;

use App\Jobs\AirCmsDevicesSaveJob;
use App\Models\Errors;
use App\Models\Records;
use App\Models\Stations;
use App\Models\TypeOfIndication;
use App\Repositories\AirCms\AirCmsRepository;
use App\Services\Mosecom\MosecomService;
use Illuminate\Http\Resources\Json\JsonResource;

class AirConroller extends Controller
{
    public function test()
    {
        $r = app(AirCmsRepository::class);
        $devices = $r->getDevices();

        foreach (array_chunk($devices['data'], 100) as $chunk) {
            AirCmsDevicesSaveJob::dispatchNow($chunk);
        }
        dd(0);

    }
}
