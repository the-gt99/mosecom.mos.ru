<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RecodrResource extends JsonResource
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
            'name' => $this->indication->name,
            'particle_name' => $this->indication->code_name,
            'proportion' => $this->proportion,
            'unit' => $this->unit,
            'measurement_at' => $this->measurement_at
        ];
    }
}
