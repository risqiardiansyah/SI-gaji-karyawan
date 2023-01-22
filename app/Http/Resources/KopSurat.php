<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class KopSurat extends JsonResource
{
    public function toArray($request)
    {
        $header = ($this->header == null ? asset('storage/img/default.png') : asset('storage/kop/' . $this->header));
        $footer = ($this->footer == null ? asset('storage/img/default.png') : asset('storage/kop/' . $this->footer));
        return [
            'idx' => $this->idx,
            'kop_code' => $this->kop_code,
            'judul' => $this->judul,
            'header' => $header,
            'footer' => $footer,
            'header_old' => $this->header,
            'footer_old' => $this->footer,
            'created_at' => $this->created_at,
        ];
    }
}
