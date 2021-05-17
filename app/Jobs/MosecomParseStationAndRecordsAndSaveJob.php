<?php


namespace App\Jobs;

use App\Services\Mosecom\MosecomParser;
use App\Services\Mosecom\MosecomService;
use ArrayIterator;
use CachingIterator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MosecomParseStationAndRecordsAndSaveJob implements ShouldQueue
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
        $response = [];

        /** @var MosecomService */
        $service = $this->getService();
        $stations = new CachingIterator(new ArrayIterator($this->mosecomData));
        $parser = $this->getParser();
        foreach ($stations as $stationName)
        {
            $isClose = !$stations->hasNext();
            $response[$stationName] = $parser->getStationInfoByName($stationName, $isClose);
        }

        $service->save($response);
    }

    private function getService(): MosecomService
    {
        return app()->make(MosecomService::class);
    }

    private function getParser(): MosecomParser
    {
        return app()->make(MosecomParser::class);
    }
}
