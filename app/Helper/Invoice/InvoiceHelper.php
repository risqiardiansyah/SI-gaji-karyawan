<?php

namespace App\Helpers\Invoice;

class InvoiceHelper
{
    public static function createInvoiceDocument(Invoice $invoice, $preview = false)
    {
        dd($invoice);

        $img = '';
        if ($invoice->administration->logo_filename) {
            $path = storage_path('app' . DIRECTORY_SEPARATOR . 'administrations' . DIRECTORY_SEPARATOR . $invoice->administration->logo_filename);
            $logo = file_get_contents($path);

            $src = 'data:' . mime_content_type($path)
                . ';charset=binary;base64,' . base64_encode($logo);
            $src = str_replace(" ", "", $src);
            $img = '<img src="' . $src . '" width="auto" height="156px"/>';
        }

        InvoiceHelper::checkStorageDir($invoice->administration->id);

        
    }
    public static function checkStorageDir($administration_id)
    {
        //Check if storage map exists
        $storageDir = Storage::disk('administrations')->getDriver()->getAdapter()->getPathPrefix() . DIRECTORY_SEPARATOR . 'administration_' . $administration_id . DIRECTORY_SEPARATOR . 'invoices';

        if (!is_dir($storageDir)) {
            mkdir($storageDir, 0777, true);
        }
    }
}