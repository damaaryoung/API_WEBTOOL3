<?php

namespace App\Http\Controllers\Transaksi;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use App\Models\Pengajuan\AO\AgunanTanah;
use App\Models\Transaksi\TransAO;
use App\Models\Transaksi\Lpdk;
use App\Models\Transaksi\Lpdk_lampiran;
use App\Models\Transaksi\Lpdk_sertifikat;
use App\Models\Transaksi\Lpdk_penjamin;
use App\Models\Pengajuan\SO\Penjamin;
use App\Models\Pengajuan\AO\AgunanKendaraan;
use App\Models\Transaksi\Lpdk_kendaraan;
use App\Models\Transaksi\TransCA;
use Illuminate\Support\Facades\File;
use App\Models\Transaksi\TransSO;
use App\Models\Pengajuan\SO\Debitur;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Lpdk_LampiranController extends BaseController
{
    public function EditLampiran($id_trans, Request $req)
    {

        $pic     = $req->pic; // From PIC middleware
        $user_id = $req->auth->user_id;
        //  dd($user_id);
        $get_lpdk = Lpdk::get();

        $cek_lpdk = Lpdk::where('trans_so', $id_trans)->first();

        // $cek_sertif = Lpdk_sertifikat::where('trans_so', $id_trans)->first();
        //dd($cek_lpdk);

        if ($cek_lpdk === null) {
            return response()->json([
                'code'  => 402,
                'message'  => 'data transaksi SO dengan id =' . ' ' . $id_trans . ' ' . 'tidak ditemukan'
            ]);
        }


        $id_deb = TransSO::where('id',$id_trans)->first();
        $check_debt_ktp = Debitur::where('id', $id_deb->id_calon_debitur)->first();
        //  $check_lpdk = Lpdk::where('id', $id)
        //  $check_debt = Lpdk::where('trans_so', $id)->first();

        $check_lamp = Lpdk_lampiran::where('trans_so', $id_trans)->first();

        $check_ktp_pen = Lpdk_penjamin::where('trans_so', $id_trans)->first();

        //   $path = 'public/' . $check_debt_ktp->no_ktp . '/debitur/lpdk';
        $check_lamp_ktp             = $check_lamp->lampiran_ktp_deb;
        $check_lamp_ktppas              = $check_lamp->lampiran_ktp_pasangan;
        $check_lamp_ktppen             = $check_lamp->lampiran_ktp_penjamin;
        $check_lamp_npwp             = $check_lamp->lampiran_npwp;
        $check_lamp_surat_kematian = $check_lamp->lampiran_surat_kematian;
        $check_lamp_sk_desa = $check_lamp->lampiran_sk_desa;
        $check_lamp_ajb = $check_lamp->lampiran_ajb;
        $check_lamp_ahliwaris = $check_lamp->lampiran_ahliwaris;
        $check_lamp_aktahibah = $check_lamp->lampiran_aktahibah;

        $check_lamp_sertifikat      = $check_lamp->lampiran_sertifikat;
        $check_lamp_sttp_pbb        = $check_lamp->lampiran_pbb;
        $check_lamp_imb             = $check_lamp->lampiran_imb;
        // $check_lamp_skk             = $check_lamp->lampiran_skk;
        // $check_lamp_sku             = $check_lamp->lampiran_sku;
        // $check_lamp_slip_gaji       = $check_lamp->lampiran_slipgaji;
        $check_lamp_kk              = $check_lamp->lampiran_kk;
        $check_surat_lahir    = $check_lamp->lampiran_surat_lahir;
        $check_surat_nikah    = $check_lamp->lampiran_surat_nikah;
        $check_surat_cerai    = $check_lamp->lampiran_surat_cerai;
        $check_lamp_ktp_pemilik_sert   = $check_lamp->lampiran_ktp_pemilik_sertifikat;
        $check_lamp_ktp_pasangan_sert   = $check_lamp->lampiran_ktp_pasangan_sertifikat;
        // if($check_ktp_pen === null) {
        //     $check_ktp_pen = null;
        // } else {
        // }
        // $check_lamp_ktp_pasangan_penjamin   = $check_ktp_pen->lampiran_ktp_penjamin;
        


        if ($file = $req->file('lampiran_npwp')) {
            $path = 'public/' . $check_debt_ktp->no_ktp . '/debitur/lpdk/lampiran';
            $name = '';
            $check = $check_lamp_npwp;

            $lamp_npwp = Helper::uploadImg($check, $file, $path, $name);
        } else {
            $lamp_npwp = $check_lamp_npwp;
        }



        // if ($files = $req->file('lampiran_pbb')) {
        //     $path = 'public/' . $check_debt_ktp->no_ktp . '/debitur/lpdk/lampiran';
        //     $name = 'pbb.';
        //     $check = $check_lamp_sttp_pbb;
        //     foreach ($files as $file) {
        //         $arrayPath[] = Helper::uploadImg($check, $file, $path, $name);
        //     }

        //     $lamp_sttp_pbb = $arrayPath;
        // } else {
        //     $lamp_sttp_pbb = $check_lamp_sttp_pbb;
        // }

        // if ($files = $req->file('lampiran_imb')) {
        //     $path = 'public/' . $check_debt_ktp->no_ktp . '/debitur/lpdk/lampiran';
        //     $name = 'imb.';
        //     $check = $check_lamp_imb;
        //     foreach ($files as $file) {
        //         $arrayPath[] = Helper::uploadImg($check, $file, $path, $name);
        //     }

        //     $lamp_imb = $arrayPath;
        // } else {
        //     $lamp_imb = $check_lamp_imb;
        // }

        if ($file = $req->file('lampiran_surat_kematian')) {
            $path = 'public/' . $check_debt_ktp->no_ktp . '/debitur/lpdk/lampiran';
            $name = '';
            $check = $check_lamp_surat_kematian;

            $lamp_sk_kematian   = Helper::uploadImg($check, $file, $path, $name);
        } else {
            $lamp_sk_kematian = $check_lamp_surat_kematian;
        }


        if ($file = $req->file('lampiran_sk_desa')) {
            $path = 'public/' . $check_debt_ktp->no_ktp . '/debitur/lpdk/lampiran';
            $name = '';
            $check = $check_lamp_sk_desa;

            $lamp_sk_desa = Helper::uploadImg($check, $file, $path, $name);
        } else {
            $lamp_sk_desa = $check_lamp_sk_desa;
        }



        if ($file = $req->file('lampiran_surat_lahir')) {
            $path = 'public/' . $check_debt_ktp->no_ktp . '/debitur/lpdk/lampiran';
            $name = '';
            $check = $check_surat_lahir;

            $lamp_suratlahir = Helper::uploadImg($check, $file, $path, $name);
        } else {
            $lamp_suratlahir = $check_surat_lahir;
        }



        if ($file = $req->file('lampiran_surat_nikah')) {
            $path = 'public/' . $check_debt_ktp->no_ktp . '/debitur/lpdk/lampiran';
            $name = '';
            $check = $check_surat_nikah;
            $lamp_suratnikah  = Helper::uploadImg($check, $file, $path, $name);
        } else {
            $lamp_suratnikah = $check_surat_nikah;
        }

        if ($file = $req->file('lampiran_surat_cerai')) {
            $path = 'public/' . $check_debt_ktp->no_ktp . '/debitur/lpdk/lampiran';
            $name = '';
            $check = $check_surat_cerai;
            $lamp_suratcerai = Helper::uploadImg($check, $file, $path, $name);
        } else {
            $lamp_suratcerai = $check_surat_cerai;
        }


        // if ($files = $req->file('lampiran_ktp_pem_sertifikat')) {
        //     $path = 'public/' . $check_debt_ktp->no_ktp . '/debitur/lpdk/ktppemilik';
        //     $name = 'lamp_ktppemiliksertifikat.';
        //     $check = $check_lamp_ktp_pemilik_sert;
        //     foreach ($files as $file) {
        //         $arrayPath[] = Helper::uploadImg($check, $file, $path, $name);
        //     }

        //     $lamp_ktppemsert = $arrayPath;
        // } else {
        //     $lamp_ktppemsert = $check_lamp_ktp_pemilik_sert;
        // }

        // if ($files = $req->file('lampiran_ktp_pas_sertifikat')) {
        //     $path = 'public/' . $check_debt_ktp->no_ktp . '/debitur/lpdk/ktppemilik';
        //     $name = 'lamp_ktppasangansertifikat.';
        //     $check = $check_lamp_ktp_pasangan_sert;

        //     foreach ($files as $file) {
        //         $arrayPath[] = Helper::uploadImg($check, $file, $path, $name);
        //     }
        //     $lamp_ktppassert = $arrayPath;
        // } else {
        //     $lamp_ktppassert = $check_lamp_ktp_pasangan_sert;
        // }


        $data_lamp = array(
            'trans_so' => $id_trans,
            //   'lampiran_ktp_deb' => $lamp_ktp,
            // 'lampiran_ktp_pasangan' => $lamp_ktp_pas,
            //'lampiran_ktp_penjamin' => $lamp_ktp_pen,
            'lampiran_npwp' => $lamp_npwp,
            // 'lampiran_pbb' => $lamp_sttp_pbb,
            // 'lampiran_imb' => $lamp_imb,
            // 'lampiran_skk' => $lamp_skk,
            // 'lampiran_sku' => $lamp_sku,
            // 'lampiran_slipgaji' => $lamp_slip_gaji,
            'lampiran_surat_kematian' => $lamp_sk_kematian,
            'lampiran_sk_desa'  => $lamp_sk_desa,
            // 'lampiran_ajb' => $lamp_ajb,
            //    'lampiran_ahliwaris' => $lamp_ahliwaris,
            //  'lampiran_aktahibah'   => $lamp_aktahibah,
            //'lampiran_kk'       => $lamp_kk,
            'lampiran_surat_lahir'  => $lamp_suratlahir,
            'lampiran_surat_nikah'  => $lamp_suratnikah,
            'lampiran_surat_cerai'  => $lamp_suratcerai,
            // 'lampiran_sertifikat' => $lamp_sertifikat,
            // 'lampiran_ktp_pasangan_sertifikat' => $lamp_ktppassert,
        );
        //     }
        $id_lamp = Lpdk::where('trans_so', $id_trans)->first();
        $lamp = Lpdk_lampiran::where('trans_so', $id_trans)->update($data_lamp);

        // Lpdk::where('trans_so', $id_trans)->update(['id_lampiran' => $id_lamp->id_lampiran . ',' . $lamp->id]);
        try {
            return response()->json([
                'code' => 200,
                'status' => 'success',
                'data' => $data_lamp
            ]);
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
