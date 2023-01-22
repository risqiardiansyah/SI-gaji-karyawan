<?php

namespace App\Http\Controllers\api\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Auth;
use App\Http\Repositories\ProfileRepository;

class profileController extends ApiController
{
    protected $profile;

    public function __construct(ProfileRepository $profile)
    {
        $this->profile = $profile;
    }
    public function getProfileUser()
    {
        $user_id = Auth::user()->id;

        $profile = $this->profile->getProfileUser($user_id);
        if ($profile) {
            return $this->sendResponse(0, "Berhasil", $profile);
        } else {
            return $this->sendError(2, 'Error');
        }
    }

    public function updateProfileUser(Request $request)
    {
        $id_user = Auth::user()->id;
        $update = $this->profile->updateProfileUser($id_user, $request);
        if ($update) {
            return $this->sendResponse(0, "Berhasil", []);
        } else {
            return $this->sendError(2, 'Error');
        }
    }

    public function getProfileKaryawan()
    {
        $user_id = Auth::user()->id;

        $profile = $this->profile->getProfileKaryawan($user_id);
        if ($profile) {
            return $this->sendResponse(0, "Berhasil", $profile);
        } else {
            return $this->sendError(2, 'Error');
        }
    }

    public function updateProfileKaryawan(Request $request)
    {
        $id_user = Auth::user()->id;
        $update = $this->profile->updateProfileKaryawan($id_user, $request);
        if ($update) {
            return $this->sendResponse(0, "Berhasil", []);
        } else {
            return $this->sendError(2, 'Error');
        }
    }
}
