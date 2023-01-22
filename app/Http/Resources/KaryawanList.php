<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class KaryawanList extends JsonResource
{
    public function toArray($request)
    {
        $type = $this->type === 1 ? 'KARYAWAN' : 'MAGANG';
        return  [
            'label' => $this->nama.' - '.$type,
            'value' => $this->karyawan_code
        ];
    }
}