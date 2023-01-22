<?php

namespace App\Http\Repositories;

use App\Exports\AbsensiExport;
use App\Http\Resources\AbsensiData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class AbsenRepository
{

    public function getAbsensi($request)
    {
        $absensi = DB::table('absensi')->get();

        for ($i = 0; $i < count($absensi); $i++) {
            $karyawan = DB::table('karyawans')->where('karyawan_code', $absensi[$i]->karyawan_code)->first(['karyawan_code', 'nama', 'email', 'foto_profile']);

            $absensi[$i]->karyawan = $karyawan;
        }

        return $absensi;
    }

    public function getDetailAbsensi($code)
    {
        $absensi = DB::table('absensi')->where('absensi_code', $code)->first();

        if (!empty($absensi)) {
            $karyawan = DB::table('karyawans')->where('karyawan_code', $absensi->karyawan_code)->first(['karyawan_code', 'nama', 'email', 'foto_profile']);

            $absensi->karyawan = $karyawan;
        }

        return $absensi;
    }

    public function setAbsensi($request)
    {
        try {
            $date = $request->date;
            $time_in = $request->time_in;
            $time_out = $request->time_out;

            $check = DB::table('absensi')->where('karyawan_code', $request->karyawan_code)->where('date', $date)->first();
            if ($check) {
                return ['success' => false, 'msg' => 'Data absen sudah diinputkan'];
            }

            $data = [
                'absensi_code' => generateFiledCode('ABS'),
                'karyawan_code' => $request->karyawan_code,
                'date' => $date,
                'time_in' => $time_in,
                'time_out' => $time_out,
                'type' => $request->type
            ];

            DB::table('absensi')->insert($data);

            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false, 'msg' => $e->getMessage()];
        }
    }

    public function editAbsensi($request)
    {
        try {
            $date = $request->date;
            $time_in = $request->time_in;
            $time_out = $request->time_out;

            $check = DB::table('absensi')->where('absensi_code', '!=', $request->absensi_code)->where('karyawan_code', $request->karyawan_code)->where('date', $date)->first();
            if ($check) {
                return ['success' => false, 'msg' => 'Data absen sudah diinputkan'];
            }

            $data = [
                'date' => $date,
                'time_in' => $time_in,
                'time_in' => $time_in,
                'time_out' => $time_out,
                'type' => $request->type
            ];

            DB::table('absensi')->where('absensi_code', $request->absensi_code)->update($data);

            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false, 'msg' => $e->getMessage()];
        }
    }

    public function deleteAbsensi($code)
    {
        try {
            DB::table('absensi')->where('absensi_code', $code)->delete();

            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false, 'msg' => $e->getMessage()];
        }
    }

    public function exportAbsensi($request)
    {
        $data = Excel::store(new AbsensiExport($request), 'public/export/Absensi.xlsx');
        Storage::putFile('exports', storage_path('app/public/export/Absensi.xlsx'));

        $file = asset('storage/export/Absensi.xlsx');
        return ['success' => true, 'data' => (object)[$file]];
    }
}
