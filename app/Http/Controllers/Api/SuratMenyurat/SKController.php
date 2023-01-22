<?php

namespace App\Http\Controllers\api\SuratMenyurat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Auth;
use App\Http\Repositories\SKRepository;
use Illuminate\Support\Facades\Validator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class SKController extends ApiController
{
    protected $sk;

    public function __construct(SKRepository $sk)
    {
        $this->sk = $sk;
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

    public function index()
    {
        $list_sk = $this->sk->listSK();
        if (collect($list_sk)->count()  > 0) {
            return $this->sendResponse(0, 19, $list_sk);
        } elseif ($list_sk->count() == 0) {
            return $this->sendResponse(0, 'Data kosong');
        } else {
            return $this->sendResponse(0, 19, []);
        }
    }

    public function detail($code)
    {
        $list_sk = $this->sk->detailSK($code);
        if ($list_sk) {
            return $this->sendResponse(0, 19, $list_sk);
        } else {
            return $this->sendResponse(0, 19, []);
        }
    }

    public function create(Request $request)
    {
        $rules = [
            'from_date' => 'required',
            'to_date' => 'required',
            'signer_code' => 'required',
            'karyawan_code' => 'required',
            'position' => 'required',
            'category' => 'required'
        ];

        $validator = $this->validateThis($request, $rules);
        if ($validator->fails()) {
            return $this->sendError(1, 'Params not complete', $this->validationMessage($validator->errors()));
        }

        $createSK = $this->sk->createSK($request);
        if ($createSK['success']) {
            return $this->sendResponse(0, 'Success', []);
        } else {
            return $this->sendError(2, 'Error');
        }
    }

    public function cetak_sk($sk_code, $send_email)
    {
        $sk = $this->sk->getSKData($sk_code);
        // dd($sk);
        if (!$sk) {
            return $this->sendError(2, 'SK Not Found', []);
        }
        $this->createQuoDocument($sk);

        $tanggal = date('d');
        $bulan = $this->getBulan(date('m'));
        $tahun = date('Y');
        $date = $tanggal . ' ' . $bulan . ' ' . $tahun;

        $folder = $sk->category === 1 ? 'KERJA' : 'MAGANG';
        $type = $folder;
        $name = $sk->unique_code . '.pdf';
        $path = $folder . '/' . $name;

        $filePath = (storage_path('app/public' . DIRECTORY_SEPARATOR . 'sk' . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR) . $name);
        // $url = asset('storage/sk/' . $path);
        // $qrcode = base64_encode(QrCode::format('png')->merge(env('BARCODE_ICON'), .35)->size(600)->errorCorrection('H')->generate($url));

        $url_signer = 'https://api.finance.alan.co.id/scan/verify?code=' . $sk->signer_code;
        $qrcode = base64_encode(QrCode::format('png')->merge(env('BARCODE_ICON'), .35)->size(600)->errorCorrection('H')->generate($url_signer));

        $kop = getKopSurat($sk->kop_code);

        $masa = getMasa($sk->from_date, $sk->to_date);

        $pdf = PDF::loadview('dashboard.tipe-surat.sk.' . $folder . '.pdf', compact('sk', 'qrcode', 'date', 'type', 'kop', 'masa'))->setPaper('A4', 'potrait');

        $pdf->save($filePath);
        $result = (object)array();
        $result->sk_code = $sk_code;
        $result->path = $path;
        $result->name = $name;
        $result->pdf_path = asset('storage/sk/' . $path);
        $sender = 'no-reply@alan.co.id';

        $datamail = [
            'person' => $sk->karyawan_nama,
            'transaction' => $sk->no_surat,
            'email_draft' => $sender,
            'self' => 'Surat Keterangan ' . $folder . ' di Alan Creative',
            'url' => 'https://alan.co.id',
        ];

        $sk_code = $sk->no_surat;
        $email = $sk->karyawan_email;

        if ($send_email == 1) {
            Mail::send('email.send_invoice', $datamail, function ($message) use ($datamail, $email, $sender, $sk_code, $filePath) {

                $message->from($sender);
                $message->subject('[Surat Keterangan - ' . $sk_code . '] Alan Creative - Thanks for Your Support');
                $message->setTo($email);
                $message->attach($filePath);
            });
        }

        if (!empty($result)) {
            return $this->sendResponse(0, 'Success', $result);
        } else {
            return $this->sendError(0, 'Error', []);
        }
    }

    public function createQuoDocument($sk, $preview = false)
    {

        $chekStorage =  $this->checkStorageDir($sk->unique_code);
        // return


    }
    public function checkStorageDir($code)
    {
        //Check if storage map exists
        $storageDir = Storage::disk('sk')->getDriver()->getAdapter()->getPathPrefix() . DIRECTORY_SEPARATOR . $code;

        if (!is_dir($storageDir)) {
            mkdir($storageDir, 0777, true);
        }
    }

    public function createSPDocument($code)
    {

        $storageDir = Storage::disk('sp')->getDriver()->getAdapter()->getPathPrefix() . DIRECTORY_SEPARATOR . $code;

        if (!is_dir($storageDir)) {
            mkdir($storageDir, 0777, true);
        }
    }

    public function createDocumentPenilaian($code)
    {

        $storageDir = Storage::disk('penilaian')->getDriver()->getAdapter()->getPathPrefix() . DIRECTORY_SEPARATOR . $code;

        if (!is_dir($storageDir)) {
            mkdir($storageDir, 0777, true);
        }
    }

    public function createDocumentMOM($code)
    {

        $storageDir = Storage::disk('mom')->getDriver()->getAdapter()->getPathPrefix() . DIRECTORY_SEPARATOR . $code;

        if (!is_dir($storageDir)) {
            mkdir($storageDir, 0777, true);
        }
    }

    public function createDocumentHandover($code)
    {

        $storageDir = Storage::disk('handover')->getDriver()->getAdapter()->getPathPrefix() . DIRECTORY_SEPARATOR . $code;

        if (!is_dir($storageDir)) {
            mkdir($storageDir, 0777, true);
        }
    }

    public function createDocumentGaransi($code)
    {

        $storageDir = Storage::disk('garansi')->getDriver()->getAdapter()->getPathPrefix() . DIRECTORY_SEPARATOR . $code;

        if (!is_dir($storageDir)) {
            mkdir($storageDir, 0777, true);
        }
    }

    public function edit(Request $request)
    {
        $rules = [
            'unique_code' => 'required',
            'from_date' => 'required',
            'to_date' => 'required',
            'signer_code' => 'required',
            'karyawan_code' => 'required',
            'position' => 'required',
            'category' => 'required'
        ];

        $validator = $this->validateThis($request, $rules);
        if ($validator->fails()) {
            return $this->sendError(1, 'Params not complete', $this->validationMessage($validator->errors()));
        }

        $createSK = $this->sk->editSK($request);
        if ($createSK['success']) {
            return $this->sendResponse(0, 'Success', []);
        } else {
            return $this->sendError(2, 'Error');
        }
    }

    public function delete(Request $request)
    {
        $rules = [
            'unique_code' => 'required'
        ];

        $validator = $this->validateThis($request, $rules);
        if ($validator->fails()) {
            return $this->sendError(1, 'Params not complete', $this->validationMessage($validator->errors()));
        }

        $createSK = $this->sk->deleteSK($request->unique_code);
        if ($createSK['success']) {
            return $this->sendResponse(0, 'Success', []);
        } else {
            return $this->sendError(2, 'Error');
        }
    }

    public function supel()
    {
        $list_sk = $this->sk->dataSupel();
        if (collect($list_sk)->count()  > 0) {
            return $this->sendResponse(0, 19, $list_sk);
        } elseif ($list_sk->count() == 0) {
            return $this->sendResponse(0, 'Data kosong');
        } else {
            return $this->sendResponse(0, 19, []);
        }
    }

    public function checkSupel($karyawan_code)
    {
        $check = $this->sk->checkSupel($karyawan_code);
        if ($check['success']) {
            return $this->sendResponse(0, 'Success', $check['data']);
        } else {
            return $this->sendError(2, 'Error', $check['msg']);
        }
    }

    public function detailSupel($code)
    {
        $list_sk = $this->sk->detailSupel($code);
        if ($list_sk) {
            return $this->sendResponse(0, 19, $list_sk);
        } else {
            return $this->sendError(2, 19, []);
        }
    }

    public function createSupel(Request $request)
    {
        $rules = [
            'karyawan_code' => 'required',
            'signer_code' => 'required',
            'pelanggaran' => 'required_if:sp_ke,1',
            'tgl_surat' => 'required',
            'sp_ke' => 'required|in:1,2,3',
            'sp_bulan' => 'required_if:sp_ke,1',
        ];

        $validator = $this->validateThis($request, $rules);
        if ($validator->fails()) {
            return $this->sendError(1, 'Params not complete', $this->validationMessage($validator->errors()));
        }

        $createSK = $this->sk->createSupel($request);
        if ($createSK['success']) {
            return $this->sendResponse(0, 'Success', []);
        } else {
            return $this->sendError(2, 'Error', $createSK['msg']);
        }
    }

    public function editSupel(Request $request)
    {
        $rules = [
            'unique_code' => 'required',
            'karyawan_code' => 'required',
            'signer_code' => 'required',
            'pelanggaran' => 'required_if:sp_ke,1',
            'tgl_surat' => 'required',
            'sp_ke' => 'required|in:1,2,3',
            'sp_bulan' => 'required_if:sp_ke,1',
        ];

        $validator = $this->validateThis($request, $rules);
        if ($validator->fails()) {
            return $this->sendError(1, 'Params not complete', $this->validationMessage($validator->errors()));
        }

        $createSK = $this->sk->editSupel($request);
        if ($createSK['success']) {
            return $this->sendResponse(0, 'Success', []);
        } else {
            return $this->sendError(2, 'Error', $createSK['msg']);
        }
    }

    public function cetak_supel($unique_code, $send_email)
    {
        $sp = $this->sk->detailSupel($unique_code);
        if (!$sp) {
            return $this->sendError(2, 'Surat Pelanggaran Not Found', []);
        }
        $this->createSPDocument($sp->karyawan_nama);

        $tanggal = date('d');
        $bulan = $this->getBulan(date('m'));
        $tahun = date('Y');
        $date = $tanggal . ' ' . $bulan . ' ' . $tahun;

        $name = $sp->sp_ke . '-' . $sp->unique_code . '.pdf';
        $path = $sp->karyawan_nama . '/' . $name;

        $filePath = (storage_path('app/public' . DIRECTORY_SEPARATOR . 'sp' . DIRECTORY_SEPARATOR) . $path);
        $url = asset('storage/sp/' . $path);
        $qrcode = base64_encode(QrCode::format('png')->merge(env('BARCODE_ICON'), .35)->size(600)->errorCorrection('H')->generate($url));
        if ($sp->sp_ke == 3) {
            $pdf = PDF::loadview('dashboard.tipe-surat.sp.pdf-sp3', compact('sp', 'qrcode'))->setPaper('A4', 'potrait');
        } else {
            $pdf = PDF::loadview('dashboard.tipe-surat.sp.pdf', compact('sp', 'qrcode'))->setPaper('A4', 'potrait');
        }

        $pdf->save($filePath);
        $result = (object)array();
        $result->sk_code = $unique_code;
        $result->path = $path;
        $result->name = $name;
        $result->pdf_path = asset('storage/sp/' . $path);
        $sender = 'no-reply@alan.co.id';

        $datamail = [
            'person' => $sp->karyawan_nama,
            'transaction' => $sp->no_surat,
            'email_draft' => $sender,
            'self' => 'Surat Peringatan - Alan Creative',
            'url' => 'https://alan.co.id',
        ];

        $unique_code = $sp->no_surat;
        $email = $sp->karyawan_email;

        if ($send_email == 1) {
            Mail::send('email.send_invoice', $datamail, function ($message) use ($datamail, $email, $sender, $unique_code, $filePath) {

                $message->from($sender);
                $message->subject('[Surat Peringatan - ' . $unique_code . '] Alan Creative - Thanks for Your Support');
                $message->setTo($email);
                $message->attach($filePath);
            });
        }

        if (!empty($result)) {
            return $this->sendResponse(0, 'Success', $result);
        } else {
            return $this->sendError(0, 'Error', []);
        }
    }

    public function deleteSupel(Request $request)
    {
        $rules = [
            'unique_code' => 'required'
        ];

        $validator = $this->validateThis($request, $rules);
        if ($validator->fails()) {
            return $this->sendError(1, 'Params not complete', $this->validationMessage($validator->errors()));
        }

        $createSK = $this->sk->deleteSupel($request->unique_code);
        if ($createSK['success']) {
            return $this->sendResponse(0, 'Success', []);
        } else {
            return $this->sendError(2, 'Error');
        }
    }

    public function suratPenilaian()
    {
        $list_sk = $this->sk->suratPenilaian();
        if (collect($list_sk)->count()  > 0) {
            return $this->sendResponse(0, 19, $list_sk);
        } elseif ($list_sk->count() == 0) {
            return $this->sendResponse(0, 'Data kosong');
        } else {
            return $this->sendResponse(0, 19, []);
        }
    }

    public function checkSuratPenilaian($karyawan_code)
    {
        $check = $this->sk->checkSuratPenilaian($karyawan_code);
        if ($check['success']) {
            return $this->sendResponse(0, 'Success', $check['data']);
        } else {
            return $this->sendError(2, 'Error', $check['msg']);
        }
    }

    public function allKriteriaPenilaian()
    {
        $data = $this->sk->allKriteriaPenilaian();
        if ($data) {
            return $this->sendResponse(0, 'Success', $data);
        } else {
            return $this->sendError(2, 'Error', []);
        }
    }

    public function kriteriaPenilaian($type)
    {
        $data = $this->sk->kriteriaPenilaian($type);
        if ($data) {
            return $this->sendResponse(0, 'Success', $data);
        } else {
            return $this->sendError(2, 'Error', []);
        }
    }

    public function addKriteriaPenilaian(Request $request)
    {
        $rules = [
            'pe_name_id' => 'required',
            'type' => 'required',
            'pe_bobot' => 'required',
            'group_mingguan' => 'required_if:type,mingguan',
        ];

        $validator = $this->validateThis($request, $rules);
        if ($validator->fails()) {
            return $this->sendError(1, 'Params not complete', $this->validationMessage($validator->errors()));
        }

        $create = $this->sk->addKriteriaPenilaian($request);
        if ($create['success']) {
            return $this->sendResponse(0, 'Success', []);
        } else {
            return $this->sendError(2, 'Error', $create['msg']);
        }
    }

    public function editKriteriaPenilaian(Request $request)
    {
        $rules = [
            'pe_code' => 'required',
            'pe_name_id' => 'required',
            'type' => 'required',
            'pe_bobot' => 'required',
            'group_mingguan' => 'required_if:type,mingguan',
        ];

        $validator = $this->validateThis($request, $rules);
        if ($validator->fails()) {
            return $this->sendError(1, 'Params not complete', $this->validationMessage($validator->errors()));
        }

        $edit = $this->sk->editKriteriaPenilaian($request);
        if ($edit['success']) {
            return $this->sendResponse(0, 'Success', []);
        } else {
            return $this->sendError(2, 'Error', $edit['msg']);
        }
    }

    public function deleteKriteriaPenilaian($code)
    {
        $delete = $this->sk->deleteKriteriaPenilaian($code);

        if ($delete['success']) {
            return $this->sendResponse(0, 'Success', []);
        } else {
            return $this->sendError(2, 'Error', $delete['msg']);
        }
    }

    public function detailSuratPenilaian($code)
    {
        $list_sk = $this->sk->detailSuratPenilaian($code);
        if ($list_sk) {
            return $this->sendResponse(0, 19, $list_sk);
        } else {
            return $this->sendError(2, 19, []);
        }
    }

    public function createSuratPenilaian(Request $request)
    {
        $rules = [
            'karyawan_code' => 'required',
            'signer_code' => 'required',
            'tgl' => 'required',
            'type' => 'required|in:mingguan,bulanan,6bulanan',
            'performance' => 'required|array',
        ];

        $validator = $this->validateThis($request, $rules);
        if ($validator->fails()) {
            return $this->sendError(1, 'Params not complete', $this->validationMessage($validator->errors()));
        }

        $createSK = $this->sk->createSuratPenilaian($request);
        if ($createSK['success']) {
            return $this->sendResponse(0, 'Success', []);
        } else {
            return $this->sendError(2, 'Error', $createSK['msg']);
        }
    }

    public function editSuratPenilaian(Request $request)
    {
        $rules = [
            'penilaian_code' => 'required',
            'karyawan_code' => 'required',
            'signer_code' => 'required',
            'tgl' => 'required',
            'type' => 'required|in:mingguan,bulanan,6bulanan',
            'performance' => 'required|array',
        ];

        $validator = $this->validateThis($request, $rules);
        if ($validator->fails()) {
            return $this->sendError(1, 'Params not complete', $this->validationMessage($validator->errors()));
        }

        $createSK = $this->sk->editSuratPenilaian($request);
        if ($createSK['success']) {
            return $this->sendResponse(0, 'Success', []);
        } else {
            return $this->sendError(2, 'Error', $createSK['msg']);
        }
    }

    public function cetak_surat_penilaian($unique_code, $send_email)
    {
        $sp = $this->sk->detailSuratPenilaian($unique_code);
        if (!$sp) {
            return $this->sendError(2, 'Penilaian Tidak Ditemukan', []);
        }
        // dd($sp);

        $this->createDocumentPenilaian($sp->karyawan_nama);

        $name = $sp->type . '-' . $sp->penilaian_code . '.pdf';
        $path = $sp->karyawan_nama . '/' . $name;

        $filePath = (storage_path('app/public' . DIRECTORY_SEPARATOR . 'penilaian' . DIRECTORY_SEPARATOR) . $path);
        $url_reviewer = 'https://api.finance.alan.co.id/scan/verify?code=' . $sp->signer_code;
        $qrcode_reviewer = base64_encode(QrCode::format('png')->merge(env('BARCODE_ICON'), .35)->size(600)->errorCorrection('H')->generate($url_reviewer));

        $url_ceo = 'https://api.finance.alan.co.id/scan/verify?code=SG-1';
        if ($sp->mengetahui_code !== '') {
            $url_ceo = 'https://api.finance.alan.co.id/scan/verify?code=' . $sp->mengetahui_code . '&type=karyawan';
        }
        $qrcode_ceo = base64_encode(QrCode::format('png')->merge(env('BARCODE_ICON'), .35)->size(600)->errorCorrection('H')->generate($url_ceo));

        $customPaper = array(0, 0, 600, 1440);
        if ($sp->type == 'mingguan') {
            $pdf = PDF::loadview('dashboard.tipe-surat.penilaian.mingguan', compact('sp', 'qrcode_reviewer', 'qrcode_ceo'))->setPaper($customPaper, 'potrait');
        } else {
            $pdf = PDF::loadview('dashboard.tipe-surat.penilaian.bulanan', compact('sp', 'qrcode_reviewer', 'qrcode_ceo'))->setPaper($customPaper, 'potrait');
        }

        $pdf->save($filePath);
        $result = (object)array();
        $result->penilaian_code = $unique_code;
        $result->path = $path;
        $result->name = $name;
        $result->pdf_path = asset('storage/penilaian/' . $path);
        $sender = 'no-reply@alan.co.id';

        $datamail = [
            'person' => $sp->karyawan_nama,
            'transaction' => $sp->penilaian_code,
            'email_draft' => $sender,
            'self' => 'Surat Peringatan - Alan Creative',
            'url' => 'https://alan.co.id',
        ];

        $unique_code = $sp->penilaian_code;
        $email = $sp->karyawan_email;

        if ($send_email == 1) {
            Mail::send('email.send_invoice', $datamail, function ($message) use ($datamail, $email, $sender, $unique_code, $filePath) {

                $message->from($sender);
                $message->subject('[Surat Peringatan - ' . $unique_code . '] Alan Creative - Thanks for Your Support');
                $message->setTo($email);
                $message->attach($filePath);
            });
        }

        if (!empty($result)) {
            return $this->sendResponse(0, 'Success', $result);
        } else {
            return $this->sendError(0, 'Error', []);
        }
    }

    public function exportPenilaian(Request $request)
    {
        $data = $this->sk->exportPenilaian($request);

        if ($data['success']) {
            return $this->sendResponse(0, 'Success', $data['data']);
        } else {
            return $this->sendError(2, 'Gagal', $data);
        }
    }

    public function deleteSuratPenilaian($code)
    {
        $createSK = $this->sk->deleteSuratPenilaian($code);
        if ($createSK['success']) {
            return $this->sendResponse(0, 'Success', []);
        } else {
            return $this->sendError(2, 'Error');
        }
    }

    public function statistikPenilaian(Request $request)
    {
        $stat = $this->sk->statistikPenilaian($request);
        if ($stat['success']) {
            return $this->sendResponse(0, 'Success', $stat['data']);
        } else {
            return $this->sendError(2, 'Error', $stat['msg']);
        }
    }

    public function MOMWithData()
    {
        $list = $this->sk->MOMWithData();
        if (collect($list)->count()  > 0) {
            return $this->sendResponse(0, 19, $list);
        } elseif ($list->count() == 0) {
            return $this->sendResponse(0, 'Data kosong');
        } else {
            return $this->sendResponse(0, 19, []);
        }
    }

    public function MOMData()
    {
        $list_sk = $this->sk->MOMData();
        if (collect($list_sk)->count()  > 0) {
            return $this->sendResponse(0, 19, $list_sk);
        } elseif ($list_sk->count() == 0) {
            return $this->sendResponse(0, 'Data kosong');
        } else {
            return $this->sendResponse(0, 19, []);
        }
    }

    public function detailMOM($code)
    {
        $list_sk = $this->sk->detailMOM($code);
        if ($list_sk) {
            return $this->sendResponse(0, 19, $list_sk);
        } else {
            return $this->sendError(2, 19, []);
        }
    }

    public function createMOM(Request $request)
    {
        $rules = [
            'tgl' => 'required',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'tempat' => 'required',
            'agenda' => 'required',
            'is_other_company' => 'required',
            'nama_perusahaan' => 'required_if:is_other_company,1',
            'type_discussion' => 'required',
            'pembahasan' => 'required_if:type_discussion,text',
            'mom_table' => 'required_if:type_discussion,table',
            'mom_with' => 'required|array',
            'signer_code' => 'required',
        ];

        $validator = $this->validateThis($request, $rules);
        if ($validator->fails()) {
            return $this->sendError(1, 'Params not complete', $this->validationMessage($validator->errors()));
        }

        $createSK = $this->sk->createMOM($request);
        if ($createSK['success']) {
            return $this->sendResponse(0, 'Success', []);
        } else {
            return $this->sendError(2, 'Error', $createSK['msg']);
        }
    }

    public function editMOM(Request $request)
    {
        $rules = [
            'mom_code' => 'required',
            'tgl' => 'required',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'tempat' => 'required',
            'agenda' => 'required',
            'is_other_company' => 'required',
            'nama_perusahaan' => 'required_if:is_other_company,1',
            'type_discussion' => 'required',
            'pembahasan' => 'required_if:type_discussion,text',
            'mom_table' => 'required_if:type_discussion,table',
            'mom_with' => 'required|array',
            'signer_code' => 'required',
        ];

        $validator = $this->validateThis($request, $rules);
        if ($validator->fails()) {
            return $this->sendError(1, 'Params not complete', $this->validationMessage($validator->errors()));
        }

        $createSK = $this->sk->editMOM($request);
        if ($createSK['success']) {
            return $this->sendResponse(0, 'Success', []);
        } else {
            return $this->sendError(2, 'Error', $createSK['msg']);
        }
    }

    public function cetak_mom($unique_code, $send_email)
    {
        $sp = $this->sk->detailCetakMOM($unique_code);
        if (!$sp) {
            return $this->sendError(2, 'MOM Tidak Ditemukan', []);
        }
        // dd($sp);

        $this->createDocumentMOM($sp->no_surat_formated);

        $path = $sp->mom_code . '.pdf';

        $filePath = (storage_path('app/public' . DIRECTORY_SEPARATOR . 'mom' . DIRECTORY_SEPARATOR) . $path);
        $url_signer = 'https://api.finance.alan.co.id/scan/verify?code=' . $sp->signer_code;
        $qrcode_signer = base64_encode(QrCode::format('png')->merge(env('BARCODE_ICON'), .35)->size(600)->errorCorrection('H')->generate($url_signer));

        $url_karyawan = 'https://api.finance.alan.co.id/scan/verify?code=' . $sp->created_by . '&type=users';
        $qrcode_karyawan = base64_encode(QrCode::format('png')->merge(env('BARCODE_ICON'), .35)->size(600)->errorCorrection('H')->generate($url_karyawan));

        $length = 0;
        $height = 800;
        if ($sp->type_discussion == 'table') {
            if (isset($sp->mom_table) &&  count($sp->mom_table) >= 2) {
                $length = count($sp->mom_table);
                $height = $length * 400;
            }
        } else {
            $length = strlen($sp->pembahasan);
            if ($length > 800) {
                $height = $length;
            }
            if ($length >= 1000) {
                $height = 1000;
            }
        }
        $customPaper = array(0, 0, 600, $height);
        $pdf = PDF::loadview('dashboard.tipe-surat.mom.pdf', compact('sp', 'qrcode_signer', 'qrcode_karyawan'))->setPaper($customPaper, 'potrait');

        $pdf->save($filePath);
        $result = (object)array();
        $result->mom_code = $unique_code;
        $result->path = $path;
        $result->pdf_path = asset('storage/mom/' . $path);

        if (!empty($result)) {
            return $this->sendResponse(0, 'Success', $result);
        } else {
            return $this->sendError(0, 'Error', []);
        }
    }

    public function deleteMOM($code)
    {
        $delete = $this->sk->deleteMOM($code);
        if ($delete['success']) {
            return $this->sendResponse(0, 'Success', []);
        } else {
            return $this->sendError(2, 'Error', $delete['msg']);
        }
    }


    public function handoverData()
    {
        $list_sk = $this->sk->handoverData();
        if (collect($list_sk)->count()  > 0) {
            return $this->sendResponse(0, 19, $list_sk);
        } elseif ($list_sk->count() == 0) {
            return $this->sendResponse(0, 'Data kosong');
        } else {
            return $this->sendResponse(0, 19, []);
        }
    }

    public function detailHandover($code)
    {
        $list_sk = $this->sk->detailHandover($code);
        if ($list_sk) {
            return $this->sendResponse(0, 19, $list_sk);
        } else {
            return $this->sendError(2, 19, $list_sk);
        }
    }

    public function createHandover(Request $request)
    {
        $rules = [
            'alasan' => 'required',
            'penerima_ho' => 'required',
            'signer_code' => 'required',
            'tgl_masuk' => 'required',
            'tgl_resign' => 'required',
            'ho_list' => 'required|array',
        ];

        $validator = $this->validateThis($request, $rules);
        if ($validator->fails()) {
            return $this->sendError(1, 'Params not complete', $this->validationMessage($validator->errors()));
        }

        $createSK = $this->sk->createHandover($request);
        if ($createSK['success']) {
            return $this->sendResponse(0, 'Success', []);
        } else {
            return $this->sendError(2, 'Error', $createSK['msg']);
        }
    }

    public function editHandover(Request $request)
    {
        $rules = [
            'ho_code' => 'required',
            'alasan' => 'required',
            'penerima_ho' => 'required',
            'signer_code' => 'required',
            'tgl_masuk' => 'required',
            'tgl_resign' => 'required',
            'ho_list' => 'required|array',
        ];

        $validator = $this->validateThis($request, $rules);
        if ($validator->fails()) {
            return $this->sendError(1, 'Params not complete', $this->validationMessage($validator->errors()));
        }

        $createSK = $this->sk->editHandover($request);
        if ($createSK['success']) {
            return $this->sendResponse(0, 'Success', []);
        } else {
            return $this->sendError(2, 'Error', $createSK['msg']);
        }
    }

    public function cetak_Handover($unique_code, $send_email)
    {
        $sp = $this->sk->detailHandover($unique_code);
        if (!$sp) {
            return $this->sendError(2, 'MOM Tidak Ditemukan', []);
        }
        // dd($sp);

        $this->createDocumentHandover($sp->ho_code);

        $path = $sp->ho_code . '.pdf';

        $filePath = (storage_path('app/public' . DIRECTORY_SEPARATOR . 'handover' . DIRECTORY_SEPARATOR) . $path);
        $url_signer = 'https://api.finance.alan.co.id/scan/verify?code=' . $sp->signer_code . '&type=signer';
        $qrcode_signer = base64_encode(QrCode::format('png')->merge(env('BARCODE_ICON'), .35)->size(600)->errorCorrection('H')->generate($url_signer));

        $url_karyawan = 'https://api.finance.alan.co.id/scan/verify?code=' . $sp->karyawan_code . '&type=karyawan';
        $qrcode_karyawan = base64_encode(QrCode::format('png')->merge(env('BARCODE_ICON'), .35)->size(600)->errorCorrection('H')->generate($url_karyawan));

        $url_penerima = 'https://api.finance.alan.co.id/scan/verify?code=' . $sp->penerima_ho . '&type=karyawan';
        $qrcode_penerima = base64_encode(QrCode::format('png')->merge(env('BARCODE_ICON'), .35)->size(600)->errorCorrection('H')->generate($url_penerima));

        $length = 0;
        $height = 800;
        if (isset($sp->list) &&  count($sp->list) >= 2) {
            $length = count($sp->list);
            $height = $length * 400;
        }
        $customPaper = array(0, 0, 600, $height);
        $pdf = PDF::loadview('dashboard.tipe-surat.handover.pdf', compact('sp', 'qrcode_signer', 'qrcode_karyawan', 'qrcode_penerima'))->setPaper($customPaper, 'potrait');

        $pdf->save($filePath);
        $result = (object)array();
        $result->ho_code = $unique_code;
        $result->path = $path;
        $result->pdf_path = asset('storage/handover/' . $path);

        if (!empty($result)) {
            return $this->sendResponse(0, 'Success', $result);
        } else {
            return $this->sendError(0, 'Error', []);
        }
    }

    public function deleteHandover($code)
    {
        $delete = $this->sk->deleteHandover($code);
        if ($delete['success']) {
            return $this->sendResponse(0, 'Success', []);
        } else {
            return $this->sendError(2, 'Error', $delete['msg']);
        }
    }


    public function garansiData()
    {
        $list_sk = $this->sk->garansiData();
        if (collect($list_sk)->count()  > 0) {
            return $this->sendResponse(0, 19, $list_sk);
        } elseif ($list_sk->count() == 0) {
            return $this->sendResponse(0, 'Data kosong');
        } else {
            return $this->sendResponse(0, 19, []);
        }
    }

    public function detailGaransi($code)
    {
        $list_sk = $this->sk->detailGaransi($code);
        if ($list_sk) {
            return $this->sendResponse(0, 19, $list_sk);
        } else {
            return $this->sendError(2, 19, $list_sk);
        }
    }

    public function createGaransi(Request $request)
    {
        $rules = [
            'pelanggan_code' => 'required',
            'lang' => 'required',
            'signer_code' => 'required',
            'tgl_surat' => 'required'
        ];

        $validator = $this->validateThis($request, $rules);
        if ($validator->fails()) {
            return $this->sendError(1, 'Params not complete', $this->validationMessage($validator->errors()));
        }

        $createSK = $this->sk->createGaransi($request);
        if ($createSK['success']) {
            return $this->sendResponse(0, 'Success', []);
        } else {
            return $this->sendError(2, 'Error', $createSK['msg']);
        }
    }

    public function editGaransi(Request $request)
    {
        $rules = [
            'unique_code' => 'required',
            'pelanggan_code' => 'required',
            'lang' => 'required',
            'signer_code' => 'required',
            'tgl_surat' => 'required'
        ];

        $validator = $this->validateThis($request, $rules);
        if ($validator->fails()) {
            return $this->sendError(1, 'Params not complete', $this->validationMessage($validator->errors()));
        }

        $createSK = $this->sk->editGaransi($request);
        if ($createSK['success']) {
            return $this->sendResponse(0, 'Success', []);
        } else {
            return $this->sendError(2, 'Error', $createSK['msg']);
        }
    }

    public function cetak_Garansi($unique_code, $send_email)
    {
        $sp = $this->sk->detailGaransi($unique_code);
        if (!$sp) {
            return $this->sendError(2, 'MOM Tidak Ditemukan', []);
        }
        // dd($sp);

        $this->createDocumentGaransi($sp->unique_code);

        $path = $sp->unique_code . '.pdf';

        $filePath = (storage_path('app/public' . DIRECTORY_SEPARATOR . 'garansi' . DIRECTORY_SEPARATOR) . $path);
        $url_signer = 'https://api.finance.alan.co.id/scan/verify?code=' . $sp->signer_code . '&type=signer';
        $qrcode_signer = base64_encode(QrCode::format('png')->merge(env('BARCODE_ICON'), .35)->size(600)->errorCorrection('H')->generate($url_signer));

        $length = 0;
        $height = 950;
        $customPaper = array(0, 0, 600, $height);

        $kop = getKopSurat($sp->kop_code);

        $pdf = PDF::loadview('dashboard.tipe-surat.garansi.' . $sp->lang . '.pdf', compact('sp', 'qrcode_signer', 'kop'))->setPaper($customPaper, 'potrait');

        $pdf->save($filePath);
        $result = (object)array();
        $result->unique_code = $unique_code;
        $result->path = $path;
        $result->pdf_path = asset('storage/garansi/' . $path);

        if (!empty($result)) {
            return $this->sendResponse(0, 'Success', $result);
        } else {
            return $this->sendError(0, 'Error', []);
        }
    }

    public function deleteGaransi($code)
    {
        $delete = $this->sk->deleteGaransi($code);
        if ($delete['success']) {
            return $this->sendResponse(0, 'Success', []);
        } else {
            return $this->sendError(2, 'Error', $delete['msg']);
        }
    }

    function getBulan($bln)
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
