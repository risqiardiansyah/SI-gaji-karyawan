<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class DetailCuti extends JsonResource
{
    public function toArray($request)
    {
        $kat_khusus = DB::table('kat_cuti')
            ->where('type', 'khusus')
            ->get();

        for ($i = 0; $i < count($kat_khusus); $i++) {
            $check = DB::table('kat_cuti_khusus')
                ->where('cuti_code', $this->cuti_code)
                ->where('kat_code', $kat_khusus[$i]->kat_code)
                ->first();
            if (!empty($check)) {
                $kat_khusus[$i]->jml_diambil = $check->jml_diambil;
                $kat_khusus[$i]->tgl_awal = $check->tgl_awal;
                $kat_khusus[$i]->tgl_akhir = $check->tgl_akhir;
            } else {
                $kat_khusus[$i]->jml_diambil = 0;
                $kat_khusus[$i]->tgl_awal = null;
                $kat_khusus[$i]->tgl_akhir = null;
            }
        }

        $kat_izin = DB::table('kat_cuti')
            ->where('type', 'izin')
            ->get();

        for ($i = 0; $i < count($kat_izin); $i++) {
            $check = DB::table('kat_cuti_izin')
                ->where('cuti_code', $this->cuti_code)
                ->where('kat_code', $kat_izin[$i]->kat_code)
                ->first();
            if (!empty($check)) {
                $kat_izin[$i]->jml_diambil = $check->jml_diambil;
                $kat_izin[$i]->tgl_awal = $check->tgl_awal;
                $kat_izin[$i]->tgl_akhir = $check->tgl_akhir;
                $kat_izin[$i]->jam_awal = $check->jam_awal;
                $kat_izin[$i]->jam_akhir = $check->jam_akhir;
            } else {
                $kat_izin[$i]->jml_diambil = 0;
                $kat_izin[$i]->tgl_awal = null;
                $kat_izin[$i]->tgl_akhir = null;
                $kat_izin[$i]->jam_awal = null;
                $kat_izin[$i]->jam_akhir = null;
            }
        }

        $jml_khusus = DB::table('kat_cuti_khusus')
            ->leftJoin('kat_cuti', 'kat_cuti.kat_code', '=', 'kat_cuti_khusus.kat_code')
            ->where('cuti_code', $this->cuti_code)
            ->sum('kat_cuti_khusus.jml_diambil');

        $jml_izin = DB::table('kat_cuti_izin')
            ->leftJoin('kat_cuti', 'kat_cuti.kat_code', '=', 'kat_cuti_izin.kat_code')
            ->where('cuti_code', $this->cuti_code)
            ->sum('kat_cuti_izin.jml_diambil');

        $pemohon = DB::table('users')
            ->where('karyawan_code', $this->pemohon_code)
            ->first();

        if (empty($pemohon)) {
            $pemohon = DB::table('karyawans')
                ->where('karyawan_code', $this->pemohon_code)
                ->first(['nama as name', 'karyawan_code', 'company_user as nik', 'address_user as telp']);
        }

        $atasan = DB::table('karyawans')
            ->where('karyawan_code', $this->karyawan_code)
            ->first();

        $signer = DB::table('signer')
            ->where('signer_code', $this->mengetahui_code)
            ->first();

        return [
            'idx' => $this->idx,
            'pemohon_code' => $this->pemohon_code,
            'cuti_code' => $this->cuti_code,
            'type' => $this->type,
            'tgl_mulai' => $this->tgl_mulai,
            'tgl_mulai_formatted' => date('d F Y', strtotime($this->tgl_mulai)),
            'tgl_selesai' => $this->tgl_selesai,
            'tgl_selesai_formatted' => date('d F Y', strtotime($this->tgl_selesai)),
            'jml_diambil' => $this->jml_diambil,
            'jml_izin_diambil' => $jml_izin,
            'jml_khusus_diambil' => $jml_khusus,
            'jml_hak_cuti' => $this->jml_hak_cuti,
            'saldo_cuti' => $this->saldo_cuti,
            'mengetahui_code' => $this->mengetahui_code,
            'karyawan_code' => $this->karyawan_code,
            'catatan' => $this->catatan,
            'status' => $this->status,
            'cuti_khusus' => $kat_khusus,
            'cuti_izin' => $kat_izin,
            'pemohon' => $pemohon,
            'atasan' => $atasan,
            'signer' => $signer,
            'created_at' => $this->created_at,
        ];
    }
}
