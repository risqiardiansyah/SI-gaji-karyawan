<?php

namespace App\Http\Repositories;

use App\Http\Resources\AbsensiData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\User;
use Illuminate\Support\Facades\Auth;

class HRDashboardRepository
{
    public function getHomeDetail($request)
    {
        $karyawan_code = Auth::user()->karyawan_code;
        $bulan = date('m');
        $tepatWaktu = DB::table('absensi')->whereDate('date', date('Y-' . $bulan . '-d'))->where('karyawan_code', $karyawan_code)->where('status', 1)->count();
        $totalHadir = DB::table('absensi')->whereDate('date', date('Y-' . $bulan . '-d'))->where('karyawan_code', $karyawan_code)->count();
        $cuti = 0;

        return (object)[
            'tepat_waktu' => $tepatWaktu,
            'total_hadir' => $totalHadir,
            'cuti' => $cuti,
            'date' => date('F Y')
        ];
    }
}
