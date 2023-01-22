<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class HomeController extends Controller
{
    public function index()
    {
        return view('layouts.homepage.homepage');
    }

    public function pdf()
    {
        $select = [
            'perihal',
            'pelanggan_code',
            'tanggal_invoice',
            'jatuh_tempo_invoice',
            'keterangan',
            'model_pembayaran',
            'ppn',
            'jumlah_tagihan',
            'invoice_code',
            'sub_total_biaya'
        ];
        $invoice_code = 'INV-0821400408123233285';
        $invoice_termin = 1;
        $invoice = DB::table('tbl_invoice')->select($select)->where('invoice_code', $invoice_code)->first();
        $no_pelanggan = $invoice->pelanggan_code;
        $invoice->pelanggan = DB::table('pelanggan')->where('pelanggan_code', $no_pelanggan)->first();
        if(!empty($invoice)){
            $project = DB::table('tbl_item_project')->select(['nama_project as nama', 'biaya_project as biaya'])->where('invoice_code', $invoice_code)->get();
            for ($i=0; $i < count($project); $i++) { 
                $project[$i]->index = $i;
            }
            $termin = DB::table('tbl_term')->select(['nominal', 'term_number as term'])->where('invoice_code', $invoice_code)->get();
            for ($t=0; $t < count($termin); $t++) { 
                $termin[$t]->index = $t;
            }
            $invoice->project = $project;
            $invoice->termin = $termin;
            $invoice->term_number = $invoice_termin;
            
        } else {
            $invoice;
        }

        return view('dashboard.tipe-surat.invoice.print',compact('invoice'));
    }
}