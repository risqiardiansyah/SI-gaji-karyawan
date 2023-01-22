<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AbsensiExport implements FromCollection, WithHeadings
{
    protected $request;

    function __construct($request)
    {
        $this->request = $request;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $karyawan = DB::table('karyawans')->where('aktif_bekerja', 1)->get(['nama', 'karyawan_code']);
        $bulan = $this->request->month;
        $tahun = $this->request->year;
        if (empty($bulan)) {
            $bulan = date('m');
            $tahun = date('Y');
        }

        for ($x = 0; $x < count($karyawan); $x++) {
            $result = [];
            $hari = date("d", strtotime(date($tahun . '-' . $bulan . '-d')));
            if ($bulan != date('m')) {
                $hari = date("t", strtotime(date($tahun . '-' . $bulan . '-d')));
            }

            $bul = date("F", strtotime(date($tahun . '-' . $bulan . '-d')));

            for ($i = (int)$hari - 1; $i >= 0; $i--) {
                $hari = ($i + 1) < 10 ? '0' . ($i + 1) : ($i + 1);
                $tgl = $bul . $hari;

                $check = DB::table('absensi')->where('karyawan_code', $karyawan[$x]->karyawan_code)->where('date', date($tahun . '-' . $bulan . '-' . $hari))->first();
                if (!empty($check)) {
                    $karyawan[$x]->$tgl = $check->type;
                } else {
                    $karyawan[$x]->$tgl = 'alpha';
                }
            }
        }
        // dd($karyawan);
        return $karyawan;
    }

    public function headings(): array
    {
        $data = [
            'Nama Karyawan',
            'Kode',
        ];

        for ($i = 1; $i <= 31; $i++) {
            array_push($data, 'Tgl ' . $i);
        }

        return $data;
    }
}
