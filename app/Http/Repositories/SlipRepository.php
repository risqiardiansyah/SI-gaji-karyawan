<?php

namespace App\Http\Repositories;

use App\Exports\SlipExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class SlipRepository
{
    public function getAllSlip()
    {
        $select = [
            'slip_gaji.*',
            'k.*',
            'k.nama as karyawan_nama',
            's.name as signer_name',
        ];
        $data = DB::table('slip_gaji')
            ->select($select)
            ->leftJoin('signer as s', 's.signer_code', '=', 'slip_gaji.signer_code')
            ->leftJoin('karyawans as k', 'k.karyawan_code', '=', 'slip_gaji.karyawan_code')
            ->get();

        for ($i = 0; $i < count($data); $i++) {
            $terima = DB::table('slip_terima')->where('slip_code', $data[$i]->slip_code)->get();
            $pengurangan = DB::table('slip_pengurangan')->where('slip_code', $data[$i]->slip_code)->get();

            $data[$i]->terima = $terima;
            $data[$i]->pengurangan = $pengurangan;
        }

        return $data;
    }

    public function createSlip($request)
    {
        try {
            $slip_code = generateFiledCode('SLIP');
            $data = [
                'slip_code' => $slip_code,
                'karyawan_code' => $request->karyawan_code,
                'signer_code' => $request->signer_code,
                'gaji_pokok' => $request->gaji_pokok,
                'tunjangan' => $request->tunjangan,
                'gaji_total' => $request->gaji_total,
                'gaji_total_terbilang' => convertToText($request->gaji_total),
                'tgl_slip' => $request->tgl_slip,
                'notes' => $request->catatan,
                'bulan' => date('F', strtotime($request->tgl_slip)),
                'total_terima' => $request->total_terima,
                'total_potongan' => $request->total_potongan,
                'kop_code' => $request->kop_code,
            ];
            DB::table('slip_gaji')->insert($data);

            $terima = $request->terima;
            for ($i = 0; $i < count($terima); $i++) {
                $dataTerima = [
                    'st_code' => generateFiledCode('ST'),
                    'slip_code' => $slip_code,
                    'judul' => $terima[$i]['nama'],
                    'jumlah' => $terima[$i]['biaya'],
                    'disabled' => $terima[$i]['disabled'],
                ];
                DB::table('slip_terima')->insert($dataTerima);
            }

            $pengurangan = $request->pengurangan;
            for ($i = 0; $i < count($pengurangan); $i++) {
                $dataPengurangan = [
                    'st_code' => generateFiledCode('ST'),
                    'slip_code' => $slip_code,
                    'judul' => $pengurangan[$i]['nama'],
                    'jumlah' => $pengurangan[$i]['biaya'],
                    'disabled' => $pengurangan[$i]['disabled'],
                ];
                DB::table('slip_pengurangan')->insert($dataPengurangan);
            }

            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false, 'msg' => $e->getMessage()];
        }
    }

    public function editSlip($request)
    {
        try {
            $slip_code = $request->slip_code;
            $data = [
                'karyawan_code' => $request->karyawan_code,
                'signer_code' => $request->signer_code,
                'gaji_pokok' => $request->gaji_pokok,
                'tunjangan' => $request->tunjangan,
                'gaji_total' => $request->gaji_total,
                'gaji_total_terbilang' => convertToText($request->gaji_total),
                'tgl_slip' => $request->tgl_slip,
                'notes' => $request->catatan,
                'bulan' => date('F', strtotime($request->tgl_slip)),
                'total_terima' => $request->total_terima,
                'total_potongan' => $request->total_potongan,
                'kop_code' => $request->kop_code,
            ];
            DB::table('slip_gaji')->where('slip_code', $slip_code)->update($data);

            DB::table('slip_terima')->where('slip_code', $slip_code)->delete();
            $terima = $request->terima;
            for ($i = 0; $i < count($terima); $i++) {
                $dataTerima = [
                    'st_code' => generateFiledCode('ST'),
                    'slip_code' => $slip_code,
                    'judul' => $terima[$i]['nama'],
                    'jumlah' => $terima[$i]['biaya'],
                    'disabled' => $terima[$i]['disabled'],
                ];
                DB::table('slip_terima')->insert($dataTerima);
            }

            DB::table('slip_pengurangan')->where('slip_code', $slip_code)->delete();
            $pengurangan = $request->pengurangan;
            for ($i = 0; $i < count($pengurangan); $i++) {
                $dataPengurangan = [
                    'st_code' => generateFiledCode('ST'),
                    'slip_code' => $slip_code,
                    'judul' => $pengurangan[$i]['nama'],
                    'jumlah' => $pengurangan[$i]['biaya'],
                    'disabled' => $pengurangan[$i]['disabled'],
                ];
                DB::table('slip_pengurangan')->insert($dataPengurangan);
            }

            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false, 'msg' => $e->getMessage()];
        }
    }

    public function deleteSlip($code)
    {
        try {
            DB::table('slip_gaji')->where('slip_code', $code)->delete();
            DB::table('slip_terima')->where('slip_code', $code)->delete();
            DB::table('slip_pengurangan')->where('slip_code', $code)->delete();

            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false, 'msg' => $e->getMessage()];
        }
    }

    public function detailSlip($slip_code)
    {
        $select = [
            'slip_gaji.*',
            'k.karyawan_code',
            'k.gaji_pokok',
            'k.tunjangan',
            'k.nama as karyawan_nama',
            's.*',
            's.name as signer_name',
        ];
        $data = DB::table('slip_gaji')
            ->select($select)
            ->leftJoin('signer as s', 's.signer_code', '=', 'slip_gaji.signer_code')
            ->leftJoin('karyawans as k', 'k.karyawan_code', '=', 'slip_gaji.karyawan_code')
            ->where('slip_gaji.slip_code', $slip_code)
            ->first();

        $penambahan = DB::table('slip_terima')->where('slip_code', $slip_code)->get(['st_code', 'slip_code', 'judul as nama', 'jumlah as biaya', 'disabled']);
        for ($i = 0; $i < count($penambahan); $i++) {
            $penambahan[$i]->index = $i;
        }

        $pengurangan = DB::table('slip_pengurangan')->where('slip_code', $slip_code)->get(['st_code', 'slip_code', 'judul as nama', 'jumlah as biaya', 'disabled']);
        for ($i = 0; $i < count($pengurangan); $i++) {
            $pengurangan[$i]->index = $i;
        }

        $data->penambahan = $penambahan;
        $data->pengurangan = $pengurangan;

        return $data;
    }

    public function exportSlip($request)
    {
        $data = Excel::store(new SlipExport($request->year), 'public/export/Penggajian Karyawan.xlsx');
        Storage::putFile('exports', storage_path('app/public/export/Penggajian Karyawan.xlsx'));

        $file = asset('storage/export/Penggajian Karyawan.xlsx');
        return ['success' => true, 'data' => (object)[$file]];
    }
}
