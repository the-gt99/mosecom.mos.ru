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
        return [
            'type' => $this->first()->type,
            'name' => $this->first() && $this->first()->type == "mosecom" ? 'МосЭкоМониторинг' : "AIRCMS",
            'stations' => StationResource::collection($this)
        ];

    }
}
