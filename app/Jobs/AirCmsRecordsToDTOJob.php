<?php

namespace App\Jobs;

use App\Models\Records;
use App\Models\Stations;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AirCmsRecordsToDTOJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var array */
    protected $jobs;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($jobs)
    {
        $this->jobs = $jobs;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->jobs as $device) {
            $c = Stations::query()->where('url', $device['id'])->first();
            if ($c) {
                $c->update(['wind_direction' => $device['wind_direction']]);
            }
        }
    }
}
