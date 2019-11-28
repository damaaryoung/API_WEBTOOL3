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

        $title = 'this is title';
        $msg   = 'this is Message';

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
}
