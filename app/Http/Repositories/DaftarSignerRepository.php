<?php

namespace App\Http\Repositories;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\User;
use Carbon\Carbon;
use App\Http\Resources\SignerList;

class DaftarSignerRepository
{
    public function getSignerList()
    {
        $data = DB::table('signer')->get();
        $all = SignerList::collection($data);

        return $all;
    }

    public function getAllSigner()
    {
        $data = DB::table('signer')->get();

        return $data;
    }

    public function deleteSigner($id)
    {
        return DB::table('signer')->where('signer_code', $id)->delete();
    }

    public function getSignerData($id)
    {
        return DB::table('signer')->where('signer_code', $id)->first();
    }

    public function addSigner($request)
    {
        $data = [
            'signer_code' => 'SG-' . rand(100000000000, 999999999999),
            'name' => $request->name,
            'instansi' => $request->instansi,
            'position' => $request->position,
            'address' => $request->address,
            'nik' => $request->nik,
            'npwp' => $request->npwp,
        ];

        return DB::table('signer')->insert($data);
    }

    public function updateSigner($request, $signer_code)
    {
        $data = [
            'name' => $request->name,
            'instansi' => $request->instansi,
            'position' => $request->position,
            'address' => $request->address,
            'nik' => $request->nik,
            'npwp' => $request->npwp,
        ];

        return DB::table('signer')->where('signer_code', $signer_code)->update($data);
    }
}
