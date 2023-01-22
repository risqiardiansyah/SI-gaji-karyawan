<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Kategori extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return  [
            'label' => $this->sub_nama,
            'value' => $this->idx_sub_kat,
            'buku_kas_code' => $this->buku_kas_code,
            'buku_nama' => $this->buku_nama
        ];
    }
}
