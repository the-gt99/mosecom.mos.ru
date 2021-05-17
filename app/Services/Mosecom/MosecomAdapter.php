<?php


namespace App\Services\Mosecom;

use App\Jobs\MosecomParseStationAndRecordsSaveJob;
use App\Services\GrabAdapterInterface;

class MosecomAdapter implements GrabAdapterInterface
{
    const NAME = 'mosecom';

    public static function getAdapterName()
    {
        return self::NAME;
    }

    private function parse()
    {
        /** @var MosecomParser */
        $parser = $this->getParser();
        $stations = $parser->getStations();
        foreach (array_chunk($stations, 30) as $chunk) {
            MosecomParseStationAndRecordsSaveJob::dispatch($chunk)->onQueue($this->getAdapterName());
        }
    }

    public function grabData(): void
    {
        $this->parse();
    }

    private function getParser()
    {
        return app()->make(MosecomParser::class);
    }
}
