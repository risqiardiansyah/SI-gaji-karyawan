<?php

namespace App\Http\Controllers\api\dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Repositories\DashboardRepository;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class dashboardController extends ApiController
{
    protected $dashboard;

    public function __construct(DashboardRepository $dashboard)
    {
        $this->dashboard = $dashboard;
    }

    public function validator($rules, $params)
    {
        return Validator::make($params, $rules);
    }

    public function getdashboard($idx_buku_kas)
    {
        $user_id = Auth::id();
        $getdashboard = $this->dashboard->getallbukukas($idx_buku_kas, $user_id);
        if (collect($getdashboard)) {
            return $this->sendResponse(0, 19, $getdashboard);
        } else {
            return $this->sendResponse(0, 19, []);
        }
    }

    public function detailkas($idx_buku_kas)
    {
        $user_id = Auth::id();
        $getdashboard = $this->dashboard->getallbukukas($idx_buku_kas, $user_id);
        if (collect($getdashboard)) {
            return $this->sendResponse(0, 19, $getdashboard);
        } else {
            return $this->sendResponse(0, 19, []);
        }
    }

    public function getListUser(Request $request)
    {
        $user_id = Auth::id();
        $getdashboard = $this->dashboard->getListUser($request, $user_id);
        if (collect($getdashboard)) {
            return $this->sendResponse(0, 19, $getdashboard);
        } else {
            return $this->sendResponse(0, 19, []);
        }
    }

    public function editDataUser(Request $request)
    {
        $edit = $this->dashboard->editDataUser($request);
        if (collect($edit)) {
            return $this->sendResponse(0, 'Berhasil', []);
        } else {
            return $this->sendError(0, 'gagal');
        }
    }

    public function createDataUser(Request $request)
    {
        $rules = [
            'email' => 'required',
            'name' => 'required',
            'role' => 'required',
            'password' => 'required'
        ];

        $thisData = [
            'email' => $request->email,
            'name' => $request->name,
            'role' => $request->role,
            'password' => $request->password
        ];

        $validate = $this->validator($rules, $thisData);
        if ($validate->fails())
            return $this->sendError(1, "Error params", $validate->errors());

        $create = $this->dashboard->createDataUser($request);
        if (collect($create)) {
            return $this->sendResponse(0, 'Berhasil', []);
        } else {
            return $this->sendError(0, 'gagal');
        }
    }

    public function changePasswordUser(Request $request)
    {
        $rules = [
            'password' => 'required',
            'old_password' => 'required'
        ];

        $thisData = [
            'password' => $request->password,
            'old_password' => $request->old_password
        ];

        $validate = $this->validator($rules, $thisData);
        if ($validate->fails())
            return $this->sendError(1, "Error params", $validate->errors());

        $user_id = Auth::id();

        $change = $this->dashboard->changePasswordUser($request, $user_id);
        if (collect($change)) {
            return $this->sendResponse(0, 'Berhasil', []);
        } else {
            return $this->sendError(0, 'gagal');
        }
    }

    public function deleteDataUser($id)
    {
        $delete = $this->dashboard->deleteDataUser($id);
        if (collect($delete)) {
            return $this->sendResponse(0, 'Berhasil', []);
        } else {
            return $this->sendError(0, 'gagal');
        }
    }
}
