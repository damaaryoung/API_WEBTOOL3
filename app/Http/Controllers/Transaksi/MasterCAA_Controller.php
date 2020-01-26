<?php

namespace App\Http\Controllers\Transaksi;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use App\Http\Requests\Transaksi\BlankRequest;
use App\Models\Pengajuan\AO\AgunanKendaraan;
use App\Models\Pengajuan\AO\AgunanTanah;
use Illuminate\Support\Facades\File;
use App\Models\Transaksi\TransTCAA;
use App\Models\Transaksi\TransCAA;
use App\Models\Transaksi\TransCA;
use App\Models\Transaksi\TransSO;
use App\Models\Transaksi\TransAO;
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

        if ($id_cabang == 0) {
            $query = TransCA::with('so', 'pic', 'cabang')->where('status_ca', 1)->get();
        }elseif ($id_cabang != 0) {
            $query = TransCA::with('so', 'pic', 'cabang')->where('id_cabang', $id_cabang)->where('status_ca', 1)->get();
        }

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

            if($val->so['caa']['status_caa'] == 0){
                $status_caa = 'waiting';
            }elseif ($val->so['caa']['status_caa'] == 1) {
                $status_caa = 'recommend';
            }elseif($val->so['caa']['status_caa'] == 2){
                $status_caa = 'not recommend';
            }elseif ($val->so['caa']['status_caa'] == null || $val->so['caa']['status_caa'] == "") {
                $status_caa = 'null';
            }

            $id_agu_ta = explode (",",$val->so['ao']['id_agunan_tanah']);
            $AguTa = AgunanTanah::whereIn('id', $id_agu_ta)->get();

            $Tan = array();
            foreach ($AguTa as $key => $value) {
                $Tan[$key] = array(
                    'id'    => $id_agu_ta[$key],
                    'jenis' => $value->jenis_sertifikat
                );
            }

            $id_agu_ke = explode (",",$val->so['ao']['id_agunan_kendaraan']);
            $AguKe = AgunanKendaraan::whereIn('id', $id_agu_ke)->get();

            $Ken = array();
            foreach ($AguKe as $key => $value) {
                $Ken[$key] = array(
                    'id'    => $id_agu_ke[$key],
                    'jenis' => $value->jenis
                );
            }

            $data[] = [
                'id_trans_so'    => $val->id_trans_so,
                'nomor_so'       => $val->so['nomor_so'],

                'nomor_ao'       => $val->so['ao']['nomor_ao'],
                'nomor_ca'       => $val->nomor_ca,
                // 'nomor_caa'      => $val->so['caa']['nomor_caa'],

                'pic'            => $val->pic['nama'],
                'area'           => $val->area['nama'],
                'cabang'         => $val->cabang['nama'],
                'asal_data'      => $val->so['asaldata']['nama'],
                'nama_marketing' => $val->so['nama_marketing'],
                'pengajuan' => [
                    'plafon' => $val->so['faspin']['plafon'],
                    'tenor'  => $val->so['faspin']['tenor']
                ],
                'nama_debitur'   => $val->so['debt']['nama_lengkap'],
                'agunan' => [
                    'tanah'     => $Tan,
                    'kendaraan' => $Ken
                ],
                'status_ca'     => $status_ca,
                'status_caa'    => $status_caa,
                'tgl_transaksi' => Carbon::parse($val->created_at)->format("d-m-Y H:i:s")
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
            for ($i = 0; $i < count($req->input('file_usaha')); $i++){
                $listUsaha['usaha'] = $req->input('file_usaha');
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

                // dd($listUsaha);

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
        // if (!empty($req->input('team_caa'))) {
        //     for ($i = 0; $i < count($req->input('team_caa')); $i++) {
        //         $arrTeam['team'][$i] = $req->input('team_caa')[$i];
        //     }

        //     $team_caa = implode(",", $arrTeam['team']);
        // }else{

        //     $arrTeam['team'] = null;
        //     $team_caa = null;
        // }

        // dd($team_caa);

        $data = array(
            'nomor_caa'          => $nomor_caa,
            'user_id'            => $user_id,
            'id_trans_so'        => $id,
            'id_pic'             => $PIC->id,
            'id_area'            => $PIC->id_mk_area,
            'id_cabang'          => $PIC->id_mk_cabang,
            'penyimpangan'       => $req->input('penyimpangan'),
            'pic_team_caa'       => $team_caa,
            'rincian'            => $req->input('rincian'),
            'file_report_mao'    => $file_report_mao,
            'file_report_mca'    => $file_report_mca,
            'status_file_agunan' => $req->input('status_file_agunan'),
            'file_agunan'        => $file_agunan,
            'status_file_usaha'  => $req->input('status_file_usaha'),
            'file_usaha'         => $file_usaha,
            'file_tempat_tinggal'=> $file_tempat_tinggal,
            'file_lain'          => $file_lain,
            'status_caa'         => 1
        );

        DB::connection('web')->beginTransaction();

        try {
            if ($check_caa == null) {

                $CAA = TransCAA::create($data);

                TransSO::where('id', $id)->update(['id_trans_caa' => $CAA->id]);

            }else{

                TransSO::where('id', $id)->update(['id_trans_caa' => $check_caa->id]);

                TransCAA::where('id', $check_caa->id)->update($data);
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

        $id_area   = $pic->id_mk_area;
        $id_cabang = $pic->id_mk_cabang;

        // List Sdi Tahap 2
        if($idOrString == 'list_done'){

            if ($id_cabang == 0) {
                $query = TransCAA::with('so', 'pic', 'cabang')
                ->where('status_caa', 1)
                ->get();
            }elseif ($id_cabang != 0) {
                $query = TransCAA::with('so', 'pic', 'cabang')
                    ->where('id_cabang', $id_cabang)
                    ->where('status_caa', 1)
                    ->get();
            }


            if ($query == '[]') {
                return response()->json([
                    'code'    => 404,
                    'status'  => 'not found',
                    'message' => 'Data kosong'
                ], 404);
            }

            foreach ($query as $key => $val) {

                $id_agu_ta = explode (",",$val->so['ao']['id_agunan_tanah']);
                $AguTa = AgunanTanah::whereIn('id', $id_agu_ta)->get();

                $Tan = array();
                foreach ($AguTa as $key => $value) {
                    $Tan[$key] = array(
                        'id'    => $id_agu_ta[$key],
                        'jenis' => $value->jenis_sertifikat
                    );
                }

                $id_agu_ke = explode (",",$val->so['ao']['id_agunan_kendaraan']);
                $AguKe = AgunanKendaraan::whereIn('id', $id_agu_ke)->get();

                $Ken = array();
                foreach ($AguKe as $key => $value) {
                    $Ken[$key] = array(
                        'id'    => $id_agu_ke[$key],
                        'jenis' => $value->jenis
                    );
                }

                $rekomendasi_ao = array(
                    'id'               => $val->so['ao']['id_recom_ao'],
                    'produk'           => $val->so['ao']['recom_ao']['produk'],
                    'plafon'           => $val->so['ao']['recom_ao']['plafon_kredit'],
                    'tenor'            => $val->so['ao']['recom_ao']['jangka_waktu'],
                    'suku_bunga'       => $val->so['ao']['recom_ao']['suku_bunga'],
                    'pembayaran_bunga' => $val->so['ao']['recom_ao']['pembayaran_bunga']
                );

                $rekomendasi_ca = array(
                    'id'               => $val->so['ca']['id_recom_ca'],
                    'produk'           => $val->so['ca']['recom_ca']['produk'],
                    'plafon'           => $val->so['ca']['recom_ca']['plafon_kredit'],
                    'tenor'            => $val->so['ca']['recom_ca']['jangka_waktu'],
                    'suku_bunga'       => $val->so['ca']['recom_ca']['suku_bunga'],
                    'pembayaran_bunga' => $val->so['ca']['recom_ca']['pembayaran_bunga'],
                    'rekomendasi_angsuran' => $val->so['ca']['recom_ca']['rekom_angsuran']
                );

                if ($val->status_caa == 0) {
                    $status_caa = 'waiting';
                }elseif ($val->status_caa == 1) {
                    $status_caa = 'approve';
                }elseif($val->status_caa == 2){
                    $status_caa = 'return';
                }elseif($val->status_caa == 3){
                    $status_caa = 'reject';
                }

                $data[] = [
                    'id_trans_so'    => $val->id_trans_so,

                    'nomor_so'       => $val->so['nomor_so'],
                    'nomor_ao'       => $val->so['ao']['nomor_ao'],
                    'nomor_ca'       => $val->so['ca']['nomor_ca'],
                    'nomor_caa'      => $val->nomor_caa,

                    'pic'            => $val->pic['nama'],
                    'area'           => $val->area['nama'],
                    'cabang'         => $val->cabang['nama'],
                    'asal_data'      => $val->so['asaldata']['nama'],
                    'nama_marketing' => $val->so['nama_marketing'],
                    'pengajuan' => [
                        'plafon' => $val->so['faspin']['plafon'],
                        'tenor'  => $val->so['faspin']['tenor']
                    ],
                    'rekomendasi_ao' => $rekomendasi_ao,
                    'rekomendasi_ca' => $rekomendasi_ca,
                    'nama_debitur'   => $val->so['debt']['nama_lengkap'],
                    'agunan' => [
                        'tanah'     => $Tan,
                        'kendaraan' => $Ken
                    ],
                    'status_caa'    => $status_caa,
                    'tgl_transaksi' => Carbon::parse($val->updated_at)->format("d-m-Y H:i:s"),
                ];
            }
        }else{
            $id = $idOrString;

            if ($id_cabang == 0) {

                $val = TransCA::with('pic', 'cabang')->where('id_trans_so', $id)->first();

            }elseif ($id_cabang != 0) {

                $val = TransCA::with('pic', 'cabang')
                        ->where('id_cabang', $id_cabang)
                        ->where('id_trans_so', $id)
                        ->first();
            }


            if ($val == null) {
                return response()->json([
                    'code'    => 404,
                    'status'  => 'not found',
                    'message' => 'Data kosong'
                ], 404);
            }

            $id_agu_ta = explode (",",$val->so['ao']['id_agunan_tanah']);

            $AguTa = AgunanTanah::whereIn('id', $id_agu_ta)->get();

            foreach ($AguTa as $key => $value) {
                $idTan[$key] = array(
                    'id' => $value->id,
                    'jenis' => $value->jenis_sertifikat,
                    'lampiran' => [
                        'agunan_depan'    => $value->lamp_agunan_depan,
                        'agunan_kanan'    => $value->lamp_agunan_kanan,
                        'agunan_kiri'     => $value->lamp_agunan_kiri,
                        'agunan_belakang' => $value->lamp_agunan_belakang,
                        'agunan_dalam'    => $value->lamp_agunan_dalam
                    ]
                );
            }


            $id_agu_ke = explode (",",$val->so['ao']['id_agunan_kendaraan']);

            $AguKe = AgunanKendaraan::whereIn('id', $id_agu_ke)->get();

            foreach ($AguKe as $key => $value) {
                $idKen[$key] = array(
                    'id' => $value->id,
                    'jenis' => $value->jenis,
                    'lampiran' => [
                        'agunan_depan'    => $value->lamp_agunan_depan,
                        'agunan_kanan'    => $value->lamp_agunan_kanan,
                        'agunan_kiri'     => $value->lamp_agunan_kiri,
                        'agunan_belakang' => $value->lamp_agunan_belakang,
                        'agunan_dalam'    => $value->lamp_agunan_dalam,
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
                'area' => [
                    'id'   => $val->id_area,
                    'nama' => $val->area['nama'],
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

        if ($id_cabang == 0) {
            $val = TransCAA::with('so', 'pic', 'cabang')->where('id_trans_so', $id)->first();
        }elseif ($id_cabang != 0) {
            $val = TransCAA::with('so', 'pic', 'cabang')
                ->where('id_cabang', $id_cabang)
                ->where('id_trans_so', $id)
                ->first();
        }


        if ($val == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        $id_agu_ta = explode (",",$val->so['ao']['id_agunan_tanah']);

        $AguTa = AgunanTanah::whereIn('id', $id_agu_ta)->get();

        foreach ($AguTa as $key => $value) {
            $idTan[$key] = array(
                'id'             => $value->id,
                'jenis'          => $value->jenis_sertifikat,
                'tipe_lokasi'    => $value->tipe_lokasi,
                'luas' => [
                    'tanah'    => $value->luas_tanah,
                    'bangunan' => $value->luas_bangunan
                ],
                'tgl_berlaku_shgb'        => Carbon::parse($value->tgl_berlaku_shgb)->format("d-m-Y"),
                'nama_pemilik_sertifikat' => $value->nama_pemilik_sertifikat,
                'tgl_atau_no_ukur'        => $value->tgl_ukur_sertifikat,
                'lampiran' => [
                    'agunan_depan'    => $value->lamp_agunan_depan,
                    'agunan_kanan'    => $value->lamp_agunan_kanan,
                    'agunan_kiri'     => $value->lamp_agunan_kiri,
                    'agunan_belakang' => $value->lamp_agunan_belakang,
                    'agunan_dalam'    => $value->lamp_agunan_dalam
                ]
            );
        }


        $id_agu_ke = explode (",",$val->so['ao']['id_agunan_kendaraan']);

        $AguKe = AgunanKendaraan::whereIn('id', $id_agu_ke)->get();

        foreach ($AguKe as $key => $value) {
            $idKen[$key] = array(
                'id'                    => $value->id,
                'jenis'                 => 'BPKB',
                'tipe_kendaraan'        => $value->jenis,
                'merk'                  => $value->merk,
                'tgl_kadaluarsa_pajak'  => Carbon::parse($value->tgl_kadaluarsa_pajak)->format('d-m-Y'),
                'tgl_kadaluarsa_stnk'   => Carbon::parse($value->tgl_kadaluarsa_stnk)->format('d-m-Y'),
                'nama_pemilik'          => $value->nama_pemilik,
                'no_bpkb'               => $value->no_bpkb,
                'no_polisi'             => $value->no_polisi,
                'no_stnk'               => $value->no_stnk,
                'lampiran' => [
                    'agunan_depan'    => $value->lamp_agunan_depan,
                    'agunan_kanan'    => $value->lamp_agunan_kanan,
                    'agunan_kiri'     => $value->lamp_agunan_kiri,
                    'agunan_belakang' => $value->lamp_agunan_belakang,
                    'agunan_dalam'    => $value->lamp_agunan_dalam,
                ]
            );
        }

        $pic_team_caa = explode(",", $val->pic_team_caa);


        $get_pic = PIC::with('jpic')->whereIn('id', explode(",", $val->pic_team_caa))->get();

        if($get_pic == '[]'){
            $ptc = null;
        }else{
            $ptc = array();
            for ($i = 0; $i < count($get_pic); $i++) {
                $ptc[] = [
                    'id_pic'    => $get_pic[$i]['id'],
                    'nama'      => $get_pic[$i]['nama'],
                    'jabatan'   => $get_pic[$i]['jpic']['nama_jenis']
                ];
            }
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
            'data_debitur' => [
                'id'           => $val->so['id_calon_debitur'],
                'nama_lengkap' => $val->so['debt']['nama_lengkap'],
                'alamat_domisili' => [
                    'alamat_singkat' => $val->so['debt']['alamat_domisili'],
                    'rt'             => $val->so['debt']['rt_domisili'],
                    'rw'             => $val->so['debt']['rw_domisili'],
                    'kelurahan' => [
                        'id'    => $val->so['debt']['id_kel_tempat_kerja'],
                        'nama'  => $val->so['debt']['kel_dom']['nama']
                    ],
                    'kecamatan' => [
                        'id'    => $val->so['debt']['id_kec_domisili'],
                        'nama'  => $val->so['debt']['kec_dom']['nama']
                    ],
                    'kabupaten' => [
                        'id'    => $val->so['debt']['id_kab_domisili'],
                        'nama'  => $val->so['debt']['kab_dom']['nama'],
                    ],
                    'provinsi'  => [
                        'id'   => $val->so['debt']['id_prov_domisili'],
                        'nama' => $val->so['debt']['prov_dom']['nama'],
                    ],
                    'kode_pos' => $val->so['debt']['kel_dom']['kode_pos']
                ],
                'lamp_usaha'   => $val->so['debt']['lamp_foto_usaha']
            ],
            'data_agunan' => [
                'agunan_tanah'     => $idTan,
                'agunan_kendaraan' => $idKen
            ],
            'pendapatan_usaha' => ['id' => $val->so['ao']['id_pendapatan_usaha']],
            'penyimpangan' => $val->penyimpangan,
            'team_caa'  => $ptc,
            'pengajuan' => [
                'plafon' => $val->so['faspin']['plafon'],
                'tenor'  => $val->so['faspin']['tenor'],
                'jenis_pinjaman' => $val->so['faspin']['jenis_pinjaman']
            ],
            'rekomendasi_ao'   => [
                'id'               => $val->so['ao']['id_recom_ao'],
                'produk'           => $val->so['ao']['recom_ao']['produk'],
                'plafon'           => $val->so['ao']['recom_ao']['plafon_kredit'],
                'tenor'            => $val->so['ao']['recom_ao']['jangka_waktu'],
                'suku_bunga'       => $val->so['ao']['recom_ao']['suku_bunga'],
                'pembayaran_bunga' => $val->so['ao']['recom_ao']['pembayaran_bunga']
            ],
            'rekomendasi_ca' => [
                'id'               => $val->so['ca']['id_recom_ca'],
                'produk'           => $val->so['ca']['recom_ca']['produk'],
                'plafon'           => $val->so['ca']['recom_ca']['plafon_kredit'],
                'tenor'            => $val->so['ca']['recom_ca']['jangka_waktu'],
                'suku_bunga'       => $val->so['ca']['recom_ca']['suku_bunga'],
                'pembayaran_bunga' => $val->so['ca']['recom_ca']['pembayaran_bunga'],
                'rekomendasi_angsuran' => $val->so['ca']['recom_ca']['rekom_angsuran']
            ],
            'data_biaya' => [
                'reguler' => $reguler = array(
                    'biaya_provisi'         => $val->so['ca']['recom_ca']['biaya_provisi'],
                    'biaya_administrasi'    => $val->so['ca']['recom_ca']['biaya_administrasi'],
                    'biaya_premi' => [
                        'asuransi_jiwa'     => $val->so['ca']['recom_ca']['biaya_asuransi_jiwa'],
                        'asuransi_jaminan'  => $val->so['ca']['recom_ca']['biaya_asuransi_jaminan']
                    ],
                    'biaya_tabungan'                    => $val->so['ca']['recom_ca']['biaya_tabungan'],
                    'biaya_notaris'                     => $val->so['ca']['recom_ca']['notaris'],
                    'angsuran_pertama_bungan_berjalan'  => $val->so['ca']['recom_ca']['angs_pertama_bunga_berjalan'],
                    'pelunasan_nasabah_ro'              => $val->so['ca']['recom_ca']['pelunasan_nasabah_ro']
                ),

                'hold_dana' => $hold_dana = array(
                    'pelunasan_tempat_lain'         => $val->so['ca']['recom_ca']['pelunasan_tempat_lain'],
                    'blokir' => [
                        'tempat_lain'               => $val->so['ca']['recom_ca']['blokir_dana'],
                        'dua_kali_angsuran_kredit'  => $val->so['ca']['recom_ca']['blokir_angs_kredit']
                    ]
                ),

                'total' => array(
                    'biaya_reguler'     => $ttl1 = array_sum($reguler + $reguler['biaya_premi']),
                    'biaya_hold_dana'   => $ttl2 = array_sum($hold_dana + $hold_dana['blokir']),
                    'jml_total'         => $ttl1 + $ttl2
                )
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

        if ($id_cabang == 0) {

            $query = TransCA::with('pic', 'cabang')
                ->where('status_ca', 1)
                ->where('nomor_ca', 'like', '%'.$search.'%')
                ->get();

        }elseif ($id_cabang != 0) {

            $query = TransCA::with('pic', 'cabang')
                    ->where('id_cabang', $id_cabang)
                    ->where('status_ca', 1)
                    ->where('nomor_ca', 'like', '%'.$search.'%')
                    ->get();
        }


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
