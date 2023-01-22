<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PenilaianExport implements WithMultipleSheets
{
    use Exportable;

    protected $year;
    protected $month;

    public function __construct(int $year, int $month)
    {
        $this->year = $year;
        $this->month = $month;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        for ($i = 1; $i <= 3; $i++) {
            $sheets[] = new PenilaianPersheetExport($this->year, $i, $this->month);
        }

        return $sheets;
    }
}
