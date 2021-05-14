<?php

namespace App\Jobs;

use App\Models\Stations;
use App\Services\AirCms\AirCmsAdapter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Grimzy\LaravelMysqlSpatial\Types\Point;

class AirCmsDevicesSaveJob implements ShouldQueue
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
        $stationsData = [];
        foreach ($this->devices as $device) {
            $station = Stations::query()->where('type_primaty_key', $device['id'])->where('type', AirCmsAdapter::NAME)->first();
            if (!$station) {
                $stationsData = new Stations([
                    'name' => 1,
                    'address' => $device['address'],
                    'type_primaty_key' => $device['id'],
                    'type' => AirCmsAdapter::NAME
                ]);
                if ($device['lat'] ?? $device['lon']) {
                    $stationsData->point = new Point((float)$device['lat'], (float)$device['lon']);
                } else {
                    // $stationsData->point = new Point(0,0);
                }

                $stationsData->save();
            } else {
                // $station->update([
                //     'addres' => $device['address'],
                //     'lat' => $device['lat'],
                //     'lon' => $device['lon'],
                //     'type' => AirCmsAdapter::NAME
                // ]);
            }
        }

        // Stations::insert($stationsData);
    }
}
