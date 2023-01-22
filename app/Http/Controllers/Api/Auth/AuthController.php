<?php

namespace App\Http\Controllers\api\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Middleware;
use Illuminate\Support\Facades\DB;
//data
use App\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Http\Repositories\AuthRepository;
use App\Http\Controllers\ApiController;

class AuthController extends ApiController

{
    protected $authRepository;

    public function __construct(AuthRepository $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    public function login(Request $request)
    {
        $rules = array(
            'email'    => 'required|email',
            'password' => 'required',
        );

        $input = array(
            'email' => $request->email,
            'password' => $request->password
        );

        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            return $this->sendError(1, "Error params", $validator->errors());
        }

        $credentials = array(
            'email' => $input['email'],
            'password' => $input['password'],
        );

        if (!Auth::attempt($credentials)) {
            return $this->sendError(2, "Email atau Password Salah", (object) array());
        }

        $success = Auth::user();

        if ($success->is_deleted) {
            return $this->sendError(2, "Akun dinonaktifkan/dihapus", (object) array());
        }

        $success['api_token'] = Auth::guard()->user()->createToken(Auth::guard()->user()->email)->accessToken;
        unset($success['email_verified_at']);
        unset($success['created_at']);
        unset($success['updated_at']);
        return $this->sendResponse(0, "Login Berhasil", $success);
    }

    public function loginKaryawan(Request $request)
    {
        $rules = array(
            'email'    => 'required|email',
            'password' => 'required',
        );

        $input = array(
            'email' => $request->email,
            'password' => $request->password
        );

        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            return $this->sendError(1, "Error params", $validator->errors());
        }

        $credentials = array(
            'email' => $input['email'],
            'password' => $input['password'],
        );

        if (!Auth::guard(@'karyawan')->attempt($credentials)) {
            return $this->sendError(2, "Email atau Password Salah", (object) array());
        }

        $success = Auth::guard('karyawan')->user();
        $success['api_token'] = Auth::guard('karyawan')->user()->createToken(Auth::guard(@'karyawan')->user()->email)->accessToken;
        unset($success['email_verified_at']);
        unset($success['created_at']);
        unset($success['updated_at']);
        return $this->sendResponse(0, "Login Berhasil", $success);
    }

    public function tokencek(Request $request)
    {
        if (!Auth::check()) {
            Auth::user()->token()->delete();
            return $this->sendError(2, "Unauthorized.", (object) array());
        } else {
            $header = Auth::user($request->header('Authorization'));
            unset($header['email_verified_at']);
            unset($header['created_at']);
            unset($header['updated_at']);
            unset($header['api_token']);

            // return  $this->sendResponse(2, 'Valid Token');
            if ($header) {
                return  $this->sendResponse(0, 'valid Token', $header);
            } else {
                return  $this->sendResponse(2, 'invalid Token');
            }
        }
    }

    public function ViewUserTokenExpired()
    {
        $result = $this->authRepository->getExpiredToken(Auth::user()->send->id());

        if ($result) {
            $result = $this->sendResponse(0, 'Valid Token');
        } else {
            $result = $this->sendError(2, 'Invalid Token');
        }
        return $result;
    }

    public function unauthorized()
    {
        $response = [
            'code' => 401,
            'success' => false,
            'message' => 'Unauthorized'
        ];
        $response['data'] =  [];
        return response()->json($response);
    }

    public function logout()
    {
        if (Auth::check()) {
            Auth::user()->token()->delete();
            return $this->sendResponse(0, "Logout berhasil.", (object) array());
        } else {
            return $this->sendError(2, "Logout gagal.", (object) array());
        }
    }

    public function check()
    {
        $data = ['data' => env('DB_USERNAME')];
        return $this->sendResponse(0, "OK.", $data);
    }
}
