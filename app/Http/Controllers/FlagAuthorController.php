<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use Illuminate\Http\Request;
use App\Models\LogActivity;
use App\Models\FlgOto;
use App\Models\User;
use Carbon\Carbon;
use DB;

class FlagAuthorController extends BaseController
{
    // Otorisasi
    public function otoIndex(Request $req) {
        $user_id = $req->auth->user_id;

        try {
            $query = FlgOto::where('id_modul',0)
                    ->where('user_id', $user_id)
                    ->where('otorisasi', '!=', 1)
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

                    if ($val->otorisasi == 0) {
                        $data[$i]['status'] = 'new';
                    }elseif ($val->otorisasi == 2) {
                        $data[$i]['status'] = 'rejected';
                        $data[$i]['info']   = $val->keterangan;
                    }

                    $data[$i]['id']      = $val->id;
                    $data[$i]['email']   = $val->email;
                    $data[$i]['no_hp']   = $val->no_hp;
                    $data[$i]['subject'] = 'Pengambilan Tabungan Tunai';
                    $data[$i]['pesan']   = $pesan;
                    $data[$i]['tgl']     = $val->tgl;
                    $data[$i]['jam']     = $val->jam;
                    $i++;
                }

                return response()->json([
                    'code'    => 200,
                    'status'  => 'success',
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

        try {
            $query = FlgOto::where('id_modul',0)
                    ->where('user_id', $user_id)
                    ->where('otorisasi', '!=', 1)
                    ->where('id', $id)
                    ->get();

            if ($query == '[]') {
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

                    if ($val->otorisasi == null || $val->otorisasi == '') {
                        $data[$i]['status'] = 'new';
                    }elseif ($val->otorisasi == 2) {
                        $data[$i]['status'] = 'rejected';
                        $data[$i]['info']   = $val->keterangan;
                    }

                    $data[$i]['id']      = $val->id;
                    $data[$i]['email']   = $val->email;
                    $data[$i]['no_hp']   = $val->no_hp;
                    $data[$i]['subject'] = 'Pengambilan Tabungan Tunai';
                    $data[$i]['pesan']   = $pesan;
                    $data[$i]['tgl']     = $val->tgl;
                    $data[$i]['jam']     = $val->jam;
                    $i++;
                }

                return response()->json([
                    'code'    => 200,
                    'status'  => 'success',

                    'data'    => $data[0]
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

        $Now = Carbon::now()->toDateTimeString();

        // $logData = array(
        //     'subject' => 'Update Otorisasi',
        //     'url'     => $req->getPathInfo(),
        //     'method'  => $req->getMethod(),
        //     'ip'      => $req->getClientIp(),
        //     'agent'   => $req->header('User-Agent'),
        //     'user_id' => $user_id
        // );

        FlgOto::where([
            ['id', $id],
            ['user_id', $user_id],
            ['id_modul',0]
        ])->update(['otorisasi' => 1, 'waktu_otorisasi' => $Now]);

        try {
            return response()->json([
                "code"    => 200,
                'status'  => 'success',
                'message' => 'Otorisasi berhasil di setujui'
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

        try {
            $query = FlgOto::where('id_modul', '>',0)
                    ->where('user_id', $user_id)
                    ->where('approval', '!=', 1)
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

                    if ($val->approval == null || $val->approval == '') {
                        $data[$i]['status'] = 'new';
                    }elseif ($val->approval == 2) {
                        $data[$i]['status'] = 'rejected';
                        $data[$i]['info']   = $val->keterangan;
                    }

                    $data[$i]['id']              = $val->id;
                    $data[$i]['email']           = $val->email;
                    $data[$i]['no_hp']           = $val->no_hp;
                    $data[$i]['subject']         = 'Pengambilan Tabungan Tunai';
                    $data[$i]['pesan']           = $pesan;
                    $data[$i]['tgl']             = $val->tgl;
                    $data[$i]['jam']             = $val->jam;
                    $i++;
                }

                return response()->json([
                    'code'    => 200,
                    'status'  => 'success',
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

        try {
            $query = FlgOto::where('id_modul', '>',0)
                    ->where('user_id', $user_id)
                    ->where('approval', '!=', 1)
                    ->where('id', $id)
                    ->get();

            if ($query == '[]') {
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

                    if ($val->approval == null || $val->approval == '') {
                        $data[$i]['status'] = 'new';
                    }elseif ($val->approval == 2) {
                        $data[$i]['status'] = 'rejected';
                        $data[$i]['info']   = $val->keterangan;
                    }

                    $data[$i]['id']              = $val->id;
                    $data[$i]['email']           = $val->email;
                    $data[$i]['no_hp']           = $val->no_hp;
                    $data[$i]['subject']         = 'Pengambilan Tabungan Tunai';
                    $data[$i]['pesan']           = $pesan;
                    $data[$i]['tgl']             = $val->tgl;
                    $data[$i]['jam']             = $val->jam;
                    $i++;
                }

                return response()->json([
                    'code'    => 200,
                    'status'  => 'success',
                    'data'    => $data[0]
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

        $Now = Carbon::now()->toDateTimeString();

        FlgOto::where([
            ['id', $id],
            ['user_id', $user_id],
            ['id_modul', '>',0]
        ])->update(['approval' => 1, 'waktu_otorisasi' => $Now]);

        try {
            return response()->json([
                "code"    => 200,
                'status'  => 'success',
                'message' => 'approval berhasil disetujui'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 501,
                'status'  => 'error',
                'message' => $e
            ], 501);
        }
    }

    // Count Otorisasi
    public function countOto(Request $req){
        $user_id = $req->auth->user_id;

        $query = FlgOto::where('id_modul',0)
                ->where('user_id', $user_id)
                ->where('otorisasi', 0)
                ->count();

        try {
            return response()->json([
                'code'    => 200,
                'status'  => 'success',
                'data'    => $query
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    // Count Approval
    public function countApro(Request $req){
        $user_id = $req->auth->user_id;

        $query = FlgOto::where('id_modul', '>',0)
                    ->where('user_id', $user_id)
                    ->where('approval', 0)
                    ->count();

        try {
            return response()->json([
                'code'    => 200,
                'status'  => 'success',
                'data'    => $query
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    // Historisasi After AOtorisasi
    public function AfterOto(Request $req) {
        $user_id = $req->auth->user_id;

        try {
            $query = FlgOto::where('id_modul',0)
                    ->where('user_id', $user_id)
                    ->where('otorisasi', '!=', 0)
                    ->orderBy('waktu_otorisasi','desc')
                    // ->limit(5)
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

                    if ($val->otorisasi == 1 ) {
                        $data[$i]['status'] = 'accepted';
                        $data[$i]['time_acepted'] = $val->waktu_otorisasi;
                    }elseif ($val->otorisasi == 2) {
                        $data[$i]['status'] = 'rejected';
                        $data[$i]['info']   = $val->keterangan;
                    }

                    $data[$i]['id']      = $val->id;
                    $data[$i]['email']   = $val->email;
                    $data[$i]['no_hp']   = $val->no_hp;
                    $data[$i]['subject'] = 'Pengambilan Tabungan Tunai';
                    $data[$i]['pesan']   = $pesan;
                    $data[$i]['tgl']     = $val->tgl;
                    $data[$i]['jam']     = $val->jam;
                    $i++;
                }

                return response()->json([
                    'code'    => 200,
                    'status'  => 'success',
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

    // Detail Historisasi After Otorisasi
    public function DetailAfterOto($id, Request $req) {
        $user_id = $req->auth->user_id;

        try {
            $query = FlgOto::where('id_modul',0)
                    ->where('user_id', $user_id)
                    ->where('otorisasi', '!=', 0)
                    ->where('id', $id)
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

                    if ($val->otorisasi == 1 ) {
                        $data[$i]['status'] = 'accepted';
                        $data[$i]['time_acepted'] = $val->waktu_otorisasi;
                    }elseif ($val->otorisasi == 2) {
                        $data[$i]['status'] = 'rejected';
                        $data[$i]['info']   = $val->keterangan;
                    }

                    $data[$i]['id']      = $val->id;
                    $data[$i]['email']   = $val->email;
                    $data[$i]['no_hp']   = $val->no_hp;
                    $data[$i]['subject'] = 'Pengambilan Tabungan Tunai';
                    $data[$i]['pesan']   = $pesan;
                    $data[$i]['tgl']     = $val->tgl;
                    $data[$i]['jam']     = $val->jam;
                    $i++;
                }

                return response()->json([
                    'code'    => 200,
                    'status'  => 'success',
                    'data'    => $data[0]
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

    // Historisasi After Approval
    public function AfterApro(Request $req) {
        $user_id = $req->auth->user_id;

        try {
            $query = FlgOto::where('id_modul', '>',0)
                    ->where('user_id', $user_id)
                    ->where('approval', '!=', 0)
                    ->orderBy('waktu_otorisasi','desc')
                    // ->limit(5)
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

                    if ($val->approval == 1 ) {
                        $data[$i]['status'] = 'accepted';
                        $data[$i]['time_acepted'] = $val->waktu_otorisasi;
                    }elseif ($val->approval == 2) {
                        $data[$i]['status'] = 'rejected';
                        $data[$i]['info']   = $val->keterangan;
                    }

                    $data[$i]['id']      = $val->id;
                    $data[$i]['email']   = $val->email;
                    $data[$i]['no_hp']   = $val->no_hp;
                    $data[$i]['subject'] = 'Pengambilan Tabungan Tunai';
                    $data[$i]['pesan']   = $pesan;
                    $data[$i]['tgl']     = $val->tgl;
                    $data[$i]['jam']     = $val->jam;
                    $i++;
                }

                return response()->json([
                    'code'    => 200,
                    'status'  => 'success',
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

    // Detail Historisasi After Approval
    public function DetailAfterApro($id, Request $req) {
        $user_id = $req->auth->user_id;

        try {
            $query = FlgOto::where('id_modul', '>',0)
                    ->where('id', $id)
                    ->where('user_id', $user_id)
                    ->where('approval', '!=', 0)
                    ->where('approval', 1)
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

                    if ($val->approval == 1 ) {
                        $data[$i]['status'] = 'accepted';
                        $data[$i]['time_acepted'] = $val->waktu_otorisasi;
                    }elseif ($val->approval == 2) {
                        $data[$i]['status'] = 'rejected';
                        $data[$i]['info']   = $val->keterangan;
                    }

                    $data[$i]['id']      = $val->id;
                    $data[$i]['email']   = $val->email;
                    $data[$i]['no_hp']   = $val->no_hp;
                    $data[$i]['subject'] = 'Pengambilan Tabungan Tunai';
                    $data[$i]['pesan']   = $pesan;
                    $data[$i]['tgl']     = $val->tgl;
                    $data[$i]['jam']     = $val->jam;
                    $i++;
                }

                return response()->json([
                    'code'    => 200,
                    'status'  => 'success',
                    'data'    => $data[0]
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

    // Rejected Otorisasi
    public function rejectOto($id, Request $req) {
        $user_id = $req->auth->user_id;

        $Now = Carbon::now()->toDateTimeString();

        $because = $req->input('keterangan');

        if (!$because) {
            return response()->json([
                "code"    => 422,
                "status"  => "not valid request",
                "message" => "keterangan harus diisi"
            ], 422);
        }

        FlgOto::where([
            ['id', $id],
            ['user_id', $user_id],
            ['id_modul',0]
        ])->update(['otorisasi' => 2, 'keterangan' => $because, 'waktu_otorisasi' => $Now]);

        try {
            return response()->json([
                "code"    => 200,
                'status'  => 'success',
                'message' => 'otorisasi berhasil ditolak'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 501,
                'status'  => 'error',
                'message' => $e
            ], 501);
        }
    }

    // Rejected Approval
    public function rejectApro($id, Request $req) {
        $user_id = $req->auth->user_id;

        $Now = Carbon::now()->toDateTimeString();

        $because = $req->input('keterangan');

        if (!$because) {
            return response()->json([
                "code"    => 422,
                "status"  => "not valid request",
                "message" => "keterangan harus diisi"
            ], 422);
        }

        FlgOto::where([
            ['id', $id],
            ['user_id', $user_id],
            ['id_modul', '>',0],
        ])->update(['approval' => 2, 'keterangan' => $because, 'waktu_otorisasi' => $Now]);

        try {
            return response()->json([
                "code"    => 200,
                'status'  => 'success',
                'message' => 'approval berhasil ditolak'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 501,
                'status'  => 'error',
                'message' => $e
            ], 501);
        }
    }

    // Reset Otorisasi
    public function otoReset($id, Request $req) {
        $user_id = $req->auth->user_id;

        $Now = Carbon::now()->toDateTimeString();

        FlgOto::where([
            ['id', $id],
            ['user_id', $user_id],
            ['id_modul',0],
            ['otorisasi', '!=', 1]
        ])->update(['otorisasi' => 0]);

        try {
            return response()->json([
                "code"    => 200,
                'status'  => 'success',
                'message' => 'Otorisasi berhasil direset'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 501,
                'status'  => 'error',
                'message' => $e
            ], 501);
        }
    }

    // Reset Approval
    public function aproUpdate($id, Request $req) {
        $user_id = $req->auth->user_id;

        $Now = Carbon::now()->toDateTimeString();

        FlgOto::where([
            ['id', $id],
            ['user_id', $user_id],
            ['id_modul', '>',0],
            ['approval','!=', 1]
        ])->update(['approval' => 0]);

        try {
            return response()->json([
                "code"    => 200,
                'status'  => 'success',
                'message' => 'approval berhasil disetujui'
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
