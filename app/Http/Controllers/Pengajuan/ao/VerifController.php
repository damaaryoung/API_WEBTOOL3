<?php

namespace App\Http\Controllers\Pengajuan\ao;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use App\Models\Pengajuan\AO\VerifModel;
// use Illuminate\Support\Facades\File;
use App\Models\Transaksi\TransSO;
use App\Models\Transaksi\TransAO;
use App\Models\AreaKantor\JPIC;
use App\Models\AreaKantor\PIC;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;

class VerifController extends BaseController
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

        $check = VerifModel::where('id', $id)->first();

        if (empty($check)) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Data tidak ada"
            ], 404);
        }

        $dataVerifikasi = array(
            'ver_ktp_debt'            => empty($req->input('ver_ktp_debt')) ? $check->ver_ktp_debt : $req->input('ver_ktp_debt'),

            'ver_kk_debt'             => empty( $req->input('ver_kk_debt')) ? $check->ver_kk_debt :  $req->input('ver_kk_debt'),

            'ver_akta_cerai_debt'     => empty($req->input('ver_akta_cerai_debt')) ? $check->ver_akta_cerai_debt : $req->input('ver_akta_cerai_debt'),

            'ver_akta_kematian_debt'  => empty($req->input('ver_akta_kematian_debt')) ? $check->ver_akta_kematian_debt : $req->input('ver_akta_kematian_debt'),

            'ver_rek_tabungan_debt'   => empty($req->input('ver_rek_tabungan_debt')) ? $check->ver_rek_tabungan_debt : $req->input('ver_rek_tabungan_debt'),

            'ver_sertifikat_debt'     => empty($req->input('ver_sertifikat_debt')) ? $check->ver_sertifikat_debt : $req->input('ver_sertifikat_debt'),

            'ver_sttp_pbb_debt'       => empty($req->input('ver_sttp_pbb_debt')) ? $check->ver_sttp_pbb_debt : $req->input('ver_sttp_pbb_debt'),

            'ver_imb_debt'            => empty($req->input('ver_imb_debt')) ? $check->ver_imb_debt : $req->input('ver_imb_debt'),

            'ver_ktp_pasangan'        => empty($req->input('ver_ktp_pasangan')) ? $check->ver_ktp_pasangan : $req->input('ver_ktp_pasangan'),

            'ver_akta_nikah_pasangan' => empty($req->input('ver_akta_nikah_pasangan')) ? $check->ver_akta_nikah_pasangan : $req->input('ver_ktp_pasangan'),

            'ver_data_penjamin'       => empty($req->input('ver_data_penjamin')) ? $check->ver_data_penjamin : $req->input('ver_data_penjamin'),

            'ver_sku_debt'            => empty($req->input('ver_sku_debt')) ? $check->ver_sku_debt : $req->input('ver_sku_debt'),

            'ver_pembukuan_usaha_debt'=> empty($req->input('ver_pembukuan_usaha_debt')) ? $check->ver_pembukuan_usaha_debt : $req->input('ver_pembukuan_usaha_debt'),

            'catatan'                 => empty($req->input('catatan')) ? $check->catatan : $req->input('catatan')
        );

        DB::connection('web')->beginTransaction();
        try{

            VerifModel::where('id', $id)->update($dataVerifikasi);

            DB::connection('web')->commit();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message'=> 'Data untuk AO berhasil dikirim',
                'data'   => $dataVerifikasi
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