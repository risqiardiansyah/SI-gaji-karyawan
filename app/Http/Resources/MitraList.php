<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MitraList extends JsonResource
{
    public function toArray($request)
    {
        return  [
            'label' => $this->name.' - '.$this->instansi,
            'value' => $this->mitra_code
        ];
    }
}