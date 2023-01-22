<?php

namespace App\Http\Controllers\api\SuratMenyurat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Repositories\KaryawanRepository;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Auth;

class KaryawanController extends ApiController
{
    protected $karyawan;

    public function __construct(KaryawanRepository $karyawan)
    {
        $this->karyawan = $karyawan;
    }

    public function getKaryawanList(Request $request)
    {
        $allkaryawan = $this->karyawan->getKaryawanList($request);

        if (count($allkaryawan)  > 0) {
            return $this->sendResponse(0, 'Berhasil', $allkaryawan);
        } elseif (count($allkaryawan) == 0) {
            return $this->sendResponse(0, 'Data kosong');
        } else {
            return $this->sendError(0, 'Error');
        }
    }

    public function getAllKaryawan()
    {
        $allkaryawan = $this->karyawan->getAllKaryawan();

        if (count($allkaryawan)  > 0) {
            return $this->sendResponse(0, 'Berhasil', $allkaryawan);
        } elseif (count($allkaryawan) == 0) {
            return $this->sendResponse(0, 'Data kosong');
        } else {
            return $this->sendError(0, 'Error');
        }
    }

    public function getOneKaryawan($karyawan_code)
    {
        $detailkaryawan = $this->karyawan->getOneKaryawan($karyawan_code);

        if (!empty($detailkaryawan)  > 0) {
            return $this->sendResponse(0, 'Berhasil', $detailkaryawan);
        } else {
            return $this->sendError(0, 'Error');
        }
    }

    public function deleteKaryawan(Request $request)
    {
        $delete = $this->karyawan->deleteKaryawan($request->karyawan_code);

        if ($delete) {
            return $this->sendResponse(0, 'Berhasil', []);
        } else {
            return $this->sendError(0, 'Error');
        }
    }

    public function addKaryawan(Request $request)
    {
        // dd($request->all());
        $delete = $this->karyawan->addKaryawan($request);

        if ($delete) {
            return $this->sendResponse(0, 'Berhasil', []);
        } else {
            return $this->sendError(0, 'Error');
        }
    }

    public function updateKaryawan(Request $request, $karyawan_code)
    {
        $delete = $this->karyawan->updateKaryawan($request, $karyawan_code);

        if ($delete) {
            return $this->sendResponse(0, 'Berhasil', []);
        } else {
            return $this->sendError(0, 'Error');
        }
    }

    public function exportKaryawan(Request $request)
    {
        $data = $this->karyawan->exportKaryawan($request);

        if ($data['success']) {
            return $this->sendResponse(0, 'Success', $data['data']);
        } else {
            return $this->sendError(2, 'Gagal', $data);
        }
    }
}
