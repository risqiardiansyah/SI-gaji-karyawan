<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class KaryawanExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $select = [
            'id',
            'nama',
            'jenis_kelamin',
            'alamat',
            'tanggal_lahir',
            'telp',
            'email',
            'nik',
            'no_bpjs',
            'no_npwp',
            'gaji_pokok',
            'tunjangan',
            'type',
            'aktif_bekerja',
        ];
        $data = DB::table('karyawans')->get($select);

        for ($i = 0; $i < count($data); $i++) {
            $data[$i]->id = $i;

            $jk = $data[$i]->jenis_kelamin == 1 ? 'L' : 'P';
            $data[$i]->jenis_kelamin = $jk;

            $ab = $data[$i]->aktif_bekerja == 1 ? 'Y' : 'N';
            $data[$i]->aktif_bekerja = $ab;

            $tp = $data[$i]->type == 1 ? 'Karyawan' : 'Magang';
            $data[$i]->type = $tp;
        }

        return $data;
    }

    public function headings(): array
    {
        $res = [
            'No',
            'Nama Karyawan',
            'Jenis Kelamin',
            'Alamat',
            'Tgl Lahir',
            'No Telp',
            'Email',
            'NIK',
            'No BPJS',
            'No NPWP',
            'Gaji Pokok',
            'Tunjangan',
            'Status',
            'Aktif Bekerja ?',
        ];

        return $res;
    }
}
