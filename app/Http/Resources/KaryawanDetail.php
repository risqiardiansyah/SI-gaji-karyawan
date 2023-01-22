<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class KaryawanDetail extends JsonResource
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
            'id' => $this->id,
            'karyawan_code' => $this->karyawan_code,
            'nama' => $this->nama,
            'email' => $this->email,
            'alamat' => $this->alamat,
            'status' => $this->status,
            'tempat_lahir' => $this->tempat_lahir,
            'tanggal_lahir' => $this->tanggal_lahir,
            'asal_kampus' => $this->asal_kampus,
            'telp' => $this->telp,
            'nik' => $this->nik,
            'type' => $this->type,
            'profile' => asset('storage/default.png'),
            // 'profile_ori' => $this->profile
        ];
    }
}
