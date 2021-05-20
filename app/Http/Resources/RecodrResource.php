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
            'particle_name' => $this->indication->code_name != 'sds_p1' &&
                                $this->indication->code_name != 'sds_p2' ?
                                    $this->indication->code_name :
                                    $this->indication->name,
            'proportion' => $this->proportion ?? 0,
            'unit' => $this->unit ?? 0,
            'measurement_at' => strtotime($this->measurement_at) //todo учитывать часовой пояс
        ];
    }
}
