<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class SlipPersheetExport implements FromCollection, WithTitle, WithHeadings
{
    private $i;
    private $year;

    public function __construct(int $year, int $i)
    {
        $this->i = $i;
        $this->year  = $year;
        if (!$year) {
            $this->year = date('Y');
        }
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $select = [
            'id',
            'karyawan_code',
            'nama',
            'type'
        ];
        $data = DB::table('karyawans')->where('aktif_bekerja', 1)->get($select);
        for ($x = 0; $x < count($data); $x++) {
            $gaji = DB::table('slip_gaji')
                ->where('karyawan_code', $data[$x]->karyawan_code)
                ->whereYear('tgl_slip', $this->year)
                ->where('bulan', getMonthEn($this->i))
                ->first();

            $data[$x]->tgl_slip = '-';
            $data[$x]->gaji_pokok = '-';
            $data[$x]->tunjangan = '-';
            $data[$x]->total_terima = '-';
            $data[$x]->total_potongan = '-';
            $data[$x]->gaji_total = '-';
            if (!empty($gaji)) {
                $data[$x]->tgl_slip = $gaji->tgl_slip;
                $data[$x]->gaji_pokok = $gaji->gaji_pokok;
                $data[$x]->tunjangan = $gaji->tunjangan;
                $data[$x]->total_terima = $gaji->total_terima;
                $data[$x]->total_potongan = $gaji->total_potongan;
                $data[$x]->gaji_total = $gaji->gaji_total;
            }

            $data[$x]->type = $data[$x]->type == 1 ? 'Karyawan' : 'Magang';
            $data[$x]->id = $x + 1;
        }

        return $data;
    }

    public function title(): string
    {
        return getMonthEn($this->i);
    }

    public function headings(): array
    {
        $res = [
            'No',
            'Kode Karyawan',
            'Nama Karyawan',
            'Tipe Karyawan',
            'Tanggal Penggajian',
            'Gaji Pokok',
            'Tunjangan',
            'Total Penambahan',
            'Total Potongan',
            'Gaji Diterima',
        ];

        return $res;
    }
}
