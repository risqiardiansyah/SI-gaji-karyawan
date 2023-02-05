<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SlipPersheetExport implements FromCollection, WithTitle, WithHeadings, ShouldAutoSize, WithStyles
{
    private $i;
    private $year;
    private $columnCount;
    private $space;

    public function __construct(int $year, int $i)
    {
        $this->i = $i;
        $this->year  = $year;
        $this->columnCount = 0;
        if (!$year) {
            $this->year = date('Y');
        }
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

        $sheet->getStyle('A1:J' . $this->columnCount)->applyFromArray([
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

            array_push($result, $data[$x]);
        }

        $this->columnCount = count($result) + 1;

        array_push($result, $this->space);

        $tgl = (object) [
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            'Depok, ' . \Carbon\Carbon::now('Asia/Jakarta')->locale('id')->isoFormat('DD MMMM Y'),
            ''
        ];
        array_push($result, $tgl);

        $jabatan = (object) [
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            'HR & People Development',
            ''
        ];
        array_push($result, $jabatan);

        for ($y=0; $y < 3; $y++) { 
            array_push($result, $this->space);
        }

        $nama = (object) [
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            'Novita Widyanti H, S.Psi., MM.',
            ''
        ];
        array_push($result, $nama);
        
        return collect($result);
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
