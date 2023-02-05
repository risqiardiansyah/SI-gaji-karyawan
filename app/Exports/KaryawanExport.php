<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KaryawanExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    private $columnCount;
    private $space;

    public function __construct()
    {
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

        $sheet->getStyle('A1:N' . $this->columnCount)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);
    }
    
    public function collection()
    {
        $result = [];

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

            array_push($result, $data[$i]);
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
            '',
            '',
            '',
            '',
            'Novita Widyanti H, S.Psi., MM.',
            '',
        ];
        array_push($result, $nama);

        return collect($result);
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
