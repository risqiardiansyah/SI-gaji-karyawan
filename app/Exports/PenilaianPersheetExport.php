<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PenilaianPersheetExport implements FromCollection, WithTitle, WithHeadings, ShouldAutoSize, WithStyles
{
    private $i;
    private $year;
    private $month;
    private $columnCount;
    private $space;

    public function __construct(int $year, int $i, int $month)
    {
        $this->i = $i;
        $this->year  = $year;
        if (!$year) {
            $this->year = date('Y');
        }

        $this->month  = $month;
        if (!$month) {
            $this->month = date('m');
        }
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
        switch ($this->i) {
            case '1':
                $lcolumn = 'H' . $this->columnCount;
                break;
            case '2':
                $lcolumn = 'F' . $this->columnCount;
                break;
            case '3':
                $lcolumn = 'Q' . $this->columnCount;
                break;

            default:
                break;
        }
        $sheet->getStyle('A1:' . $lcolumn)->applyFromArray([
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
        $result = [];
        $select = [
            'id',
            'karyawan_code',
            'nama',
            'type'
        ];
        $data = DB::table('karyawans')->where('aktif_bekerja', 1)->get($select);

        switch ($this->i) {
            case '1':
                for ($x = 0; $x < count($data); $x++) {
                    for ($y = 1; $y < 5; $y++) {
                        $where = 'Minggu ke-' . $y;
                        $penilaian = DB::table('penilaian')
                            ->whereYear('tgl', $this->year)
                            ->whereMonth('tgl', $this->month)
                            ->where('karyawan_code', $data[$x]->karyawan_code)
                            ->where('periode', $where)
                            ->first();
                        if (empty($penilaian)) {
                            $data[$x]->$y = '0';
                        } else {
                            $data[$x]->$y = $penilaian->total_nilai;
                        }
                    }

                    $data[$x]->type = $data[$x]->type == 1 ? 'Karyawan' : 'Magang';
                    $data[$x]->id = $x + 1;

                    array_push($result, $data[$x]);
                }

                array_push($result, $this->space);

                $tgl = (object) [
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    'Depok, ' . \Carbon\Carbon::now('Asia/Jakarta')->locale('id')->isoFormat('DD MMMM Y'),
                    '',
                ];
                array_push($result, $tgl);

                $jabatan = (object) [
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    'HR & People Development',
                    '',
                ];
                array_push($result, $jabatan);

                for ($y = 0; $y < 3; $y++) {
                    array_push($result, $this->space);
                }

                $nama = (object) [
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    'Novita Widyanti H, S.Psi., MM.',
                    '',
                ];
                array_push($result, $nama);
                break;
            case '2':
                for ($x = 0; $x < count($data); $x++) {
                    for ($y = 1; $y < 3; $y++) {
                        $where = 'Semester ' . $y;
                        $penilaian = DB::table('penilaian')
                            ->whereYear('tgl', $this->year)
                            ->whereMonth('tgl', $this->month)
                            ->where('karyawan_code', $data[$x]->karyawan_code)
                            ->where('periode', $where)
                            ->first();
                        if (empty($penilaian)) {
                            $data[$x]->$y = '0';
                        } else {
                            $data[$x]->$y = $penilaian->total_nilai;
                        }
                    }

                    $data[$x]->type = $data[$x]->type == 1 ? 'Karyawan' : 'Magang';
                    $data[$x]->id = $x + 1;

                    array_push($result, $data[$x]);
                }

                array_push($result, $this->space);

                $tgl = (object) [
                    '',
                    '',
                    '',
                    '',
                    'Depok, ' . \Carbon\Carbon::now('Asia/Jakarta')->locale('id')->isoFormat('DD MMMM Y'),
                    '',
                ];
                array_push($result, $tgl);

                $jabatan = (object) [
                    '',
                    '',
                    '',
                    '',
                    'HR & People Development',
                    '',
                ];
                array_push($result, $jabatan);

                for ($y = 0; $y < 3; $y++) {
                    array_push($result, $this->space);
                }

                $nama = (object) [
                    '',
                    '',
                    '',
                    '',
                    'Novita Widyanti H, S.Psi., MM.',
                    '',
                ];
                array_push($result, $nama);
                break;
            case '3':
                for ($x = 0; $x < count($data); $x++) {
                    for ($y = 1; $y < 13; $y++) {
                        $where = 'Bulan ' . getMonth($y);
                        $penilaian = DB::table('penilaian')
                            ->whereYear('tgl', $this->year)
                            ->whereMonth('tgl', $this->month)
                            ->where('karyawan_code', $data[$x]->karyawan_code)
                            ->where('periode', $where)
                            ->first();
                        if (empty($penilaian)) {
                            $data[$x]->$y = '0';
                        } else {
                            $data[$x]->$y = $penilaian->total_nilai;
                        }
                    }

                    $data[$x]->type = $data[$x]->type == 1 ? 'Karyawan' : 'Magang';
                    $data[$x]->id = $x + 1;

                    array_push($result, $data[$x]);
                }

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
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
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
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
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
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                ];
                array_push($result, $nama);
                break;

            default:
                break;
        }

        $this->columnCount = count($result) - 6;

        return collect($result);
    }

    public function title(): string
    {
        switch ($this->i) {
            case '1':
                return 'Penilaian Mingguan Bulan ' . $this->month . ', ' . $this->year;
                break;
            case '2':
                return 'Penilaian 6 Bulanan Tahun ' . $this->year;
                break;
            case '3':
                return 'Penilaian Tahunan ' . $this->year;
                break;
            default:
                break;
        }
    }

    public function headings(): array
    {
        switch ($this->i) {
            case '1':
                $res = [
                    'No',
                    'Kode Karyawan',
                    'Nama Karyawan',
                    'Tipe Karyawan',
                    'Nilai Minggu ke-1',
                    'Nilai Minggu ke-2',
                    'Nilai Minggu ke-3',
                    'Nilai Minggu ke-4',
                ];
                break;
            case '2':
                $res = [
                    'No',
                    'Kode Karyawan',
                    'Nama Karyawan',
                    'Tipe Karyawan',
                    'Nilai Semester 1',
                    'Nilai Semester 2',
                ];
                break;
            case '3':
                $res = [
                    'No',
                    'Kode Karyawan',
                    'Nama Karyawan',
                    'Tipe Karyawan',
                    'Nilai Bulan ' . getMonth(1),
                    'Nilai Bulan ' . getMonth(2),
                    'Nilai Bulan ' . getMonth(3),
                    'Nilai Bulan ' . getMonth(4),
                    'Nilai Bulan ' . getMonth(4),
                    'Nilai Bulan ' . getMonth(5),
                    'Nilai Bulan ' . getMonth(6),
                    'Nilai Bulan ' . getMonth(7),
                    'Nilai Bulan ' . getMonth(8),
                    'Nilai Bulan ' . getMonth(9),
                    'Nilai Bulan ' . getMonth(10),
                    'Nilai Bulan ' . getMonth(11),
                    'Nilai Bulan ' . getMonth(12),
                ];
                break;
            default:
                break;
        }

        return $res;
    }
}
