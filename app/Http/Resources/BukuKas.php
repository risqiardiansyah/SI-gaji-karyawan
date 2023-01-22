<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BukuKas extends JsonResource
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
            'id' => $this->idx_buku_kas,
            'buku_nama' => $this->buku_nama,
            'buku_deskripsi' => $this->buku_deskripsi,
            'buku_mata_uang' => $this->buku_mata_uang,
            'buku_saldo' => $this->buku_saldo,
            'status' => $this->status,
        ];
    }
}
