<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use intervention\image\Imagick;

class Controller extends BaseController
{
    public static function push_notif($fcm_token, $title, $msg)
    {
        define('API_ACCESS_KEY', 'AAAAt-7q_AI:APA91bH6xE4YaKuoiKoHqBIJY3O3vN9nvwZByWKi8UIoPrleakjmMK2wYg8AkISiuj4zEyiuHn5PjCxV2dV3ZYQfLDhXA7QZVoBCp5v_vbK3SbbpgseuIgb8qhBVzc48dEa8PXjQ_423');

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

    public static function img64enc($file)
    {
        $type = pathinfo($file, PATHINFO_EXTENSION);
        $extention = $file->getClientOriginalExtension();
        $data = file_get_contents($file);
        $base64 = 'data:image/' . $extention . ';base64,' . base64_encode($data);

        return $base64;
    }

    public static function group_by($key, $array)
    {
        $result = array();
        $q = 0;
        foreach ($array as $keys =>  $val) {
            if (array_key_exists($key, $val)) {
                $result[$val[$key]]['tgl'] = $val[$key];
                $result[$val[$key]]['list'][] = $val;
            } else {
                $result[""][] = $val;
            }
        }

        return $result;
    }

     public static function checkDir($scope, $query_dir, $id_area, $id_cabang)
    {

        if ($scope === 'PUSAT') {

            $query = $query_dir->get();
        } elseif ($scope === 'AREA') {

            $query = $query_dir->where('id_area',$id_area)->get();
        } else {
            $query = $query_dir->whereIn('id_cabang',$id_cabang)->get();
        }

        return $query;
    }

    public static function recom_angs($plafon, $tenor, $bunga)
    {
        if ($plafon == 0 || $tenor == 0 || $bunga == 0) {
            $result = 0;
        } else {
            $exec   = ($plafon + ($plafon * $tenor * $bunga)) / $tenor;
            $result = ceil($exec / 1000) * 1000;
        }

        return (int) $result;
    }

    public static function recom_ltv($plafon, $sumAllTaksasi)
    {
        if ($sumAllTaksasi == 0) {
            $result = 0.00;
        } else {
            $result = ($plafon / $sumAllTaksasi) * 100;
        }

        return round($result, 2);
    }

    public static function recom_idir($recom_angs, $rekomen_pendapatan, $rekomen_pengeluaran)
    {
        $selisih = $rekomen_pendapatan - $rekomen_pengeluaran;

        if ($recom_angs == 0 || $selisih == 0) {
            $result = 0.00;
        } else {
            $result = ($recom_angs / $selisih) * 100;
        }

        return round($result, 2);
    }

    public static function recom_dsr($recom_angs, $rekomen_pendapatan, $rekomen_angsuran)
    {
        $selisih = $rekomen_pendapatan - $rekomen_angsuran;

        if ($recom_angs == 0 || $selisih == 0) {
            $result = 0.00;
        } else {
            $result = ($recom_angs / ($rekomen_pendapatan - $rekomen_angsuran)) * 100;
        }

        return round($result, 2);
    }

    public static function recom_hasil($recom_dsr, $recom_ltv, $recom_idir)
    {
        $staticMin    = 35;
        $staticMax    = 80;
        $ltvMax       = 70;

        if ($recom_dsr <= $staticMin && $recom_ltv <= $ltvMax && $recom_idir > 0 && $recom_idir < $staticMax) {

            $result = 'LAYAK';
        } elseif ($recom_dsr <= $staticMin && $recom_ltv > $ltvMax) {

            $result = 'DIPERTIMBANGKAN';
        } elseif ($recom_dsr > $staticMin && $recom_ltv <= $ltvMax) {

            $result = 'DIPERTIMBANGKAN';
        } elseif ($recom_dsr <= $staticMin && $recom_ltv <= $ltvMax) {

            $result = 'DIPERTIMBANGKAN';
        } else {

            $result = 'RESIKO TINGGI';
        }

        return $result;
    }

    public static function second_flip_array($array)
    {
        foreach ($array as $key => $subarr) {
            foreach ($subarr as $subkey => $subvalue) {
                $out[$subkey][$key] = ($subvalue);
            }
        }

        return $out;
    }

    public static function third_flip_array($array)
    {
        foreach ($array as $key => $subarr) {
            foreach ($subarr as $subkey => $subvalue) {
                foreach ($subvalue as $childkey => $childvalue) {
                    $out[$key][$childkey][$subkey] = ($childvalue);
                }
            }
        }

        return $out;
    }

    public static function uploadImg($check, $file, $path, $name)
    {
        // Check Directory
        if (!File::isDirectory($path)) {
            File::makeDirectory($path, 0777, true, true);
        }

        // Delete File is Exists
        if (!empty($check)) {
            File::delete($check);
        }

        if ($name != '') {
            $namefile = $name . '.' . $file->getClientOriginalName();
        } else {
            $namefile = $file->getClientOriginalName();
        }

        $fullPath = $path . '/' . $namefile;

        if ($file->getClientMimeType() == "application/pdf") {
            $file->move($path, $namefile);
        } else {
            // cut size image
          //  Image::make(realpath($file))->save($fullPath);

             Image::make(realpath($file))->resize(800, 750, function ($constraint) {
                $constraint->aspectRatio();
            })->save($fullPath);

           //  $quick = Image::cache(function($image) use ($file, $fullPath) {
             //    return $image->make($file)->resize(480, 360)->save($fullPath);
            // });
        }

        return $fullPath;
    }
 public static function copyFile($trans_so, $jenis, $path)
    {
        $path_to = 'public/log_verifikasi/' . $trans_so . '/' . $jenis . '/';
        $storage = Storage::copy($path, $path_to);

        return $storage;
    }

   public static function uploadImgWebtool($path, $file, $namefile)
    {
        // $filesystem = new Filesystem(new Adapter([
        //     'host'     => '103.234.254.186',
        //     'username' => 'bonar',
        //     'password' => 'Abc123!!**',

        //     /** optional config settings */
        //     'port' => 2123,
        //     // 'root' => '/path/to/root',
        //     // 'passive' => true,
        //     //  'ssl' => true,
        //     //  'timeout' => 30,
        //     'ignorePassiveAddress' => true,
        // ]));

        $ftp = Storage::createFtpDriver([
            'host'     => '103.234.254.186',
            'username' => 'bonar',
            'password' => 'Abc123!!**',
            'port'     => '2123', // your ftp port
            // 'timeout'  => '30', // timeout setting 
            'ignorePassiveAddress' => true,
            // 'root' => 'F:/Apache2.2/htdocs/efiling'
        ]);
        if (!$ftp->exists($path)) {

            $ftp->makeDirectory($path, 0775, true); //creates directory

        }
        // Check Directory
        // if (!$ftp->isDirectory($path)) {
        //     $ftp->makeDirectory($path, 0777, true, true);
        // }
        // if (!empty($check)) {
        //     File::delete($check);
        // }
        // if ($name != '') {
        //     $namefile = $name . '.' . $file->getClientOriginalName();
        // } else {
        //     $namefile = $file->getClientOriginalName();
        // }
        $namefile = $file->getClientOriginalName();

        $fullPath = $path . '/' . $namefile;

        if ($file->getClientMimeType() == "application/pdf") {
            // $file->move($path, $namefile);
            $ftp->put($fullPath, $namefile);
        } else {
            $ftp->put($fullPath, $namefile);
        }
        return $fullPath;

        // $filecontent = $ftp->get($path . $file);


        // return $filecontent;
    }

    public static function array_search_partial($arr, $keyword)
    {
        foreach ($arr as $string) {
            if (strpos($string, $keyword) !== FALSE)
                return substr($string, 36, 32 - 1);
        }
    }
     public static function fcn($var)
    {
        //  dd($var);
        if (empty($var)) {
            $arrayfile = null;
        } elseif ($var === '[]') {
            $arrayfile = null;
        } else {
            $patterns = array();
            $patterns[0] = '/]/';
            $patterns[1] = '/"/';
            $patterns[2] = "/~\~/";
            $arrayfile  = array();
            $var = preg_replace($patterns, "", $var);
            $var = str_replace("[", "", $var);
            $output = multiexplode(array("/", ","), $var);
            // dd(count($output) < 9);
            $exp = explode(",", $var);

            $arr = array();
            foreach ($exp as $key => $val) {
                $arr[$key] =  multiexplode(array("/", ","), $val);
            }
            //    dd($output[3]);
            if (count($output) < 8) {
                $arrayfile = array($output[3]);
                // dd($arrayfile);
            } else {
                foreach ($arr as $key => $val) {
                    $arrayfile[$key] = $val[3];
                    //   dd($val[3]);
                }
            }
        }
        // dd($arrayfile);
        return $arrayfile;
    }
    public static function subsPengajuan($var, $no_ktp)
    {
        $patterns = array();
        $patterns[0] = '/]/';
        $patterns[1] = '/"/';
        $patterns[2] = "/~\~/";
        $arrayfile  = array();
        $i = 0;
        foreach ($var as $key => $item) {
            $arrayfile[$key][$i] = $item;
            foreach ($item as $val) {
                $j = 0;
                if ($arrayfile[$key][$i] = substr($val, 33, 32 - 2) === false) {
                    $arrayfile[$key][$i] = null;
                } else {

                    $arrayfile[$key][$i] =  preg_replace($patterns, "", str_replace("public/" . $no_ktp . "/debitur" . "/" . "PengajuanBI/", "", $val));
                    $j++;
                }

                $i++;
            }
            return $arrayfile;
        }
    }

    public static function subsCA($var, $no_ktp)
    {
        $patterns = array();
        $patterns[0] = '/]/';
        $patterns[1] = '/"/';
        $patterns[2] = "/~\~/";
        $arrayfile  = array();
        $i = 0;
        foreach ($var as $key => $item) {
            $arrayfile[$key][$i] = $item;
            foreach ($item as $val) {
                $j = 0;
                if ($arrayfile[$key][$i] = substr($val, 33, 32 - 2) === false) {
                    $arrayfile[$key][$i] = null;
                } else {

                    $arrayfile[$key][$i] =  preg_replace($patterns, "", str_replace("public/" . $no_ktp . "/debitur" . "/" . "CreditAnalist/", "", $val));
                    $j++;
                }

                $i++;
            }
            return $arrayfile;
        }
    }

    public static function subsBI($var, $no_ktp)
    {
        $patterns = array();
        $patterns[0] = '/]/';
        $patterns[1] = '/"/';
        $patterns[2] = "/~\~/";
        $arrayfile  = array();
        $i = 0;
        foreach ($var as $key => $item) {
            $arrayfile[$key][$i] = $item;
            foreach ($item as $val) {
                $j = 0;
                if ($arrayfile[$key][$i] = substr($val, 33, 32 - 2) === false) {
                    $arrayfile[$key][$i] = null;
                } else {

                    $arrayfile[$key][$i] =  preg_replace($patterns, "", str_replace("public/" . $no_ktp . "/debitur" . "/" . "PengajuanBI/", "", $val));
                    $j++;
                }

                $i++;
            }
            return $arrayfile;
        }
    }

    public static function subsLegal($var, $no_ktp)
    {
        $patterns = array();
        $patterns[0] = '/]/';
        $patterns[1] = '/"/';
        $patterns[2] = "/~\~/";
        $arrayfile  = array();
        $i = 0;
        foreach ($var as $key => $item) {
            $arrayfile[$key][$i] = $item;
            foreach ($item as $val) {
                $j = 0;
                if ($arrayfile[$key][$i] = substr($val, 33, 32 - 2) === false) {
                    $arrayfile[$key][$i] = null;
                } else {

                    $arrayfile[$key][$i] =  preg_replace($patterns, "", str_replace("public/" . $no_ktp . "/debitur" . "/" . "EFILLINGLEGAL/", "", $val));
                    $j++;
                }

                $i++;
            }
            return $arrayfile;
        }
    }

    public static function subsJaminan($var)
    {
        //  dd($var);
        if (empty($var)) {
            $arrayfile = null;
        } elseif ($var === '[]') {
            $arrayfile = null;
        } else {
            $patterns = array();
            $patterns[0] = '/]/';
            $patterns[1] = '/"/';
            $patterns[2] = "/~\~/";
            $arrayfile  = array();
            $var = preg_replace($patterns, "", $var);
            $var = str_replace("[", "", $var);
            $output = multiexplode(array("/", ","), $var);
           
            $exp = explode(",", $var);

            $arr = array();
            foreach ($exp as $key => $val) {
                $arr[$key] =  multiexplode(array("/", ","), $val);
            }

            if (count($output) < 8) {
                $arrayfile = $output[6];
               
            } else {
                foreach ($arr as $key => $val) {
                    $arrayfile[$key] = $val[6];
                }
            }
        }

        return $arrayfile;
    }

    public static function subsFoto($var)
    {
        if (empty($var)) {
            $arrayfile = array(null);
        } else {
            $patterns = array();
            $patterns[0] = '/]/';
            $patterns[1] = '/"/';
            $patterns[2] = "/~\~/";
            $arrayfile  = array();
            $var = preg_replace($patterns, "", $var);
            $var = str_replace("[", "", $var);
            $output = multiexplode(array("/", ","), $var);
            // dd(count($output) < 9);
            $exp = explode(",", $var);

            $arr = array();
            foreach ($exp as $key => $val) {
                $arr[$key] =  multiexplode(array("/", ","), $val);
            }
            //  dd($arr);
            if (count($output) < 8) {
                $arrayfile = array($output[3]);
                // dd($arrayfile);
            } else {
                foreach ($arr as $key => $val) {
                    $arrayfile[$key] = $val[3];
                    //   dd($val[3]);
                }
            }
        }
        return $arrayfile;

    }

    public static function subsPermohonan($var, $no_ktp)
    {
        $patterns = array();
        $patterns[0] = '/]/';
        $patterns[1] = '/"/';
        $patterns[2] = "/~\~/";
        $arrayfile  = array();
        $i = 0;
        foreach ($var as $key => $item) {
            $arrayfile[$key][$i] = $item;
            foreach ($item as $val) {
                $j = 0;
                if ($arrayfile[$key][$i] = substr($val, 33, 32 - 2) === false) {
                    $arrayfile[$key][$i] = null;
                } else {

                    $arrayfile[$key][$i] =  preg_replace($patterns, "", str_replace("public/" . $no_ktp . "/debitur" . "/" . "kredit/", "", $val));
                    $j++;
                }

                $i++;
            }
            return $arrayfile;
        }
    }

    public static function subsAsset($var, $no_ktp)
    {
        $patterns = array();
        $patterns[0] = '/]/';
        $patterns[1] = '/"/';
        $patterns[2] = "/~\~/";
        $arrayfile  = array();
        $i = 0;
        foreach ($var as $key => $item) {
            $arrayfile[$key][$i] = $item;
            foreach ($item as $val) {
                $j = 0;
                if ($arrayfile[$key][$i] = substr($val, 33, 32 - 2) === false) {
                    $arrayfile[$key][$i] = null;
                } else {

                    $arrayfile[$key][$i] =  preg_replace($patterns, "", str_replace("public/" . $no_ktp . "/debitur" . "/" . "EFILLINGASSET/", "", $val));
                    $j++;
                }

                $i++;
            }
            return $arrayfile;
        }
    }
    public static function subsSpk($var, $no_ktp)
    {
        $patterns = array();
        $patterns[0] = '/]/';
        $patterns[1] = '/"/';
        $patterns[2] = "/~\~/";
        $arrayfile  = array();
        $i = 0;
        foreach ($var as $key => $item) {
            $arrayfile[$key][$i] = $item;
            foreach ($item as $val) {
                $j = 0;
                if ($arrayfile[$key][$i] = substr($val, 33, 32 - 2) === false) {
                    $arrayfile[$key][$i] = null;
                } else {

                    $arrayfile[$key][$i] =  preg_replace($patterns, "", str_replace("public/" . $no_ktp . "/debitur" . "/" . "EFILLINGSPKNDK/", "", $val));
                    $j++;
                }

                $i++;
            }
            return $arrayfile;
        }
    }
}
