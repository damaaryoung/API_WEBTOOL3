<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\User;
use DB;

class FlagAuthorController extends BaseController
{
    public function updateOtorisasi(Request $req, Helper $help) {
        $id   = $req->auth->user_id;
        $user = User::select('reg_id_gcm')->where('user_id', $id)->first();

        $Now = Carbon::now()->toDateTimeString();

        $data = DB::connection('dpm')->table('flg_otorisasi')
            ->where('user_id', $id)
            ->where('otorisasi', 0)
            ->orderBy('tgl','asc')
            ->orderBy('jam', 'asc')
            ->first();

        if($data == null){
            return response()->json([
                "code"    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        $update = DB::connection('dpm')->table('flg_otorisasi')
        ->where('user_id', $id)
        ->where('id', $data->id)
        ->update([
            'otorisasi' => 1,
            'waktu_otorisasi' => $Now
        ]);

        try {
            return response()->json([
                "code"    => 200,
                'status'  => 'success',
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

    public function index() {
        try {
            $query = DB::connection('dpm')->table('flg_otorisasi')->first();

            if (!$query) {
                return response()->json([
                    'code'    => 404,
                    'status'  => 'not found',
                    'message' => 'Data kosong'
                ], 404);
            }else{
                return response()->json([
                    'code'    => 200,
                    'status'  => 'success',
                    'data'    => $query
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

    public function store(Request $req) {
        $now   = Carbon::now()->toDateTimeString();
        $date  = Carbon::parse($now)->format('Y-m-d');
        $clock = Carbon::parse($now)->format('H:i:s a');

        $user_id         = $req->input('user_id');
        $modul           = $req->input('modul');
        $id_modul        = $req->input('id_modul');
        $ip              = $req->input('ip');
        $email           = $req->input('email');
        $no_hp           = $req->input('no_hp');
        $pesan           = $req->input('pesan');
        $tgl             = $date;
        $jam             = $clock;
        $approval        = $req->input('approval');
        $otorisasi       = $req->input('otorisasi');
        $subject         = $req->input('subject');
        $keterangan      = $req->input('keterangan');
        $waktu_otorisasi = $req->input('waktu_otorisasi');
        $sent_android    = $req->input('sent_android');

        if (!$user_id) {
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "Field 'user_id' harus diisi!!"
            ], 400);
        }

        if (!$modul) {
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "Field 'modul' harus diisi!!"
            ], 400);
        }

        if (!$id_modul) {
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "Field 'id_modul' harus diisi!!"
            ], 400);
        }

        if (!$ip) {
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "Field 'ip' harus diisi!!"
            ], 400);
        }

        if (!$email) {
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "Field 'email' harus diisi!!"
            ], 400);
        }

        if (!$tgl) {
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "Field 'tgl' harus diisi!!"
            ], 400);
        }

        if (!$jam) {
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "Field 'jam' harus diisi!!"
            ], 400);
        }

        if (!$approval) {
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "Field 'approval' harus diisi!!"
            ], 400);
        }

        if (!$subject) {
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "Field 'subject' harus diisi!!"
            ], 400);
        }

        if (!$keterangan) {
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "Field 'keterangan' harus diisi!!"
            ], 400);
        }

        if (!$waktu_otorisasi) {
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "Field 'waktu_otorisasi' harus diisi!!"
            ], 400);
        }

        try {
            $query = DB::connection('dpm')->table('flg_otorisasi')->insert([
                'user_id'         => $user_id,
                'modul'           => $modul,
                'id_modul'        => $id_modul,
                'ip'              => $ip,
                'email'           => $email,
                'no_hp'           => $no_hp,
                'pesan'           => $pesan,
                'tgl'             => $tgl,
                'jam'             => $jam,
                'approval'        => $approval,
                'otorisasi'       => $otorisasi,
                'subject'         => $subject,
                'keterangan'      => $keterangan,
                'waktu_otorisasi' => $waktu_otorisasi,
                'sent_android'    => $sent_android
            ]);

            return response()->json([
                'code'    => 200,
                'status'  => 'success',
                'message' => 'Data berhasil dibuat'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function show($id) {
        try {
            $query = DB::connection('dpm')->table('flg_otorisasi')->where('id', $id)->first();

            if (!$query) {
                return response()->json([
                    'code'    => 404,
                    'status'  => 'not found',
                    'message' => 'Data kosong'
                ], 404);
            }else{
                return response()->json([
                    'code'    => 200,
                    'status'  => 'success',
                    'data'    => $query
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

    public function update($id, Request $req) {

        $check = DB::connection('dpm')->table('flg_otorisasi')->where('id', $id)->first();

        if (!$check) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        $user_id         = empty($req->input('user_id')) ? $check->user_id : $req->input('user_id');
        $modul           = empty($req->input('modul')) ? $check->modul : $req->input('modul');
        $id_modul        = empty($req->input('id_modul')) ? $check->id_modul : $req->input('id_modul');
        $ip              = empty($req->input('ip')) ? $check->ip : $req->input('ip');
        $email           = empty($req->input('email')) ? $check->email : $req->input('email');
        $no_hp           = empty($req->input('no_hp')) ? $check->no_hp : $req->input('no_hp');
        $pesan           = empty($req->input('pesan')) ? $check->pesan : $req->input('pesan');
        $tgl             = $date;
        $jam             = $clock;
        $approval        = empty($req->input('approval')) ? $check->approval : $req->input('approval');
        $otorisasi       = empty($req->input('otorisasi')) ? $check->otorisasi : $req->input('otorisasi');
        $subject         = empty($req->input('subject')) ? $check->subject : $req->input('subject');
        $keterangan      = empty($req->input('keterangan')) ? $check->keterangan : $req->input('keterangan');
        $waktu_otorisasi = empty($req->input('waktu_otorisasi')) ? $check->waktu_otorisasi : $req->input('waktu_otorisasi');
        $sent_android    = empty($req->input('sent_android')) ? $check->sent_android : $req->input('sent_android');

        try {
            $query = DB::connection('dpm')->table('flg_otorisasi')->where('id', $id)->update([
                'user_id'         => $user_id,
                'modul'           => $modul,
                'id_modul'        => $id_modul,
                'ip'              => $ip,
                'email'           => $email,
                'no_hp'           => $no_hp,
                'pesan'           => $pesan,
                'tgl'             => $tgl,
                'jam'             => $jam,
                'approval'        => $approval,
                'otorisasi'       => $otorisasi,
                'subject'         => $subject,
                'keterangan'      => $keterangan,
                'waktu_otorisasi' => $waktu_otorisasi,
                'sent_android'    => $sent_android
            ]);

            return response()->json([
                'code'    => 200,
                'status'  => 'success',
                'message' => 'Data berhasil diupdate'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function delete($id) {
        $check = DB::connection('dpm')->table('flg_otorisasi')->where('id', $id)->first();

        if (!$check) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        try {
            $query = DB::connection('dpm')->table('flg_otorisasi')->where('id', $id)->delete();

            return response()->json([
                'code'    => 200,
                'status'  => 'success',
                'message' => 'Data dengan ID '.$id.', berhasil dihapus'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }
}
