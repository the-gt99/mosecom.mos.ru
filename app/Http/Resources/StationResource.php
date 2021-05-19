<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StationResource extends JsonResource
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
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address,
            'lat' => $this->point ? $this->point->getLat() : null,
            'lon' => $this->point ? $this->point->getLng() : null,
            'indications' => RecodrResource::collection($this->whenLoaded('records'))
        ];
    }
}
