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

        $pic = PIC::where('user_id', $user_id)->first();

        if ($pic == null) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "User_ID anda adalah '".$user_id."' dengan username '".$req->auth->user."' . Namun anda belum terdaftar sebagai PIC(CAA). Harap daftarkan diri sebagai PIC(CAA) pada form PIC atau hubungi bagian IT"
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
                // 'nomor_caa'      => $val->so['caa']['nomor_caa'],

                'pic'            => $val->pic['nama'],
                'cabang'         => $val->cabang['nama'],
                'asal_data'      => $val->so['asaldata']['nama'],
                'nama_marketing' => $val->so['nama_marketing'],
                'pengajuan' => [
                    'plafon' => $val->so['faspin']['plafon'],
                    'tenor'  => $val->so['faspin']['tenor']
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

    // public function show($id, Request $req){
    //     $user_id = $req->auth->user_id;
    //     $pic     = PIC::with('jpic','area','cabang')->where('user_id', $user_id)->first();

    //     if ($pic == null) {
    //         return response()->json([
    //             "code"    => 404,
    //             "status"  => "not found",
    //             "message" => "User_ID anda adalah '".$user_id."' dengan username '".$req->auth->user."' . Namun anda belum terdaftar sebagai PIC(CAA). Harap daftarkan diri sebagai PIC(CAA) pada form PIC atau hubungi bagian IT"
    //         ], 404);
    //     }

    //     $id_cabang = $pic->id_mk_cabang;

    //     $val = TransCA::with('pic', 'cabang')->where('id_cabang', $id_cabang)->where('id_trans_so', $id)->first();

    //     if ($val == null) {
    //         return response()->json([
    //             'code'    => 404,
    //             'status'  => 'not found',
    //             'message' => 'Data kosong'
    //         ], 404);
    //     }


    //     $id_agu_ta = explode (",",$val->so['ao']['id_agunan_tanah']);

    //     foreach ($id_agu_ta as $key => $value) {
    //         $idTan[$key] = array(
    //             'id' => $value,
    //             'jenis' => $val->so['ao']['tan']['jenis_sertifikat'],
    //             'lampiran' => [
    //                 'agunan_depan'    => $val->so['ao']['tan']['lamp_agunan_depan'],
    //                 'agunan_kanan'    => $val->so['ao']['tan']['lamp_agunan_kanan'],
    //                 'agunan_kiri'     => $val->so['ao']['tan']['lamp_agunan_kiri'],
    //                 'agunan_belakang' => $val->so['ao']['tan']['lamp_agunan_belakang'],
    //                 'agunan_dalam'    => $val->so['ao']['tan']['lamp_agunan_dalam'],
    //             ]
    //         );
    //     }


    //     $id_agu_ke = explode (",",$val->so['ao']['id_agunan_kendaraan']);

    //     foreach ($id_agu_ke as $key => $value) {
    //         $idKen[$key] = array(
    //             'id' => $value,
    //             'jenis' => $val->so['ao']['ken']['jenis'],
    //             'lampiran' => [
    //                 'agunan_depan'    => $val->so['ao']['ken']['lamp_agunan_depan'],
    //                 'agunan_kanan'    => $val->so['ao']['ken']['lamp_agunan_kanan'],
    //                 'agunan_kiri'     => $val->so['ao']['ken']['lamp_agunan_kiri'],
    //                 'agunan_belakang' => $val->so['ao']['ken']['lamp_agunan_belakang'],
    //                 'agunan_dalam'    => $val->so['ao']['ken']['lamp_agunan_dalam'],
    //             ]
    //         );
    //     }

    //     if ($val->status_ca == 1) {
    //         $status_ca = 'recommend';
    //     }elseif($val->status_ca == 2){
    //         $status_ca = 'not recommend';
    //     }else{
    //         $status_ca = 'waiting';
    //     }

    //     $data[] = [
    //         'id_trans_so'    => $val->id_trans_so,
    //         'transaksi'   => [
    //             'so' => [
    //                 'nomor' => $val->so['nomor_so'],
    //                 'nama'  => $val->so['pic']['nama']
    //             ],
    //             'ao' => [
    //                 'nomor' => $val->so['ao']['nomor_ao'],
    //                 'nama'  => $val->so['ao']['pic']['nama']
    //             ],
    //             'ca' => [
    //                 'nomor' => $val->so['ca']['nomor_ca'],
    //                 'nama'  => $val->so['ca']['pic']['nama']
    //             ],
    //             // 'caa' => [
    //             //     'nomor' => $val->so['caa']['nomor_caa'],
    //             //     'nama'  => $val->so['caa']['pic']['nama']
    //             // ]
    //         ],

    //         'nama_marketing' => $val->so['nama_marketing'],

    //         'pic'  => [
    //             'id'   => $val->id_pic,
    //             'nama' => $val->pic['nama'],
    //         ],
    //         'cabang' => [
    //             'id'   => $val->id_cabang,
    //             'nama' => $val->cabang['nama'],
    //         ],
    //         'asaldata' => [
    //             'id'   => $val->so['asaldata']['id'],
    //             'nama' => $val->so['asaldata']['nama'],
    //         ],
    //         'pengajuan' => [
    //             'plafon' => $val->so['faspin']['plafon'],
    //             'tenor'  => $val->so['faspin']['tenor']
    //         ],
    //         'data_debitur' => [
    //             'id'           => $val->so['id_calon_debitur'],
    //             'nama_lengkap' => $val->so['debt']['nama_lengkap'],
    //             'lamp_usaha'   => $val->so['debt']['lamp_foto_usaha']
    //         ],
    //         'data_agunan' => [
    //             'agunan_tanah'     => $idTan,
    //             'agunan_kendaraan' => $idKen
    //         ],
    //         'pendapatan_usaha' => ['id' => $val->so['ao']['id_pendapatan_usaha']],
    //         'rekomendasi_ao'   => [
    //             'id'     => $val->so['ao']['id_recom_ao'],
    //             'plafon' => $val->so['ao']['recom_ao']['plafon_kredit'],
    //             'tenor'  => $val->so['ao']['recom_ao']['jangka_waktu']
    //         ],
    //         'rekomendasi_ca' => [
    //             'id'     => $val->id_recom_ca,
    //             'plafon' => $val->recom_ca['plafon_kredit'],
    //             'tenor'  => $val->recom_ca['jangka_waktu']
    //         ],
    //         'status_ao' => $status_ca
    //     ];

    //     try {
    //         return response()->json([
    //             'code'   => 200,
    //             'status' => 'success',
    //             'data'   => $data[0]
    //         ], 200);
    //     } catch (Exception $e) {
    //         return response()->json([
    //             "code"    => 501,
    //             "status"  => "error",
    //             "message" => $e
    //         ], 501);
    //     }
    // }

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
            $no = $countCAA->nomor_caa;

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

        $check_caa = TransCAA::where('id_trans_so', $id)->first();

        $lamp_dir = 'public/'.$check->debt['no_ktp'];

        // file_report_mao
        if($file = $req->file('file_report_mao')){

            $path = $lamp_dir.'/mcaa/file_report_mao';

            $name = $file->getClientOriginalName();

            if(!empty($check_caa->file_report_mao))
            {
                File::delete($check_caa->file_report_mao);
            }

            $file->move($path,$name);

            $file_report_mao = $path.'/'.$name;

        }else{
            $file_report_mao = null;
        }

        // file_report_mca
        if($file = $req->file('file_report_mca')){

            $path = $lamp_dir.'/mcaa/file_report_mca';

            $name = $file->getClientOriginalName();

            if(!empty($check_caa->file_report_mca))
            {
                File::delete($check_caa->file_report_mca);
            }

            $file->move($path,$name);

            $file_report_mca = $path.'/'.$name;

        }else{
            $file_report_mca = null;
        }

        // Agunan Files Condition
        $statusFileAgunan = $req->input('status_file_agunan');
        if ($statusFileAgunan == 'ORIGINAL') {
            for ($i = 0; $i < count($req->file_agunan); $i++){
                $listAgunan['agunan'] = $req->file_agunan;
            }

            $file_agunan = implode(";", $listAgunan['agunan']);
        }elseif ($statusFileAgunan == 'CUSTOM') {

            if($files = $req->file('file_agunan')){

                $i = 0;
                foreach($files as $file){

                    if ($file->getClientOriginalExtension() != 'pdf' && $file->getClientOriginalExtension() != 'jpg' && $file->getClientOriginalExtension() != 'jpeg' && $file->getClientOriginalExtension() != 'png' && $file->getClientOriginalExtension() != 'gif') {

                        return response()->json([
                            "code"    => 422,
                            "status"  => "not valid request",
                            "message" => ["file_agunan.".$i => ["file_agunan.".$i." harus bertipe jpg, jpeg, png, pdf"]]
                        ], 422);
                    }

                    $path = $lamp_dir.'/mcaa/file_agunan';
                    $name = $file->getClientOriginalName();
                    $file->move($path,$name);

                    $listAgunan['agunan'][] = $path.'/'.$name;
                }

                $file_agunan = implode(";", $listAgunan['agunan']);
            }
        }else{
            $file_agunan = null;
        }

        // Usaha Files Condition
        $statusFileUsaha = $req->input('status_file_usaha');
        if ($statusFileUsaha == 'ORIGINAL') {
            for ($i = 0; $i < count($req->file_usaha); $i++){
                $listUsaha['usaha'] = $req->file_usaha;
            }

            $file_usaha = implode(";", $listUsaha['usaha']);
        }elseif ($statusFileUsaha == 'CUSTOM') {

            if($files = $req->file('file_usaha')){
                $i = 0;
                foreach($files as $file){

                    if ($file->getClientOriginalExtension() != 'pdf' && $file->getClientOriginalExtension() != 'jpg' && $file->getClientOriginalExtension() != 'jpeg' && $file->getClientOriginalExtension() != 'png' && $file->getClientOriginalExtension() != 'gif') {

                        return response()->json([
                            "code"    => 422,
                            "status"  => "not valid request",
                            "message" => ["file_usaha.".$i => ["file_usaha.".$i." harus bertipe jpg, jpeg, png, pdf"]]
                        ], 422);
                    }

                    $path = $lamp_dir.'/mcaa/file_usaha';
                    $name = $file->getClientOriginalName();
                    $file->move($path,$name);

                    $listUsaha['usaha'][] = $path.'/'.$name;
                }

                $file_usaha = implode(";", $listUsaha['usaha']);
            }
        }else{
            $file_usaha = null;
        }

        // Home File
        if($file = $req->file('file_tempat_tinggal')){

            $path = $lamp_dir.'/mcaa/file_tempat_tinggal';

            $name = $file->getClientOriginalName();

            if(!empty($check_caa->file_tempat_tinggal))
            {
                File::delete($check_caa->file_tempat_tinggal);
            }

            $file->move($path,$name);

            $file_tempat_tinggal = $path.'/'.$name;

        }else{
            $file_tempat_tinggal = $check->so['debt']['lamp_tempat_tinggal'];
        }

        // Othe File
        if($file = $req->file('file_lain')){

            $path = $lamp_dir.'/mcaa/file_lain';

            $name = $file->getClientOriginalName();

            if(!empty($check_caa->file_lain))
            {
                File::delete($check_caa->file_lain);
            }

            $file->move($path,$name);

            $file_lain = $path.'/'.$name;

        }else{
            $file_lain = null;
        }

        // Email Team CAA
        $reqTeam = $req->input('team_caa');
        for ($i = 0; $i < count($reqTeam); $i++) {
            $arrTeam['email'] = $req->team_caa;
        }
        $team_caa = implode(";", $arrTeam['email']);


        $transCAA = array(
            'nomor_caa'          => $nomor_caa,
            'user_id'            => $user_id,
            'id_trans_so'        => $id,
            'id_pic'             => $PIC->id,
            'id_cabang'          => $PIC->id_mk_cabang,
            'penyimpangan'       => $req->input('penyimpangan'),
            'team_caa'           => $team_caa,
            'rincian'            => $req->input('rincian'),
            'file_report_mao'    => $file_report_mao,
            'file_report_mca'    => $file_report_mca,
            'status_file_agunan' => $req->input('status_file_agunan'),
            'file_agunan'        => $file_agunan,
            'status_file_usaha'  => $req->input('status_file_usaha'),
            'file_usaha'         => $file_usaha,
            'file_tempat_tinggal'=> $file_tempat_tinggal,
            'file_lain'          => $file_lain,
            'catatan_caa'        => $req->input('catatan_caa'),
            'status_caa'         => empty($req->input('status_caa')) ? 1 : $req->input('status_caa'),
        );

        DB::connection('web')->beginTransaction();

        try {
            if ($check_caa == null) {
                $CAA = TransCAA::create($transCAA);

                TransSO::where('id', $id)->update(['id_trans_caa' => $CAA->id]);
            }else{
                TransCAA::where('id', $check_caa->id)->update($transCAA);
            }

            DB::connection('web')->commit();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message'=> 'Data untuk CAA berhasil dikirim'
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

    public function idOrString($idOrString, Request $req){
        $user_id  = $req->auth->user_id;

        $pic = PIC::where('user_id', $user_id)->first();

        if ($pic == null) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "User_ID anda adalah '".$user_id."' dengan username '".$req->auth->user."' . Namun anda belum terdaftar sebagai PIC(CAA). Harap daftarkan diri sebagai PIC(CAA) pada form PIC atau hubungi bagian IT"
            ], 404);
        }

        $id_cabang = $pic->id_mk_cabang;

        if($idOrString == 'list_done'){
            $query = TransCAA::with('so', 'pic', 'cabang')->where('id_cabang', $id_cabang)->where('status_caa', 1)->get();

            if ($query == '[]') {
                return response()->json([
                    'code'    => 404,
                    'status'  => 'not found',
                    'message' => 'Data kosong lho'
                ], 404);
            }

            foreach ($query as $key => $val) {

                if ($val->status_caa == 1) {
                    $status_caa = 'recommend';
                }elseif($val->status_caa == 2){
                    $status_caa = 'not recommend';
                }else{
                    $status_caa = 'waiting';
                }

                $data[$key] = [
                    'id_trans_so'    => $val->id_trans_so,

                    'nomor_so'       => $val->so['nomor_so'],
                    'nomor_ao'       => $val->so['ao']['nomor_ao'],
                    'nomor_ca'       => $val->so['ca']['nomor_ca'],
                    'nomor_caa'      => $val->nomor_caa,

                    'pic'            => $val->pic['nama'],
                    'cabang'         => $val->cabang['nama'],
                    'asal_data'      => $val->so['asaldata']['nama'],
                    'nama_marketing' => $val->so['nama_marketing'],
                    'pengajuan' => [
                        'plafon' => $val->so['faspin']['plafon'],
                        'tenor'  => $val->so['faspin']['tenor']
                    ],
                    'nama_debitur'   => $val->so['debt']['nama_lengkap'],
                    'status_caa'     => $status_caa
                ];
            }
        }else{
            $id = $idOrString;

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

            $data = array(
                'id_trans_so'    => $val->id_trans_so,
                'transaksi'   => [
                    'so' => [
                        'nomor' => $val->so['nomor_so'],
                        'nama'  => $val->so['pic']['nama']
                    ],
                    'ao' => [
                        'nomor' => $val->so['ao']['nomor_ao'],
                        'nama'  => $val->so['ao']['pic']['nama']
                    ],
                    'ca' => [
                        'nomor' => $val->so['ca']['nomor_ca'],
                        'nama'  => $val->so['ca']['pic']['nama']
                    ],
                    // 'caa' => [
                    //     'nomor' => $val->so['caa']['nomor_caa'],
                    //     'nama'  => $val->so['caa']['pic']['nama']
                    // ]
                ],

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
            );
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

    public function detail($id, Request $req){
        $user_id  = $req->auth->user_id;

        $pic = PIC::where('user_id', $user_id)->first();

        if ($pic == null) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "User_ID anda adalah '".$user_id."' dengan username '".$req->auth->user."' . Namun anda belum terdaftar sebagai PIC(CAA). Harap daftarkan diri sebagai PIC(CAA) pada form PIC atau hubungi bagian IT"
            ], 404);
        }

        $id_cabang = $pic->id_mk_cabang;

        $val = TransCAA::with('so', 'pic', 'cabang')->where('id_cabang', $id_cabang)->where('id_trans_so', $id)->first();

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

            if ($val->status_caa == 1) {
                $status_caa = 'recommend';
            }elseif($val->status_caa == 2){
                $status_caa = 'not recommend';
            }else{
                $status_caa = 'waiting';
            }

            $data = array(
                'id_trans_so'    => $val->id_trans_so,
                'transaksi'   => [
                    'so' => [
                        'nomor' => $val->so['nomor_so'],
                        'nama'  => $val->so['pic']['nama']
                    ],
                    'ao' => [
                        'nomor' => $val->so['ao']['nomor_ao'],
                        'nama'  => $val->so['ao']['pic']['nama']
                    ],
                    'ca' => [
                        'nomor' => $val->so['ca']['nomor_ca'],
                        'nama'  => $val->so['ca']['pic']['nama']
                    ],
                    'caa' => [
                        'nomor' => $val->nomor_caa,
                        'nama'  => $val->pic['nama']
                    ]
                ],

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
                    'id'     => $val->so['ca']['id_recom_ca'],
                    'plafon' => $val->so['ca']['recom_ca']['plafon_kredit'],
                    'tenor'  => $val->so['ca']['recom_ca']['jangka_waktu']
                ],
                'lampiran' => [
                    'file_report_mao'     => $val->file_report_mao,
                    'file_report_mca'     => $val->file_report_mca,
                    'file_agunan'         => explode(";", $val->file_agunan),
                    'file_usaha'          => explode(";", $val->file_usaha),
                    'file_tempat_tinggal' => $val->file_tempat_tinggal,
                    'file_lain'           => explode(";", $val->file_lain)
                ],
                'status_caa' => $status_caa
            );

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

    public function search($search, Request $req){
        $user_id  = $req->auth->user_id;

        $pic = PIC::where('user_id', $user_id)->first();

        if ($pic == null) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "User_ID anda adalah '".$user_id."' dengan username '".$req->auth->user."' . Namun anda belum terdaftar sebagai PIC(CAA). Harap daftarkan diri sebagai PIC(CAA) pada form PIC atau hubungi bagian IT"
            ], 404);
        }

        $id_cabang = $pic->id_mk_cabang;

        $query = TransCA::with('pic', 'cabang')
                ->where('id_cabang', $id_cabang)
                ->where('status_ca', 1)
                ->where('nomor_ca', 'like', '%'.$search.'%')
                ->get();

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
                // 'nomor_caa'      => $val->so['caa']['nomor_caa'],

                'pic'            => $val->pic['nama'],
                'cabang'         => $val->cabang['nama'],
                'asal_data'      => $val->so['asaldata']['nama'],
                'nama_marketing' => $val->so['nama_marketing'],
                'pengajuan' => [
                    'plafon' => $val->so['faspin']['plafon'],
                    'tenor'  => $val->so['faspin']['tenor']
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
}
