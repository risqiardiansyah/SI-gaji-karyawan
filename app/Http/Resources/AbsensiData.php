<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AbsensiData extends JsonResource
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
            'idx' => $this->idx,
            'karyawan_code' => $this->karyawan_code,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'detail_alamat' => $this->detail_alamat,
            'date' => date('d F Y', strtotime($this->date)),
            'time' => date('H:i', strtotime($this->time)),
            'type' => $this->type,
            'status' => $this->status
        ];
    }
}
