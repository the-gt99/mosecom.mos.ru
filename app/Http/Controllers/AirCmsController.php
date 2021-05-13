<?php

namespace App\Http\Controllers;

use App\Jobs\AirCmsDevicesToDTOJob;
use App\Jobs\AirCmsRecordsToDTOJob;
use App\Repositories\AirCms\AirCmsRepository;
use Doctrine\DBAL\Query;
use Illuminate\Contracts\Queue\Queue;

class AirCmsController extends Controller
{
    public function getDevices(AirCmsRepository $airCmsRepository)
    {
        // $r = $airCmsRepository->getDevices();
        // foreach (array_chunk($r['data'], 50) as $chunk) {
        //     AirCmsDevicesToDTOJob::dispatch($chunk)->onQueue('aircms');
        // }

        $a = $airCmsRepository->getRecords();
        foreach (array_chunk($a['data'], 50) as $chunk) {
            AirCmsRecordsToDTOJob::dispatch($chunk)->onQueue('aircms');
        }
    }
}

