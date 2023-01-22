<?php

namespace App\Http\Controllers\Api\hr;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Http\Repositories\HRDashboardRepository;
use Illuminate\Support\Facades\Validator;

class DashboardController extends ApiController
{
    protected $dashboard;

    public function __construct(HRDashboardRepository $dashboard)
    {
        $this->dashboard = $dashboard;
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

    // public function setAbsensi(Request $request)
    // {
    //     $rules = [
    //         'lat' => 'required',
    //         'lng' => 'required',
    //         'detail_alamat' => 'required',
    //         'type' => 'required|in:1,2' // 1 => Masuk, 2 => Keluar
    //     ];

    //     $validator = $this->validateThis($request, $rules);
    //     if ($validator->fails()) {
    //         return $this->sendError(1, 'Params not complete', $this->validationMessage($validator->errors()));
    //     }

    //     $data = $this->dashboard->setAbsensi($request);

    //     if ($data['success']) {
    //         return $this->sendResponse(0, 'Success', $data);
    //     } else {
    //         return $this->sendError(2, 'Gagal', $data);
    //     }
    // }

    public function getHomeDetail(Request $request)
    {
        $data = $this->dashboard->getHomeDetail($request);

        if ($data) {
            return $this->sendResponse(0, 'Success', $data);
        } else {
            return $this->sendResponse(0, 'Data Kosong', []);
        }
    }
}
