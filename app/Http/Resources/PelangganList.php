<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PelangganList extends JsonResource
{
    public function toArray($request)
    {
        return  [
            'label' => $this->nama.' - '.$this->instansi,
            'value' => $this->pelanggan_code
        ];
    }
}