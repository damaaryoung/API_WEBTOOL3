<?php

namespace App\Http\Controllers\Transaksi;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use App\Http\Requests\Transaksi\BlankRequest;
use App\Models\Pengajuan\CAA\Penyimpangan;
use App\Models\Pengajuan\AO\AgunanKendaraan;
use App\Models\Pengajuan\AO\AgunanTanah;
use App\Models\Pengajuan\AO\PemeriksaanAgunTan;
use App\Models\Pengajuan\AO\PemeriksaanAgunKen;
use App\Models\Pengajuan\SO\Penjamin;
use App\Models\Pengajuan\CA\InfoACC;
use Illuminate\Support\Facades\File;
use App\Models\Transaksi\Approval;
use App\Models\Transaksi\TransCAA;
use App\Models\Transaksi\TransCA;
use App\Models\Transaksi\TransSO;
use App\Models\Transaksi\TransAO;
use App\Models\AreaKantor\JPIC;
use App\Models\AreaKantor\PIC;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Image;
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

        $id_area   = $pic->id_area;
        $id_cabang = $pic->id_cabang;
        $scope     = $pic->jpic['cakupan'];

        $query_dir = TransCA::with('so', 'pic', 'cabang')->where('status_ca', 1)->orderBy('created_at', 'desc');

        $query = Helper::checkDir($scope, $query_dir, $id_area, $id_cabang);

        if ($query->get() == '[]') {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan di CA masih kosong'
            ], 404);
        }

        $data = array();
        foreach ($query->get() as $key => $val) {

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
                    'id'    => $id_agu_ta[$key] == null ? null : (int) $id_agu_ta[$key],
                    'jenis' => $value->jenis_sertifikat
                );
            }

            $id_agu_ke = explode (",",$val->so['ao']['id_agunan_kendaraan']);
            $AguKe = AgunanKendaraan::whereIn('id', $id_agu_ke)->get();

            if ($AguKe == '[]') {
                $Ken = null;
            }else{
                $Ken = array();
                foreach ($AguKe as $key => $value) {
                    $Ken[$key] = array(
                        'id'    => $id_agu_ke[$key] == null ? null : (int) $id_agu_ke[$key],
                        'jenis' => $value->jenis
                    );
                }
            }


            // Check Approval
            $id_komisi = explode(",", $val->so['caa']['pic_team_caa']);

            $check_approval = Approval::whereIn("id_pic", $id_komisi)
                    ->where('id_trans_so', $val->id_trans_so)
                    ->select("id_pic","id","plafon","tenor","rincian", "status", "updated_at as tgl_approve")
                    ->get();

            $Appro = array();
            foreach ($check_approval as $key => $cap) {
                $Appro[$key] = array(
                    "id_pic"      => $cap->id_pic,
                    "jabatan"     => $cap->pic['jpic']['nama_jenis'],
                    "id_approval" => $cap->id,
                    "plafon"      => $cap->plafon,
                    "tenor"       => $cap->tenor,
                    "rincian"     => $cap->rincian,
                    "status"      => $cap->status,
                    "tgl_approve" => $cap->updated_at
                );
            }

            $rekomendasi_ao = array(
                'id'               => $val->so['ao']['id_recom_ao'] == null ? null : (int) $val->so['ao']['id_recom_ao'],
                'produk'           => $val->so['ao']['recom_ao']['produk'],
                'plafon'           => (int) $val->so['ao']['recom_ao']['plafon_kredit'],
                'tenor'            => (int) $val->so['ao']['recom_ao']['jangka_waktu'],
                'suku_bunga'       => floatval($val->so['ao']['recom_ao']['suku_bunga']),
                'pembayaran_bunga' => (int) $val->so['ao']['recom_ao']['pembayaran_bunga']
            );

            $rekomendasi_ca = array(
                'id'                   => $val->so['ca']['id_recom_ca'] == null ? null : (int) $val->so['ca']['id_recom_ca'],
                'produk'               => $val->so['ca']['recom_ca']['produk'],
                'plafon'               => (int) $val->recom_ca['plafon_kredit'],
                'tenor'                => (int) $val->recom_ca['jangka_waktu'],
                'suku_bunga'           => floatval($val->recom_ca['suku_bunga']),
                'pembayaran_bunga'     => (int) $val->recom_ca['pembayaran_bunga'],
                'rekomendasi_angsuran' => (int) $val->recom_ca['rekom_angsuran']
            );

            $data[] = [
                'status_revisi'  => $val->revisi >= 1 ? 'Y' : 'N',
                'id_trans_so'    => $val->id_trans_so == null ? null : (int) $val->id_trans_so,
                'id_trans_ca'    => $val->id == null ? null : (int) $val->id,
                'nomor_so'       => $val->so['nomor_so'],

                'nomor_ao'       => $val->so['ao']['nomor_ao'],
                'nomor_ca'       => $val->nomor_ca,
                'nomor_caa'      => $val->so['caa']['nomor_caa'],

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
                'status_ca'     => $status_ca,
                'status_caa'    => $status_caa,
                'tgl_transaksi' => $val->created_at,
                'approval'      => $Appro
            ];
        }

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'count'  => sizeof($data),
                'data'   => $data
            ], 200);
        } catch (\Exception $e) {
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

        if (empty($PIC)) {
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
        $nomor_caa = $PIC->id_cabang.'-'.$JPIC->nama_jenis.'-'.$month.'-'.$year.'-'.$lastNumb;

        $check = TransSO::where('id',$id)->where('status_das', 1)->where('status_hm', 1)->first();

        if (!$check) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id '.$id.' belum ada di SO atau belum komplit saat pemeriksaaan DAS dan HM'
            ], 404);
        }


        $check_ao = TransAO::where('id_trans_so', $id)->where('status_ao', 1)->first();

        if (!$check_ao) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id '.$id.' belum sampai ke AO'
            ], 404);
        }

        $check_ca = TransCA::where('id_trans_so', $id)->where('status_ca', 1)->first();

        if (!$check_ca) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id '.$id.' belum sampai ke CA'
            ], 404);
        }

        $check_caa = TransCAA::where('id_trans_so', $id)->where('status_caa', 1)->first();

        if ($check_caa != null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id '.$id.' sudah ada di CAA'
            ], 404);
        }

        /** Check Lampiran CAA */
        $check_file_report_mao      = $check->caa['file_report_mao'];
        $check_file_report_mca      = $check->caa['file_report_mca'];
        $check_file_tempat_tinggal  = $check->caa['file_tempat_tinggal'];
        $check_file_lain            = $check->caa['file_lain'];
        $check_file_usaha           = $check->caa['file_usaha'];
        $check_file_agunan          = $check->caa['file_agunan'];
        /** */

        $lamp_dir = 'public/'.$check->debt['no_ktp'];

        // file_report_mao
        if($file = $req->file('file_report_mao')){

            $path = $lamp_dir.'/mcaa/file_report_mao';

            $name = ''; //$file->getClientOriginalName();

            $check_file = $check_file_report_mao;
            
            $file_report_mao = Helper::uploadImg($check_file, $file, $path, $name);

        }else{
            $file_report_mao = $check_file_report_mao;
        }

        // file_report_mca
        if($file = $req->file('file_report_mca')){

            $path = $lamp_dir.'/mcaa/file_report_mca';

            $name = '';
            
            $check_file = $check_file_report_mca;
            
            $file_report_mao = Helper::uploadImg($check_file, $file, $path, $name);

        }else{
            $file_report_mca = $check_file_report_mca;
        }

        // Agunan Files Condition
        $statusFileAgunan = $req->input('status_file_agunan');

        if ($statusFileAgunan == 'ORIGINAL') {
            for ($i = 0; $i < count($req->file_agunan); $i++){
                $listAgunan[] = $req->file_agunan;
            }

            $file_agunan = implode(";", $listAgunan);
        }elseif($statusFileAgunan == 'CUSTOM'){

            if($files = $req->file('file_agunan')){

                $check_file = $check_file_agunan;
                $path = $lamp_dir.'/mcaa/file_agunan';
                $i = 0;
                
                $name = '';

                $arrayPath = array();
                foreach($files as $file)
                {
                    if (
                        $file->getClientOriginalExtension() != 'pdf'  &&
                        $file->getClientOriginalExtension() != 'jpg'  &&
                        $file->getClientOriginalExtension() != 'jpeg' &&
                        $file->getClientOriginalExtension() != 'png'  &&
                        $file->getClientOriginalExtension() != 'gif'
                    ){
                        return response()->json([
                            "code"    => 422,
                            "status"  => "not valid request",
                            "message" => ["file_usaha.".$i => ["file_usaha.".$i." harus bertipe jpg, jpeg, png, pdf"]]
                        ], 422);
                    }

                    $arrayPath[] = Helper::uploadImg($check_file, $file, $path, $name);
                }

                $file_agunan = implode(";", $arrayPath);
            }else{
                $file_agunan = $check_file_agunan;
            }
        }else{
            $file_agunan = null;
        }

        // Usaha Files Condition
        $statusFileUsaha = $req->input('status_file_usaha');
        if ($statusFileUsaha == 'ORIGINAL') {
            for ($i = 0; $i < count($req->input('file_usaha')); $i++){
                $listUsaha[] = $req->input('file_usaha');
            }

            $file_usaha = implode(";", $listUsaha);

        }elseif ($statusFileUsaha == 'CUSTOM') {

            if($files = $req->file('file_usaha')){
                $i = 0;
                $path = $lamp_dir.'/mcaa/file_usaha';
                $name = '';
                
                $check_file = $check_file_usaha;

                $arrayPath = array();
                foreach($files as $file)
                {
                    if (
                        $file->getClientOriginalExtension() != 'pdf'  &&
                        $file->getClientOriginalExtension() != 'jpg'  &&
                        $file->getClientOriginalExtension() != 'jpeg' &&
                        $file->getClientOriginalExtension() != 'png'  &&
                        $file->getClientOriginalExtension() != 'gif'
                    ){
                        return response()->json([
                            "code"    => 422,
                            "status"  => "not valid request",
                            "message" => ["file_usaha.".$i => ["file_usaha.".$i." harus bertipe jpg, jpeg, png, pdf"]]
                        ], 422);
                    }

                    $arrayPath[] = Helper::uploadImg($check_file, $file, $path, $name);
                }

                $file_usaha = implode(";", $arrayPath);
            }else{
                $file_usaha = $check_file_usaha;
            }
        }else{
            $file_usaha = null;
        }

        // Home File
        if($file = $req->file('file_tempat_tinggal')){

            $path = $lamp_dir.'/mcaa/file_tempat_tinggal';

            $name = '';
            
            $check_file = $check_file_tempat_tinggal;
            
            $file_report_mao = Helper::uploadImg($check_file, $file, $path, $name);

        }else{
            $file_tempat_tinggal = $check_file_tempat_tinggal;
        }

        // Othe File
        if($file = $req->file('file_lain')){

            $path = $lamp_dir.'/mcaa/file_lain';

            $name = '';
            
            $check_file = $check_file_lain;
            
            $file_report_mao = Helper::uploadImg($check_file, $file, $path, $name);

        }else{
            $file_lain = $check_file_lain;
        }

        // Email Team CAA
        if (!empty($req->input('team_caa'))) {
            for ($i = 0; $i < count($req->input('team_caa')); $i++) {
                $arrTeam['team'][$i] = $req->input('team_caa')[$i];
            }

            $team_caa = implode(",", $arrTeam['team']);

        }else{
            $team_caa = null;
        }

        $data = array(
            'nomor_caa'          => $nomor_caa,
            'user_id'            => $user_id,
            'id_trans_so'        => $id,
            'id_pic'             => $PIC->id,
            'id_area'            => $PIC->id_area,
            'id_cabang'          => $PIC->id_cabang,
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

        $teamS = explode(",", $data['pic_team_caa']);

        $penyimpangan = array(
            'id_trans_so'           => $id,
            'biaya_provisi'         => $req->input('biaya_provisi'),
            'biaya_admin'           => $req->input('biaya_admin'),
            'biaya_kredit'          => $req->input('biaya_kredit'),
            'ltv'                   => $req->input('ltv'),
            'tenor'                 => $req->input('tenor'),
            'kartu_pinjaman'        => $req->input('kartu_pinjaman'),
            'sertifikat_diatas_50'  => $req->input('sertifikat_diatas_50'),
            'sertifikat_diatas_150' => $req->input('sertifikat_diatas_150'),
            'profesi_beresiko'      => $req->input('profesi_beresiko'),
            'jaminan_kp_tenor_48'   => $req->input('jaminan_kp_tenor_48')
        );

        DB::connection('web')->beginTransaction();

        try {

            $CAA = TransCAA::create($data);

            TransSO::where('id', $id)->update(['id_trans_caa' => $CAA->id]);

            $PIC_app = PIC::whereIn('id', explode(",", $team_caa))->get()->toArray();

            if($PIC_app == []){
                return response()->json([
                    'code'    => 404,
                    'status'  => 'not found',
                    'message' => 'Team CAA tidak terdaftar'
                ], 404);
            }

            $approval = array();
            for ($i=0; $i < count($teamS); $i++){

                $approval[] = Approval::create([
                    'id_trans_so'  => $id,
                    'id_trans_caa' => $CAA->id,
                    'user_id'      => $PIC_app[$i]['user_id'],
                    'id_pic'       => $teamS[$i],
                    'status'       => 'waiting'
                ]);
            }

            if (!empty($penyimpangan)) {
                $penyimpangan_merge = array_merge($penyimpangan, array('id_trans_caa' => $CAA->id));
                Penyimpangan::create($penyimpangan_merge);
            }

            DB::connection('web')->commit();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message'=> 'Data untuk CAA berhasil dikirim',
                'data'   => $approval
            ], 200);
        } catch (\Exception $e) {
            $err = DB::connection('web')->rollback();
            return response()->json([
                'code'    => 501,
                'status'  => 'error',
                'message' => $err
            ], 501);
        }
    }

    public function show($id, Request $req){
        $user_id  = $req->auth->user_id;

        $pic = PIC::where('user_id', $user_id)->first();

        if (empty($pic)) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "User_ID anda adalah '".$user_id."' dengan username '".$req->auth->user."' . Namun anda belum terdaftar sebagai PIC(CAA). Harap daftarkan diri sebagai PIC(CAA) pada form PIC atau hubungi bagian IT"
            ], 404);
        }

        $id_area   = $pic->id_area;
        $id_cabang = $pic->id_cabang;
        $scope     = $pic->jpic['cakupan'];

        $check_so = TransSO::with('pic', 'cabang')->where('id', $id)->where('status_das', 1)->where('status_hm', 1)->first();

        if (empty($check_so)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id '.$id.' belum ada di SO atatu belum komplit saat pemeriksaaan DAS dan HM'
            ], 404);
        }

        $check_ao = TransAO::with('pic', 'cabang')->where('id_trans_so', $id)->where('status_ao', 1)->first();

        if (empty($check_ao)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id '.$id.' belum sampai ke AO'
            ], 404);
        }

        $query_dir = TransCA::with('pic', 'cabang')->where('id_trans_so', $id)
            ->where('status_ca', 1);

        $ca = Helper::checkDir($scope, $query_dir, $id_area, $id_cabang);
        $check_ca = $ca->first();

        if (empty($check_ca)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id '.$id.' belum sampai ke CA'
            ], 404);
        }


        $id_agu_ta = explode (",",$check_ao->id_agunan_tanah);

        $AguTa = AgunanTanah::whereIn('id', $id_agu_ta)->get();

        if (empty($AguTa)) {
            $idTan = null;
        }else{
            $idTan = array();
            foreach ($AguTa as $key => $value) {
                $idTan[$key] = array(
                    'id'             => $value->id == null ? null : (int) $value->id,
                    'jenis'          => $value->jenis_sertifikat,
                    'tipe_lokasi'    => $value->tipe_lokasi,
                    'luas' => [
                        'tanah'    => (int) $value->luas_tanah,
                        'bangunan' => (int) $value->luas_bangunan
                    ],
                    'tgl_berlaku_shgb'        => Carbon::parse($value->tgl_berlaku_shgb)->format("d-m-Y"),
                    'nama_pemilik_sertifikat' => $value->nama_pemilik_sertifikat,
                    'tgl_atau_no_ukur'        => $value->tgl_ukur_sertifikat,
                    'lampiran' => [
                        'agunan_bag_depan'      => $value->agunan_bag_depan,
                        'agunan_bag_jalan'      => $value->agunan_bag_jalan,
                        'agunan_bag_ruangtamu'  => $value->agunan_bag_ruangtamu,
                        'agunan_bag_kamarmandi' => $value->agunan_bag_kamarmandi,
                        'agunan_bag_dapur'      => $value->agunan_bag_dapur
                    ]
                );
            }
        }

        $id_agu_ke = explode (",",$check_ao->id_agunan_kendaraan);
        $AguKe = AgunanKendaraan::whereIn('id', $id_agu_ke)->get();

        if (empty($AguKe)) {
            $idKen = null;
        }else{
            $idKen = array();
            foreach ($AguKe as $key => $value) {
                $idKen[$key] = array(
                    'id'                    => $value->id == null ? null : (int) $value->id,
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
        }


        $infoCC = InfoACC::whereIn('id', explode(",", $check_ca->id_info_analisa_cc))->get()->toArray();

        if ($check_ca->status_ca == 1) {
            $status_ca = 'recommend';
        }elseif($check_ca->status_ca == 2){
            $status_ca = 'not recommend';
        }else{
            $status_ca = 'waiting';
        }

        $data = array(
            'status_revisi' => $check_ca->revisi >= 1 ? 'Y' : 'N',
            'id_trans_so' => $check_so->id == null ? null : (int) $check_so->id,
            'id_trans_ca' => $check_ca->id == null ? null : (int) $check_ca->id,
            'transaksi'   => [
                'so' => [
                    'nomor' => $check_so->nomor_so,
                    'nama'  => $check_so->pic['nama']
                ],
                'ao' => [
                    'nomor' => $check_ao->nomor_ao,
                    'nama'  => $check_ao->pic['nama']
                ],
                'ca' => [
                    'nomor' => $check_ca->nomor_ca,
                    'nama'  => $check_ca->pic['nama']
                ]
            ],
            'nama_marketing' => $check_so->nama_marketing,
            'pic'  => [
                'id'   => $check_ca->id_pic == null ? null : (int) $check_ca->id_pic,
                'nama' => $check_ca->pic['nama'],
            ],
            'area' => [
                'id'   => $check_ca->id_area == null ? null : (int) $check_ca->id_area,
                'nama' => $check_ca->area['nama'],
            ],
            'cabang' => [
                'id'   => $check_ca->id_cabang == null ? null : (int) $check_ca->id_cabang,
                'nama' => $check_ca->cabang['nama'],
            ],
            'asaldata' => [
                'id'   => $check_so->id_asal_data == null ? null : (int) $check_so->id_asal_data,
                'nama' => $check_so->asaldata['nama'],
            ],
            'data_debitur' => [
                'id'           => $check_so->id_calon_debitur == null ? null : (int) $check_so->id_calon_debitur,
                'nama_lengkap' => $check_so->debt['nama_lengkap'],
                'lamp_usaha'   => $check_so->debt['lamp_foto_usaha']
            ],
            'data_agunan' => [
                'agunan_tanah'     => $idTan,
                'agunan_kendaraan' => $idKen
            ],
            'pendapatan_usaha' => [
                'id'        => $check_ca->id_pendapatan_usaha == null ? null : (int) $check_ao->id_pendapatan_usaha,
                'pemasukan' => array(
                    'tunai' => (int) $check_ca->usaha['pemasukan_tunai'],
                    'kredit'=> (int) $check_ca->usaha['pemasukan_kredit'],
                    'total' => (int) $check_ca->usaha['total_pemasukan']
                ),
                'pengeluaran' => array(
                    'biaya_sewa'           => (int) $check_ca->usaha['biaya_sewa'],
                    'biaya_gaji_pegawai'   => (int) $check_ca->usaha['biaya_gaji_pegawai'],
                    'biaya_belanja_brg'    => (int) $check_ca->usaha['biaya_belanja_brg'],
                    'biaya_telp_listr_air' => (int) $check_ca->usaha['biaya_telp_listr_air'],
                    'biaya_sampah_kemanan' => (int) $check_ca->usaha['biaya_sampah_kemanan'],
                    'biaya_kirim_barang'   => (int) $check_ca->usaha['biaya_kirim_barang'],
                    'biaya_hutang_dagang'  => (int) $check_ca->usaha['biaya_hutang_dagang'],
                    'angsuran'             => (int) $check_ca->usaha['biaya_angsuran'],
                    'lain_lain'            => (int) $check_ca->usaha['biaya_lain_lain'],
                    'total'                => (int) $check_ca->usaha['total_pengeluaran']
                ),
                'penghasilan_bersih' => (int) $check_ca->usaha['laba_usaha']
            ],
            'pengajuan' => [
                'plafon' => (int) $check_so->faspin['plafon'],
                'tenor'  => (int) $check_so->faspin['tenor']
            ],
            'rekomendasi_ao'   => [
                'id'               => $check_ao->id_recom_ao == null ? null : (int) $check_ao->id_recom_ao,
                'plafon'           => (int) $check_ao->recom_ao['plafon_kredit'],
                'tenor'            => (int) $check_ao->recom_ao['jangka_waktu'],
                'suku_bunga'       => floatval($check_ao->recom_ao['suku_bunga']),
                'pembayaran_bunga' => (int) $check_ao->recom_ao['pembayaran_bunga'],
                'catatan'          => $check_ao->catatan_ao
            ],
            'rekomendasi_ca' => [
                'id'               => $check_ca->id_recom_ca == null ? null : (int) $check_ca->id_recom_ca,
                'plafon'           => (int) $check_ca->recom_ca['plafon_kredit'],
                'tenor'            => (int) $check_ca->recom_ca['jangka_waktu'],
                'suku_bunga'       => floatval($check_ca->recom_ca['suku_bunga']),
                'pembayaran_bunga' => (int) $check_ca->recom_ca['pembayaran_bunga'],
                'catatan'          => $check_ca->catatan_ca
            ],
            'rekomendasi_pinjaman' => [
                'id'                    => $check_ca->id_rekomendasi_pinjaman,
                'penyimpangan_struktur' => $check_ca->recom_pin['penyimpangan_struktur'],
                'penyimpangan_dokumen'  => $check_ca->recom_pin['penyimpangan_dokumen'],
                'recom_nilai_pinjaman'  => $check_ca->recom_pin['recom_nilai_pinjaman'],
                'recom_tenor'           => $check_ca->recom_pin['recom_tenor'],
                'recom_angsuran'        => $check_ca->recom_pin['recom_angsuran'],
                'recom_produk_kredit'   => $check_ca->recom_pin['recom_produk_kredit'],
                'note_recom'            => $check_ca->recom_pin['note_recom'],
                'bunga_pinjaman'        => $check_ca->recom_pin['bunga_pinjaman'],
                'nama_ca'               => $check_ca->recom_pin['nama_ca']
            ],
            'kapasitas_bulanan' => [
                'id'                    => $check_ca->id_kapasitas_bulanan,
                'pemasukan_cadebt'      => $check_ca->kapbul['pemasukan_cadebt'],
                'pemasukan_pasangan'    => $check_ca->kapbul['pemasukan_pasangan'],
                'pemasukan_penjamin'    => $check_ca->kapbul['pemasukan_penjamin'],
                'biaya_rumah_tangga'    => $check_ca->kapbul['biaya_rumah_tangga'],
                'biaya_transport'       => $check_ca->kapbul['biaya_transport'],
                'biaya_pendidikan'      => $check_ca->kapbul['biaya_pendidikan'],
                'biaya_telp_listr_air'  => $check_ca->kapbul['biaya_telp_listr_air'],
                'angsuran'              => $check_ca->kapbul['angsuran'],
                'biaya_lain'            => $check_ca->kapbul['biaya_lain'],
                'total_pemasukan'       => $check_ca->kapbul['total_pemasukan'],
                'total_pengeluaran'     => $check_ca->kapbul['total_pengeluaran'],
                'penghasilan_bersih'    => $check_ca->kapbul['penghasilan_bersih'],
                'disposable_income'     => $check_ca->kapbul['disposable_income']
            ],
            'data_biaya' => [
                'reguler' => $reguler = array(
                    'biaya_provisi'         => (int) $check_ca->recom_ca['biaya_provisi'],
                    'biaya_administrasi'    => (int) $check_ca->recom_ca['biaya_administrasi'],
                    'biaya_credit_checking' => (int) $check_ca->recom_ca['biaya_credit_checking'],
                    'biaya_premi' => [
                        'asuransi_jiwa'     => (int) $check_ca->recom_ca['biaya_asuransi_jiwa'],
                        'asuransi_jaminan'  => (int) $check_ca->recom_ca['biaya_asuransi_jaminan']
                    ],
                    'biaya_tabungan'                    => (int) $check_ca->recom_ca['biaya_tabungan'],
                    'biaya_notaris'                     => (int) $check_ca->recom_ca['notaris'],
                    'angsuran_pertama_bungan_berjalan'  => (int) $check_ca->recom_ca['angs_pertama_bunga_berjalan'],
                    'pelunasan_nasabah_ro'              => (int) $check_ca->recom_ca['pelunasan_nasabah_ro']
                ),

                'hold_dana' => $hold_dana = array(
                    'pelunasan_tempat_lain'         => (int) $check_ca->recom_ca['pelunasan_tempat_lain'],
                    'blokir' => [
                        'tempat_lain'               => (int) $check_ca->recom_ca['blokir_dana'],
                        'dua_kali_angsuran_kredit'  => (int) $check_ca->recom_ca['blokir_angs_kredit']
                    ]
                ),

                'total' => array(
                    'biaya_reguler'     => $ttl1 = array_sum($reguler + $reguler['biaya_premi']),
                    'biaya_hold_dana'   => $ttl2 = array_sum($hold_dana + $hold_dana['blokir']),
                    'jml_total'         => $ttl1 + $ttl2
                )
            ],
            'info_analisa_cc' => [
                'count_table'              => count($infoCC),
                'ttl_plafon'               => array_sum(array_column($infoCC, 'plafon')),
                'ttl_debet'                => array_sum(array_column($infoCC, 'baki_debet')),
                'ttl_angsuran'             => array_sum(array_column($infoCC, 'angsuran')),
                'collectabilitas_terendah' => max(array_column($infoCC, 'collectabilitas')),
                'table'                    => $infoCC
            ],
            'ringkasan_analisa' => [
                'kuantitatif_ttl_pendapatan'    => $check_ca->ringkasan['kuantitatif_ttl_pendapatan'],
                'kuantitatif_ttl_pengeluaran'   => $check_ca->ringkasan['kuantitatif_ttl_pengeluaran'],
                'kuantitatif_pendapatan_bersih' => $check_ca->ringkasan['kuantitatif_pendapatan_bersih'],
                'kuantitatif_angsuran'          => $check_ca->ringkasan['kuantitatif_angsuran'],
                'kuantitatif_ltv'               => $check_ca->ringkasan['kuantitatif_ltv'],
                'kuantitatif_dsr'               => $check_ca->ringkasan['kuantitatif_dsr'],
                'kuantitatif_idir'              => $check_ca->ringkasan['kuantitatif_idir'],
                'kuantitatif_hasil'             => $check_ca->ringkasan['kuantitatif_hasil']
            ],
            'status_ca'     => $status_ca,
            'tgl_transaksi' => $check_ca->created_at
        );

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => $data
            ], 200);
        } catch (\Exception $e) {
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

        if (empty($pic)) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "User_ID anda adalah '".$user_id."' dengan username '".$req->auth->user."' . Namun anda belum terdaftar sebagai PIC(CAA). Harap daftarkan diri sebagai PIC(CAA) pada form PIC atau hubungi bagian IT"
            ], 404);
        }

        $id_area   = $pic->id_area;
        $id_cabang = $pic->id_cabang;
        $scope     = $pic->jpic['cakupan'];

        $check_so = TransSO::where('id', $id)->where('status_das', 1)->where('status_hm', 1)->first();

        if (!$check_so) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id '.$id.' belum ada di SO atau belum komplit saat pemeriksaan DAS da HM'
            ], 404);
        }

        $check_ao = TransAO::where('id_trans_so', $id)->where('status_ao', 1)->first();

        if (!$check_ao) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id '.$id.' belum sampai ke AO'
            ], 404);
        }

        $check_ca = TransCA::where('id_trans_so', $id)->where('status_ca', 1)->first();

        if (!$check_ca) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id '.$id.' belum sampai ke CA'
            ], 404);
        }

        $query_dir = TransCAA::with('so', 'pic', 'cabang')->where('id_trans_so', $id);

        $caa = Helper::checkDir($scope, $query_dir, $id_area, $id_cabang);
        $check_caa = $caa->first();

        if ($check_caa == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id '.$id.' belum sampai ke CAA'
            ], 404);
        }

        $id_agu_ta = explode (",",$check_ao->id_agunan_tanah);

        $AguTa = AgunanTanah::whereIn('id', $id_agu_ta)->get();

        if ($AguTa == '[]') {
            $idTan = null;
        }else{
            $idTan = array();
            foreach ($AguTa as $key => $value) {
                $idTan[$key] = array(
                    'id'             => $value->id == null ? null : (int) $value->id,
                    'jenis'          => $value->jenis_sertifikat,
                    'tipe_lokasi'    => $value->tipe_lokasi,
                    'luas' => [
                        'tanah'    => (int) $value->luas_tanah,
                        'bangunan' => (int) $value->luas_bangunan
                    ],
                    'tgl_berlaku_shgb'        => Carbon::parse($value->tgl_berlaku_shgb)->format("d-m-Y"),
                    'nama_pemilik_sertifikat' => $value->nama_pemilik_sertifikat,
                    'tgl_atau_no_ukur'        => $value->tgl_ukur_sertifikat,
                    'lampiran' => [
                        'agunan_bag_depan'      => $value->agunan_bag_depan,
                        'agunan_bag_jalan'      => $value->agunan_bag_jalan,
                        'agunan_bag_ruangtamu'  => $value->agunan_bag_ruangtamu,
                        'agunan_bag_kamarmandi' => $value->agunan_bag_kamarmandi,
                        'agunan_bag_dapur'      => $value->agunan_bag_dapur
                    ]
                );
            }  
        }


        $id_agu_ke = explode (",",$check_ao->id_agunan_kendaraan);

        $AguKe = AgunanKendaraan::whereIn('id', $id_agu_ke)->get();

        if ($AguKe == '[]') {
            $idKen = null;
        }else{
            $idKen = array();
            foreach ($AguKe as $key => $value) {
                $idKen[$key] = array(
                    'id'                    => $value->id == null ? null : (int) $value->id,
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
        }

        $get_pic = PIC::with('jpic')->whereIn('id', explode(",", $check_caa->pic_team_caa))->get();

        if($get_pic == '[]'){
            $ptc = null;
        }else{
            $ptc = array();
            for ($i = 0; $i < count($get_pic); $i++) {
                $ptc[] = [
                    'id_pic'    => $get_pic[$i]['id'] == null ? null : (int) $get_pic[$i]['id'],
                    'nama'      => $get_pic[$i]['nama'],
                    'jabatan'   => $get_pic[$i]['jpic']['nama_jenis'],
                    'user_id'   => $get_pic[$i]['user_id'] == null ? null : (int) $get_pic[$i]['user_id']
                ];
            }
        }


        if ($check_caa->status_caa == 1) {
            $status_caa = 'recommend';
        }elseif($check_caa->status_caa == 2){
            $status_caa = 'not recommend';
        }else{
            $status_caa = 'waiting';
        }

        $data = array(
            'id_trans_so' => $check_so->id == null ? null : (int) $check_so->id,
            'transaksi'   => [
                'so' => [
                    'nomor' => $check_so->nomor_so,
                    'nama'  => $check_so->pic['nama']
                ],
                'ao' => [
                    'nomor' => $check_ao->nomor_ao,
                    'nama'  => $check_ao->pic['nama']
                ],
                'ca' => [
                    'nomor' => $check_ca->nomor_ca,
                    'nama'  => $check_ca->pic['nama']
                ],
                'caa' => [
                    'nomor' => $check_caa->nomor_caa,
                    'nama'  => $check_caa->pic['nama']
                ]
            ],

            'nama_marketing' => $check_so->nama_marketing,
            'pic'  => [
                'id'   => $check_caa->id_pic == null ? null : (int) $check_caa->id_pic,
                'nama' => $check_caa->pic['nama'],
            ],
            'area'   => [
                'id'   => $check_caa->id_area == null ? null : (int) $check_caa->id_area,
                'nama' => $check_caa->area['nama']
            ],
            'cabang' => [
                'id'   => $check_caa->id_cabang == null ? null : (int) $check_caa->id_cabang,
                'nama' => $check_caa->cabang['nama'],
            ],
            'asaldata' => [
                'id'   => $check_so->asaldata['id'] == null ? null : (int) $check_so->asaldata['id'],
                'nama' => $check_so->asaldata['nama'],
            ],
            'data_debitur' => [
                'id'           => $check_so->id_calon_debitur == null ? null : (int) $check_so->id_calon_debitur,
                'nama_lengkap' => $check_so->debt['nama_lengkap'],
                'alamat_domisili' => [
                    'alamat_singkat' => $check_so->debt['alamat_domisili'],
                    'rt'             => $check_so->debt['rt_domisili'] == null ? null : (int) $check_so->debt['rt_domisili'],
                    'rw'             => $check_so->debt['rw_domisili'] == null ? null : (int) $check_so->debt['rw_domisili'],
                    'kelurahan' => [
                        'id'    => $check_so->debt['id_kel_tempat_kerja'] == null ? null : (int) $check_so->debt['id_kel_tempat_kerja'],
                        'nama'  => $check_so->debt['kel_dom']['nama']
                    ],
                    'kecamatan' => [
                        'id'    => $check_so->debt['id_kec_domisili'] == null ? null : (int) $check_so->debt['id_kec_domisili'],
                        'nama'  => $check_so->debt['kec_dom']['nama']
                    ],
                    'kabupaten' => [
                        'id'    => $check_so->debt['id_kab_domisili'] == null ? null : (int) $check_so->debt['id_kab_domisili'],
                        'nama'  => $check_so->debt['kab_dom']['nama'],
                    ],
                    'provinsi'  => [
                        'id'   => $check_so->debt['id_prov_domisili'] == null ? null : (int) $check_so->debt['id_prov_domisili'],
                        'nama' => $check_so->debt['prov_dom']['nama'],
                    ],
                    'kode_pos' => $check_so->debt['kel_dom']['kode_pos'] == null ? null : (int) $check_so->debt['kel_dom']['kode_pos']
                ],
                'lamp_usaha'   => $check_so->debt['lamp_foto_usaha']
            ],
            'data_agunan' => [
                'agunan_tanah'     => $idTan,
                'agunan_kendaraan' => $idKen
            ],

            'pendapatan_usaha' => [
                'id'        => $check_ao->id_pendapatan_usaha == null ? null : (int) $check_ao->id_pendapatan_usaha,
                'pemasukan' => array(
                    'tunai' => $check_ao->usaha['pemasukan_tunai'],
                    'kredit'=> $check_ao->usaha['pemasukan_kredit'],
                    'total' => $check_ao->usaha['total_pemasukan']
                ),
                'pengeluaran' => array(
                    'biaya_sewa'           => $check_ao->usaha['biaya_sewa'],
                    'biaya_gaji_pegawai'   => $check_ao->usaha['biaya_gaji_pegawai'],
                    'biaya_belanja_brg'    => $check_ao->usaha['biaya_belanja_brg'],
                    'biaya_telp_listr_air' => $check_ao->usaha['biaya_telp_listr_air'],
                    'biaya_sampah_kemanan' => $check_ao->usaha['biaya_sampah_kemanan'],
                    'biaya_kirim_barang'   => $check_ao->usaha['biaya_kirim_barang'],
                    'biaya_hutang_dagang'  => $check_ao->usaha['biaya_hutang_dagang'],
                    'angsuran'             => $check_ao->usaha['biaya_angsuran'],
                    'lain_lain'            => $check_ao->usaha['biaya_lain_lain'],
                    'total'                => $check_ao->usaha['total_pengeluaran']
                ),
                'penghasilan_bersih' => $check_ao->usaha['laba_usaha']
            ],

            'penyimpangan' => $check_caa->penyimpangan,
            'team_caa'  => $ptc,
            'pengajuan' => [
                'plafon'         => $check_so->faspin['plafon'],
                'tenor'          => $check_so->faspin['tenor'],
                'jenis_pinjaman' => $check_so->faspin['jenis_pinjaman']
            ],
            'rekomendasi_ao'   => [
                'id'               => $check_ao->id_recom_ao == null ? null : (int) $check_ao->id_recom_ao,
                'produk'           => $check_ao->recom_ao['produk'],
                'plafon'           => $check_ao->recom_ao['plafon_kredit'],
                'tenor'            => $check_ao->recom_ao['jangka_waktu'],
                'suku_bunga'       => floatval($check_ao->recom_ao['suku_bunga']),
                'pembayaran_bunga' => $check_ao->recom_ao['pembayaran_bunga'],
                'catatan'          => $check_ao->catatan_ao
            ],
            'rekomendasi_ca' => [
                'id'                   => $check_ca->id_recom_ca == null ? null : (int) $check_ca->id_recom_ca,
                'produk'               => $check_ca->recom_ca['produk'],
                'plafon'               => $check_ca->recom_ca['plafon_kredit'],
                'tenor'                => $check_ca->recom_ca['jangka_waktu'],
                'suku_bunga'           => floatval($check_ca->recom_ca['suku_bunga']),
                'pembayaran_bunga'     => $check_ca->recom_ca['pembayaran_bunga'],
                'rekomendasi_angsuran' => $check_ca->recom_ca['rekom_angsuran'],
                'catatan'              => $check_ca->catatan_ca
            ],
            'data_biaya' => [
                'reguler' => $reguler = array(
                    'biaya_provisi'         => (int) $check_ca->recom_ca['biaya_provisi'],
                    'biaya_administrasi'    => (int) $check_ca->recom_ca['biaya_administrasi'],
                    'biaya_credit_checking' => (int) $check_ca->recom_ca['biaya_credit_checking'],
                    'biaya_premi' => [
                        'asuransi_jiwa'     => (int) $check_ca->recom_ca['biaya_asuransi_jiwa'],
                        'asuransi_jaminan'  => (int) $check_ca->recom_ca['biaya_asuransi_jaminan']
                    ],
                    'biaya_tabungan'                    => (int) $check_ca->recom_ca['biaya_tabungan'],
                    'biaya_notaris'                     => (int) $check_ca->recom_ca['notaris'],
                    'angsuran_pertama_bungan_berjalan'  => (int) $check_ca->recom_ca['angs_pertama_bunga_berjalan'],
                    'pelunasan_nasabah_ro'              => (int) $check_ca->recom_ca['pelunasan_nasabah_ro']
                ),

                'hold_dana' => $hold_dana = array(
                    'pelunasan_tempat_lain'         => (int) $check_ca->recom_ca['pelunasan_tempat_lain'],
                    'blokir' => [
                        'tempat_lain'               => (int) $check_ca->recom_ca['blokir_dana'],
                        'dua_kali_angsuran_kredit'  => (int) $check_ca->recom_ca['blokir_angs_kredit']
                    ]
                ),

                'total' => array(
                    'biaya_reguler'     => $ttl1 = array_sum($reguler + $reguler['biaya_premi']),
                    'biaya_hold_dana'   => $ttl2 = array_sum($hold_dana + $hold_dana['blokir']),
                    'jml_total'         => $ttl1 + $ttl2
                )
            ],
            'lampiran' => [
                'file_report_mao'     => $check_caa->file_report_mao,
                'file_report_mca'     => $check_caa->file_report_mca,
                'file_agunan'         => empty($check_caa->file_agunan) ? null : explode(";", $check_caa->file_agunan),
                'file_usaha'          => empty($check_caa->file_usaha) ? null : explode(";", $check_caa->file_usaha),
                'file_tempat_tinggal' => $check_caa->file_tempat_tinggal,
                'file_lain'           => empty($check_caa->file_lain) ? null : explode(";", $check_caa->file_lain)
            ],
            'rincian'       => $check_caa->rincian,
            'status_caa'    => $status_caa,
            'tgl_transaksi' => $check_caa->created_at
        );

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function search($param, $key, $value, $status, $orderVal, $orderBy, $limit, Request $req)
    {
        $user_id  = $req->auth->user_id;

        $pic = PIC::where('user_id', $user_id)->first();

        if ($pic == null) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "User_ID anda adalah '".$user_id."' dengan username '".$req->auth->user."' . Namun anda belum terdaftar sebagai PIC(CAA). Harap daftarkan diri sebagai PIC(CAA) pada form PIC atau hubungi bagian IT"
            ], 404);
        }

        $column = array(
            'id', 'nomor_ca', 'user_id', 'id_trans_so', 'id_pic', 'id_area', 'id_cabang', 'id_mutasi_bank', 'id_log_tabungan', 'id_info_analisa_cc', 'id_ringkasan_analisa', 'id_recom_ca', 'id_rekomendasi_pinjaman', 'id_asuransi_jiwa', 'id_asuransi_jaminan', 'id_kapasitas_bulanan', 'id_pendapatan_usaha', 'catatan_ca', 'status_ca', 'revisi'
        );

        if($param != 'filter' && $param != 'search'){
            return response()->json([
                'code'    => 412,
                'status'  => 'not valid',
                'message' => 'gunakan parameter yang valid diantara berikut: filter, search'
            ], 412);
        }

        if (in_array($key, $column) == false)
        {
            return response()->json([
                'code'    => 412,
                'status'  => 'not valid',
                'message' => 'gunakan key yang valid diantara berikut: '.implode(",", $column)
            ], 412);
        }

        if (in_array($orderBy, $column) == false)
        {
            return response()->json([
                'code'    => 412,
                'status'  => 'not valid',
                'message' => 'gunakan order by yang valid diantara berikut: '.implode(",", $column)
            ], 412);
        }

        if($param == 'search'){
            $operator   = "like";
            $func_value = "%{$value}%";
        }else{
            $operator   = "=";
            $func_value = "{$value}";
        }

        $id_area   = $pic->id_area;
        $id_cabang = $pic->id_cabang;
        $scope     = $pic->jpic['cakupan'];

        $query_dir = TransCA::with('pic', 'cabang')
            ->where('status_ca', 1)
            ->where('flg_aktif', $status)
            ->orderBy($orderBy, $orderVal);
        
        $query = Helper::checkDir($scope, $query_dir, $id_area, $id_cabang);

        if($value == 'default'){
            $res = $query;
        }else{
            $res = $query->where($key, $operator, $func_value);
        }

        if($limit == 'default'){
            $result = $res;
        }else{
            $result = $res->limit($limit);
        }

        if ($result->get() == '[]') {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $data = array();
        foreach ($result->get() as $key => $val) {

            if ($val->status_ca == 1) {
                $status_ca = 'recommend';
            }elseif($val->status_ca == 2){
                $status_ca = 'not recommend';
            }else{
                $status_ca = 'waiting';
            }

            $data[$key] = [
                'id_trans_so'    => $val->id_trans_so == null ? null : (int) $val->id_trans_so,
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
                'status_ca'      => $status_ca,
                'tgl_transaksi'  => $val->created_at
            ];
        }

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'count'  => sizeof($data),
                'data'   => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function filter($year, $month, Request $req){
        $user_id  = $req->auth->user_id;

        $pic = PIC::where('user_id', $user_id)->first();

        if ($pic == null) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "User_ID anda adalah '".$user_id."' dengan username '".$req->auth->user."' . Namun anda belum terdaftar sebagai PIC(CAA). Harap daftarkan diri sebagai PIC(CAA) pada form PIC atau hubungi bagian IT"
            ], 404);
        }

        $id_area   = $pic->id_area;
        $id_cabang = $pic->id_cabang;
        $scope     = $pic->jpic['cakupan'];

        if ($month == null) {
            $query_dir = TransCA::with('so', 'pic', 'cabang')->where('status_ca', 1)
                    ->whereYear('created_at', '=', $year)
                    ->orderBy('created_at', 'desc');
        }else{

            $query_dir = TransCA::with('so', 'pic', 'cabang')->where('status_ca', 1)
                    ->whereYear('created_at', '=', $year)
                    ->whereMonth('created_at', '=', $month)
                    ->orderBy('created_at', 'desc');
        }

        $query = Helper::checkDir($scope, $query_dir, $id_area, $id_cabang);

        if ($query->get() == '[]') {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        $data = array();
        foreach ($query->get() as $key => $val) {

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
                    'id'    => $id_agu_ta[$key] == null ? null : (int) $id_agu_ta[$key],
                    'jenis' => $value->jenis_sertifikat
                );
            }

            $id_agu_ke = explode (",",$val->so['ao']['id_agunan_kendaraan']);
            $AguKe = AgunanKendaraan::whereIn('id', $id_agu_ke)->get();

            if ($AguKe == '[]') {
                $Ken = null;
            }else{
                $Ken = array();
                foreach ($AguKe as $key => $value) {
                    $Ken[$key] = array(
                        'id'    => $id_agu_ke[$key] == null ? null : (int) $id_agu_ke[$key],
                        'jenis' => $value->jenis
                    );
                }
            }


            // Check Approval
            $id_komisi = explode(",", $val->so['caa']['pic_team_caa']);

            $check_approval = Approval::whereIn("id_pic", $id_komisi)
                    ->where('id_trans_so', $val->id_trans_so)
                    ->select("id_pic","id","plafon","tenor","rincian", "status", "updated_at as tgl_approve")
                    ->get();

            $Appro = array();
            foreach ($check_approval as $key => $cap) {
                $Appro[$key] = array(
                    "id_pic"      => $cap->id_pic,
                    "jabatan"     => $cap->pic['jpic']['nama_jenis'],
                    "id_approval" => $cap->id,
                    "plafon"      => $cap->plafon,
                    "tenor"       => $cap->tenor,
                    "rincian"     => $cap->rincian,
                    "status"      => $cap->status,
                    "tgl_approve" => $cap->updated_at
                );
            }

            $rekomendasi_ao = array(
                'id'               => $val->so['ao']['id_recom_ao'] == null ? null : (int) $val->so['ao']['id_recom_ao'],
                'produk'           => $val->so['ao']['recom_ao']['produk'],
                'plafon'           => (int) $val->so['ao']['recom_ao']['plafon_kredit'],
                'tenor'            => (int) $val->so['ao']['recom_ao']['jangka_waktu'],
                'suku_bunga'       => floatval($val->so['ao']['recom_ao']['suku_bunga']),
                'pembayaran_bunga' => (int) $val->so['ao']['recom_ao']['pembayaran_bunga']
            );

            $rekomendasi_ca = array(
                'id'                   => $val->so['ca']['id_recom_ca'] == null ? null : (int) $val->so['ca']['id_recom_ca'],
                'produk'               => $val->so['ca']['recom_ca']['produk'],
                'plafon'               => (int) $val->recom_ca['plafon_kredit'],
                'tenor'                => (int) $val->recom_ca['jangka_waktu'],
                'suku_bunga'           => floatval($val->recom_ca['suku_bunga']),
                'pembayaran_bunga'     => (int) $val->recom_ca['pembayaran_bunga'],
                'rekomendasi_angsuran' => (int) $val->recom_ca['rekom_angsuran']
            );

            $data[] = [
                'id_trans_so'    => $val->id_trans_so == null ? null : (int) $val->id_trans_so,
                'nomor_so'       => $val->so['nomor_so'],

                'nomor_ao'       => $val->so['ao']['nomor_ao'],
                'nomor_ca'       => $val->nomor_ca,
                'nomor_caa'      => $val->so['caa']['nomor_caa'],

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
                'status_ca'     => $status_ca,
                'status_caa'    => $status_caa,
                'tgl_transaksi' => $val->created_at,
                'approval'      => $Appro
            ];
        }

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'count'  => sizeof($data),
                'data'   => $data
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
