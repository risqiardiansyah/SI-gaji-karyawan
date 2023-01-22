<?php

namespace App\Http\Controllers\Api\hr;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Http\Repositories\AbsenRepository;
use Illuminate\Support\Facades\Validator;

class AbsenController extends ApiController
{
    protected $absen;

    public function __construct(AbsenRepository $absen)
    {
        $this->absen = $absen;
    }

    public function validateThis($request, $rules = array())
    {
        return Validator::make($request->all(), $rules);
    }

    public function validationMessage($validation)
    {
        $validate = collect($validation)->flatten();
        return $validate->values()->all();
    }

    public function setAbsensi(Request $request)
    {
        $rules = [
            'date' => 'required',
            'time_in' => 'required_if:type,masuk',
            'time_out' => 'required_if:type,masuk',
            'karyawan_code' => 'required|exists:karyawans',
            'type' => 'required|in:masuk,alpha,izin'
        ];

        $validator = $this->validateThis($request, $rules);
        if ($validator->fails()) {
            return $this->sendError(1, 'Params not complete', $this->validationMessage($validator->errors()));
        }

        $data = $this->absen->setAbsensi($request);

        if ($data['success']) {
            return $this->sendResponse(0, 'Success', []);
        } else {
            return $this->sendError(2, 'Gagal', $data);
        }
    }

    public function editAbsensi(Request $request)
    {
        $rules = [
            'date' => 'required',
            'absensi_code' => 'required',
            'karyawan_code' => 'required',
            'time_in' => 'required_if:type,masuk',
            'time_out' => 'required_if:type,masuk',
            'type' => 'required|in:masuk,alpha,izin'
        ];

        $validator = $this->validateThis($request, $rules);
        if ($validator->fails()) {
            return $this->sendError(1, 'Params not complete', $this->validationMessage($validator->errors()));
        }

        $data = $this->absen->editAbsensi($request);

        if ($data['success']) {
            return $this->sendResponse(0, 'Success', []);
        } else {
            return $this->sendError(2, 'Gagal', $data);
        }
    }

    public function deleteAbsensi($code)
    {
        $data = $this->absen->deleteAbsensi($code);

        if ($data['success']) {
            return $this->sendResponse(0, 'Success', []);
        } else {
            return $this->sendError(2, 'Gagal', $data);
        }
    }

    public function getAbsensi(Request $request)
    {
        $data = $this->absen->getAbsensi($request);

        if ($data) {
            return $this->sendResponse(0, 'Success', $data);
        } else {
            return $this->sendResponse(0, 'Data Kosong', []);
        }
    }

    public function getDetailAbsensi($code)
    {
        $data = $this->absen->getDetailAbsensi($code);

        if ($data) {
            return $this->sendResponse(0, 'Success', $data);
        } else {
            return $this->sendResponse(0, 'Data Kosong', []);
        }
    }

    public function exportAbsensi(Request $request)
    {
        $rules = [
            'month' => 'required'
        ];

        $validator = $this->validateThis($request, $rules);
        if ($validator->fails()) {
            return $this->sendError(1, 'Params not complete', $this->validationMessage($validator->errors()));
        }

        $data = $this->absen->exportAbsensi($request);

        if ($data['success']) {
            return $this->sendResponse(0, 'Success', $data['data']);
        } else {
            return $this->sendError(2, 'Gagal', $data);
        }
    }
}
