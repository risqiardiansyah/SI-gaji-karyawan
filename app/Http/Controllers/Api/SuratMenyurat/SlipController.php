<?php

namespace App\Http\Controllers\api\SuratMenyurat;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use App\Http\Repositories\SlipRepository;
use Barryvdh\DomPDF\Facade as PDF;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SlipController extends ApiController
{
    protected $slipGaji;

    public function __construct(SlipRepository $slip)
    {
        $this->slipGaji = $slip;
    }

    public function getAllSlip()
    {
        $data = $this->slipGaji->getAllSlip();

        if (count($data) > 0) {
            return $this->sendResponse(0, 'Success', $data);
        } else {
            return $this->sendResponse(0, 'Kosong', []);
        }
    }

    public function createSlip(Request $request)
    {
        $data = $this->slipGaji->createSlip($request);

        if ($data['success']) {
            return $this->sendResponse(0, 'Success', $data);
        } else {
            return $this->sendError(2, 'Error', $data['msg']);
        }
    }

    public function editSlip(Request $request)
    {
        $data = $this->slipGaji->editSlip($request);

        if ($data['success']) {
            return $this->sendResponse(0, 'Success', $data);
        } else {
            return $this->sendError(2, 'Error', $data['msg']);
        }
    }

    public function deleteSlip($slip_code)
    {
        $data = $this->slipGaji->deleteSlip($slip_code);

        if ($data['success']) {
            return $this->sendResponse(0, 'Success', $data);
        } else {
            return $this->sendError(2, 'Error', $data['msg']);
        }
    }

    public function detailSlip($slip_code)
    {
        $data = $this->slipGaji->detailSlip($slip_code);

        if ($data) {
            return $this->sendResponse(0, 'Success', $data);
        } else {
            return $this->sendResponse(2, 'Data tidak ditemukan', []);
        }
    }

    public function cetak_slip($slip_code)
    {
        $select = [
            'slip_gaji.*',
            'k.*',
            's.*',
            'k.nama as karyawan_nama',
            'k.nik as karyawan_nik',
            's.name as signer_name',
        ];
        $data = DB::table('slip_gaji')
            ->select($select)
            ->leftJoin('signer as s', 's.signer_code', '=', 'slip_gaji.signer_code')
            ->leftJoin('karyawans as k', 'k.karyawan_code', '=', 'slip_gaji.karyawan_code')
            ->where('slip_code', $slip_code)
            ->first();

        $createInvoice =  $this->checkStorageDir($slip_code, false);
        $name = $slip_code . '.pdf';
        $path = $slip_code
            . DIRECTORY_SEPARATOR . 'slip' . DIRECTORY_SEPARATOR . $name;

        $filePath = (storage_path('app/public' . DIRECTORY_SEPARATOR . 'slip' . DIRECTORY_SEPARATOR) . $path);
        $url = asset('storage/slip/' . $path);
        $terima = DB::table('slip_terima')->where('slip_code', $slip_code)->get();
        $pengurangan = DB::table('slip_pengurangan')->where('slip_code', $slip_code)->get();

        $qrcode = base64_encode(QrCode::format('png')->merge(env('BARCODE_ICON'), .35)->size(600)->errorCorrection('H')->generate($url));
        // return view('dashboard.tipe-surat.slip.pdf', compact('data', 'terima', 'pengurangan', 'qrcode'));
        $kop = getKopSurat($data->kop_code);
        $pdf = PDF::loadview('dashboard.tipe-surat.slip.pdf', compact('data', 'terima', 'pengurangan', 'qrcode', 'kop'))->setPaper('A4', 'landscape');

        $pdf->save($filePath);
        $result = (object)array();
        $result->slip_code = $slip_code;
        $result->path = $path;
        $result->name = $name;
        $result->pdf_path = asset('storage/slip/' . $path);

        if (!empty($result)) {
            return $this->sendResponse(0, 19, $result);
        } else {
            return $this->sendResponse(0, 19, []);
        }
    }

    public function checkStorageDir($slip_code)
    {
        //Check if storage map exists
        $storageDir = Storage::disk('slip')->getDriver()->getAdapter()->getPathPrefix() . DIRECTORY_SEPARATOR . $slip_code . DIRECTORY_SEPARATOR . 'slip';

        if (!is_dir($storageDir)) {
            mkdir($storageDir, 0777, true);
        }
    }

    public function exportSlip(Request $request)
    {
        $data = $this->slipGaji->exportSlip($request);

        if ($data['success']) {
            return $this->sendResponse(0, 'Success', $data['data']);
        } else {
            return $this->sendError(2, 'Gagal', $data);
        }
    }
}
