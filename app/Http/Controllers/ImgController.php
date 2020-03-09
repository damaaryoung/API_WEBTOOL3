<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use Illuminate\Support\Facades\File;
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

        // DB::connection('web')->table('agunan_tanah')->insert(['lam_imb' => $img]);

        try {
            return response()->json([
                'encode' => $img 
            ], 200);
        } catch (Exception $e) {
            echo 'bad';
        }
    }

    public function getDecode(){
        // $query = DB::connection('web')->table('agunan_tanah')->where('id', 7)->first();
        // $img64 = $query->lam_imb;

        // dd($img64);
        $base64 =  "data:image/pdf;base64,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L1Jlc291cmNlcyA8PAovWE9iamVjdCA8PAovWDAgNCAwIFIKPj4KPj4KL0NvbnRlbnRzIDUgMCBSCi9QYXJlbnQgMiAwIFIKPj4KZW5kb2JqCjIgMCBvYmoKPDwKL1R5cGUgL1BhZ2VzCi9LaWRzIFszIDAgUl0KL0NvdW50IDEKPj4KZW5kb2JqCjEgMCBvYmoKPDwKL1R5cGUgL0NhdGFsb2cKL1BhZ2VzIDIgMCBSCj4+CmVuZG9iago2IDAgb2JqCjw8Ci9Qcm9kdWNlciAod3d3Lmlsb3ZlcGRmLmNvbSkKL01vZERhdGUgKEQ6MjAyMDAzMDYxMDA3MzdaKQo+PgplbmRvYmoKNyAwIG9iago8PAovU2l6ZSA4Ci9Sb290IDEgMCBSCi9JbmZvIDYgMCBSCi9JRCBbPDE2OUUzMDhGN0ExMjdGNEUxRkY2OTY1QzdBNzBBQkJCPiA8QjhEN0I4MkNDQjM5RjI2ODYyOTRGN0Q2MEFEMTM3N0M+XQovVHlwZSAvWFJlZgovVyBbMSAyIDJdCi9GaWx0ZXIgL0ZsYXRlRGVjb2RlCi9JbmRleCBbMCA4XQovTGVuZ3RoIDQwCj4+CnN0cmVhbQp4nGNgYPj/n1F5MwMDo3IVkFD6CSQY+EGsVpDYEyChYszAAACjMQaoCmVuZHN0cmVhbQplbmRvYmoKc3RhcnR4cmVmCjkyNjcKJSVFT0YK";

        // $imgRep = str_replace(array("data:image/jpeg;base64,","data:image/jpg;base64,","data:image/png;base64,","data:image/pdf;base64,"), "", $base64);
        // $file = base64_decode($imgRep);

        // if(preg_match("/^data:image\/(?<extension>(?:png|gif|jpg|jpeg));base64,(?<image>.+)$/", $base64, $matchings))
        // {
        //     $imageData = base64_decode($matchings['image']);
        //     $extension = $matchings['extension'];
        //     $filename = sprintf("image.%s", $extension);

        //     if(file_put_contents($filename, $imageData))
        //     {
        //         $puth_file = 'oke';
        //     }
        // }else{
        //     $puth_file = 'not same';
        // }
        if (preg_match('/^data:image\/(\w+);base64,/', $base64)) {
            $imgRep = substr($base64, strpos($base64, ',') + 1);
        
            $imgdata = base64_decode($imgRep);

            $f = finfo_open();

            $mime_type = finfo_buffer($f, $imgdata, FILEINFO_MIME_TYPE);

            $extension = explode("/",$mime_type);

            $uploadPath = public_path("base");
            $fileName = "new.".$extension[1];
            $fileStream = fopen($uploadPath . '/' . $fileName , "wb"); 

            fwrite($fileStream, $imgdata);
            fclose($fileStream);
        }else{
            return response()->json([
                'error' => 'image not valid'
            ], 200);
        }

        return response()->json([
            'path_dir' => 'ok'
        ], 200);
        // return response($file, 200)->header('Content-Type', 'image/png');
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
