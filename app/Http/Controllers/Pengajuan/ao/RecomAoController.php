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
use App\Models\Pengajuan\AO\RekomendasiAO;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RecomAoController extends BaseController
{
    public function update($id, Request $req)
    {
        $user_id  = $req->auth->user_id;

        $PIC = PIC::where('user_id', $user_id)->first();

        if (empty($PIC)) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "User_ID anda adalah '" . $user_id . "'. Namun anda belum terdaftar sebagai PIC. Harap daftarkan diri sebagai PIC pada form PIC atau hubungi bagian IT"
            ], 404);
        }

        $check = RekomendasiAO::where('id', $id)->first();

        if (empty($check)) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Data tidak ada"
            ], 404);
        }
        //  dd($req->input('ver_ktp_debt'));
        $dataRecom = array(
            'produk'            => $req->input('produk'),

            'plafon_kredit'             => $req->input('plafon_kredit'),

            'jangka_waktu'     => $req->input('jangka_waktu'),

            'suku_bunga'  => $req->input('suku_bunga'),

            'pembayaran_bunga'   =>  $req->input('pembayaran_bunga'),

            'akad_kredit'     => $req->input('akad_kredit'),

            'ikatan_agunan'       => $req->input('ikatan_agunan'),

            'analisa_ao'            =>  $req->input('analisa_ao'),

            'biaya_provisi'        => $req->input('biaya_provisi'),

            'biaya_administrasi' =>  $req->input('biaya_administrasi'),

            'biaya_credit_checking'       =>  $req->input('biaya_credit_checking'),

            'biaya_tabungan'            => $req->input('biaya_tabungan'),

        );

        //  dd($dataVerifikasi);
        DB::connection('web')->beginTransaction();
        try {

            RekomendasiAO::where('id', $id)->update($dataRecom);

            DB::connection('web')->commit();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message' => 'Data untuk AO berhasil dikirim',
                'data'   => $dataRecom
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
