<?php

namespace App\Http\Repositories;

use App\Http\Resources\KopSurat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Mockery\Undefined;

class WebSettingRepository
{
    public function dataKopSurat($request)
    {
        $type = $request->type;
        if ($type == 'standar') {
            $data = DB::table('kop_surat')->get();

            return KopSurat::collection($data);
        } else if ($type == 'list') {
            $data = DB::table('kop_surat')->get(['kop_code as value', 'judul as label']);

            return $data;
        }
    }

    public function detailKopSurat($code)
    {
        $data = DB::table('kop_surat')->where('kop_code', $code)->first();

        return new KopSurat($data);
    }

    public function addKopSurat($request)
    {
        try {
            $kop_code = generateFiledCode('KOP');
            $head = $this->uploadKopWithFileNameApi($request->head, $kop_code . '-header');
            $foot = $this->uploadKopWithFileNameApi($request->foot, $kop_code . '-footer');

            $data = [
                'kop_code' => $kop_code,
                'judul' => $request->judul,
                'header' => $head,
                'footer' => $foot,
            ];
            DB::table('kop_surat')->insert($data);

            return ['success' => true, 'message' => 'Data berhasil ditambahkan'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function editKopSurat($request)
    {
        try {
            $head = $request->head_old;
            if ($request->head) {
                $head = $this->uploadKopWithFileNameApi($request->head, $request->kop_code . '-header');
            }

            $foot = $request->foot_old;
            if ($request->foot) {
                $foot = $this->uploadKopWithFileNameApi($request->foot, $request->kop_code . '-footer');
            }

            $data = [
                'judul' => $request->judul,
                'header' => $head,
                'footer' => $foot,
            ];
            DB::table('kop_surat')->where('kop_code', $request->kop_code)->update($data);

            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false, 'msg' => $e->getMessage()];
        }
    }

    public function deleteKopSurat($code)
    {
        try {
            DB::table('kop_surat')->where('kop_code', $code)->delete();

            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false, 'msg' => $e->getMessage()];
        }
    }

    public function editCatatanQuotation($request)
    {
        try {
            $ct = $request->catatan;
            DB::table('catatan_quotation')->delete();
            for ($i = 0; $i < count($ct); $i++) {
                $data = [
                    'text' => $ct[$i]['text'],
                    'bahasa' => $ct[$i]['bahasa']
                ];

                DB::table('catatan_quotation')->insert($data);
            }

            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false, 'msg' => $e->getMessage()];
        }
    }

    public function getCatatanQuotation()
    {
        $data = DB::table('catatan_quotation')->get(['idx', 'text', 'bahasa']);

        return $data;
    }

    function uploadKopWithFileNameApi($base64Data, $file_name)
    {
        $file_name = $file_name . '.png';

        $insert_image = Storage::disk('public')->put('kop/' . $file_name, $this->normalizeAndDecodePhoto($base64Data));

        if ($insert_image) {
            return $file_name;
        }

        return false;
    }

    function normalizeAndDecodePhoto($base64Data)
    {
        $replaceList = array(
            'data:image/jpeg;base64,',
            '/^data:image\/\w+;/^name=\/\w+;base64,/',
            'data:image/jpeg;base64,',
            'data:image/jpg;base64,',
            'data:image/png;base64,',
            'data:image/webp;base64,',
            '[protected]',
            '[removed]',
        );
        $exploded = explode(',', $base64Data);
        if (!isset($exploded[1])) {
            $exploded[1] = null;
        }

        $base64 = $exploded[1];
        $base64Data = str_replace($replaceList, '', $base64Data);

        return base64_decode($base64);
    }

    public function getSettingPerjanjian($request)
    {
        $data = DB::table('mou_pasal');
        if (isset($request->bahasa)) {
            $data = $data->where('bahasa', $request->bahasa);
        }
        $data = $data->get();

        return $data;
    }

    public function createSettingPerjanjian($request)
    {
        try {
            DB::table('mou_pasal')->delete();
            $mou = $request->data;
            for ($i = 0; $i < count($mou); $i++) {
                $data = [
                    'ke' => $i + 1,
                    'judul' => $mou[$i]['judul'],
                    'content' => $mou[$i]['content']
                ];

                DB::table('mou_pasal')->insert($data);
            }

            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false, 'msg' => $e->getMessage()];
        }
    }

    public function deleteSettingPerjanjian($idx)
    {
        if ($idx == 'undefined') {
            return ['success' => false, 'msg' => 'Data Belum Disimpan'];
        }
        try {
            DB::table('mou_pasal')->where('idx', $idx)->delete();

            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false, 'msg' => $e->getMessage()];
        }
    }

    public function getOffice($request)
    {
        $data = DB::table('office')->get();

        return $data;
    }

    public function getOfficeList($request)
    {
        $data = DB::table('office')->get();
        $all = [];
        for ($i = 0; $i < count($data); $i++) {
            $inp = [
                'label' => $data[$i]->office_name,
                'value' => $data[$i]->office_name,
            ];
            array_push($all, $inp);
        }

        return $all;
    }

    public function addOffice($request)
    {
        try {
            $data = [
                'office_name' => $request->office_name
            ];

            DB::table('office')->insert($data);

            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false, 'msg' => $e->getMessage()];
        }
    }

    public function editOffice($request)
    {
        try {
            $data = [
                'office_name' => $request->office_name
            ];

            DB::table('office')->where('idx', $request->idx)->update($data);

            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false, 'msg' => $e->getMessage()];
        }
    }

    public function deleteOffice($idx)
    {
        try {
            DB::table('office')->where('idx', $idx)->delete();

            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false, 'msg' => $e->getMessage()];
        }
    }

    public function officeLocation()
    {
        try {
            $lat = getSetting('office_lat');
            $lng = getSetting('office_lng');

            $data = (object)[
                'lat' => $lat,
                'lng' => $lng
            ];

            return ['success' => true, 'data' => $data];
        } catch (\Exception $e) {
            return ['success' => false, 'msg' => $e->getMessage()];
        }
    }

    public function officeOperationTime()
    {
        try {
            $user = Auth::user();

            $userType = $user->type;
            $type = 'karyawan';
            if ($userType == 2) {
                $type = 'magang';
            }

            if ($userType == 1) {
                $masuk = getSetting('jam_masuk_' . $type);
                $keluar = getSetting('jam_keluar_' . $type);
            } else {
                $masuk = getSetting('jam_masuk_' . $type);
                $keluar = getSetting('jam_keluar_' . $type);
            }

            $data = (object)[
                'masuk' => $masuk,
                'keluar' => $keluar
            ];

            return ['success' => true, 'data' => $data];
        } catch (\Exception $e) {
            return ['success' => false, 'msg' => $e->getMessage()];
        }
    }

    public function getSettingPerjanjianKontrak($request)
    {
        $data = DB::table('sukon_pasal')->get();

        return $data;
    }

    public function createSettingPerjanjianKontrak($request)
    {
        try {
            DB::table('sukon_pasal')->delete();
            $mou = $request->data;
            for ($i = 0; $i < count($mou); $i++) {
                $data = [
                    'ke' => $i + 1,
                    'judul' => $mou[$i]['judul'],
                    'content' => $mou[$i]['content']
                ];

                DB::table('sukon_pasal')->insert($data);
            }

            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false, 'msg' => $e->getMessage()];
        }
    }

    public function deleteSettingPerjanjianKontrak($idx)
    {
        if ($idx == 'undefined') {
            return ['success' => false, 'msg' => 'Data Belum Disimpan'];
        }
        try {
            DB::table('sukon_pasal')->where('idx', $idx)->delete();

            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false, 'msg' => $e->getMessage()];
        }
    }

    public function getTunjangan($request)
    {
        $data = DB::table('tunjangan')->get();
        if (isset($request->unique_code)) {
            $detail = DB::table('surat_penerimaan_kerja')->where('unique_code', $request->unique_code)->first();
            for ($i = 0; $i < count($data); $i++) {
                $all_tj = '';
                // $all_tj .= ', ' . $detail->tunjangan_biasa;
                $all_tj .= ', ' . $detail->tunjangan_privilege;
                $all_tj .= ', ' . $detail->tunjangan_lain;
                $all_tj = explode(', ', $all_tj);

                if (in_array($data[$i]->tj_code, $all_tj)) {
                    $data[$i]->checked = true;
                } else {
                    $data[$i]->checked = false;
                }
            }
        }

        return $data;
    }

    public function getTunjanganList($request)
    {
        $data = DB::table('tunjangan')->get();
        $all = [];
        for ($i = 0; $i < count($data); $i++) {
            $inp = [
                'label' => $data[$i]->tj_name,
                'value' => $data[$i]->tj_code,
            ];
            array_push($all, $inp);
        }

        return $all;
    }

    public function addTunjangan($request)
    {
        try {
            $data = [
                'tj_code' => 'TJ' . rand(10000, 99999),
                'tj_name' => $request->tj_name,
                'type' => $request->type
            ];

            DB::table('tunjangan')->insert($data);

            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false, 'msg' => $e->getMessage()];
        }
    }

    public function editTunjangan($request)
    {
        try {
            $data = [
                'tj_name' => $request->tj_name,
                'type' => $request->type
            ];

            DB::table('tunjangan')->where('tj_code', $request->tj_code)->update($data);

            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false, 'msg' => $e->getMessage()];
        }
    }

    public function deleteTunjangan($tj_code)
    {
        try {
            DB::table('tunjangan')->where('tj_code', $tj_code)->delete();

            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false, 'msg' => $e->getMessage()];
        }
    }

    public function getStatistikCRM($request)
    {
        $users_code = $request->users_code;
        $type = $request->type;
        $ke = $request->ke;
        $all = $request->all;
        $tgl = $request->tgl;

        if (isset($all)) {
            return $this->statistikPerformaAllUser($type, $ke, $tgl, $users_code);
        }

        switch ($type) {
            case 'bulan':
                return $this->statistikPerformaBulan($ke, $users_code);
            case 'tahun':
                return $this->statistikPerformaTahun($ke, $users_code);
            default:
                return false;
        }
    }

    public function statistikPerformaAllUser($type, $ke, $tgl, $users_code)
    {
        $data = DB::table('users')->get();
        $filtered_user = [];
        switch ($type) {
            case 'harix':
                for ($i = 0; $i < count($data); $i++) {
                    $cek = DB::table('pelanggan')->where('created_by', $data[$i]->users_code)->where('deleted', 0)->whereBetween('created_at', [date('Y-m-01 00:00:00'), date('Y-m-31 23:59:00')])->count();
                    if ($cek > 0) {
                        array_push($filtered_user, $data[$i]);
                    }
                }

                $result = [];
                for ($i = 0; $i < count($filtered_user); $i++) {
                    $result[$i]['name'] = $filtered_user[$i]->name;

                    $ditambahkan = DB::table('pelanggan')->where('created_by', $filtered_user[$i]->users_code)->whereBetween('created_at', [date('Y-m-' . $ke . ' 00:00:00'), date('Y-m-' . $ke . ' 23:59:00')])->count();
                    $result[$i]['Total'] = $ditambahkan;
                }

                return $result;
                break;

            case 'bulan':
                $result = [];
                $hari = date("t", strtotime(date('Y-' . $ke . '-d')));

                for ($i = 0; $i < count($data); $i++) {
                    $cek = DB::table('pelanggan')->where('created_by', $data[$i]->users_code)->where('deleted', 0)->whereBetween('created_at', [date('Y-' . $ke . '-01 00:00:00'), date('Y-' . $ke . '-' . $hari . ' 23:59:00')])->count();
                    if ($cek > 0) {
                        array_push($filtered_user, $data[$i]);
                    }
                }

                for ($i = 0; $i < (int)$hari; $i++) {
                    $result[$i]['name'] = 'Tgl ' . ($i + 1);
                }
                for ($i = 0; $i < count($result); $i++) {
                    $hari = ($i + 1) < 10 ? '0' . ($i + 1) : ($i + 1);

                    for ($x = 0; $x < count($filtered_user); $x++) {
                        $ditambahkan = DB::table('pelanggan')->where('created_by', $filtered_user[$x]->users_code)->whereBetween('created_at', [date('Y-' . $ke . '-' . $hari . ' 00:00:00'), date('Y-' . $ke . '-' . $hari . ' 23:59:00')])->count();
                        $result[$i][$filtered_user[$x]->name] = $ditambahkan;
                    }
                }

                return $result;
                break;
            case 'tahun':
                for ($i = 0; $i < count($data); $i++) {
                    $cek = DB::table('pelanggan')->where('created_by', $data[$i]->users_code)->where('deleted', 0)->whereBetween('created_at', [date($ke . '-01-01 00:00:00'), date($ke . '-12-31 23:59:00')])->count();
                    if ($cek > 0) {
                        array_push($filtered_user, $data[$i]);
                    }
                }

                $bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                $result = [];
                for ($i = 0; $i < 12; $i++) {
                    $result[$i]['name'] = $bulan[$i];
                }
                for ($i = 0; $i < count($result); $i++) {
                    $bulan = ($i + 1) < 10 ? '0' . ($i + 1) : ($i + 1);
                    $end_tgl = date("t", strtotime(date($ke . '-' . $bulan . '-d')));

                    for ($x = 0; $x < count($filtered_user); $x++) {
                        $ditambahkan = DB::table('pelanggan')->where('created_by', $filtered_user[$x]->users_code)->whereBetween('created_at', [date($ke . '-' . $bulan . '-01 00:00:00'), date($ke . '-' . $bulan . '-' . $end_tgl . ' 23:59:00')])->count();
                        $result[$i][$filtered_user[$x]->name] = $ditambahkan;
                    }
                }

                return $result;
                break;
            default:
                return [];
                break;
        }
    }

    public function statistikPerformaBulan($bulan, $users_code)
    {
        $result = [];
        $hari = date("t", strtotime(date('Y-' . $bulan . '-d')));

        for ($i = 0; $i < (int)$hari; $i++) {
            $day = date("N", strtotime(date('Y-' . $bulan . '-' . ($i + 1))));
            if ($day > 5) {
                $result[$i]['name'] = 'Tgl ' . ($i + 1) . ' (Libur)';
            } else {
                $result[$i]['name'] = 'Tgl ' . ($i + 1);
            }
        }
        for ($i = 0; $i < count($result); $i++) {
            $hari = ($i + 1) < 10 ? '0' . ($i + 1) : ($i + 1);

            $ditambahkan = DB::table('pelanggan')->where('created_by', $users_code)->whereBetween('created_at', [date('Y-' . $bulan . '-' . $hari . ' 00:00:00'), date('Y-' . $bulan . '-' . $hari . ' 23:59:00')])->count();

            $result[$i]['Total'] = $ditambahkan;
        }

        return $result;
    }

    public function statistikPerformaTahun($ke, $users_code)
    {
        $bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $result = [];
        for ($i = 0; $i < 12; $i++) {
            $result[$i]['name'] = $bulan[$i];
        }
        for ($i = 0; $i < count($result); $i++) {
            $bulan = ($i + 1) < 10 ? '0' . ($i + 1) : ($i + 1);
            $end_tgl = date("t", strtotime(date($ke . '-' . $bulan . '-d')));

            $ditambahkan = DB::table('pelanggan')->where('created_by', $users_code)->whereBetween('created_at', [date($ke . '-' . $bulan . '-01 00:00:00'), date($ke . '-' . $bulan . '-' . $end_tgl . ' 23:59:00')])->count();

            $result[$i]['Total'] = $ditambahkan;
        }

        return $result;
    }
}
