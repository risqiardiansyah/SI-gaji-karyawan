<?php

namespace App\Imports;

use App\Pelanggan;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class PelangganImport implements ToModel, WithStartRow, WithValidation
{
    /**
     * @return int
     */
    public function startRow(): int
    {
        return 2;
    }

    public function rules(): array
    {
        return [
            '0' => 'required|string',
            '2' => 'required|string'
        ];
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $data = [
            'pelanggan_code' => generateFiledCode('DUS'),
            'nama' => $row[0],
            'alamat' => $row[1],
            'instansi' => $row[2],
            'kontak' => $row[3],
            'email' => $row[4],
            'nik' => $row[5],
            'npwp' => $row[6],
            'created_by' => Auth::user()->users_code
        ];

        return new Pelanggan($data);
    }
}
