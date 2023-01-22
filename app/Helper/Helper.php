<?
namespace App\Helper;

class Helper
{
    // if (!function_exists('uploadFotoWithFileNameApi')) {
        function uploadFotoWithFileNameApi($base64Data, $file_prefix_name)
        {
            $file_name = generateFiledCode($file_prefix_name).'.png';
    
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
            if ( ! isset($exploded[1])) {
                $exploded[1] = null;
             }
    
            $base64 = $exploded[1];
            $base64Data = str_replace($replaceList, '', $base64Data);
    
            return base64_decode( $base64);
        }
    // }
}