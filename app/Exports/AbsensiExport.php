<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AbsensiExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $request;
    private $columnCount;
    private $space;

    function __construct($request)
    {
        $this->request = $request;
        $this->columnCount = 0;
        $this->space = (object) [
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            ''
        ];
    }

    public function styles(Worksheet $sheet)
    {

        $sheet->getStyle('A1:AG' . $this->columnCount)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);
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

        $result = [];
        for ($x = 0; $x < count($karyawan); $x++) {
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

            array_push($result, $karyawan[$x]);
        }

        $this->columnCount = count($result) + 1;

        array_push($result, $this->space);

        $tgl = (object) [
            '',
            'Depok, ' . \Carbon\Carbon::now('Asia/Jakarta')->locale('id')->isoFormat('DD MMMM Y'),
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            ''
        ];
        array_push($result, $tgl);

        $jabatan = (object) [
            '',
            'HR & People Development',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            ''
        ];
        array_push($result, $jabatan);

        for ($y = 0; $y < 3; $y++) {
            array_push($result, $this->space);
        }

        $nama = (object) [
            '',
            'Novita Widyanti H, S.Psi., MM.',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            ''
        ];
        array_push($result, $nama);

        return collect($result);
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
