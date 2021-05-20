<?php

namespace App\Jobs;

use App\Models\Records;
use App\Models\Stations;
use App\Models\TypeOfIndication;
use App\Services\AirCms\AirCmsAdapter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AirCmsRecordsSaveJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var array */
    protected $records;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($records)
    {
        $this->records = $records;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $recordsToInsert = [];
        $sds_p1 = TypeOfIndication::query()->where('code_name', 'sds_p1')->first();
        $sds_p2 = TypeOfIndication::query()->where('code_name', 'sds_p2')->first();
        $date = date("Y-m-d H:i:s", time());
        foreach ($this->records as $record) {
            if ($record['ts'] < 3600) {

                /** @var Station */
                $station = Stations::query()
                    ->where('type', AirCmsAdapter::NAME)
                    ->where('type_primaty_key', $record['device_id'])->first();
                if ($station) {

                    $station->update(['wind_direction' => $record['wind_direction']]);
                    if ($sds_p1) {
                        $recordsToInsert[] = [
                            'unit' => $record['sds_p1'],
                            'measurement_at' => date('Y-m-d H:m:s', time() - $record['ts']),
                            'station_id' => $station->getKey(),
                            'indication_id' => $sds_p1->getKey(),
                            'created_at' => $date
                        ];
                    }

                    if ($sds_p2) {
                        $recordsToInsert[] = [
                            'unit' => $record['sds_p2'],
                            'measurement_at' => date('Y-m-d H:m:s', time() - $record['ts']),
                            'station_id' => $station->getKey(),
                            'indication_id' => $sds_p2->getKey(),
                            'created_at' => $date
                        ];
                    }
                }
            }
        }
        Records::insert($recordsToInsert);
    }
}
