<?php

namespace App\Http\Controllers\api\SuratMenyurat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Repositories\DaftarSignerRepository;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Auth;

class DaftarSignerController extends ApiController
{
    protected $signer;

    public function __construct(DaftarSignerRepository $signer)
    {
        $this->signer = $signer;
    }
    
    public function getSignerList()
    {
        $allsigner = $this->signer->getSignerList();

        if (count($allsigner)  > 0) {
            return $this->sendResponse(0, 'Berhasil', $allsigner);
        } elseif (count($allsigner) == 0) {
            return $this->sendResponse(0, 'Data kosong');
        }  else {
            return $this->sendError(0, 'Error');
        }
    }
    
    public function getAllSigner()
    {
        $allsigner = $this->signer->getAllSigner();

        if (count($allsigner)  > 0) {
            return $this->sendResponse(0, 'Berhasil', $allsigner);
        } elseif (count($allsigner) == 0) {
            return $this->sendResponse(0, 'Data kosong');
        }  else {
            return $this->sendError(0, 'Error');
        }
    }
    
    public function deleteSigner(Request $request)
    {
        $delete = $this->signer->deleteSigner($request->signer_code);

        if ($delete) {
            return $this->sendResponse(0, 'Berhasil', []);
        }else {
            return $this->sendError(0, 'Error');
        }
    }
    
    public function addSigner(Request $request)
    {
        $delete = $this->signer->addSigner($request);

        if ($delete) {
            return $this->sendResponse(0, 'Berhasil', []);
        }else {
            return $this->sendError(0, 'Error');
        }
    }
    
    public function updateSigner(Request $request, $signer_code)
    {
        $delete = $this->signer->updateSigner($request, $signer_code);

        if ($delete) {
            return $this->sendResponse(0, 'Berhasil', []);
        }else {
            return $this->sendError(0, 'Error');
        }
    }
}
