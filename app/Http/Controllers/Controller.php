<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Support\Facades\File;
use Image;
class Controller extends BaseController
{
    public static function push_notif($fcm_token, $title, $msg)
    {
        define('API_ACCESS_KEY','AAAAt-7q_AI:APA91bH6xE4YaKuoiKoHqBIJY3O3vN9nvwZByWKi8UIoPrleakjmMK2wYg8AkISiuj4zEyiuHn5PjCxV2dV3ZYQfLDhXA7QZVoBCp5v_vbK3SbbpgseuIgb8qhBVzc48dEa8PXjQ_423');

        $url = 'https://fcm.googleapis.com/fcm/send';

        $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
        // $fcm_token = array( $_GET['id'] );

        $notification = [
            'title'    => $title,
            'body'     => $msg,
            'icon'     => 'stock_ticker_update',
            'color'    => '#23e60d',
            'sound'    => 'default',
            'priority' => 'high'
        ];

        // $notification = array(
        //     'message'   => 'here is a message. message',
        //     'title'     => 'This is a title. title',
        //     'subtitle'  => 'This is a subtitle. subtitle',
        //     'tickerText'=> 'Ticker text here...Ticker text here...Ticker text here',
        //     'vibrate'   => 1,
        //     'sound'     => 1,
        //     'largeIcon' => 'large_icon',
        //     'smallIcon' => 'small_icon',
        //     'priority'  => 'high'
        // );

        $data = [
            'to'            => $fcm_token, //single token
            'notification'  => $notification,
            // 'data'          => 'somethng'
        ];

        $headers = [
            'Authorization: key=' . API_ACCESS_KEY,
            'Content-Type: application/json'
        ];

        $options = array(
            'http' => array(
                'header'  => $headers,
                'method'  => 'POST',
                'content' => json_encode($data)
            )
        );

        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        return $result;
    }

    public static function sendOTP($hp, $msg_otp)
    {
        $url = 'https://kreditmandiri.co.id/API_SMS/sms.php';

        $data  = array(
            'username' => 'dasjhfsj12EDD',
            'password' => 'uykmshfj126AEE',
            'hp'       => $hp,
            'message'  => $msg_otp
        );

        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data)
            )
        );

        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        return $result;
    }

    public static function img64enc($file) {
        $type = pathinfo($file, PATHINFO_EXTENSION);
        $extention = $file->getClientOriginalExtension();
        $data = file_get_contents($file);
        $base64 = 'data:image/' . $extention . ';base64,' . base64_encode($data);

        return $base64;
    }

    public static function group_by($key, $array) {
        $result = array();
        $q = 0;
        foreach($array as $keys =>  $val) {
            if(array_key_exists($key, $val)){
                $result[$val[$key]]['tgl'] = $val[$key];
                $result[$val[$key]]['list'][] = $val;
            }else{
                $result[""][] = $val;
            }
        }

        return $result;
    }

    public static function checkDir($scope, $query_dir, $id_area, $id_cabang){

        if($scope == 'PUSAT'){

            $query = $query_dir;

        }elseif($scope == 'AREA'){

            $query = $query_dir->where('id_area', $id_area);

        }else{

            $query = $query_dir->where('id_cabang', $id_cabang);
        }

        return $query;
    }

    public static function recom_angs($plafon, $tenor, $bunga){
        if ($plafon == 0 || $tenor == 0 || $bunga == 0) {
            $result = 0;
        }else{
            $exec   = ($plafon + ($plafon * $tenor * $bunga)) / $tenor;
            $result = ceil($exec / 1000) * 1000;
        }

        return (int) $result;
    }

    public static function recom_ltv($plafon, $sumAllTaksasi){
        if ($sumAllTaksasi == 0) {
            $result = 0.00;
        }else{
            $result = ($plafon / $sumAllTaksasi) * 100;
        }

        return round($result, 2);
    }

    public static function recom_idir($recom_angs, $rekomen_pendapatan, $rekomen_pengeluaran){
        $selisih = $rekomen_pendapatan - $rekomen_pengeluaran;

        if ($recom_angs == 0 || $selisih == 0) {
            $result = 0.00;
        }else{
            $result = ($recom_angs / $selisih) * 100;
        }

        return round($result, 2);
    }

    public static function recom_dsr($recom_angs, $rekomen_pendapatan, $rekomen_angsuran){
        $selisih = $rekomen_pendapatan - $rekomen_angsuran;

        if ($recom_angs == 0 || $selisih == 0) {
            $result = 0.00;
        }else{
            $result = ($recom_angs / ($rekomen_pendapatan - $rekomen_angsuran)) * 100;
        }

        return round($result, 2);
    }

    public static function recom_hasil($recom_dsr, $recom_ltv, $recom_idir){
        $staticMin    = 35;
        $staticMax    = 80;
        $ltvMax       = 70;

        if ($recom_dsr <= $staticMin && $recom_ltv <= $ltvMax && $recom_idir > 0 && $recom_idir < $staticMax) {

            $result = 'LAYAK';

        }elseif ($recom_dsr <= $staticMin && $recom_ltv > $ltvMax) {

            $result = 'DIPERTIMBANGKAN';

        }elseif ($recom_dsr > $staticMin && $recom_ltv <= $ltvMax) {

            $result = 'DIPERTIMBANGKAN';

        }elseif ($recom_dsr <= $staticMin && $recom_ltv <= $ltvMax) {

            $result = 'DIPERTIMBANGKAN';

        }else{

            $result = 'RESIKO TINGGI';

        }

        return $result;
    }

    public static function second_flip_array($array){
        foreach ($array as $key => $subarr)
        {
            foreach ($subarr as $subkey => $subvalue)
            {
                $out[$subkey][$key] = ($subvalue);
            }
        }

        return $out;
    }

    public static function third_flip_array($array){
        foreach ($array as $key => $subarr)
        {
            foreach ($subarr as $subkey => $subvalue)
            {
                foreach($subvalue as $childkey => $childvalue)
                {
                    $out[$key][$childkey][$subkey] = ($childvalue);
                }
            }
        }

        return $out;
    }

    public static function uploadImg($check, $file, $path, $name)
    {
        // Check Directory
        if(!File::isDirectory($path)){
            File::makeDirectory($path, 0777, true, true);
        }
        
        // Delete File is Exists
        if(!empty($check))
        {
            File::delete($check);
        }

        $namefile = $file->getClientOriginalName();
        $fullPath = $path.'/'.$namefile;
        
        if($file->getClientMimeType() == "application/pdf"){
            $file->move($path, $namefile);
        }else{
            // cut size image
            $img = Image::make(realpath($file))->resize(320, 240);
    
            // Save Image to Directory
            $img->save($fullPath);
        }
        
        return $fullPath;
    }
}
