<?php

namespace App\Http\Controllers;

use App\Jobs\AirCmsDevicesSaveJob;
use App\Jobs\AirCmsRecordsSaveJob;
use App\Models\Errors;
use App\Models\Records;
use App\Models\Stations;
use App\Models\TypeOfIndication;
use App\Repositories\AirCms\AirCmsRepository;
use App\Repositories\StationsRepository;
use App\Services\Mosecom\MosecomService;
use Illuminate\Http\Resources\Json\JsonResource;

class AirConroller extends Controller
{
    public function test()
    {
        $c = app(StationsRepository::class)->getStationsByCoords();
        foreach ($c as $b) {
            if ($b->getKey() === 80) {
                dd($b);
                $v = $b->relationLoaded('records');
                dd($v);
            }
        }
        dd(1);

        $v = $c->first()->records;
        dd($v);
    }
}
