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
//        $this->load('stations');
        return [
            'type' => 'MosEkoMonitoring',
            'name' => 'МосЭкоМониторинг',
//            'stations' => StationResource::collection($this->whenLoaded('stations'))
        ];
    }
}
