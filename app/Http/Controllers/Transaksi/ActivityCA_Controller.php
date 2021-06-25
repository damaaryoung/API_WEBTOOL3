<?php

namespace App\Http\Controllers\Transaksi;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Models\Pengajuan\CA\RekomendasiPinjaman;

use App\Models\Pengajuan\AO\AgunanTanah;
use App\Models\Pengajuan\AO\AgunanKendaraan;
use App\Models\Pengajuan\AO\PemeriksaanAgunTan;
use App\Models\Pengajuan\AO\PemeriksaanAgunKen;
use App\Models\Pengajuan\SO\FasilitasPinjaman;
use App\Models\Pengajuan\SO\Debitur;
use App\Models\Pengajuan\CA\AsuransiJaminan;
use App\Models\Pengajuan\CA\AsuransiJaminanKen;
use App\Models\Pengajuan\AO\PendapatanUsaha;
use App\Models\Pengajuan\SO\Penjamin;
use App\Http\Controllers\Controller as Helper;
use App\Http\Requests\Transaksi\BlankRequest;
use App\Models\Pengajuan\CA\RingkasanAnalisa;
use App\Models\Pengajuan\CA\RekomendasiCA;
use App\Models\Pengajuan\CA\AsuransiJiwa;
use App\Models\Pengajuan\AO\KapBulanan;
use App\Models\Pengajuan\CA\MutasiBank;
use App\Models\Pengajuan\CA\TabDebt;
use App\Models\Pengajuan\CA\InfoACC;
use App\Models\Transaksi\TransCA;
use App\Models\Transaksi\TransAO;
use App\Models\Transaksi\TransSO;
use App\Models\AreaKantor\JPIC;
use App\Models\AreaKantor\PIC;
use Illuminate\Http\Request;
use App\Models\Pengajuan\SO\Anak;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use App\Models\master_nilai;
use App\Models\master_transaksi;
use App\Models\Pengajuan\CA\PicCA;
// use Image;
//use DB;
use Illuminate\Support\Facades\DB;

class ActivityCA_Controller extends BaseController
{
    public function getpicCA()
    {

        $team_ca = PicCA::get();
        if ($team_ca == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data All Pic Tidak Ditemukan'
            ], 404);
        }

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => $team_ca
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function getpicCAid($id)
    {

        $team_ca = PicCA::where('id', $id)->first();
        if ($team_ca == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Pic dengan id' . '' . $id . '' . 'Tidak Ditemukan'
            ], 404);
        }

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => $team_ca
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }
}
