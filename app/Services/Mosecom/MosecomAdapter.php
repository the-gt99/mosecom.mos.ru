<?php


namespace App\Services\Mosecom;

use App\Jobs\MosecomParseStationAndRecordsSaveJob;
use App\Models\TypeOfIndication;
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
        $parser = $this->getParser();
        $service = $this->getService();

        $indicationsList = $parser->getTypeOfIndications(true);
        $service->saveTypeOfIndications($indicationsList);
        
        $stations = $parser->getStations();
        foreach (array_chunk($stations, 20) as $chunk) {
            MosecomParseStationAndRecordsSaveJob::dispatch($chunk)->onQueue($this->getAdapterName());
        }
    }

    public function grabData(): void
    {
        $this->parse();
    }

    private function getParser(): MosecomParser
    {
        return app()->make(MosecomParser::class);
    }

    private function getService(): MosecomService
    {
        return app()->make(MosecomService::class);
    }
}
