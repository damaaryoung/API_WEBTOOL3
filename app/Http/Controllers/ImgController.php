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
            "Accept: application/json",
            "Authorization: ".$auth,
            // "content-type: multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW"
        );

        $fields = array(
            'penyimpangan'       => $req->input('penyimpangan'),
            'team_caa[]'         => $req->team_caa,
            'rincian'            => $req->input('rincian'),
            'file_report_mao'    => $req->file('file_report_mao'),
            'file_report_mca'    => $req->file('file_report_mca'),
            'status_file_agunan' => $req->input('status_file_agunan'),
            'file_agunan[]'      => $req->file_agunan,
            'status_file_usaha'  => $req->input('status_file_usaha'),
            'file_usaha[]'       => $req->file_usaha,
            'file_tempat_tinggal'=> $req->file('file_tempat_tinggal'),
            'file_lain'          => $req->file('file_lain'),
            'catatan_caa'        => $req->input('catatan_caa')
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        // echo json_encode($fields);

        $result = curl_exec($ch);
        echo curl_error($ch);

        if ($result === false) {
            die('Curl failed: ' . curl_error($ch));
        }

        curl_close($ch);
        return $result;
    }
}
