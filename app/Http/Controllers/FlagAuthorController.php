<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use DB;

class FlagAuthorController extends BaseController
{
    // Otorisasi
    public function otoIndex(Request $req) {
        $user_id = $req->auth->user_id;
        // $user_id = '1131';

        try {
            $query = DB::connection('dpm')->table('flg_otorisasi')
                ->select('id', 'email', 'no_hp', 'tgl', 'jam', 'keterangan', 'waktu_otorisasi')
                ->where('id_modul',0)
                ->where('user_id', $user_id)
                ->where('otorisasi', 0)
                ->orderBy('tgl','asc')
                ->orderBy('jam', 'asc')
                ->limit(5)
                ->get();

            if($query == '[]'){
                return response()->json([
                    'code'    => 404,
                    'status'  => 'not found',
                    // 'jenis'   => 'otorisasi',
                    'message' => 'Data kosong'
                ], 404);
            }else{
                $pesan = array(
                    "transaksi"     => "Pengambilan Tabungan Tunai",
                    "tgl_transaksi" => "17-10-2013",
                    "no_rekening"   => "32-02-00066",
                    "nama_nasabah"  => "PONIMIN",
                    "alamat"        => "VILA MUTIARA GADING 2 BLOK F04 NO 18 RT/RW 007/016",
                    "jumlah"        => "7,000,000.00",
                    "keterangan"    => "Pengambilan Tabungan Tunai an: 32-02-00066 PONIMIN",
                    "nama_teller"   => "GRIS"
                );

                $data = array();
                $i = 0;
                foreach ($query as $key => $val) {
                    $data[$i]['id']              = $val->id;
                    $data[$i]['email']           = $val->email;
                    $data[$i]['no_hp']           = $val->no_hp;
                    $data[$i]['subject']         = 'Pengambilan Tabungan Tunai';
                    $data[$i]['pesan']           = $pesan;
                    $data[$i]['tgl']             = $val->tgl;
                    $data[$i]['jam']             = $val->jam;
                    $data[$i]['keterangan']      = $val->keterangan;
                    $data[$i]['waktu_otorisasi'] = $val->waktu_otorisasi;
                    $i++;
                }

                return response()->json([
                    'code'    => 200,
                    'status'  => 'success',
                    // 'jenis'   => 'otorisasi',
                    'data'    => $data
                ], 200);
            }
        } catch (Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function otoShow($id, Request $req) {
        $user_id = $req->auth->user_id;
        // $user_id = '1131';

        try {
            $query = DB::connection('dpm')->table('flg_otorisasi')
                ->select('id', 'email', 'no_hp', 'tgl', 'jam', 'keterangan', 'waktu_otorisasi')
                ->where('user_id', $user_id)
                ->where('otorisasi', 0)
                ->where('id', $id)
                ->get();

            if ($query == '[]') {
                return response()->json([
                    'code'    => 404,
                    'status'  => 'not found',
                    // 'jenis'   => 'otorisasi',
                    'message' => 'Data kosong'
                ], 404);
            }else{
                $pesan = array(
                    "transaksi"     => "Pengambilan Tabungan Tunai",
                    "tgl_transaksi" => "17-10-2013",
                    "no_rekening"   => "32-02-00066",
                    "nama_nasabah"  => "PONIMIN",
                    "alamat"        => "VILA MUTIARA GADING 2 BLOK F04 NO 18 RT/RW 007/016",
                    "jumlah"        => "7,000,000.00",
                    "keterangan"    => "Pengambilan Tabungan Tunai an: 32-02-00066 PONIMIN",
                    "nama_teller"   => "GRIS"
                );

                $data = array();
                $i = 0;

                foreach ($query as $key => $val) {
                    $data[$i]['id']              = $val->id;
                    $data[$i]['email']           = $val->email;
                    $data[$i]['no_hp']           = $val->no_hp;
                    $data[$i]['subject']         = 'Pengambilan Tabungan Tunai';
                    $data[$i]['pesan']           = $pesan;
                    $data[$i]['tgl']             = $val->tgl;
                    $data[$i]['jam']             = $val->jam;
                    $data[$i]['keterangan']      = $val->keterangan;
                    $data[$i]['waktu_otorisasi'] = $val->waktu_otorisasi;
                    $i++;
                }

                return response()->json([
                    'code'    => 200,
                    'status'  => 'success',
                    // 'jenis'   => 'otorisasi',
                    'data'    => $data
                ], 200);
            }
        } catch (Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function otoUpdate($id, Request $req) {
        $user_id = $req->auth->user_id;
        // $user_id = '1131';

        $Now = Carbon::now()->toDateTimeString();

        $update = DB::connection('dpm')->table('flg_otorisasi')
        ->where('user_id', $user_id)
        ->where('id', $data->id)
        ->update([
            'otorisasi' => 1,
            'waktu_otorisasi' => $Now
        ]);

        try {
            return response()->json([
                "code"    => 200,
                'status'  => 'success',
                // 'jenis'   => 'otorisasi',
                'message' => 'Otorisasi berhasil di update ke 1'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 501,
                'status'  => 'error',
                'message' => $e
            ], 501);
        }
    }

    // Approval
    public function aproIndex(Request $req) {
        $user_id = $req->auth->user_id;
        // $user_id = '1131';

        try {
            $query = DB::connection('dpm')->table('flg_otorisasi')
                ->select('id', 'email', 'no_hp', 'tgl', 'jam', 'keterangan', 'waktu_otorisasi')
                ->where('id_modul', '>',0)
                ->where('user_id', $user_id)
                ->where('approval', 0)
                ->orderBy('tgl','asc')
                ->orderBy('jam', 'asc')
                ->limit(5)
                ->get();

            if($query == '[]'){
                return response()->json([
                    'code'    => 404,
                    'status'  => 'not found',
                    'message' => 'Data kosong'
                ], 404);
            }else{
                $pesan = array(
                    "transaksi"     => "Pengambilan Tabungan Tunai",
                    "tgl_transaksi" => "17-10-2013",
                    "no_rekening"   => "32-02-00066",
                    "nama_nasabah"  => "PONIMIN",
                    "alamat"        => "VILA MUTIARA GADING 2 BLOK F04 NO 18 RT/RW 007/016",
                    "jumlah"        => "7,000,000.00",
                    "keterangan"    => "Pengambilan Tabungan Tunai an: 32-02-00066 PONIMIN",
                    "nama_teller"   => "GRIS"
                );

                $data = array();
                $i = 0;
                foreach ($query as $key => $val) {
                    $data[$i]['id']              = $val->id;
                    $data[$i]['email']           = $val->email;
                    $data[$i]['no_hp']           = $val->no_hp;
                    $data[$i]['subject']         = 'Pengambilan Tabungan Tunai';
                    $data[$i]['pesan']           = $pesan;
                    $data[$i]['tgl']             = $val->tgl;
                    $data[$i]['jam']             = $val->jam;
                    $data[$i]['keterangan']      = $val->keterangan;
                    $data[$i]['waktu_otorisasi'] = $val->waktu_otorisasi;
                    $i++;
                }

                return response()->json([
                    'code'    => 200,
                    'status'  => 'success',
                    // 'jenis'   => 'approval',
                    'data'    => $data
                ], 200);
            }
        } catch (Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function aproShow($id, Request $req) {
        $user_id = $req->auth->user_id;
        // $user_id = '1131';

        try {
            $query = DB::connection('dpm')->table('flg_otorisasi')
                ->where('user_id', $user_id)
                ->where('approval', 0)
                ->where('id', $id)
                ->get();

            if ($query == '[]') {
                return response()->json([
                    'code'    => 404,
                    'status'  => 'not found',
                    // 'jenis'   => 'approval',
                    'message' => 'Data kosong'
                ], 404);
            }else{
                $pesan = array(
                    "transaksi"     => "Pengambilan Tabungan Tunai",
                    "tgl_transaksi" => "17-10-2013",
                    "no_rekening"   => "32-02-00066",
                    "nama_nasabah"  => "PONIMIN",
                    "alamat"        => "VILA MUTIARA GADING 2 BLOK F04 NO 18 RT/RW 007/016",
                    "jumlah"        => "7,000,000.00",
                    "keterangan"    => "Pengambilan Tabungan Tunai an: 32-02-00066 PONIMIN",
                    "nama_teller"   => "GRIS"
                );

                $data = array();
                $i = 0;

                foreach ($query as $key => $val) {
                    $data[$i]['id']              = $val->id;
                    $data[$i]['email']           = $val->email;
                    $data[$i]['no_hp']           = $val->no_hp;
                    $data[$i]['subject']         = 'Pengambilan Tabungan Tunai';
                    $data[$i]['pesan']           = $pesan;
                    $data[$i]['tgl']             = $val->tgl;
                    $data[$i]['jam']             = $val->jam;
                    $data[$i]['keterangan']      = $val->keterangan;
                    $data[$i]['waktu_otorisasi'] = $val->waktu_otorisasi;
                    $i++;
                }

                return response()->json([
                    'code'    => 200,
                    'status'  => 'success',
                    // 'jenis'   => 'approval',
                    'data'    => $data
                ], 200);
            }
        } catch (Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function aproUpdate($id, Request $req) {
        $user_id = $req->auth->user_id;
        // $user_id = '1131';

        $Now = Carbon::now()->toDateTimeString();

        $update = DB::connection('dpm')->table('flg_otorisasi')
        ->where('user_id', $user_id)
        ->where('id', $id)
        ->update([
            'approval'        => 1,
            'waktu_otorisasi' => $Now
        ]);

        try {
            return response()->json([
                "code"    => 200,
                'status'  => 'success',
                // 'jenis'   => 'approval',
                'message' => 'Approval berhasil di update ke 1'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 501,
                'status'  => 'error',
                'message' => $e
            ], 501);
        }
    }
}
