<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RecodrByDateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $station = $this->first();
        return [
            'type' => ($this->first() ? $this->first()->type == "mosecom" : null)? 'MosEkoMonitoring' : "undefined",
            'name' => ($this->first() ? $this->first()->type == "mosecom": null)? 'МосЭкоМониторинг' : "не МосЭкоМониторинг",
            'stations' => StationResource::collection($this)
        ];
    }
}
