<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class Cuti extends JsonResource
{
    public function toArray($request)
    {
        $khusus = DB::table('kat_cuti_khusus')
            ->leftJoin('kat_cuti', 'kat_cuti.kat_code', '=', 'kat_cuti_khusus.kat_code')
            ->where('cuti_code', $this->cuti_code)
            ->get(['kat_cuti_khusus.*', 'kat_cuti.kat_nama']);

        $jml_khusus = DB::table('kat_cuti_khusus')
            ->leftJoin('kat_cuti', 'kat_cuti.kat_code', '=', 'kat_cuti_khusus.kat_code')
            ->where('cuti_code', $this->cuti_code)
            ->sum('kat_cuti_khusus.jml_diambil');

        $izin = DB::table('kat_cuti_izin')
            ->leftJoin('kat_cuti', 'kat_cuti.kat_code', '=', 'kat_cuti_izin.kat_code')
            ->where('cuti_code', $this->cuti_code)
            ->get(['kat_cuti_izin.*', 'kat_cuti.kat_nama']);

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
                ->first(['nama as name', 'karyawan_code']);
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
            'cuti_khusus' => $khusus,
            'cuti_izin' => $izin,
            'pemohon' => $pemohon,
            'atasan' => $atasan,
            'signer' => $signer,
            'created_at' => $this->created_at,
            'pemohon_name' => $pemohon->name,
        ];
    }
}
