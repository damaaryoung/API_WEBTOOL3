<?php

namespace App\Http\Controllers\Pengajuan\ao;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use App\Models\Pengajuan\AO\ValidModel;
// use Illuminate\Support\Facades\File;
use App\Models\AreaKantor\JPIC;
use App\Models\AreaKantor\PIC;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;

class ValidController extends BaseController
{
    public function update($id, Request $req) 
    {
        $user_id  = $req->auth->user_id;

        $PIC = PIC::where('user_id', $user_id)->first();

        if (empty($PIC)) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "User_ID anda adalah '".$user_id."'. Namun anda belum terdaftar sebagai PIC. Harap daftarkan diri sebagai PIC pada form PIC atau hubungi bagian IT"
            ], 404);
        }

        $check = ValidModel::where('id', $id)->first();

        if ($check == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data tidak ada'
            ], 404);
        }

        $dataValidasi = array(
            'val_data_debt'       => empty($req->input('val_data_debt')) ? $check->val_data_debt : $req->input('val_data_debt'),
            'val_lingkungan_debt' => empty($req->input('val_lingkungan_debt')) ? $check->val_lingkungan_debt : $req->input('val_lingkungan_debt'),
            'val_domisili_debt' => empty($req->input('val_domisili_debt')) ? $check->val_domisili_debt : $req->input('val_domisili_debt'),
            'val_pekerjaan_debt' => empty($req->input('val_pekerjaan_debt')) ? $check->val_pekerjaan_debt : $req->input('val_pekerjaan_debt'),
            'val_data_pasangan' => empty($req->input('val_data_pasangan')) ? $check->val_data_pasangan : $req->input('val_data_pasangan'),
            'val_data_penjamin' => empty($req->input('val_data_penjamin')) ? $check->val_data_penjamin : $req->input('val_data_penjamin'),
            'val_agunan' => empty($req->input('val_agunan')) ? $check->val_agunan : $req->input('val_agunan'),
            'catatan' => empty($req->input('catatan')) ? $check->catatan : $req->input('catatan')
        );

        DB::connection('web')->beginTransaction();
        try{

            ValidModel::where('id', $id)->update($dataValidasi);

            DB::connection('web')->commit();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message'=> 'Data untuk AO berhasil dikirim',
                'data'   => $dataValidasi
                // 'message'=> $msg
            ], 200);
        } catch (\Exception $e) {
            $err = DB::connection('web')->rollback();
            return response()->json([
                'code'    => 501,
                'status'  => 'error',
                'message' => $err
            ], 501);
        }
    }
}