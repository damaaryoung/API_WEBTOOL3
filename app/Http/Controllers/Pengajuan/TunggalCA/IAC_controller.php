<?php

namespace App\Http\Controllers\Pengajuan\TunggalCA;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

// Form Request
use App\Http\Requests\Pengajuan\IACRequest;
use Exception;

// Models
use App\Models\Pengajuan\CA\InfoACC;
use App\Models\Transaksi\TransCA;
use Illuminate\Support\Facades\DB;

class IAC_Controller extends BaseController
{
    public function index(){
        $query = InfoACC::get()->toArray();

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
        $query = InfoACC::where('id', $id)->first();

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

    public function update($id, IACRequest $req){
        $check = InfoACC::where('id', $id)->first();

        if ($check === null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => "Data dengan id {$id} tidak ditemukan"
            ], 404);
        }

        // dd($check->id);

        $check_ca = TransCA::where('id_log_tabungan', $check->id)->first();
        // dd($check_ca);
        $rekomen_pendapatan = ($check_ca == null ? 0 : $check_ca->kapbul['total_pemasukan']);
$data_cc = array(
        'nama_bank'       => empty($req->input('nama_bank_acc'))       ? "" : $req->input('nama_bank_acc'),
                    'plafon'          => empty($req->input('plafon_acc'))          ? 0 : $req->input('plafon_acc'),
                    'baki_debet'      => empty($req->input('baki_debet_acc'))      ? 0 : $req->input('baki_debet_acc'),
                    'angsuran'        => empty($req->input('angsuran_acc'))        ? 0 : $req->input('angsuran_acc'),
                    'collectabilitas' => empty($req->input('collectabilitas_acc')) ? 0 : $req->input('collectabilitas_acc'),
                    'jenis_kredit'    => empty($req->input('jenis_kredit_acc'))    ? "" : $req->input('jenis_kredit_acc')
                );

        DB::connection('web')->beginTransaction();
      // $t = TransCA::where('id_trans_so',$id)->first();
        try {
           $info = InfoACC::where('id',$id)->update($data_cc);

        //    TransCA::where('id_trans_so',$id)->update(['id_info_analisa_cc' => $t->id_info_analisa_cc.",".$info->id]);

            DB::connection('web')->commit();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message'=> 'Update Data Tabungan Nasabah Berhasil',
                'data'   => $data_cc
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

    public function store($id, IACRequest $req){
        $check = TransCA::where('id_trans_so', $id)->first();

        if ($check == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => "Data dengan id {$id} tidak ditemukan"
            ], 404);
        }

        // dd($check->id);

        $check_ca = TransCA::where('id_log_tabungan', $check->id)->first();
        // dd($check_ca);
        $rekomen_pendapatan = ($check_ca == null ? 0 : $check_ca->kapbul['total_pemasukan']);
$data_cc = array(
        'nama_bank'       => empty($req->input('nama_bank_acc'))       ? "" : $req->input('nama_bank_acc'),
                    'plafon'          => empty($req->input('plafon_acc'))          ? 0 : $req->input('plafon_acc'),
                    'baki_debet'      => empty($req->input('baki_debet_acc'))      ? 0 : $req->input('baki_debet_acc'),
                    'angsuran'        => empty($req->input('angsuran_acc'))        ? 0 : $req->input('angsuran_acc'),
                    'collectabilitas' => empty($req->input('collectabilitas_acc')) ? 0 : $req->input('collectabilitas_acc'),
                    'jenis_kredit'    => empty($req->input('jenis_kredit_acc'))    ? "" : $req->input('jenis_kredit_acc')
                );

        DB::connection('web')->beginTransaction();
       $t = TransCA::where('id_trans_so',$id)->first();
        try {
           $info = InfoACC::create($data_cc);

           TransCA::where('id_trans_so',$id)->update(['id_info_analisa_cc' => $t->id_info_analisa_cc.",".$info->id]);

            DB::connection('web')->commit();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message'=> 'Update Data Tabungan Nasabah Berhasil',
                'data'   => $data_cc
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
