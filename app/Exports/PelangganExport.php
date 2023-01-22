<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PelangganExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithColumnFormatting
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $select = [
            'id_pelanggan',
            'pelanggan_code',
            'nama',
            'alamat',
            'instansi',
            'kontak',
            'email',
            'nik',
            'npwp',
            'created_at',
        ];
        $data = DB::table('pelanggan')->select($select)->orderBy('nama', 'ASC')->get();
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]->id_pelanggan = $i + 1;
            $data[$i]->created_at = Carbon::parse($data[$i]->created_at)->locale('id')->isoFormat('DD MMMM Y');
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'No. ',
            'No Pelanggan',
            'Nama',
            'Alamat',
            'Instansi',
            'Kontak',
            'Email',
            'NIK',
            'NPWP',
            'Tanggal Dibuat',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true]],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'H' => NumberFormat::FORMAT_NUMBER,
            'I' => NumberFormat::FORMAT_NUMBER,
        ];
    }
}
