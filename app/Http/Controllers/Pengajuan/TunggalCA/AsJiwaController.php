<?php

namespace App\Http\Controllers\Pengajuan\TunggalCA;

use Laravel\Lumen\Routing\Controller as BaseController;

// Form Request
use App\Http\Requests\Pengajuan\AsJiwaReq;

// Models
use App\Models\Pengajuan\CA\AsuransiJiwa;

use Carbon\Carbon;
use DB;

class AsJiwaController extends BaseController
{
    public function index(){
        $query = AsuransiJiwa::get()->toArray();

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => $query
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function show($id){
        $query = AsuransiJiwa::where('id', $id)->first();

        if($query == null){
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'data kosong'
            ], 404);
        }

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => $query
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function update($id, AsJiwaReq $req){
        $check = AsuransiJiwa::where('id', $id)->first();

        if ($check == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => "Data dengan id {$id} tidak ditemukan"
            ], 404);
        }

        $asJiwa = array(
            'nama_asuransi'       
                => empty($req->input('nama_asuransi_jiwa')) 
                ? $check->nama_asuransi : $req->input('nama_asuransi_jiwa'),

            'jangka_waktu'        
                => empty($req->input('jangka_waktu_as_jiwa')) 
                ? $check->jangka_waktu : $req->input('jangka_waktu_as_jiwa'),

            'nilai_pertanggungan' 
                => empty($req->input('nilai_pertanggungan_as_jiwa')) 
                ? $check->nilai_pertanggungan : $req->input('nilai_pertanggungan_as_jiwa'),

            'jatuh_tempo'         
                => empty($req->input('jatuh_tempo_as_jiwa')) 
                ? $check->jatuh_tempo : Carbon::parse($req->input('jatuh_tempo_as_jiwa'))->format('Y-m-d'),

            'berat_badan'         
                => empty($req->input('berat_badan_as_jiwa')) 
                ? $check->berat_badan : $req->input('berat_badan_as_jiwa'),

            'tinggi_badan'        
                => empty($req->input('tinggi_badan_as_jiwa')) 
                ? $check->tinggi_badan : $req->input('tinggi_badan_as_jiwa'),

            'umur_nasabah'        
                => empty($req->input('umur_nasabah_as_jiwa')) 
                ? $check->umur_nasabah : $req->input('umur_nasabah_as_jiwa')
        );

        DB::connection('web')->beginTransaction();

        try {
            AsuransiJiwa::where('id', $id)->update($asJiwa);

            DB::connection('web')->commit();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message'=> 'Update Data Berhasil',
                'data'   => $asJiwa
            ], 200);
        } catch (Exception $e) {

            $err = DB::connection('web')->rollback();

            return response()->json([
                'code'    => 501,
                'status'  => 'error',
                'message' => $err
            ], 501);
        }
    }
}
