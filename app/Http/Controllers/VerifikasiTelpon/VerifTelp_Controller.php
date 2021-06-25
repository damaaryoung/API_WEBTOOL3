<?php

namespace App\Http\Controllers\VerifikasiTelpon;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use App\Http\Requests\Transaksi\BlankRequest;
use App\Models\Pengajuan\CAA\Penyimpangan;
use App\Models\Pengajuan\AO\AgunanKendaraan;
use App\Models\Pengajuan\AO\AgunanTanah;
use App\Models\Pengajuan\AO\PemeriksaanAgunTan;
use App\Models\Pengajuan\AO\PemeriksaanAgunKen;
use App\Models\Pengajuan\SO\Penjamin;
use App\Models\Pengajuan\SO\Debitur;
use App\Models\Pengajuan\CA\InfoACC;
use App\Models\Pengajuan\CA\MutasiBank;
use Illuminate\Support\Facades\File;
use App\Models\Transaksi\Approval;
use App\Models\Transaksi\TransCAA;
use App\Models\Transaksi\TransCA;
use App\Models\Transaksi\TransSO;
use App\Models\Transaksi\TransAO;
use App\Models\AreaKantor\JPIC;
use App\Models\AreaKantor\PIC;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\ActivitySo;
use App\Models\Efilling\Bi_checking;
use App\Models\Efilling\Efilling;
use App\Models\Efilling\Efilling_asset;
use App\Models\Efilling\Efilling_bi;
use App\Models\Efilling\Efilling_ca;
use App\Models\Efilling\Efilling_legal;
use App\Models\Efilling\Efilling_spkndk;
use App\Models\Efilling\EfillingJaminan;
use App\Models\Transaksi\LogRekomCA;
use App\Models\Transaksi\LogRingAnalisa;
use App\Models\v2\Target_lending;
use App\Models\master_nilai;
use App\Models\master_transaksi;
use Image;
use App\Models\Efilling\EfillingNasabah;
use App\Models\Efilling\EfillingPermohonan;
use App\Models\Efilling\Verifnpwp as EfillingVerifnpwp;
use App\Models\Verifikasi\Verifcadebt;
use App\Models\Verifikasi\Verifnpwp;
use App\Models\Verifikasi\Verifpasangan;
use Illuminate\Support\Facades\Storage;

// use Intervention\Image\Image;

// use Intervention\Image\ImageManagerStatic as Image;


use App\Models\MasterActivity\Activity;
use App\Models\MasterActivity\TargetPeriodik;
use App\Models\MasterActivity\TargetApproval;
use App\Models\Pengajuan\SO\Pasangan;
use App\Models\Transaksi\Lpdk_lampiran;
use App\Models\Verifikasi\ApinegCase;
use App\Models\Verifikasi\Verifpenjamin;
use App\Models\Verifikasi\Verifproperti;
use App\Models\Verifikasi\VerifReqcadebt;
use App\Models\Verifikasi\VerifReqnpwp;
use App\Models\Verifikasi\VerifReqpasangan;
use App\Models\Verifikasi\VerifReqproperti;
use App\Models\VerifikasiTelp\VerifTelp;
use Illuminate\Support\Facades\DB;


class VerifTelp_Controller extends BaseController
{
    public function store(Request $req, $id)
    {
        $user_id = $req->auth->user_id;
        $pic = $req->pic; // From PIC middleware
        //  dd($user_id);
        $id_area = array();
        $i = 0;
        foreach ($pic as $val) {
            $id_area[] = $val['id_area'];
            $i++;
        }

        $id_cabang = array();
        foreach ($pic as $val) {
            $id_cabang[] = $val['id_cabang'];
            $i++;
        }
        $arrrr = array();
        foreach ($pic as $val) {
            $arrrr[] = $val['jpic']['cakupan'];
            $i++;
        }

        $id_area   = $id_area;
        $id_cabang = $id_cabang;

        $scope     = $arrrr;

        $so = TransSO::where('id', $id)->first();
        //dd($so);
        if (!empty($req->input('id_param_verif'))) {

            for ($i = 0; $i < count($req->input('id_param_verif')); $i++) {

                $data[] = array(
                    'id_trans_so'
                    => empty($so)
                        ? null : $so->id,
                    'id_param_verif'
                    => empty($req->input('id_param_verif')[$i])
                        ? null : $req->id_param_verif[$i],
                    'user_id_ca'
                    => empty($user_id)
                        ? null : $user_id,
                    'hasil'
                    => empty($req->input('hasil')[$i])
                        ? 0 : $req->hasil[$i],
                    'keterangan'
                    => empty($req->input('keterangan')[$i])
                        ? null : $req->keterangan[$i],
                );
            }

            for ($i = 0; $i < count($data); $i++) {
                $mutasi = VerifTelp::create($data[$i]);
            }
        }

        return response()->json([
            "code" => 200,
            "status" => "success",
            "data" => $data
        ]);
    }

    public function show($id, Request $req)
    {
        $user_id = $req->auth->user_id;
        $pic = $req->pic; // From PIC middleware
        //  dd($user_id);
        $id_area = array();
        $i = 0;
        foreach ($pic as $val) {
            $id_area[] = $val['id_area'];
            $i++;
        }

        $id_cabang = array();
        foreach ($pic as $val) {
            $id_cabang[] = $val['id_cabang'];
            $i++;
        }
        $arrrr = array();
        foreach ($pic as $val) {
            $arrrr[] = $val['jpic']['cakupan'];
            $i++;
        }

        $id_area   = $id_area;
        $id_cabang = $id_cabang;

        $scope     = $arrrr;
        $telp = VerifTelp::where('id_trans_so', $id)->get();
        $so = TransSO::where('id', $id)->first();

        $data = DB::connection('web')->select("SELECT (SELECT parameter FROM verif_telp_param WHERE id = id_param_verif) AS kolom, 
(CASE hasil
    WHEN 1 THEN 'sesuai'
    WHEN 2 THEN 'tidak'
    WHEN 3 THEN 'janggal'
    ELSE 'belum verifikasi' END)  AS hasil,
    keterangan FROM verif_telp WHERE id_trans_so='$id'");

        return response()->json([
            "code" => 200,
            "status" => "success",
            "nomor_so" => $so->nomor_so,
            "data" => $data
        ]);
    }
}
