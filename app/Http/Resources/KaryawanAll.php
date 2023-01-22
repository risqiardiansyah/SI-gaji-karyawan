<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class KaryawanAll extends JsonResource
{
    public function toArray($request)
    {
        return  [
            'karyawan_code' => $this->karyawan_code,
            'nama' => $this->nama,
            'alamat' => $this->alamat,
            'email' => $this->email,
            'status' => $this->status,
            'tempat_lahir' => $this->tempat_lahir,
            'tanggal_lahir' => $this->tanggal_lahir,
            'tanggal_lahir_text' => date('d F Y', strtotime($this->tanggal_lahir)),
            'asal_kampus' => $this->asal_kampus,
            'telp' => $this->telp,
            'nik' => $this->nik,
            'type' => $this->type,
            'foto_ktp' => ($this->foto_ktp == null ? asset('storage/img/default.png') : asset('storage/karyawan/' . $this->foto_ktp)),
            'foto_ktp_old' => $this->foto_ktp,
            'foto_profile' => ($this->foto_profile == null ? asset('storage/img/default.png') : asset('storage/karyawan/' . $this->foto_profile)),
            'foto_profile_old' => $this->foto_profile,
            'foto_npwp' => ($this->foto_npwp == null ? asset('storage/img/default.png') : asset('storage/karyawan/' . $this->foto_npwp)),
            'foto_npwp_old' => $this->foto_npwp,
            'foto_bpjs' => ($this->foto_bpjs == null ? asset('storage/img/default.png') : asset('storage/karyawan/' . $this->foto_bpjs)),
            'foto_bpjs_old' => $this->foto_bpjs,
            'no_bpjs' => $this->no_bpjs,
            'no_npwp' => $this->no_npwp,
            'gaji_pokok' => $this->gaji_pokok,
            'tunjangan' => $this->tunjangan,
            'posisi' => $this->posisi,
            'aktif_bekerja' => $this->aktif_bekerja,
            'atasan_langsung' => $this->atasan_langsung,
            'hak_cuti' => $this->hak_cuti,
            'jenis_kelamin' => $this->jenis_kelamin,
        ];
    }
}
