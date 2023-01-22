<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DetailInvitation extends JsonResource
{
    public function toArray($request)
    {
        return  [
            'profile' => ($this->profile == null ? '' : asset('storage/' . $this->profile)),
            'buku_nama' => $this->buku_nama,
            'name' => $this->name,
            'idx_buku_kas' => $this->idx_buku_kas,
            'id_inv' => $this->id_inv,
            'status' => $this->status,
            'buku_kas_code' => $this->buku_kas_code
        ];
    }
}