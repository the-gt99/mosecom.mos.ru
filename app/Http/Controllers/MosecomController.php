<?php

namespace App\Http\Controllers;

use App\Http\Resources\RecodrByDateResource;
use App\Http\Resources\RecodrResource;
use App\Http\Resources\StationResource;
use App\Models\Errors;
use App\Models\Records;
use App\Models\Stations;
use App\Models\TypeOfIndication;
use App\Services\Mosecom\MosecomService;
use Illuminate\Http\Resources\Json\JsonResource;

class MosecomController extends Controller
{
    /** @var MosecomService $mosecomService */
    private $mosecomService;

    public function __construct(MosecomService $mosecomService)
    {
        $this->mosecomService = $mosecomService;
    }

    /**
     * @param string $name
     *
     * @return array
     */
    public function parse(string $name = null)
    {
        $response = $this->mosecomService->parse($name);
        $this->mosecomService->save($response);

        return $response;
    }

    public function getRecordByDate(string $date)
    {

//        $record = Errors::query()->first();
//        $record->load('records');
//        dd($record);
//
////        dd($station->relationLoaded('records'));
////        dd($station);
////        dd($station->records);

        //$data = $this->mosecomService->getRecordByDate($date);
        return StationResource::collection(Stations::all());
    }
}
