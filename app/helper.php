<?php

use Illuminate\Support\Facades\Storage;

use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use LaravelFCM\Message\Topics;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

if (!function_exists('generateFiledCode')) {
    function generateFiledCode($code)
    {
        $result = $code . '-' . date('s') . date('y') . date('i') . date('m') . date('h') . date('d') . mt_rand(1000000, 9999999);

        return $result;
    }
}

if (!function_exists('uploadFotoWithFileNameApi')) {
    function uploadFotoWithFileNameApi($base64Data, $file_prefix_name)
    {
        $file_name = generateFiledCode($file_prefix_name) . '.png';

        $insert_image = Storage::disk('public')->put($file_name, normalizeAndDecodeBase64PhotoApi($base64Data));

        if ($insert_image) {
            return $file_name;
        }

        return false;
    }

    function normalizeAndDecodeBase64PhotoApi($base64Data)
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

if (!function_exists('uploadKopWithFileNameApi')) {
    function uploadKopWithFileNameApi($base64Data, $file_name)
    {
        $file_name = $file_name . '.png';

        $insert_image = Storage::disk('public')->put('kop' . $file_name, normalizeAndDecodePhoto($base64Data));

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


if (!function_exists('generateBreadcrumb')) {
    function generateBreadcrumb($data)
    {
        if (empty($data)) {
            return null;
        }

        $result = '<ul class="m-subheader__breadcrumbs m-nav m-nav--inline">
					<li class="m-nav__item m-nav__item--home">
						<a href="' . url('/') . '" class="m-nav__link m-nav__link--icon">
							<i class="m-nav__link-icon la la-home"></i>
						</a>
					</li>';

        foreach ($data as $uri => $item) {
            if ($uri == '!end!' || $uri == '#') {
                $result .= '<li class="m-nav__separator">-</li>
                            <a class="m-nav__link">
                                <span class="m-nav__link-text">' . $item . '</span>
                            </a>';

                continue;
            }
            $result .= '<li class="m-nav__separator">-</li>
						<li class="m-nav__item">
							<a href="' . url($uri) . '" class="m-nav__link">
								<span class="m-nav__link-text">' . $item . '</span>
							</a>
						</li>';
        }

        $result .= '</ul>';

        return $result;
    }
}

/*
 *  Encode base64 image and save to Storage
 */
if (!function_exists('uploadFotoWithFileName')) {
    function uploadFotoWithFileName($base64Data, $file_prefix_name)
    {
        $file_name = generateFiledCode($file_prefix_name) . '.png';

        $insert_image = Storage::disk('public')->put($file_name, normalizeAndDecodeBase64Photo($base64Data));
        dd($insert_image);
        if ($insert_image) {
            return $file_name;
        }

        return false;
    }

    function normalizeAndDecodeBase64Photo($base64Data)
    {
        $replaceList = array(
            'data:image/jpeg;base64,',
            'data:image/jpg;base64,',
            'data:image/png;base64,',
            '[protected]',
            '[removed]',
        );
        $base64Data = str_replace($replaceList, '', $base64Data);

        dd($base64Data);

        return base64_decode($base64Data);
    }
}

if (!function_exists('validationMessage')) {
    function validationMessage($validation)
    {
        $validate = collect($validation)->flatten();
        return $validate->values()->all();
    }
}

if (!function_exists('base64ToPng')) {
    function base64ToPng($base64, $title)
    {
        $file_name = generateFiledCode($title) . '.png';
        $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64));
        $insert_image = Storage::disk('public')->put($file_name, $data);


        if ($insert_image) {
            return $file_name;
        }

        return false;
    }
}

if (!function_exists('uploadImg')) {
    function uploadImg($file, $title)
    {
        $file_name = generateFiledCode($title) . '.png';
        $filenameWithExt = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $fileNameToStore = $file_name . '_' . time() . '.' . $extension;
        $path = $file->storeAs('public/cover_images', $fileNameToStore);


        if ($path) {
            return $fileNameToStore;
        }

        return false;
    }
}
/**
 * Normalize date input
 */
if (!function_exists('normalizeDateInput')) {
    function normalizeDateInput($date)
    {
        $invalidList = array(
            '0000-00-00'
        );

        if (in_array($date, $invalidList)) {
            return null;
        }

        return $date;
    }
}


/**
 * Easy to read date for user
 */
if (!function_exists('dateForUser')) {
    function dateForUser($date)
    {
        $date = normalizeDateInput($date);

        if ($date) {
            return date('d F Y', strtotime($date));
        }
        return $date;
    }
}


if (!function_exists('SMSverifyRequest')) {
    function SMSverifyRequest($phone)
    {
        $url = 'https://api.nexmo.com/verify/json?' . http_build_query([
            'api_key' => env('SMS_API_KEY', null),
            'api_secret' => env('SMS_API_SECRET', null),
            'number' => $phone,
            'brand' => 'ON-VERIFY'
        ]);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);

        return response()->json(['data' => json_decode($response)]);
    }
}

if (!function_exists('SMSverifyCheck')) {
    function SMSverifyCheck($request_id, $code)
    {
        $url = 'https://api.nexmo.com/verify/check/json?' . http_build_query([
            'api_key' => env('SMS_API_KEY', null),
            'api_secret' => env('SMS_API_SECRET', null),
            'request_id' => $request_id,
            'code' => $code
        ]);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);

        return response()->json(['data' => json_decode($response)]);
    }
}

if (!function_exists('checkVerificationEmail')) {
    function checkVerificationEmail($token, $code, $email)
    {
        $where = ['token' => $token, 'email' => $email];
        $verify = DB::table('verification_email')->where($where)->first();

        if ($verify) {
            $checkExpires = ($verify->expires <= now() ? FALSE : TRUE);

            if (!$checkExpires) {

                return "TOKEN_EXPIRED";
            } else if ($code != $verify->code) {
                return "CODE_NOT_MATCH";
            } else {
                DB::table('verification_email')->where($where)->update(['verified_at' => now()]);
            }
        }
    }
}

if (!function_exists('getSetting')) {
    function getSetting($column)
    {
        $data = DB::table('web_setting')->first([$column]);

        return $data->$column;
    }
}

if (!function_exists('getInvoiceCode')) {
    function getInvoiceCode()
    {
        $month = date('m');
        $year = date('Y');
        $no = 0;
        $jml = DB::table('tbl_invoice')->whereMonth('jatuh_tempo_invoice', $month)->count();
        $total = $jml + 1;
        if (strlen((string)$total) == 1) {
            $no = '00' . $total;
        } elseif (strlen((string)$total) == 2) {
            $no = '0' . $total;
        } else {
            $no = $total;
        }

        return 'INV-' . $no . $month . $year;
    }
}

if (!function_exists('weekOfMonth')) {
    function weekOfMonth($date)
    {
        $firstOfMonth = strtotime(date("Y-m-01", $date));
        return weekOfYear($date) - weekOfYear($firstOfMonth) + 1;
    }
}


if (!function_exists('weekOfYear')) {
    function weekOfYear($date)
    {
        $weekOfYear = intval(date("W", $date));
        if (date('n', $date) == "1" && $weekOfYear > 51) {
            return 0;
        } elseif (date('n', $date) == "12" && $weekOfYear == 1) {
            return 53;
        } else {
            return $weekOfYear;
        }
    }
}

if (!function_exists('convertToText')) {
    function convertToText($nilai)
    {
        if ($nilai < 0) {
            $hasil = "minus " . trim(penyebut($nilai));
        } else {
            $hasil = trim(penyebut($nilai));
        }
        return $hasil;
    }

    function penyebut($nilai)
    {
        $nilai = abs($nilai);
        $huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
        $temp = "";
        if ($nilai < 12) {
            $temp = " " . $huruf[$nilai];
        } else if ($nilai < 20) {
            $temp = penyebut($nilai - 10) . " belas";
        } else if ($nilai < 100) {
            $temp = penyebut($nilai / 10) . " puluh" . penyebut($nilai % 10);
        } else if ($nilai < 200) {
            $temp = " seratus" . penyebut($nilai - 100);
        } else if ($nilai < 1000) {
            $temp = penyebut($nilai / 100) . " ratus" . penyebut($nilai % 100);
        } else if ($nilai < 2000) {
            $temp = " seribu" . penyebut($nilai - 1000);
        } else if ($nilai < 1000000) {
            $temp = penyebut($nilai / 1000) . " ribu" . penyebut($nilai % 1000);
        } else if ($nilai < 1000000000) {
            $temp = penyebut($nilai / 1000000) . " juta" . penyebut($nilai % 1000000);
        } else if ($nilai < 1000000000000) {
            $temp = penyebut($nilai / 1000000000) . " milyar" . penyebut(fmod($nilai, 1000000000));
        } else if ($nilai < 1000000000000000) {
            $temp = penyebut($nilai / 1000000000000) . " trilyun" . penyebut(fmod($nilai, 1000000000000));
        }
        return $temp;
    }
}

if (!function_exists('uploadFiles')) {
    function uploadFiles($file, $title, $ext, $dir = '')
    {
        $fileNameToStore = generateFiledCode($title) . '.' . $ext;
        $path = Storage::disk('public')->put($dir . '/' . $fileNameToStore, normalizeAndDecodeBase64FileApi($file));

        if ($path) {
            return $fileNameToStore;
        }

        return false;
    }

    function normalizeAndDecodeBase64FileApi($base64Data)
    {
        $replaceList = array(
            'data:@file/octet-stream;base64,',
            'data:@file/msword;base64,',
            'data:@file/vnd.oasis.opendocument.text;base64,',
            'data:@file/vnd.openxmlformats-officedocument.wordprocessingml.document;base64,',
            'data:@file/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,',
            '[protected]',
            '[removed]',
        );
        $exploded = explode(',', $base64Data);
        if (!isset($exploded[1])) {
            $exploded[1] = null;
        }

        $base64 = $exploded[1];
        // dd($base64);
        $base64Data = str_replace($replaceList, '', $base64Data);

        return base64_decode($base64);
    }
}

if (!function_exists('saveLogSurat')) {
    function saveLogSurat($data)
    {
        return DB::table('log_print_surat')->insert($data);
    }
}

if (!function_exists('detailUsersKaryawans')) {
    function detailUsersKaryawans($users_code)
    {
        return DB::table('users')
            ->leftJoin('karyawans', 'karyawans.karyawan_code', '=', 'users.karyawan_code')
            ->where('users_code', $users_code)
            ->first(['users.users_code', 'users.name', 'karyawans.nama as karyawan_nama', 'karyawans.posisi', 'karyawans.karyawan_code', 'karyawans.type']);
    }
}

if (!function_exists('getKopSurat')) {
    function getKopSurat($kop_code)
    {
        $data = DB::table('kop_surat')->where('kop_code', $kop_code)->first();

        return $data;
    }
}

if (!function_exists('getMasa')) {
    function getMasa($date1, $date2)
    {
        $res = '';

        $d1 = new DateTime($date1);
        $d2 = new DateTime($date2);
        $interval = $d1->diff($d2);
        $diffInDays    = $interval->d; //21
        $diffInMonths  = $interval->m; //4
        $diffInYears   = $interval->y; //1

        if ($diffInYears > 0) {
            $res .= $diffInYears . ' tahun';
        }
        if ($diffInMonths > 0) {
            $res .= ' ' . $diffInMonths . ' bulan';
        }
        if ($diffInDays > 0) {
            $res .= ' ' . $diffInDays . ' hari';
        }

        return $res;
    }
}

if (!function_exists('getMonth')) {
    function getMonth($bln)
    {
        switch ($bln) {
            case 1:
                return "Januari";
                break;
            case 2:
                return "Februari";
                break;
            case 3:
                return "Maret";
                break;
            case 4:
                return "April";
                break;
            case 5:
                return "Mei";
                break;
            case 6:
                return "Juni";
                break;
            case 7:
                return "Juli";
                break;
            case 8:
                return "Agustus";
                break;
            case 9:
                return "September";
                break;
            case 10:
                return "Oktober";
                break;
            case 11:
                return "November";
                break;
            case 12:
                return "Desember";
                break;
        }
    }
}

if (!function_exists('getMonthEn')) {
    function getMonthEn($bln)
    {
        switch ($bln) {
            case 1:
                return "January";
                break;
            case 2:
                return "February";
                break;
            case 3:
                return "March";
                break;
            case 4:
                return "April";
                break;
            case 5:
                return "May";
                break;
            case 6:
                return "Juny";
                break;
            case 7:
                return "July";
                break;
            case 8:
                return "August";
                break;
            case 9:
                return "September";
                break;
            case 10:
                return "October";
                break;
            case 11:
                return "November";
                break;
            case 12:
                return "December";
                break;
        }
    }
}
