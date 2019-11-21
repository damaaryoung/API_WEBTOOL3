<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use Illuminate\Http\Request;
use Image;
use DB;

class ImgController extends BaseController
{
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
