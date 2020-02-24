<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use Illuminate\Http\Request;
use Image;
use DB;

class ImgController extends BaseController
{
    public function push(Request $req){
        // $fcm_token = 'eVl--mRK8IY:APA91bFcPr9jKKpPN-XYRHGQtbRRP14MI4CYrW0FDUhp-AE_Pb2uytKVKm-mkYm5GdNlGqNuvoVhRPuTGNPU1P0BdIEzIFsuv3qE6dJUWJBzhOF7fNwiXm8W_kPfLAPzFEMj4j6Oq-4_';

        $fcm_token = $req->input('fcm_token');

        $title = $req->input('title');
        $msg   = $req->input('msg');

        $push_an = Helper::push_notif($fcm_token, $title, $msg);

        try {
            return response()->json([
                'success' => true,
                'code'    => 200,
                'data'    => $push_an
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'code'    => 501,
                'data'    => $e
            ], 501);
        }
    }

    public function upload(Request $req){
        $file = $req->file('lam_imb');
        // $type = pathinfo($file, PATHINFO_EXTENSION);
        // $extention = $file->getClientOriginalExtension();
        // $data = file_get_contents($file);
        // $base64 = 'data:image/' . $extention . ';base64,' . base64_encode($data);

        // $img = $this->img64enc($file);
        $img = Helper::img64enc($file);

        DB::connection('web')->table('agunan_tanah')->insert(['lam_imb' => $img]);

        try {
            echo 'ok';
        } catch (Exception $e) {
            echo 'bad';
        }
    }

    public function getDecode(){
        $query = DB::connection('web')->table('agunan_tanah')->where('id', 7)->first();
        $img64 = $query->lam_imb;

        dd($img64);

        $imgRep = str_replace(array("data:image/jpeg;base64,","data:image/jpg;base64,","data:image/png;base64,"), "", $img64);
        $base64 = base64_decode($imgRep);

        return response($base64, 200)->header('Content-Type', 'image/png');
    }

    public function uploadCAA(Request $req){
        // $url = '103.31.232.149:3737/api/master/mcaa/66';
        $url = 'localhost:4100/api/master/mcaa/66';

        $auth = 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJCUFIgS3JlZGl0IE1hbmRpcmkgSW5kb25lc2lhIiwiaWQiOjExMzAsIm5payI6IjAyMTkwNzA4OSIsInVzZW5hbWUiOiJhZ2lmIiwia2RfY2FiYW5nIjoyLCJkaXZpc2lfaWQiOiJJVCIsImphYmF0YW4iOiJJVCBTVEFGRiIsImVtYWlsIjoiaXRAa3JlZGl0bWFuZGlyaS5jby5pZCIsIm5hbWEiOiJBUFJFTEEgQUdJRiBTT0ZZQU4iLCJpYXQiOjE1Nzk1MTAyNTEsImV4cCI6MTU4MDExNTA1MX0.1l30NvuMaxRXUsqogoIpAD1A0G6eegRfIXZL3vmO9qo';

        $headers = array(
            'Acept: application/json',
            // 'Content-Type: application/json',
            'Authorization: '.$auth,
            'Content-Type: multipart/form-data',
            // 'Content-Type: application/x-www-form-urlencoded',
            // 'Content-Length: 395',
            // 'User-Agent: Wget/1.12 (solaris2.10)',
            // 'Connection: Keep-Alive',
            // 'Accept: */*'
        );

        // $files = array();

        // foreach ($_FILES["file_agunan"]["error"] as $key => $error) {
        //     if ($error == UPLOAD_ERR_OK) {

        //         $files["file_agunan[$key]"] = curl_file_create(
        //             $_FILES['file_agunan']['tmp_name'][$key],
        //             $_FILES['file_agunan']['type'][$key],
        //             $_FILES['file_agunan']['name'][$key]
        //         );
        //     }
        // }


        $fields = array(
            // 'penyimpangan'       => $req->input('penyimpangan'),
            'team_caa'         => $req->team_caa,
            // 'rincian'            => $req->input('rincian')
            // 'file_report_mao'    => new \CURLFile($_FILES['file_report_mao']['tmp_name'], $_FILES['file_report_mao']['type'], $_FILES['file_report_mao']['name']),

            // 'file_report_mca'    => new \CURLFile($_FILES['file_report_mca']['tmp_name'], $_FILES['file_report_mca']['type'], $_FILES['file_report_mca']['name']),

            // 'status_file_agunan' => $req->input('status_file_agunan'),
            // 'file_agunan' => array($files),
            // 'status_file_usaha'  => $req->input('status_file_usaha'),
            // 'file_usaha[]'       => new \CURLFile($_FILES['file_usaha']['tmp_name'], $_FILES['file_usaha']['type'], $_FILES['file_usaha']['name']),
            // 'file_tempat_tinggal'=> new \CURLFile($_FILES['file_tempat_tinggal']['tmp_name'], $_FILES['file_tempat_tinggal']['type'], $_FILES['file_tempat_tinggal']['name']),
            // 'file_lain'          => new \CURLFile($_FILES['file_lain']['tmp_name'], $_FILES['file_lain']['type'], $_FILES['file_lain']['name']),
            // 'catatan_caa'        => $req->input('catatan_caa')
        );

        // dd(json_encode($fields['team_caa']));

        // $data = $fields + $files;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        // curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        // curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
        // curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 ;Windows NT 6.1; WOW64; AppleWebKit/537.36 ;KHTML, like Gecko; Chrome/39.0.2171.95 Safari/537.36");
        // echo json_encode($fields);

        $result = curl_exec($ch);
        // $info   = curl_getinfo($ch);

        curl_close($ch);

        return $result;
    }
}
