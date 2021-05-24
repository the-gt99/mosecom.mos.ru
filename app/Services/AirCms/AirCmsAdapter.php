<?php

namespace App\Services\AirCms;

use App\Jobs\AirCmsDevicesSaveJob;
use App\Jobs\AirCmsRecordsSaveJob;
use App\Repositories\AirCms\AirCmsRepository;
use App\Services\GrabAdapterInterface;
use Illuminate\Support\Facades\Queue;


class AirCmsAdapter implements GrabAdapterInterface
{

    const NAME = 'aircms';

    public static function getAdapterName()
    {
        return self::NAME;
    }

    private function parse()
    {
        /** @var AirCmsRepository */
        $repository = $this->getRepository();
        $devices = $repository->getDevices();
        foreach (array_chunk($devices['data'], 300) as $chunk) {
            AirCmsDevicesSaveJob::dispatch($chunk)->onQueue($this->getAdapterName());
        }

        while (Queue::size($this->getAdapterName()) !== 0) {
            //
        }

        $records = $repository->getRecords();
        foreach (array_chunk($records['data'], 100) as $chunk) {
            AirCmsRecordsSaveJob::dispatch($chunk)->onQueue('aircms');
        }

    }

    public function grabData(): void
    {
        $this->parse();
    }

    public function getRepository(): AirCmsRepository
    {
        return app()->make(AirCmsRepository::class);
    }
}
