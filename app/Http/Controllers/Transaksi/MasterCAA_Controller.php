<?php

namespace App\Http\Controllers\Transaksi;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use App\Http\Requests\Transaksi\BlankRequest;
use App\Models\Pengajuan\CAA\Penyimpangan;
use App\Models\Pengajuan\AO\AgunanKendaraan;
use App\Models\Pengajuan\AO\AgunanTanah;
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

        $id_area   = $pic->id_area;
        $id_cabang = $pic->id_cabang;
        $scope     = $pic->jpic['cakupan'];

        $query_dir = TransCA::with('so', 'pic', 'cabang')->where('status_ca', 1)->orderBy('created_at', 'desc');
        $method = 'get';

        $query = Helper::checkDir($user_id, $scope, $query_dir, $id_area, $id_cabang, $method);

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
                    'id'    => $id_agu_ta[$key] == null ? null : (int) $id_agu_ta[$key],
                    'jenis' => $value->jenis_sertifikat
                );
            }

            $id_agu_ke = explode (",",$val->so['ao']['id_agunan_kendaraan']);
            $AguKe = AgunanKendaraan::whereIn('id', $id_agu_ke)->get();

            $Ken = array();
            foreach ($AguKe as $key => $value) {
                $Ken[$key] = array(
                    'id'    => $id_agu_ke[$key] == null ? null : (int) $id_agu_ke[$key],
                    'jenis' => $value->jenis
                );
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
                    'plafon' => (int) $val->so['faspin']['plafon'],
                    'tenor'  => (int) $val->so['faspin']['tenor']
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
                'tgl_transaksi' => Carbon::parse($val->created_at)->format("d-m-Y H:i:s"),
                'approval'      => $Appro
            ];
        }

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'count'  => $query->count(),
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
        $nomor_caa = $PIC->id_cabang.'-'.$JPIC->nama_jenis.'-'.$month.'-'.$year.'-'.$lastNumb;

        $check_ca = TransCA::where('id_trans_so', $id)->first();

        if (!$check_ca) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id '.$id.' belum sampai ke CA'
            ], 404);
        }

        $check_ao = TransAO::where('id_trans_so', $id)->first();

        if (!$check_ao) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id '.$id.' belum sampai ke AO'
            ], 404);
        }

        $check = TransSO::where('id',$id)->first();

        if (!$check) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id '.$id.' belum ada di SO'
            ], 404);
        }

        $check_caa = TransCAA::where('id_trans_so', $id)->first();

        if ($check_caa != null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id '.$id.' sudah ada di CAA'
            ], 404);
        }

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
            $file_tempat_tinggal = $check->debt['lamp_tempat_tinggal'];
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
        if (!empty($req->input('team_caa'))) {
            for ($i = 0; $i < count($req->input('team_caa')); $i++) {
                $arrTeam['team'][$i] = $req->input('team_caa')[$i];
            }

            $team_caa = implode(",", $arrTeam['team']);
        }else{

            $arrTeam['team'] = null;
            $team_caa = null;
        }

        // dd($team_caa);

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
            'sertifikat_diatas_150' => $req->input('sertifikat_diatas_150')
        );

        DB::connection('web')->beginTransaction();

        try {

            $CAA = TransCAA::create($data);

            TransSO::where('id', $id)->update(['id_trans_caa' => $CAA->id]);

            for ($i=0; $i < count($teamS); $i++){
                Approval::create([
                    'id_trans_so'  => $id,
                    'id_trans_caa' => $CAA->id,
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

    public function show($id, Request $req){
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

        $query_dir = TransCA::with('pic', 'cabang')->where('id_trans_so', $id)->where('status_ca', 1);
        $method = 'first';

        $check_ca = Helper::checkDir($user_id, $scope, $query_dir, $id_area, $id_cabang, $method);

        if ($check_ca == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data belum sampai ke CA'
            ], 404);
        }

        $check_ao = TransAO::with('pic', 'cabang')->where('id_trans_so', $id)->where('status_ao', 1)->first();

        if ($check_ao == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data belum sampai ke AO'
            ], 404);
        }

        $check_so = TransSO::with('pic', 'cabang')->where('id', $id)->first();

        if ($check_so == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data belum sampai ada di SO'
            ], 404);
        }

        $id_agu_ta = explode (",",$check_ao->id_agunan_tanah);

        $AguTa = AgunanTanah::whereIn('id', $id_agu_ta)->get();

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


        $id_agu_ke = explode (",",$check_ao->id_agunan_kendaraan);
        $AguKe = AgunanKendaraan::whereIn('id', $id_agu_ke)->get();

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

        $infoCC = InfoACC::whereIn('id', explode(",", $check_ca->id_info_analisa_cc))->get()->toArray();

        if ($check_ca->status_ca == 1) {
            $status_ca = 'recommend';
        }elseif($check_ca->status_ca == 2){
            $status_ca = 'not recommend';
        }else{
            $status_ca = 'waiting';
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
                // 'caa' => [
                //     'nomor' => $check_so->caa['nomor_caa'],
                //     'nama'  => $check_so->caa['pic']['nama']
                // ]
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
                'id'        => $check_ao->id_pendapatan_usaha == null ? null : (int) $check_ao->id_pendapatan_usaha,
                'pemasukan' => array(
                    'tunai' => (int) $check_ao->usaha['pemasukan_tunai'],
                    'kredit'=> (int) $check_ao->usaha['pemasukan_kredit'],
                    'total' => (int) $check_ao->usaha['total_pemasukan']
                ),
                'pengeluaran' => array(
                    'biaya_sewa'           => (int) $check_ao->usaha['biaya_sewa'],
                    'biaya_gaji_pegawai'   => (int) $check_ao->usaha['biaya_gaji_pegawai'],
                    'biaya_belanja_brg'    => (int) $check_ao->usaha['biaya_belanja_brg'],
                    'biaya_telp_listr_air' => (int) $check_ao->usaha['biaya_telp_listr_air'],
                    'biaya_sampah_kemanan' => (int) $check_ao->usaha['biaya_sampah_kemanan'],
                    'biaya_kirim_barang'   => (int) $check_ao->usaha['biaya_kirim_barang'],
                    'biaya_hutang_dagang'  => (int) $check_ao->usaha['biaya_hutang_dagang'],
                    'angsuran'             => (int) $check_ao->usaha['biaya_angsuran'],
                    'lain_lain'            => (int) $check_ao->usaha['biaya_lain_lain'],
                    'total'                => (int) $check_ao->usaha['total_pengeluaran']
                ),
                'penghasilan_bersih' => (int) $check_ao->usaha['laba_usaha']
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
                'collectabilitas_terendah' => min(array_column($infoCC, 'collectabilitas')),
                'table'                    => $infoCC
            ],
            'status_ca'  => $status_ca
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

        $id_area   = $pic->id_area;
        $id_cabang = $pic->id_cabang;
        $scope     = $pic->jpic['cakupan'];

        $query_dir = TransCAA::with('so', 'pic', 'cabang')->where('id_trans_so', $id);
        $method = 'first';

        $val = Helper::checkDir($user_id, $scope, $query_dir, $id_area, $id_cabang, $method);

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


        $id_agu_ke = explode (",",$val->so['ao']['id_agunan_kendaraan']);

        $AguKe = AgunanKendaraan::whereIn('id', $id_agu_ke)->get();

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

        $pic_team_caa = explode(",", $val->pic_team_caa);


        $get_pic = PIC::with('jpic')->whereIn('id', explode(",", $val->pic_team_caa))->get();

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


        if ($val->status_caa == 1) {
            $status_caa = 'recommend';
        }elseif($val->status_caa == 2){
            $status_caa = 'not recommend';
        }else{
            $status_caa = 'waiting';
        }

        $data = array(
            'id_trans_so' => $val->id_trans_so == null ? null : (int) $val->id_trans_so,
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
                'id'   => $val->id_pic == null ? null : (int) $val->id_pic,
                'nama' => $val->pic['nama'],
            ],
            'area'   => [
                'id'   => $val->id_area == null ? null : (int) $val->id_area,
                'nama' => $val->area['nama']
            ],
            'cabang' => [
                'id'   => $val->id_cabang == null ? null : (int) $val->id_cabang,
                'nama' => $val->cabang['nama'],
            ],
            'asaldata' => [
                'id'   => $val->so['asaldata']['id'] == null ? null : (int) $val->so['asaldata']['id'],
                'nama' => $val->so['asaldata']['nama'],
            ],
            'data_debitur' => [
                'id'           => $val->so['id_calon_debitur'] == null ? null : (int) $val->so['id_calon_debitur'],
                'nama_lengkap' => $val->so['debt']['nama_lengkap'],
                'alamat_domisili' => [
                    'alamat_singkat' => $val->so['debt']['alamat_domisili'],
                    'rt'             => $val->so['debt']['rt_domisili'] == null ? null : (int) $val->so['debt']['rt_domisili'],
                    'rw'             => $val->so['debt']['rw_domisili'] == null ? null : (int) $val->so['debt']['rw_domisili'],
                    'kelurahan' => [
                        'id'    => $val->so['debt']['id_kel_tempat_kerja'] == null ? null : (int) $val->so['debt']['id_kel_tempat_kerja'],
                        'nama'  => $val->so['debt']['kel_dom']['nama']
                    ],
                    'kecamatan' => [
                        'id'    => $val->so['debt']['id_kec_domisili'] == null ? null : (int) $val->so['debt']['id_kec_domisili'],
                        'nama'  => $val->so['debt']['kec_dom']['nama']
                    ],
                    'kabupaten' => [
                        'id'    => $val->so['debt']['id_kab_domisili'] == null ? null : (int) $val->so['debt']['id_kab_domisili'],
                        'nama'  => $val->so['debt']['kab_dom']['nama'],
                    ],
                    'provinsi'  => [
                        'id'   => $val->so['debt']['id_prov_domisili'] == null ? null : (int) $val->so['debt']['id_prov_domisili'],
                        'nama' => $val->so['debt']['prov_dom']['nama'],
                    ],
                    'kode_pos' => $val->so['debt']['kel_dom']['kode_pos'] == null ? null : (int) $val->so['debt']['kel_dom']['kode_pos']
                ],
                'lamp_usaha'   => $val->so['debt']['lamp_foto_usaha']
            ],
            'data_agunan' => [
                'agunan_tanah'     => $idTan,
                'agunan_kendaraan' => $idKen
            ],

            'pendapatan_usaha' => [
                'id' => $val->so['ao']['id_pendapatan_usaha'] == null ? null : (int) $val->so['ao']['id_pendapatan_usaha'],
                'pemasukan' => array(
                    'tunai' => (int) $val->so['ao']['usaha']['pemasukan_tunai'],
                    'kredit'=> (int) $val->so['ao']['usaha']['pemasukan_kredit'],
                    'total' => (int) $val->so['ao']['usaha']['total_pemasukan']
                ),
                'pengeluaran' => array(
                    'biaya_sewa'           => (int) $val->so['ao']['usaha']['biaya_sewa'],
                    'biaya_gaji_pegawai'   => (int) $val->so['ao']['usaha']['biaya_gaji_pegawai'],
                    'biaya_belanja_brg'    => (int) $val->so['ao']['usaha']['biaya_belanja_brg'],
                    'biaya_telp_listr_air' => (int) $val->so['ao']['usaha']['biaya_telp_listr_air'],
                    'biaya_sampah_kemanan' => (int) $val->so['ao']['usaha']['biaya_sampah_kemanan'],
                    'biaya_kirim_barang'   => (int) $val->so['ao']['usaha']['biaya_kirim_barang'],
                    'biaya_hutang_dagang'  => (int) $val->so['ao']['usaha']['biaya_hutang_dagang'],
                    'angsuran'             => (int) $val->so['ao']['usaha']['biaya_angsuran'],
                    'lain_lain'            => (int) $val->so['ao']['usaha']['biaya_lain_lain'],
                    'total'                => (int) $val->so['ao']['usaha']['total_pengeluaran']
                ),
                'penghasilan_bersih' => (int) $val->so['ao']['usaha']['laba_usaha']
            ],

            'penyimpangan' => $val->penyimpangan,
            'team_caa'  => $ptc,
            'pengajuan' => [
                'plafon' => (int) $val->so['faspin']['plafon'],
                'tenor'  => (int) $val->so['faspin']['tenor'],
                'jenis_pinjaman' => $val->so['faspin']['jenis_pinjaman']
            ],
            'rekomendasi_ao'   => [
                'id'               => $val->so['ao']['id_recom_ao'] == null ? null : (int) $val->so['ao']['id_recom_ao'],
                'produk'           => $val->so['ao']['recom_ao']['produk'],
                'plafon'           => (int) $val->so['ao']['recom_ao']['plafon_kredit'],
                'tenor'            => (int) $val->so['ao']['recom_ao']['jangka_waktu'],
                'suku_bunga'       => floatval($val->so['ao']['recom_ao']['suku_bunga']),
                'pembayaran_bunga' => (int) $val->so['ao']['recom_ao']['pembayaran_bunga'],
                'catatan'          => $val->so['ao']['catatan_ao']
            ],
            'rekomendasi_ca' => [
                'id'                   => $val->so['ca']['id_recom_ca'] == null ? null : (int) $val->so['ca']['id_recom_ca'],
                'produk'               => $val->so['ca']['recom_ca']['produk'],
                'plafon'               => (int) $val->so['ca']['recom_ca']['plafon_kredit'],
                'tenor'                => (int) $val->so['ca']['recom_ca']['jangka_waktu'],
                'suku_bunga'           => floatval($val->so['ca']['recom_ca']['suku_bunga']),
                'pembayaran_bunga'     => (int) $val->so['ca']['recom_ca']['pembayaran_bunga'],
                'rekomendasi_angsuran' => (int) $val->so['ca']['recom_ca']['rekom_angsuran'],
                'catatan'              => $val->so['ca']['catatan_ca']
            ],
            'data_biaya' => [
                'reguler' => $reguler = array(
                    'biaya_provisi'         => (int) $val->so['ca']['recom_ca']['biaya_provisi'],
                    'biaya_administrasi'    => (int) $val->so['ca']['recom_ca']['biaya_administrasi'],
                    'biaya_credit_checking' => (int) $val->so['ca']['recom_ca']['biaya_credit_checking'],
                    'biaya_premi' => [
                        'asuransi_jiwa'     => (int) $val->so['ca']['recom_ca']['biaya_asuransi_jiwa'],
                        'asuransi_jaminan'  => (int) $val->so['ca']['recom_ca']['biaya_asuransi_jaminan']
                    ],
                    'biaya_tabungan'                    => (int) $val->so['ca']['recom_ca']['biaya_tabungan'],
                    'biaya_notaris'                     => (int) $val->so['ca']['recom_ca']['notaris'],
                    'angsuran_pertama_bungan_berjalan'  => (int) $val->so['ca']['recom_ca']['angs_pertama_bunga_berjalan'],
                    'pelunasan_nasabah_ro'              => (int) $val->so['ca']['recom_ca']['pelunasan_nasabah_ro']
                ),

                'hold_dana' => $hold_dana = array(
                    'pelunasan_tempat_lain'         => (int) $val->so['ca']['recom_ca']['pelunasan_tempat_lain'],
                    'blokir' => [
                        'tempat_lain'               => (int) $val->so['ca']['recom_ca']['blokir_dana'],
                        'dua_kali_angsuran_kredit'  => (int) $val->so['ca']['recom_ca']['blokir_angs_kredit']
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
                'file_agunan'         => empty($val->file_agunan) ? null : explode(";", $val->file_agunan),
                'file_usaha'          => empty($val->file_usaha) ? null : explode(";", $val->file_usaha),
                'file_tempat_tinggal' => $val->file_tempat_tinggal,
                'file_lain'           => empty($val->file_lain) ? null : explode(";", $val->file_lain)
            ],
            'rincian'    => $val->rincian,
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

        $id_area   = $pic->id_area;
        $id_cabang = $pic->id_cabang;
        $scope     = $pic->jpic['cakupan'];

        $query_dir = TransCA::with('pic', 'cabang')
                ->where('status_ca', 1)
                ->where('nomor_ca', 'like', '%'.$search.'%')->orderBy('created_at', 'desc');

        $method = 'get';

        $query = Helper::checkDir($user_id, $scope, $query_dir, $id_area, $id_cabang, $method);


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
                    'plafon' => (int) $val->so['faspin']['plafon'],
                    'tenor'  => (int) $val->so['faspin']['tenor']
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
                    ->orderBy('created_at', 'desc')
                    ->whereYear('created_at', '=', $year);
        }else{

            $query_dir = TransCA::with('so', 'pic', 'cabang')->where('status_ca', 1)
                    ->orderBy('created_at', 'desc')
                    ->whereYear('created_at', '=', $year)
                    ->whereMonth('created_at', '=', $month);
        }

        $method = 'get';

        $query = Helper::checkDir($user_id, $scope, $query_dir, $id_area, $id_cabang, $method);

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
                    'id'    => $id_agu_ta[$key] == null ? null : (int) $id_agu_ta[$key],
                    'jenis' => $value->jenis_sertifikat
                );
            }

            $id_agu_ke = explode (",",$val->so['ao']['id_agunan_kendaraan']);
            $AguKe = AgunanKendaraan::whereIn('id', $id_agu_ke)->get();

            $Ken = array();
            foreach ($AguKe as $key => $value) {
                $Ken[$key] = array(
                    'id'    => $id_agu_ke[$key] == null ? null : (int) $id_agu_ke[$key],
                    'jenis' => $value->jenis
                );
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
                    'plafon' => (int) $val->so['faspin']['plafon'],
                    'tenor'  => (int) $val->so['faspin']['tenor']
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
                'tgl_transaksi' => Carbon::parse($val->created_at)->format("d-m-Y H:i:s"),
                'approval'      => $Appro
            ];
        }

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'count'  => $query->count(),
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
