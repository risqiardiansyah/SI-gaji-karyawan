<?php

namespace App\Http\Repositories;

use App\Http\Resources\KaryawanDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PDF;
use App\Http\Resources\UserDetail;
use Illuminate\Support\Facades\Storage;

class ProfileRepository
{
    public function getProfileUser($user_id)
    {
        $select = [
            'id',
            'name',
            'email',
            'address_user',
            'company_user',
            'province_user',
            'city_user',
            'role',
            'profile',
            'users_code',
        ];
        $data = DB::table('users')
            ->where('id', $user_id)
            ->first($select);
        $data = (collect($data)->count()) ? new UserDetail($data) : false;
        return $data;
    }

    public function updateProfileUser($id_user, $request)
    {
        $pict = $request->profile_old;
        if (!empty($request->profile)) {
            $file_name = $this->generateFiledCode('PROFILE') . '.png';
            $insert_image = Storage::disk('public')->put($file_name, $this->normalizeAndDecodeBase64Photo($request->profile));
            $pict = $file_name;
        }
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'address_user' => $request->address_user,
            'company_user' => $request->company_user,
            'province_user' => $request->province_user,
            'city_user' => $request->city_user,
            'profile' => $pict
        ];

        return DB::table('users')->where('id', $id_user)->update($data);
    }

    public function getProfileKaryawan($user_id)
    {
        $data = DB::table('karyawans')
            ->where('id', $user_id)
            ->first();
        $data = (collect($data)->count()) ? new KaryawanDetail($data) : false;
        return $data;
    }

    public function updateProfileKaryawan($id_user, $request)
    {
        // $pict = $request->profile_old;
        // if (!empty($request->profile)) {
        //     $file_name = $this->generateFiledCode('PROFILE') . '.png';
        //     $insert_image = Storage::disk('public')->put($file_name, $this->normalizeAndDecodeBase64Photo($request->profile));
        //     $pict = $file_name;
        // }
        $data = [
            'nama' => $request->nama,
            // 'email' => $request->email,
            'telp' => $request->telp,
        ];

        return DB::table('karyawans')->where('id', $id_user)->update($data);
    }

    public function normalizeAndDecodeBase64Photo($base64Data)
    {
        $replaceList = array(
            'data:image/jpeg;base64,',
            'data:image/jpg;base64,',
            'data:image/png;base64,',
            '[protected]',
            '[removed]',
        );
        $base64Data = str_replace($replaceList, '', $base64Data);

        return base64_decode($base64Data);
    }

    public function generateFiledCode($code)
    {
        $result = $code . '-' . date('s') . date('y') . date('i') . date('m') . date('h') . date('d') . mt_rand(1000000, 9999999);

        return $result;
    }
}
