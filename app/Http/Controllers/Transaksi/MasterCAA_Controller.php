<?php

namespace App\Http\Controllers\Transaksi;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use App\Http\Requests\Transaksi\BlankRequest;
use Illuminate\Support\Facades\File;
use App\Models\Transaksi\TransCAA;
use App\Models\Transaksi\TransCA;
use App\Models\Transaksi\TransSO;
use App\Models\AreaKantor\JPIC;
use App\Models\AreaKantor\PIC;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\User;
use Carbon\Carbon;
use DB;

class MasterCAA_Controller extends BaseController
{
    public function index(Request $req){
        $user_id  = $req->auth->user_id;
        $username = $req->auth->username;

        $pic = PIC::where('user_id', $user_id)->first();

        if ($pic == null) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "User_ID anda adalah '".$user_id."' dengan username '".$username."' . Namun anda belum terdaftar sebagai PIC(CA). Harap daftarkan diri sebagai PIC(CA) pada form PIC atau hubungi bagian IT"
            ], 404);
        }

        $id_cabang = $pic->id_mk_cabang;

        $query = TransCA::with('pic', 'cabang')->where('id_cabang', $id_cabang)->where('status_ca', 1)->get();

        if ($query == '[]') {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }


        foreach ($query as $key => $val) {

            if ($val->status_ca == 1) {
                $status_ca = 'recommend';
            }elseif($val->status_ca == 2){
                $status_ca = 'not recommend';
            }else{
                $status_ca = 'waiting';
            }

            $data[$key] = [
                'id_trans_so'    => $val->id_trans_so,
                'nomor_so'       => $val->so['nomor_so'],

                'nomor_ao'       => $val->so['ao']['nomor_ao'],
                'nomor_ca'       => $val->nomor_ca,
                'nomor_caa'      => $val->so['caa']['nomor_caa'],

                'pic'            => $val->pic['nama'],
                'cabang'         => $val->cabang['nama'],
                'asal_data'      => $val->so['asaldata']['nama'],
                'nama_marketing' => $val->so['nama_marketing'],
                'pengajuan_ca' => [
                    'plafon' => $val->recom_ca['plafon_kredit'],
                    'tenor'  => $val->recom_ca['jangka_waktu']
                ],
                'nama_debitur'   => $val->so['debt']['nama_lengkap'],
                'status_ca'      => $status_ca
            ];
        }

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => $data
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function show($id, Request $req){
        $user_id = $req->auth->user_id;
        $pic     = PIC::where('user_id', $user_id)->first();
        $id_cabang = $pic->id_mk_cabang;

        $val = TransCA::with('pic', 'cabang')->where('id_cabang', $id_cabang)->where('id_trans_so', $id)->first();

        if ($val == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }


        $id_agu_ta = explode (",",$val->so['ao']['id_agunan_tanah']);

        foreach ($id_agu_ta as $key => $value) {
            $idTan[$key] = array(
                'id' => $value,
                'jenis' => $val->so['ao']['tan']['jenis_sertifikat'],
                'lampiran' => [
                    'agunan_depan'    => $val->so['ao']['tan']['lamp_agunan_depan'],
                    'agunan_kanan'    => $val->so['ao']['tan']['lamp_agunan_kanan'],
                    'agunan_kiri'     => $val->so['ao']['tan']['lamp_agunan_kiri'],
                    'agunan_belakang' => $val->so['ao']['tan']['lamp_agunan_belakang'],
                    'agunan_dalam'    => $val->so['ao']['tan']['lamp_agunan_dalam'],
                ]
            );
        }


        $id_agu_ke = explode (",",$val->so['ao']['id_agunan_kendaraan']);

        foreach ($id_agu_ke as $key => $value) {
            $idKen[$key] = array(
                'id' => $value,
                'jenis' => $val->so['ao']['ken']['jenis'],
                'lampiran' => [
                    'agunan_depan'    => $val->so['ao']['ken']['lamp_agunan_depan'],
                    'agunan_kanan'    => $val->so['ao']['ken']['lamp_agunan_kanan'],
                    'agunan_kiri'     => $val->so['ao']['ken']['lamp_agunan_kiri'],
                    'agunan_belakang' => $val->so['ao']['ken']['lamp_agunan_belakang'],
                    'agunan_dalam'    => $val->so['ao']['ken']['lamp_agunan_dalam'],
                ]
            );
        }

        if ($val->status_ca == 1) {
            $status_ca = 'recommend';
        }elseif($val->status_ca == 2){
            $status_ca = 'not recommend';
        }else{
            $status_ca = 'waiting';
        }

        $data[] = [
            'id_trans_so'    => $val->id_trans_so,

            'nomor_so'       => $val->so['nomor_so'],
            'nomor_ao'       => $val->so['ao']['nomor_ao'],
            'nomor_ca'       => $val->nomor_ca,
            'nomor_caa'      => $val->so['caa']['nomor_caa'],

            'nama_marketing' => $val->so['nama_marketing'],

            'pic'  => [
                'id'   => $val->id_pic,
                'nama' => $val->pic['nama'],
            ],
            'cabang' => [
                'id'   => $val->id_cabang,
                'nama' => $val->cabang['nama'],
            ],

            'asaldata' => [
                'id'   => $val->so['asaldata']['id'],
                'nama' => $val->so['asaldata']['nama'],
            ],
            'pengajuan' => [
                'plafon' => $val->so['faspin']['plafon'],
                'tenor'  => $val->so['faspin']['tenor']
            ],
            'data_debitur' => [
                'id'           => $val->so['id_calon_debitur'],
                'nama_lengkap' => $val->so['debt']['nama_lengkap'],
                'lamp_usaha'   => $val->so['debt']['lamp_foto_usaha']
            ],
            'data_agunan' => [
                'agunan_tanah'     => $idTan,
                'agunan_kendaraan' => $idKen
            ],
            'pendapatan_usaha' => ['id' => $val->so['ao']['id_pendapatan_usaha']],
            'rekomendasi_ao'   => [
                'id'     => $val->so['ao']['id_recom_ao'],
                'plafon' => $val->so['ao']['recom_ao']['plafon_kredit'],
                'tenor'  => $val->so['ao']['recom_ao']['jangka_waktu']
            ],
            'rekomendasi_ca' => [
                'id'     => $val->id_recom_ca,
                'plafon' => $val->recom_ca['plafon_kredit'],
                'tenor'  => $val->recom_ca['jangka_waktu']
            ],
            'status_ao' => $status_ca
        ];

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => $data[0]
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function update($id, Request $request, BlankRequest $req){
        $user_id  = $request->auth->user_id;
        $username = $request->auth->user;

        $PIC = PIC::where('user_id', $user_id)->first();

        if ($PIC == null) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "User_ID anda adalah '".$user_id."' dengan username '".$username."' . Namun anda belum terdaftar sebagai PIC(CAA). Harap daftarkan diri sebagai PIC pada form PIC(CAA) atau hubungi bagian IT"
            ], 404);
        }

        $countCAA = TransCAA::latest('id','nomor_caa')->first();

        if (!$countCAA) {
            $lastNumb = 1;
        }else{
            $no = $countCAA->nomor_ca;

            $arr = explode("-", $no, 5);

            $lastNumb = $arr[4] + 1;
        }

        //Data Transaksi SO
        $nows  = Carbon::now();
        $year  = $nows->year;
        $month = $nows->month;

        $JPIC   = JPIC::where('id', $PIC->id_mj_pic)->first();

        //  ID-Cabang - AO / CA / SO - Bulan - Tahun - NO. Urut
        $nomor_caa = $PIC->id_mk_cabang.'-'.$JPIC->nama_jenis.'-'.$month.'-'.$year.'-'.$lastNumb;

        $check = TransSO::where('id',$id)->first();

        if (!$check) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        $lamp_dir = 'public/'.$check->debt['no_ktp'];

        if($file = $req->file('file_mao_mca')){

            $path = $lamp_dir.'/mcaa';

            $name = 'file_mao_mca/'.$file->getClientOriginalName();

            $file->move($path,$name);

            $mao_mca = $path.'/'.$name;

        }else{
            $mao_mca = null;
        }

        if($file = $req->file('file_lain')){

            $path = $lamp_dir.'/mcaa';

            $name = 'file_lain/'.$file->getClientOriginalName();

            $file->move($path,$name);

            $lain = $path.'/'.$name;

        }else{
            $lain = null;
        }

        $transCAA = array(
            'nomor_caa'   => $nomor_caa,
            'user_id'     => $user_id,
            'id_trans_so' => $id,
            'id_pic'      => $PIC->id,
            'id_cabang'   => $PIC->id_mk_cabang,
            'peyimpangan' => $req->input('peyimpangan'),
            'team_caa'    => $req->input('team_caa'),
            'rincian'     => $req->input('rincian'),
            'file_mao_mca'=> $mao_mca,
            'file_lain'   => $lain,
            'catatan_ca'  => $req->input('catatan_caa'),
            'status_ca'   => empty($req->input('status_caa')) ? 1 : $req->input('status_caa'),
        );
    }
}
