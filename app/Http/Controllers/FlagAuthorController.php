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
            $query = FlgOto::where('id_modul', '<=', 0)
                    ->where('user_id', $user_id)
                    ->where('otorisasi', 0)
                    ->orderBy('tgl','desc')
                    ->orderBy('jam', 'desc')
                    ->get();

            if($query == '[]'){
                return response()->json([
                    'code'    => 404,
                    'status'  => 'not found',
                    'message' => 'Data kosong'
                ], 404);
            }else{
                // $pesan = array(
                //     "transaksi"     => "Pengambilan Tabungan Tunai",
                //     "tgl_transaksi" => "17-10-2013",
                //     "no_rekening"   => "32-02-00066",
                //     "nama_nasabah"  => "PONIMIN",
                //     "alamat"        => "VILA MUTIARA GADING 2 BLOK F04 NO 18 RT/RW 007/016",
                //     "jumlah"        => "7,000,000.00",
                //     "keterangan"    => "Pengambilan Tabungan Tunai an: 32-02-00066 PONIMIN",
                //     "nama_teller"   => "GRIS"
                // );


                $data = array();
                $i = 0;
                foreach ($query as $key => $val) {
                    $pesan = str_replace(array("\r\n","\r","\n"),";", $val->pesan);

                    $data[$i]['status']  = 'new';
                    $data[$i]['id']      = $val->id;
                    $data[$i]['email']   = $val->email;
                    $data[$i]['no_hp']   = $val->no_hp;
                    $data[$i]['subject'] = $val->subject; //'Pengambilan Tabungan Tunai';
                    $data[$i]['pesan']   = explode(";", $pesan);
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

    // Limit
    // Otorisasi
    public function otoLimit(Request $req, Helper $help, $limit) {
        $user_id = $req->auth->user_id;

        try {
            $query = FlgOto::where('id_modul', '<=', 0)
                    ->where('user_id', $user_id)
                    ->where('otorisasi', 0)
                    ->orderBy('tgl','desc')
                    ->orderBy('jam', 'desc')
                    ->limit($limit)
                    ->get();

            if ($query == '[]') {
                return response()->json([
                    'code'    => 404,
                    'status'  => 'not found',
                    'message' => 'Data kosong'
                ], 404);
            }

            $j = 0;

            $arrData = array();
            foreach ($query as $key => $val) {

                $pesan = str_replace(array("\r\n","\r","\n"),";", $val->pesan);

                $arrData[$key]['id']      = $val->id;
                $arrData[$key]['email']   = $val->email;
                $arrData[$key]['no_hp']   = $val->no_hp;
                $arrData[$key]['subject'] = $val->subject;
                $arrData[$key]['pesan']   = explode(";", $pesan);
                $arrData[$key]['tgl']     = $val->tgl;
                $arrData[$key]['jam']     = $val->jam;
                $arrData[$key]['status']  = 'new';
            }

            // Group data by the "tgl_trans" key
            $byGroup = $help->group_by("tgl", $arrData);

            $data = array();
            foreach ($byGroup as $key => $val) {
                $data[$j]['tgl'] = $val['tgl'];
                $data[$j] = $val;
                $j++;
            }

            return response()->json([
                'code'    => 200,
                'status'  => 'success',
                'data'    => $data
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function otoShow($id, Request $req, Helper $help) {
        $user_id = $req->auth->user_id;

        try {
            $val = FlgOto::where('id_modul', '<=', 0)
                    ->where('user_id', $user_id)
                    ->where('id', $id)
                    ->first();

            if ($val == null) {
                return response()->json([
                    'code'    => 404,
                    'status'  => 'not found',
                    'message' => 'Data kosong'
                ], 404);
            }

            $pesan = str_replace(array("\r\n","\r","\n"),";", $val->pesan);

            $data = array();

            $data['id']      = $val->id;
            $data['email']   = $val->email;
            $data['no_hp']   = $val->no_hp;
            $data['subject'] = $val->subject; //'Pengambilan Tabungan Tunai';
            $data['pesan']   = explode(";", $pesan);
            $data['tgl']     = $val->tgl;
            $data['jam']     = $val->jam;

            if ($val->otorisasi == 1) {
                $data['status'] = 'accepted';
                $data['tgl_accepted'] = $val->waktu_otorisasi;
            }elseif ($val->otorisasi == 2) {
                $data['status'] = 'rejected';
                $data['tgl_rejected'] = $val->waktu_otorisasi;
            }else{
                $data['status'] = 'new';
            }

            return response()->json([
                'code'    => 200,
                'status'  => 'success',
                'data'    => $data
            ], 200);
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

        $checkFLG = FlgOto::where('id', $id)->first();

        if ($checkFLG == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        if ($checkFLG->otorisasi == 1) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data has been accepted'
            ], 404);
        }elseif ($checkFLG->otorisasi == 2) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data has been rejected'
            ], 404);
        }

        $checkFCM = User::where('user_id', $user_id)->first();

        $because = empty($req->input('keterangan')) ? $checkFLG->keterangan : $req->input('keterangan');

        // $logData = array(
        //     'subject' => 'Update Otorisasi',
        //     'url'     => $req->getPathInfo(),
        //     'method'  => $req->getMethod(),
        //     'ip'      => $req->getClientIp(),
        //     'agent'   => $req->header('User-Agent'),
        //     'user_id' => $user_id
        // );

        $fcm   = $checkFCM->fcm_token;
        $title = $checkFLG->subject;
        $msg   = 'Otorisasi berhasil di setujui';

        DB::connection('web')->beginTransaction();
        try {

            FlgOto::where([
                ['id', $id],
                ['user_id', $user_id],
                ['otorisasi', 0]
            ])->update(['otorisasi' => 1, 'keterangan' => $because, 'waktu_otorisasi' => $Now]);

            if ($fcm != null) {
                $push = Helper::push_notif($fcm, $title, $msg);
            }else{
                $push = null;
            }

            DB::connection('web')->commit();

            return response()->json([
                "code"    => 200,
                'status'  => 'success',
                'message' => [
                    'accepted'   => true,
                    'push_notif' => $push
                ]
            ], 200);
        } catch (Exception $e) {
            $err = DB::connection('web')->rollback();

            return response()->json([
                "code"    => 501,
                'status'  => 'error',
                'message' => [
                    'accepted' => false,
                    'error'    => $err
                ]
            ], 501);
        }
    }

    // Approval
    public function aproIndex(Request $req) {
        $user_id = $req->auth->user_id;

        try {
            $query = FlgOto::where('id_modul', '>',0)
                    ->where('user_id', $user_id)
                    ->where('otorisasi', 0)
                    ->orderBy('tgl','desc')
                    ->orderBy('jam', 'desc')
                    ->get();

            if($query == '[]'){
                return response()->json([
                    'code'    => 404,
                    'status'  => 'not found',
                    'message' => 'Data kosong'
                ], 404);
            }else{
                // $pesan = array(
                //     "transaksi"     => "Pengambilan Tabungan Tunai",
                //     "tgl_transaksi" => "17-10-2013",
                //     "no_rekening"   => "32-02-00066",
                //     "nama_nasabah"  => "PONIMIN",
                //     "alamat"        => "VILA MUTIARA GADING 2 BLOK F04 NO 18 RT/RW 007/016",
                //     "jumlah"        => "7,000,000.00",
                //     "keterangan"    => "Pengambilan Tabungan Tunai an: 32-02-00066 PONIMIN",
                //     "nama_teller"   => "GRIS"
                // );

                $data = array();
                $i = 0;
                foreach ($query as $key => $val) {

                    $pesan = str_replace(array("\r\n","\r","\n"),";", $val->pesan);

                    $data[$i]['status']  = 'new';
                    $data[$i]['id']      = $val->id;
                    $data[$i]['email']   = $val->email;
                    $data[$i]['no_hp']   = $val->no_hp;
                    $data[$i]['subject'] = $val->subject; //'Pengambilan Tabungan Tunai';
                    $data[$i]['pesan']   = explode(";", $pesan);
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

    // lImit
    // Aproval
    public function aproLimit(Request $req, Helper $help, $limit) {
        $user_id = $req->auth->user_id;

        try {
            $query = FlgOto::where('id_modul', '>',0)
                    ->where('user_id', $user_id)
                    ->where('otorisasi', 0)
                    ->orderBy('tgl','desc')
                    ->orderBy('jam', 'desc')
                    ->limit($limit)
                    ->get();

            if ($query == '[]') {
                return response()->json([
                    'code'    => 404,
                    'status'  => 'not found',
                    'message' => 'Data kosong'
                ], 404);
            }

            $j = 0;

            $arrData = array();
            foreach ($query as $key => $val) {

                $pesan = str_replace(array("\r\n","\r","\n"),";", $val->pesan);

                $arrData[$key]['id']      = $val->id;
                $arrData[$key]['email']   = $val->email;
                $arrData[$key]['no_hp']   = $val->no_hp;
                $arrData[$key]['subject'] = $val->subject;
                $arrData[$key]['pesan']   = explode(";", $pesan);
                $arrData[$key]['tgl']     = $val->tgl;
                $arrData[$key]['jam']     = $val->jam;
                $arrData[$key]['status']  = 'new';
            }

            // Group data by the "tgl_trans" key
            $byGroup = $help->group_by("tgl", $arrData);

            $res = array();
            foreach ($byGroup as $key => $val) {
                $res[$j]['tgl'] = $val['tgl'];
                $res[$j] = $val;
                $j++;
            }

            return response()->json([
                'code'    => 200,
                'status'  => 'success',
                'data'    => $res
            ], 200);
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
            $val = FlgOto::where('id_modul', '>',0)
                    ->where('user_id', $user_id)
                    ->where('id', $id)
                    ->first();

            if ($val == null) {
                return response()->json([
                    'code'    => 404,
                    'status'  => 'not found',
                    'message' => 'Data kosong'
                ], 404);
            }

            $pesan = str_replace(array("\r\n","\r","\n"),";", $val->pesan);

            $data = array();

            $data['id']      = $val->id;
            $data['email']   = $val->email;
            $data['no_hp']   = $val->no_hp;
            $data['subject'] = $val->subject;
            $data['pesan']   = explode(";",$pesan);
            $data['tgl']     = $val->tgl;
            $data['jam']     = $val->jam;

            if ($val->otorisasi == 1) {
                $data['status'] = 'accepted';
                $data['tgl_accepted'] = $val->waktu_otorisasi;
            }elseif ($val->otorisasi == 2) {
                $data['status'] = 'rejected';
                $data['tgl_rejected'] = $val->waktu_otorisasi;
            }else{
                $data['status'] = 'new';
            }

            return response()->json([
                'code'    => 200,
                'status'  => 'success',
                'data'    => $data
            ], 200);
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

        $checkFLG = FlgOto::where('id', $id)->first();

        if ($checkFLG == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        if ($checkFLG->otorisasi == 1) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data has been accepted'
            ], 404);
        }elseif ($checkFLG->otorisasi == 2) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data has been rejected'
            ], 404);
        }

        $checkFCM = User::where('user_id', $user_id)->first();

        $because = empty($req->input('keterangan')) ? $checkFLG->keterangan : $req->input('keterangan');

        $fcm   = $checkFCM->fcm_token;
        $title = $checkFLG->subject;
        $msg   = 'Approval berhasil di setujui';

        DB::connection('web')->beginTransaction();
        try {

            FlgOto::where([
                ['id', $id],
                ['user_id', $user_id],
                ['otorisasi', 0]
            ])->update(['otorisasi' => 1, 'keterangan' => $because, 'waktu_otorisasi' => $Now]);

            if ($fcm != null) {
                $push = Helper::push_notif($fcm, $title, $msg);
            }else{
                $push = null;
            }

            DB::connection('web')->commit();

            return response()->json([
                "code"    => 200,
                'status'  => 'success',
                'message' => [
                    'accepted'   => true,
                    'push_notif' => $push
                ]
            ], 200);
        } catch (Exception $e) {
            $err = DB::connection('web')->rollback();

            return response()->json([
                "code"    => 501,
                'status'  => 'error',
                'message' => [
                    'accepted' => false,
                    'error'    => $err
                ]
            ], 501);
        }
    }

    // Count Otorisasi
    public function countOto(Request $req){
        $user_id = $req->auth->user_id;

        $query = FlgOto::where('id_modul', '<=', 0)
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

        $query = FlgOto::where('id_modul', '>', 0)
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

    // Historisasi After AOtorisasi
    public function otoH(Request $req) {
        $user_id = $req->auth->user_id; // 1130


        $query = FlgOto::where('id_modul', '<=', 0)
            ->where('user_id', $user_id)
            ->where('otorisasi', '!=', 0)
            ->orderBy('waktu_otorisasi','desc')
            ->get();

        if($query == '[]'){
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        foreach ($query as $key => $val) {
            if ($val->otorisasi == 1) {
                $msg = 'accepted';
            }elseif ($val->otorisasi == 2) {
                $msg = 'rejected';
            }else{
                $msg = 'new';
            }

            $pesan = str_replace(array("\r\n","\r","\n"),";", $val->pesan);

            $data[$key] = [
                'id'      => $val->id,
                'email'   => $val->email,
                'no_hp'   => $val->no_hp,
                'subject' => $val->subject, //'Pengambilan Tabungan Tunai';
                'pesan'   => explode(";",$pesan),
                'tgl'     => $val->tgl,
                'jam'     => $val->jam,
                'status'  => $msg,
                'tgl_'.$msg => $val->waktu_otorisasi
            ];
        }

        try {
            return response()->json([
                'code'    => 200,
                'status'  => 'success',
                'data'    => $data
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    // Historisasi After AOtorisasi Year
    public function otoHY(Request $req, $year) {
        $user_id = $req->auth->user_id; // 1130


        $query = FlgOto::where('id_modul', '<=', 0)
            ->where('user_id', $user_id)
            ->where('otorisasi', '!=', 0)
            ->whereYear('tgl', $year)
            ->orderBy('waktu_otorisasi','desc')
            ->get();

        if($query == '[]'){
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        foreach ($query as $key => $val) {
            if ($val->otorisasi == 1) {
                $msg = 'accepted';
            }elseif ($val->otorisasi == 2) {
                $msg = 'rejected';
            }else{
                $msg = 'new';
            }

            $pesan = str_replace(array("\r\n","\r","\n"),";", $val->pesan);

            $data[$key] = [
                'id'      => $val->id,
                'email'   => $val->email,
                'no_hp'   => $val->no_hp,
                'subject' => $val->subject, //'Pengambilan Tabungan Tunai';
                'pesan'   => explode(";",$pesan),
                'tgl'     => $val->tgl,
                'jam'     => $val->jam,
                'status'  => $msg,
                'tgl_'.$msg => $val->waktu_otorisasi
            ];
        }

        try {
            return response()->json([
                'code'    => 200,
                'status'  => 'success',
                'data'    => $data
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    // Historisasi After AOtorisasi Year and Month
    public function otoHYM(Request $req, $year, $month) {
        $user_id = $req->auth->user_id; // 1130


        $query = FlgOto::where('id_modul', '<=', 0)
            ->where('user_id', $user_id)
            ->where('otorisasi', '!=', 0)
            ->whereYear('tgl', $year)
            ->whereMonth('tgl', $month)
            ->orderBy('waktu_otorisasi','desc')
            ->get();

        if($query == '[]'){
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        foreach ($query as $key => $val) {
            if ($val->otorisasi == 1) {
                $msg = 'accepted';
            }elseif ($val->otorisasi == 2) {
                $msg = 'rejected';
            }else{
                $msg = 'new';
            }

            $pesan = str_replace(array("\r\n","\r","\n"),";", $val->pesan);

            $data[$key] = [
                'id'      => $val->id,
                'email'   => $val->email,
                'no_hp'   => $val->no_hp,
                'subject' => $val->subject, //'Pengambilan Tabungan Tunai';
                'pesan'   => explode(";",$pesan),
                'tgl'     => $val->tgl,
                'jam'     => $val->jam,
                'status'  => $msg,
                'tgl_'.$msg => $val->waktu_otorisasi
            ];
        }

        try {
            return response()->json([
                'code'    => 200,
                'status'  => 'success',
                'data'    => $data
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    // Historisasi After Approval
    public function aproH(Request $req) {
        $user_id = $req->auth->user_id;

        $query = FlgOto::where('id_modul', '>',0)
            ->where('user_id', $user_id)
            ->where('otorisasi', '!=', 0)
            ->orderBy('waktu_otorisasi','desc')
            ->get();

        if($query == '[]'){
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        $data = array();
        foreach ($query as $key => $val) {
            if ($val->otorisasi == 1) {
                $msg = 'accepted';
            }elseif ($val->otorisasi == 2) {
                $msg = 'rejected';
            }else{
                $msg = 'new';
            }

            $pesan = str_replace(array("\r\n","\r","\n"),";", $val->pesan);

            $data[$key] = [
                'id'      => $val->id,
                'email'   => $val->email,
                'no_hp'   => $val->no_hp,
                'subject' => $val->subject, //'Pengambilan Tabungan Tunai';
                'pesan'   => explode(";", $pesan),
                'tgl'     => $val->tgl,
                'jam'     => $val->jam,
                'status'  => $msg,
                'tgl_'.$msg => $val->waktu_otorisasi
            ];
        }

        try{
            return response()->json([
                'code'    => 200,
                'status'  => 'success',
                'data'    => $data
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    // Historisasi After Approval Year
    public function aproHY(Request $req, $year) {
        $user_id = $req->auth->user_id;

        $query = FlgOto::where('id_modul', '>',0)
            ->where('user_id', $user_id)
            ->where('otorisasi', '!=', 0)
            ->whereYear('tgl', $year)
            ->orderBy('waktu_otorisasi','desc')
            ->get();

        if($query == '[]'){
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        $data = array();
        foreach ($query as $key => $val) {
            if ($val->otorisasi == 1) {
                $msg = 'accepted';
            }elseif ($val->otorisasi == 2) {
                $msg = 'rejected';
            }else{
                $msg = 'new';
            }

            $pesan = str_replace(array("\r\n","\r","\n"),";", $val->pesan);

            $data[$key] = [
                'id'      => $val->id,
                'email'   => $val->email,
                'no_hp'   => $val->no_hp,
                'subject' => $val->subject, //'Pengambilan Tabungan Tunai';
                'pesan'   => explode(";", $pesan),
                'tgl'     => $val->tgl,
                'jam'     => $val->jam,
                'status'  => $msg,
                'tgl_'.$msg => $val->waktu_otorisasi
            ];
        }

        try{
            return response()->json([
                'code'    => 200,
                'status'  => 'success',
                'data'    => $data
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    // Historisasi After Approval Year and Month
    public function aproHYM(Request $req, $year, $month) {
        $user_id = $req->auth->user_id;

        $query = FlgOto::where('id_modul', '>',0)
            ->where('user_id', $user_id)
            ->where('otorisasi', '!=', 0)
            ->whereYear('tgl', $year)
            ->whereMonth('tgl', $month)
            ->orderBy('waktu_otorisasi','desc')
            ->get();

        if($query == '[]'){
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        $data = array();
        foreach ($query as $key => $val) {
            if ($val->otorisasi == 1) {
                $msg = 'accepted';
            }elseif ($val->otorisasi == 2) {
                $msg = 'rejected';
            }else{
                $msg = 'new';
            }

            $pesan = str_replace(array("\r\n","\r","\n"),";", $val->pesan);

            $data[$key] = [
                'id'      => $val->id,
                'email'   => $val->email,
                'no_hp'   => $val->no_hp,
                'subject' => $val->subject, //'Pengambilan Tabungan Tunai';
                'pesan'   => explode(";",$pesan),
                'tgl'     => $val->tgl,
                'jam'     => $val->jam,
                'status'  => $msg,
                'tgl_'.$msg => $val->waktu_otorisasi
            ];
        }

        try{
            return response()->json([
                'code'    => 200,
                'status'  => 'success',
                'data'    => $data
            ], 200);
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

        $checkFLG = FlgOto::where('id', $id)->first();

        if ($checkFLG == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        if ($checkFLG->otorisasi == 1) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data has been accepted'
            ], 404);
        }elseif ($checkFLG->otorisasi == 2) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data has been rejected'
            ], 404);
        }

        $checkFCM = User::where('user_id', $user_id)->first();

        $because = $req->input('keterangan');

        if (!$because) {
            return response()->json([
                "code"    => 422,
                "status"  => "not valid request",
                "message" => "keterangan harus diisi"
            ], 422);
        }

        $fcm   = $checkFCM->fcm_token;
        $title = $checkFLG->subject;
        $msg   = 'Otorisasi berhasil ditolak';

        DB::connection('web')->beginTransaction();
        try {

            FlgOto::where([
                ['id', $id],
                ['user_id', $user_id],
                ['otorisasi', 0]
            ])->update(['otorisasi' => 2, 'keterangan' => $because, 'waktu_otorisasi' => $Now]);

            if ($fcm != null) {
                $push = Helper::push_notif($fcm, $title, $msg);
            }else{
                $push = null;
            }

            DB::connection('web')->commit();

            return response()->json([
                "code"    => 200,
                'status'  => 'success',
                'message' => [
                    'rejected'   => true,
                    'push_notif' => $push
                ]
            ], 200);
        } catch (Exception $e) {
            $err = DB::connection('web')->rollback();

            return response()->json([
                "code"    => 501,
                'status'  => 'error',
                'message' => [
                    'rejected' => false,
                    'error'    => $err
                ]
            ], 501);
        }
    }

    // Rejected Approval
    public function rejectApro($id, Request $req) {
        $user_id = $req->auth->user_id;
        // $user_id = '1131';

        $Now = Carbon::now()->toDateTimeString();

        $checkFLG = FlgOto::where('id', $id)->first();

        if ($checkFLG == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        if ($checkFLG->otorisasi == 1) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data has been accepted'
            ], 404);
        }elseif ($checkFLG->otorisasi == 2) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data has been rejected'
            ], 404);
        }

        $checkFCM = User::where('user_id', $user_id)->first();

        $because = empty($req->input('keterangan')) ? $checkFLG->keterangan : $req->input('keterangan');

        $fcm   = $checkFCM->fcm_token;
        $title = $checkFLG->subject;
        $msg   = 'Approval berhasil ditolak';

        DB::connection('web')->beginTransaction();
        try {

            FlgOto::where([
                ['id', $id],
                ['user_id', $user_id],
                ['otorisasi',0],
            ])->update(['otorisasi' => 2, 'keterangan' => $because, 'waktu_otorisasi' => $Now]);

            if ($fcm != null) {
                $push = Helper::push_notif($fcm, $title, $msg);
            }else{
                $push = null;
            }

            DB::connection('web')->commit();

            return response()->json([
                "code"    => 200,
                'status'  => 'success',
                'message' => [
                    'rejected'   => true,
                    'push_notif' => $push
                ]
            ], 200);
        } catch (Exception $e) {
            $err = DB::connection('web')->rollback();

            return response()->json([
                "code"    => 501,
                'status'  => 'error',
                'message' => [
                    'rejected' => false,
                    'error'    => $err
                ]
            ], 501);
        }
    }

    // Reset Otorisasi
    public function otoReset(Request $req) {
        $user_id = $req->auth->user_id;

        $Now = Carbon::now()->toDateTimeString();

        $check_FLG = FlgOto::where('user_id', $user_id)->where('id_modul', '<=', 0)->get();

        $ids = array();
        foreach ($check_FLG as $value) {
            $ids[] = array(
                'id' => $value->id
            );
        }

        FlgOto::whereIn('id', $ids)->update(['otorisasi' => 0]);

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
    public function aproReset(Request $req) {
        $user_id = $req->auth->user_id;

        $Now = Carbon::now()->toDateTimeString();

        $check_FLG = FlgOto::where('user_id', $user_id)->where('id_modul', '>', 0)->get();

        $ids = array();
        foreach ($check_FLG as $value) {
            $ids[] = array(
                'id' => $value->id
            );
        }

        FlgOto::whereIn('id', $ids)->update(['approval' => 0, 'otorisasi' => 0]);

        try {
            return response()->json([
                "code"    => 200,
                'status'  => 'success',
                'message' => 'approval berhasil direset'
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
