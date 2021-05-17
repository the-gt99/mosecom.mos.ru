<?php


namespace App\Jobs;

use App\Services\Mosecom\MosecomService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MosecomStationAndRecordsSaveJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var array */
    protected $mosecomData = [];

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($mosecomData)
    {
        $this->mosecomData = $mosecomData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        /** @var MosecomService */
        $service = $this->getService();
        $service->save($this->mosecomData);
    }

    private function getService()
    {
        app()->make(MosecomService::class);
    }
}
