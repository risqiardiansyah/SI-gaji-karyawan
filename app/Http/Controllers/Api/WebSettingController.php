<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Http\Repositories\WebSettingRepository;
use App\Http\Repositories\KaryawanRepository;
use App\Http\Repositories\DaftarSignerRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class WebSettingController extends ApiController
{
    protected $setting;

    public function __construct(WebSettingRepository $setting)
    {
        $this->setting = $setting;
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

    public function dataKopSurat(Request $request)
    {
        $data = $this->setting->dataKopSurat($request);

        if ($data) {
            return $this->sendResponse(0, 'Success', $data);
        } else {
            return $this->sendResponse(2, 'Kosong', []);
        }
    }

    public function detailKopSurat($code)
    {
        $data = $this->setting->detailKopSurat($code);

        if ($data) {
            return $this->sendResponse(0, 'Success', $data);
        } else {
            return $this->sendResponse(2, 'Kosong', []);
        }
    }

    public function addKopSurat(Request $request)
    {
        $edit = $this->setting->addKopSurat($request);

        if ($edit['success']) {
            return $this->sendResponse(0, 'Success', []);
        } else {
            return $this->sendError(2, 'Gagal', $edit);
        }
    }

    public function editKopSurat(Request $request)
    {
        $edit = $this->setting->editKopSurat($request);

        if ($edit['success']) {
            return $this->sendResponse(0, 'Success', []);
        } else {
            return $this->sendError(2, 'Gagal', $edit);
        }
    }

    public function deleteKopSurat($code)
    {
        $edit = $this->setting->deleteKopSurat($code);

        if ($edit['success']) {
            return $this->sendResponse(0, 'Success', []);
        } else {
            return $this->sendError(2, 'Gagal', $edit);
        }
    }

    public function editCatatanQuotation(Request $request)
    {
        $rules = [
            'bahasa' => 'required',
            'catatan' => 'required'
        ];

        $validator = $this->validateThis($request, $rules);
        if ($validator->fails()) {
            return $this->sendError(1, 'Params not complete', $this->validationMessage($validator->errors()));
        }

        $edit = $this->setting->editCatatanQuotation($request);

        if ($edit['success']) {
            return $this->sendResponse(0, 'Success', []);
        } else {
            return $this->sendError(2, 'Gagal', $edit);
        }
    }

    public function getCatatanQuotation()
    {
        $get = $this->setting->getCatatanQuotation();

        if ($get) {
            return $this->sendResponse(0, 'Success', $get);
        } else {
            return $this->sendError(2, 'Gagal', []);
        }
    }

    public function getSettingPerjanjian(Request $request)
    {
        $get = $this->setting->getSettingPerjanjian($request);

        if ($get) {
            return $this->sendResponse(0, 'Success', $get);
        } else {
            return $this->sendError(2, 'Gagal', []);
        }
    }

    public function createSettingPerjanjian(Request $request)
    {
        $create = $this->setting->createSettingPerjanjian($request);

        if ($create['success']) {
            return $this->sendResponse(0, 'Success', []);
        } else {
            return $this->sendError(2, 'Gagal', $create);
        }
    }

    public function deleteSettingPerjanjian($idx)
    {
        $create = $this->setting->deleteSettingPerjanjian($idx);

        if ($create['success']) {
            return $this->sendResponse(0, 'Success', []);
        } else {
            return $this->sendError(2, 'Gagal', $create);
        }
    }

    public function getOffice(Request $request)
    {
        $get = $this->setting->getOffice($request);

        if ($get) {
            return $this->sendResponse(0, 'Success', $get);
        } else {
            return $this->sendError(2, 'Gagal', []);
        }
    }

    public function getOfficeList(Request $request)
    {
        $get = $this->setting->getOfficeList($request);

        if ($get) {
            return $this->sendResponse(0, 'Success', $get);
        } else {
            return $this->sendError(2, 'Gagal', []);
        }
    }

    public function addOffice(Request $request)
    {
        $create = $this->setting->addOffice($request);

        if ($create['success']) {
            return $this->sendResponse(0, 'Success', []);
        } else {
            return $this->sendError(2, 'Gagal', $create);
        }
    }

    public function editOffice(Request $request)
    {
        $create = $this->setting->editOffice($request);

        if ($create['success']) {
            return $this->sendResponse(0, 'Success', []);
        } else {
            return $this->sendError(2, 'Gagal', $create);
        }
    }

    public function deleteOffice($idx)
    {
        $create = $this->setting->deleteOffice($idx);

        if ($create['success']) {
            return $this->sendResponse(0, 'Success', []);
        } else {
            return $this->sendError(2, 'Gagal', $create);
        }
    }

    public function officeLocation()
    {
        $get = $this->setting->officeLocation();

        if ($get['success']) {
            return $this->sendResponse(0, 'Success', $get);
        } else {
            return $this->sendError(2, 'Gagal', $get);
        }
    }

    public function officeOperationTime()
    {
        $get = $this->setting->officeOperationTime();

        if ($get['success']) {
            return $this->sendResponse(0, 'Success', $get);
        } else {
            return $this->sendError(2, 'Gagal', $get);
        }
    }

    public function getSettingPerjanjianKontrak(Request $request)
    {
        $get = $this->setting->getSettingPerjanjianKontrak($request);

        if ($get) {
            return $this->sendResponse(0, 'Success', $get);
        } else {
            return $this->sendError(2, 'Gagal', []);
        }
    }

    public function createSettingPerjanjianKontrak(Request $request)
    {
        $create = $this->setting->createSettingPerjanjianKontrak($request);

        if ($create['success']) {
            return $this->sendResponse(0, 'Success', []);
        } else {
            return $this->sendError(2, 'Gagal', $create);
        }
    }

    public function deleteSettingPerjanjianKontrak($idx)
    {
        $create = $this->setting->deleteSettingPerjanjianKontrak($idx);

        if ($create['success']) {
            return $this->sendResponse(0, 'Success', []);
        } else {
            return $this->sendError(2, 'Gagal', $create);
        }
    }

    public function getTunjangan(Request $request)
    {
        $get = $this->setting->getTunjangan($request);

        if ($get) {
            return $this->sendResponse(0, 'Success', $get);
        } else {
            return $this->sendError(2, 'Gagal', []);
        }
    }

    public function getTunjanganList(Request $request)
    {
        $get = $this->setting->getTunjanganList($request);

        if ($get) {
            return $this->sendResponse(0, 'Success', $get);
        } else {
            return $this->sendError(2, 'Gagal', []);
        }
    }

    public function addTunjangan(Request $request)
    {
        $create = $this->setting->addTunjangan($request);

        if ($create['success']) {
            return $this->sendResponse(0, 'Success', []);
        } else {
            return $this->sendError(2, 'Gagal', $create);
        }
    }

    public function editTunjangan(Request $request)
    {
        $create = $this->setting->editTunjangan($request);

        if ($create['success']) {
            return $this->sendResponse(0, 'Success', []);
        } else {
            return $this->sendError(2, 'Gagal', $create);
        }
    }

    public function deleteTunjangan($tj_code)
    {
        $create = $this->setting->deleteTunjangan($tj_code);

        if ($create['success']) {
            return $this->sendResponse(0, 'Success', []);
        } else {
            return $this->sendError(2, 'Gagal', $create);
        }
    }

    public function test()
    {
        dd(env('JWT_SECRET'));
        $email = 'rsqard@gmail.com';
        $sender = 'incompanyk@gmail.com';
        $datamail = [
            'name' => "KIKI",
        ];
        $send = Mail::send('email.success_verified', $datamail, function ($message) use ($email, $sender) {
            $message->from($sender, 'Alan Desa');
            $message->subject('Alan Desa - Konfirmasi Email !');
            $message->setTo($email);
        });
        dd($send);

        return view('dashboard.tipe-surat.penilaian.mingguan');
        dd(env('MAIL_HOST'));
        $create = $this->setting->test();

        if ($create['success']) {
            return $this->sendResponse(0, 'Success', []);
        } else {
            return $this->sendError(2, 'Gagal', $create);
        }
    }

    public function getStatistikCRM(Request $request)
    {
        $get = $this->setting->getStatistikCRM($request);

        if ($get) {
            return $this->sendResponse(0, 'Success', $get);
        } else {
            return $this->sendResponse(4, 'Empty', []);
        }
    }

    public function verifyScan(Request $request)
    {
        $type = $request->type;
        $code = $request->code;

        try {
            if (!isset($type) || $type == 'signer') {
                $type = 'signer';
                $user = DB::table('signer')->where('signer_code', $code)->first();

                if (!empty($user)) {
                    return view(
                        'scan.verify',
                        compact('user', 'code', 'type')
                    );
                } else {
                    return view(
                        'scan.verify',
                        compact('code')
                    );
                }
            } else if ($type == 'users') {
                $user = DB::table('users')
                    ->leftJoin('karyawans', 'karyawans.karyawan_code', '=', 'users.karyawan_code')
                    ->where('users_code', $code)
                    ->first(['users.users_code', 'users.name', 'karyawans.nama as karyawan_nama', 'karyawans.posisi']);

                if (!empty($user)) {
                    return view(
                        'scan.verify',
                        compact('user', 'code', 'type')
                    );
                } else {
                    return view(
                        'scan.verify',
                        compact('code')
                    );
                }
            } else if ($type == 'karyawan') {
                $user = DB::table('karyawans')
                    ->where('karyawan_code', $code)
                    ->first(['karyawans.karyawan_code', 'karyawans.nama', 'karyawans.posisi']);

                if (!empty($user)) {
                    return view(
                        'scan.verify',
                        compact('user', 'code', 'type')
                    );
                } else {
                    return view(
                        'scan.verify',
                        compact('code')
                    );
                }
            }
        } catch (\Exception $e) {
            return view(
                'scan.verify',
                compact('code')
            );
        }
    }
}
