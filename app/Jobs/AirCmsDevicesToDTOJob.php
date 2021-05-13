<?php

namespace App\Jobs;

use App\Models\Stations;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AirCmsDevicesToDTOJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var array */
    protected $devices;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($devices)
    {
        $this->devices = $devices;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->devices as $device) {
            $c = Stations::query()->where('url', $device['id'])->first();
            if (!$c) {
                $stationsData[] = [
                    'name' => $device['address'],
                    'url' => $device['id']
                ];
            }
        }

        Stations::insert($stationsData);
    }
}
