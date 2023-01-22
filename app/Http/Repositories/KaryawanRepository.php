<?php

namespace App\Http\Repositories;

use App\Exports\KaryawanExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\KaryawanList;
use App\Http\Resources\KaryawanAll;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class KaryawanRepository
{
    public function getKaryawanList($request)
    {
        $data = DB::table('karyawans');
        if (!empty($request->type) && $request->type != 3) {
            $data = $data->where('type', $request->type);
        }
        if (!empty($request->status)) {
            $status = $request->status == 'active' ? 1 : 0;
            $data = $data->where('aktif_bekerja', $status);
        }
        $data = $data->orderBy('nama', 'ASC')->get();
        $all = KaryawanList::collection($data);

        return $all;
    }

    public function getAllKaryawan()
    {
        $data = DB::table('karyawans')->orderBy('nama', 'ASC')->get();
        $all = KaryawanAll::collection($data);

        return $all;
    }

    public function getOneKaryawan($karyawan_code)
    {
        $data = DB::table('karyawans')->where('karyawan_code', $karyawan_code)->first();
        $all = new KaryawanAll($data);

        return $all;
    }

    public function deleteKaryawan($id)
    {
        return DB::table('karyawans')->where('karyawan_code', $id)->delete();
    }

    public function addKaryawan($request)
    {
        $ktp = '';
        if (!empty($request->foto_ktp)) {
            $ktp = $this->uploadFotoWithFileNameApi($request->foto_ktp, 'KTP');
        }

        $npwp = '';
        if (!empty($request->foto_npwp)) {
            $npwp = $this->uploadFotoWithFileNameApi($request->foto_npwp, 'NPWP');
        }

        $bpjs = '';
        if (!empty($request->foto_bpjs)) {
            $bpjs = $this->uploadFotoWithFileNameApi($request->foto_bpjs, 'BPJS');
        }

        $profile = 'default.png';
        if (!empty($request->foto_profile)) {
            $profile = $this->uploadFotoWithFileNameApi($request->foto_profile, 'KARY');
        }

        $kcode = 'KAR-' . rand(100000000000, 999999999999);
        $data = [
            'karyawan_code' => $kcode,
            'nama' => $request->nama == 'undefined' ? '' : $request->nama,
            'alamat' => $request->alamat == 'undefined' ? '' : $request->alamat,
            'email' => $request->email == 'undefined' ? '' : $request->email,
            'status' => $request->status == 'undefined' ? '' : $request->status,
            'tempat_lahir' => $request->tempat_lahir == 'undefined' ? '' : $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir == 'undefined' ? '' : $request->tanggal_lahir,
            'asal_kampus' => $request->asal_kampus == 'undefined' ? '' : $request->asal_kampus,
            'telp' => $request->telp == 'undefined' ? '' : $request->telp,
            'nik' => $request->nik == 'undefined' ? '' : $request->nik,
            'type' => $request->type == 'undefined' ? '' : $request->type,
            'foto_ktp' => $ktp,
            'foto_profile' => $profile,
            'foto_bpjs' => $bpjs,
            'foto_npwp' => $npwp,
            'no_bpjs' => $request->no_bpjs == 'undefined' ? '' : $request->no_bpjs,
            'no_npwp' => $request->no_npwp == 'undefined' ? '' : $request->no_npwp,
            'gaji_pokok' => $request->gaji_pokok == 'undefined' ? 0 : $request->gaji_pokok,
            'tunjangan' => $request->tunjangan == 'undefined' ? 0 : $request->tunjangan,
            'posisi' => $request->posisi == 'undefined' ? '' : $request->posisi,
            'atasan_langsung' => $request->atasan_langsung == 'undefined' ? '' : $request->atasan_langsung,
            'hak_cuti' => $request->hak_cuti == 'undefined' ? 12 : $request->hak_cuti,
            'jenis_kelamin' => $request->jenis_kelamin == 'undefined' ? 1 : $request->jenis_kelamin,
        ];

        $dataUser = [
            'users_code' => generateFiledCode('USERS'),
            'karyawan_code' => $kcode,
            'profile' => $profile,
            'name' => $request->nama,
            'email' => $request->email,
            'address_user' => $request->alamat,
        ];
        DB::table('users')->insert($dataUser);

        return DB::table('karyawans')->insert($data);
    }

    public function updateKaryawan($request, $karyawan_code)
    {
        $ktp = $request->foto_ktp_old;
        if (!empty($request->foto_ktp) && $request->foto_ktp != 'undefined') {
            $ktp = $this->uploadFotoWithFileNameApi($request->foto_ktp, 'KTP');
        }

        $profile = $request->foto_profile_old;
        if (!empty($request->foto_profile) && $request->foto_profile != 'undefined') {
            $profile = $this->uploadFotoWithFileNameApi($request->foto_profile, 'KARY');
        }

        $npwp = $request->foto_npwp;
        if (!empty($request->foto_npwp)) {
            $ktp = $this->uploadFotoWithFileNameApi($request->foto_npwp, 'NPWP');
        }

        $bpjs = $request->foto_bpjs;
        if (!empty($request->foto_bpjs)) {
            $ktp = $this->uploadFotoWithFileNameApi($request->foto_bpjs, 'BPJS');
        }

        $data = [
            'nama' => $request->nama == 'undefined' ? '' : $request->nama,
            'alamat' => $request->alamat == 'undefined' ? '' : $request->alamat,
            'email' => $request->email == 'undefined' ? '' : $request->email,
            'status' => $request->status == 'undefined' ? '' : $request->status,
            'tempat_lahir' => $request->tempat_lahir == 'undefined' ? '' : $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir == 'undefined' ? '' : $request->tanggal_lahir,
            'asal_kampus' => $request->asal_kampus == 'undefined' ? '' : $request->asal_kampus,
            'telp' => $request->telp == 'undefined' ? '' : $request->telp,
            'nik' => $request->nik == 'undefined' ? '' : $request->nik,
            'type' => $request->type == 'undefined' ? '' : $request->type,
            'foto_ktp' => $ktp,
            'foto_profile' => $profile,
            'foto_bpjs' => $bpjs,
            'foto_npwp' => $npwp,
            'no_bpjs' => $request->no_bpjs == 'undefined' ? '' : $request->no_bpjs,
            'no_npwp' => $request->no_npwp == 'undefined' ? '' : $request->no_npwp,
            'gaji_pokok' => $request->gaji_pokok == 'undefined' ? 0 : $request->gaji_pokok,
            'tunjangan' => $request->tunjangan == 'undefined' ? 0 : $request->tunjangan,
            'posisi' => $request->posisi == 'undefined' ? '' : $request->posisi,
            'aktif_bekerja' => $request->aktif_bekerja == 'undefined' ? 0 : $request->aktif_bekerja,
            'atasan_langsung' => $request->atasan_langsung == 'undefined' ? '' : $request->atasan_langsung,
            'hak_cuti' => $request->hak_cuti == 'undefined' ? 12 : $request->hak_cuti,
            'jenis_kelamin' => $request->jenis_kelamin == 'undefined' ? 1 : $request->jenis_kelamin,
        ];

        return DB::table('karyawans')->where('karyawan_code', $karyawan_code)->update($data);
    }

    public function exportKaryawan($request)
    {
        $data = Excel::store(new KaryawanExport($request), 'public/export/Karyawan.xlsx');
        Storage::putFile('exports', storage_path('app/public/export/Karyawan.xlsx'));

        $file = asset('storage/export/Karyawan.xlsx');
        return ['success' => true, 'data' => (object)[$file]];
    }

    function uploadFotoWithFileNameApi($base64Data, $file_name)
    {
        $file_name = generateFiledCode($file_name) . '.png';

        $insert_image = Storage::disk('public')->put('karyawan/' . $file_name, $this->normalizeAndDecodePhoto($base64Data));

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
}
