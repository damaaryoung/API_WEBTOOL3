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
use App\Models\Pengajuan\CA\MutasiBank;
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
use App\Models\Transaksi\LogRekomCA;
use App\Models\Pengajuan\CA\RingkasanAnalisa;
use App\Models\Pengajuan\CA\RekomendasiPinjaman;
use Image;
use App\Models\Wilayah\Kelurahan;
use App\Models\Wilayah\Kecamatan;
use App\Models\Wilayah\Kabupaten;
use App\Models\Wilayah\Provinsi;
use Illuminate\Support\Facades\DB;

class MasterCAA_Controller extends BaseController
{
     public function index(Request $req)
    {
set_time_limit(0);
        $pic = $req->pic; // From PIC middleware

        $arr = array();
        $i=0;
        foreach ($pic as $val) {
            $arr[] = $val['id_area'];
          $i++;
        }   

        $arrr = array();
        foreach ($pic as $val) {
            $arrr[] = $val['id_cabang'];
          $i++;
        }   
        $arrrr = array();
        foreach ($pic as $val) {
            $arrrr[] = $val['jpic']['cakupan'];
          $i++;
        }  
          //  dd($arr);
        $id_area   = $arr;
        $id_cabang = $arrr;
       // dd($id_cabang);
        $scope     = $arrrr;

        $query_dir = TransCA::with('so', 'pic', 'cabang')->where('status_ca', 1)->orderBy('created_at', 'desc');

        $query = Helper::checkDir($scope, $query_dir, $id_area, $id_cabang)->take(50);

        if (empty($query)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan di CA masih kosong'
            ], 404);
        }

        $data = array();
        foreach ($query as $key => $val) {

            if ($val->status_ca == 1) {
                $status_ca = 'recommend';
            } elseif ($val->status_ca == 2) {
                $status_ca = 'not recommend';
            } else {
                $status_ca = 'waiting';
            }

            if ($val->so['caa']['status_caa'] == 0) {
                $status_caa = 'waiting';
            } elseif ($val->so['caa']['status_caa'] == 1) {
                $status_caa = 'recommend';
            } elseif ($val->so['caa']['status_caa'] == 2) {
                $status_caa = 'not recommend';
            } elseif ($val->so['caa']['status_caa'] == null || $val->so['caa']['status_caa'] == "") {
                $status_caa = 'null';
            }


            $id_agu_ta = explode(",", $val->so['ao']['id_agunan_tanah']);
            $AguTa = AgunanTanah::whereIn('id', $id_agu_ta)->get();

            $Tan = array();
            foreach ($AguTa as $key => $value) {
                $Tan[$key] = array(
                    'id'    => $id_agu_ta[$key] == null ? null : (int) $id_agu_ta[$key],
                    'jenis' => $value->jenis_sertifikat
                );
            }

            $id_agu_ke = explode(",", $val->so['ao']['id_agunan_kendaraan']);
            $AguKe = AgunanKendaraan::whereIn('id', $id_agu_ke)->get();

            if ($AguKe == '[]') {
                $Ken = null;
            } else {
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
                ->select("id_pic", "id", "plafon", "tenor", "rincian", "status", "updated_at as tgl_approve")
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

            $tr = TransCA::where('id',$val->id)->first();
            $rekom_pinj = RekomendasiPinjaman::where('id',$tr->id_rekomendasi_pinjaman)->first();
         //   dd($rekom_pinj);
            //dd($val->id_recom_ca);
            // dd($val->so['ca']['id_recom_ca']);
                // $rekomendasi_ca = array(
                //     'id'                   => $val->so['ca']['id_recom_ca'] == null ? null : (int) $val->so['ca']['id_recom_ca'],
                //     'produk'               => $val->so['ca']['recom_ca']['produk'],
                //      'plafon'               => $rekom_pinj->recom_nilai_pinjaman,
                //     // 'plafon'               => (int) $val->recom_ca['plafon_kredit'],
                //     'tenor'                => (int) $val->recom_ca['jangka_waktu'],
                //     'suku_bunga'           => floatval($val->recom_ca['suku_bunga']),
                //     'pembayaran_bunga'     => (int) $val->recom_ca['pembayaran_bunga'],
                //     'rekomendasi_angsuran' => (int) $val->recom_ca['rekom_angsuran']
                // );

            $rekomendasi_ca = array(
                'id'                   => $val->so['ca']['id_recom_ca'] == null ? null : (int) $val->so['ca']['id_recom_ca'],
                'produk'               => $rekom_pinj->recom_produk_kredit,
                 'plafon'               => $rekom_pinj->recom_nilai_pinjaman,
                // 'plafon'               => (int) $val->recom_ca['plafon_kredit'],
                'tenor'                => $rekom_pinj->recom_tenor,
                'suku_bunga'           => $rekom_pinj->bunga_pinjaman,
             //   'pembayaran_bunga'     => $rekom_pinj->recom,
                'rekomendasi_angsuran' => $rekom_pinj->recom_angsuran,
            );


            $data[] = [
                'status_revisi'  => $val->revisi >= 1 ? 'Y' : 'N',
                'id_trans_so'    => $val->id_trans_so == null ? null : (int) $val->id_trans_so,
                'nomor_so'       => $val->so['nomor_so'],
                'nama_so'        => $val->so['nama_so'],
                'status_ca'      => $status_ca,
                'status_caa'     => $status_caa,
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
                'rekomendasi_pinjaman' => $val->recom_pin,
                'nama_debitur'   => $val->so['debt']['nama_lengkap'],
                'agunan' => [
                    'tanah'     => $Tan,
                    'kendaraan' => $Ken
                ],
                'tgl_transaksi' => $val->created_at,
                'approval'      => $Appro,
            ];
        }

        $log_rekomca = LogRekomCA::select('id', 'produk', 'plafon_kredit', 'jangka_waktu', 'suku_bunga', 'pembayaran_bunga', 'rekom_angsuran')->get();
        foreach ($log_rekomca as $key => $log) {
            // dd($log_rekomca);
            //     //   dd($val->so['ca']['id_recom_ca']);
            $rev_rekomendasi_ca = array(
                'id'                   =>
                $log->id,
                'produk'               =>  $log->produk,
                'plafon'               =>  $log->plafon_kredit,
                'tenor'                => $log->jangka_waktu,
                'suku_bunga'           => $log->suku_bunga,
                'pembayaran_bunga'     => $log->pembayaran_bunga,
                'rekomendasi_angsuran' => $log->rekom_angsuran,
            );
        }

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'count'  => sizeof($data),
                'data'   => $data,
                'revisi_ca' => $log_rekomca,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }
    public function update($id, Request $request, BlankRequest $req)
    {
        $pic     = $request->pic; // From PIC middleware
        $user_id = $request->auth->user_id;

        $mj = array();
        $i=0;
        foreach ($pic as $val) {
            $mj[] = $val['id_mj_pic'];
          $i++;
        }   
        $id_pic = array();
        $i=0;
        foreach ($pic as $val) {
            $id_pic[] = $val['id'];
          $i++;
        }   
 $arrr = array();
        foreach ($pic as $val) {
            $arrr[] = $val['id_cabang'];
          $i++;
        }  
        $area = array();
        $i=0;
        foreach ($pic as $val) {
            $area[] = $val['id_area'];
          $i++;
        }    
        $nama = array();
        $i=0;
        foreach ($pic as $val) {
            $nama[] = $val['nama'];
          $i++;
        }       

        $countCAA = TransCAA::latest('id', 'nomor_caa')->first();

        if (!$countCAA) {
            $lastNumb = 1;
        } else {
            $no = $countCAA->nomor_caa;

            $arr = explode("-", $no, 5);

            $lastNumb = $arr[4] + 1;
        }

        //Data Transaksi SO
        $nows  = Carbon::now();
        $year  = $nows->year;
        $month = $nows->month;

        $JPIC   = JPIC::whereIn('id', $mj)->first();

        //  ID-Cabang - AO / CA / SO - Bulan - Tahun - NO. Urut
        $nomor_caa = $arrr[0] . '-' . $JPIC->nama_jenis . '-' . $month . '-' . $year . '-' . $lastNumb;

        $check = TransSO::where('id', $id)->where('status_das', 1)->where('status_hm', 1)->first();

        if (!$check) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id ' . $id . ' belum ada di SO atau belum komplit saat pemeriksaaan DAS dan HM'
            ], 404);
        }


        $check_ao = TransAO::where('id_trans_so', $id)->where('status_ao', 1)->first();

        if (!$check_ao) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id ' . $id . ' belum sampai ke AO'
            ], 404);
        }

        $check_ca = TransCA::where('id_trans_so', $id)->where('status_ca', 1)->first();

        if (!$check_ca) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id ' . $id . ' belum sampai ke CA'
            ], 404);
        }

        $check_caa = TransCAA::where('id_trans_so', $id)->where('status_caa', 1)->first();

        if ($check_caa != null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id ' . $id . ' sudah ada di CAA'
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

        $lamp_dir = 'public/' . $check->debt['no_ktp'];

        // file_report_mao
        if ($file = $req->file('file_report_mao')) {

            $path = $lamp_dir . '/mcaa/file_report_mao';

            $name = ''; //$file->getClientOriginalName();

            $check_file = $check_file_report_mao;

            $file_report_mao = Helper::uploadImg($check_file, $file, $path, $name);
        } else {
            $file_report_mao = $check_file_report_mao;
        }

        // file_report_mca
        if ($file = $req->file('file_report_mca')) {

            $path = $lamp_dir . '/mcaa/file_report_mca';

            $name = '';

            $check_file = $check_file_report_mca;

            $file_report_mao = Helper::uploadImg($check_file, $file, $path, $name);
        } else {
            $file_report_mca = $check_file_report_mca;
        }

        // Agunan Files Condition
        $statusFileAgunan = $req->input('status_file_agunan');

        if ($statusFileAgunan == 'ORIGINAL') {
            for ($i = 0; $i < count($req->file_agunan); $i++) {
                $listAgunan[] = $req->file_agunan;
            }

            $file_agunan = implode(";", $listAgunan);
        } elseif ($statusFileAgunan == 'CUSTOM') {

            if ($files = $req->file('file_agunan')) {

                $check_file = $check_file_agunan;
                $path = $lamp_dir . '/mcaa/file_agunan';
                $i = 0;

                $name = '';

                $arrayPath = array();
                foreach ($files as $file) {
                    if (
                        $file->getClientOriginalExtension() != 'pdf'  &&
                        $file->getClientOriginalExtension() != 'jpg'  &&
                        $file->getClientOriginalExtension() != 'jpeg' &&
                        $file->getClientOriginalExtension() != 'png'  &&
                        $file->getClientOriginalExtension() != 'gif'
                    ) {
                        return response()->json([
                            "code"    => 422,
                            "status"  => "not valid request",
                            "message" => ["file_usaha." . $i => ["file_usaha." . $i . " harus bertipe jpg, jpeg, png, pdf"]]
                        ], 422);
                    }

                    $arrayPath[] = Helper::uploadImg($check_file, $file, $path, $name);
                }

                $file_agunan = implode(";", $arrayPath);
            } else {
                $file_agunan = $check_file_agunan;
            }
        } else {
            $file_agunan = null;
        }

        // Usaha Files Condition
        $statusFileUsaha = $req->input('status_file_usaha');
        if ($statusFileUsaha == 'ORIGINAL') {
            for ($i = 0; $i < count($req->input('file_usaha')); $i++) {
                $listUsaha[] = $req->input('file_usaha');
            }

            $file_usaha = implode(";", $listUsaha);
        } elseif ($statusFileUsaha == 'CUSTOM') {

            if ($files = $req->file('file_usaha')) {
                $i = 0;
                $path = $lamp_dir . '/mcaa/file_usaha';
                $name = '';

                $check_file = $check_file_usaha;

                $arrayPath = array();
                foreach ($files as $file) {
                    if (
                        $file->getClientOriginalExtension() != 'pdf'  &&
                        $file->getClientOriginalExtension() != 'jpg'  &&
                        $file->getClientOriginalExtension() != 'jpeg' &&
                        $file->getClientOriginalExtension() != 'png'  &&
                        $file->getClientOriginalExtension() != 'gif'
                    ) {
                        return response()->json([
                            "code"    => 422,
                            "status"  => "not valid request",
                            "message" => ["file_usaha." . $i => ["file_usaha." . $i . " harus bertipe jpg, jpeg, png, pdf"]]
                        ], 422);
                    }

                    $arrayPath[] = Helper::uploadImg($check_file, $file, $path, $name);
                }

                $file_usaha = implode(";", $arrayPath);
            } else {
                $file_usaha = $check_file_usaha;
            }
        } else {
            $file_usaha = null;
        }

        // Home File
        if ($file = $req->file('file_tempat_tinggal')) {

            $path = $lamp_dir . '/mcaa/file_tempat_tinggal';

            $name = '';

            $check_file = $check_file_tempat_tinggal;

            $file_report_mao = Helper::uploadImg($check_file, $file, $path, $name);
        } else {
            $file_tempat_tinggal = $check_file_tempat_tinggal;
        }

        // Othe File
        if ($file = $req->file('file_lain')) {

            $path = $lamp_dir . '/mcaa/file_lain';

            $name = '';

            $check_file = $check_file_lain;

            $file_report_mao = Helper::uploadImg($check_file, $file, $path, $name);
        } else {
            $file_lain = $check_file_lain;
        }

        // Email Team CAA
        if (!empty($req->input('team_caa'))) {
            for ($i = 0; $i < count($req->input('team_caa')); $i++) {
                $arrTeam['team'][$i] = $req->input('team_caa')[$i];
            }

            $team_caa = implode(",", $arrTeam['team']);
        } else {
            $team_caa = null;
        }
 $cab = TransSO::where('id', $id)->first();
        $data = array(
            'nomor_caa'          => $nomor_caa,
            'user_id'            => $user_id,
            'id_trans_so'        => $id,
            'id_pic'             => $id_pic[0],
            'id_area'            => $cab->id_area,
            'id_cabang'          => $cab->id_cabang,
            'penyimpangan'       => $req->input('penyimpangan'),
            'pic_team_caa'       => $team_caa,
            'rincian'            => $req->input('rincian'),
            'file_report_mao'    => $file_report_mao,
            'file_report_mca'    => $file_report_mca,
            'status_file_agunan' => $req->input('status_file_agunan'),
            'file_agunan'        => $file_agunan,
            'status_file_usaha'  => $req->input('status_file_usaha'),
            'file_usaha'         => $file_usaha,
            'file_tempat_tinggal' => $file_tempat_tinggal,
            'file_lain'          => $file_lain,
            'status_caa'         => 1
        );

        $teamS = explode(",", $data['pic_team_caa']);

        $penyimpangan = array(
            'id_trans_so'           => $id,
            'biaya_provisi'         => $req->input('biaya_provisi'),
            'biaya_admin'           => $req->input('biaya_admin'),
            'biaya_kredit'          => $req->input('biaya_kredit'),
            'past_due_ro'           => $req->input('past_due_ro'),
            'struktur_kredit'       => $req->input('struktur_kredit'),
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

            $pic_app = PIC::whereIn('id', explode(",", $team_caa))->get()->toArray();

            if ($pic_app == []) {
                return response()->json([
                    'code'    => 404,
                    'status'  => 'not found',
                    'message' => 'Team CAA tidak terdaftar'
                ], 404);
            }

            $approval = array();
            for ($i = 0; $i < count($teamS); $i++) {

                $approval[] = Approval::create([
                    'id_trans_so'  => $id,
                    'id_trans_caa' => $CAA->id,
                    'user_id'      => $pic_app[$i]['user_id'],
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
                'message' => 'Data untuk CAA berhasil dikirim',
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

    public function show($id, Request $req)
    {
        $pic = $req->pic; // From PIC middleware

        $area = array();
        $i=0;
        foreach ($pic as $val) {
            $area[] = $val['id_area'];
          $i++;
        }   

        $arrr = array();
        foreach ($pic as $val) {
            $arrr[] = $val['id_cabang'];
          $i++;
        }   
        $arrrr = array();
        foreach ($pic as $val) {
            $arrrr[] = $val['jpic']['cakupan'];
          $i++;
        }  
          //  dd($arr);
        $id_area   = $area;
        $id_cabang = $arrr;
       // dd($id_cabang);
        $scope     = $arrrr;
//dd($id_area,$id_cabang,$scope);
        $check_so = TransSO::with('pic', 'cabang')->where('id', $id)->where('status_das', 1)->where('status_hm', 1)->first();

        if (empty($check_so)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id ' . $id . ' belum ada di SO atatu belum komplit saat pemeriksaaan DAS dan HM'
            ], 404);
        }

        $check_ao = TransAO::with('pic', 'cabang')->where('id_trans_so', $id)->where('status_ao', 1)->first();
        //  dd($check_ao->recom_ao);
        $check_catatan = TransCA::select('catatan_ca')->where('id_trans_so', $id)->where('status_ca', 1)->first();
        //  dd($check_ca->catatan_ca);
        if (empty($check_ao)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id ' . $id . ' belum sampai ke AO'
            ], 404);
        }

        $query_dir = TransCA::with('pic', 'cabang')->where('id_trans_so', $id)
            ->where('status_ca', 1);



        $ca = Helper::checkDir($scope, $query_dir, $id_area, $id_cabang);
        $check_ca = $ca->first();
        //   dd($check_ca->recom_ca);
        if (empty($check_ca)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id ' . $id . ' belum sampai ke CA'
            ], 404);
        }


        $id_agu_ta = explode(",", $check_ao->id_agunan_tanah);

        $AguTa = AgunanTanah::whereIn('id', $id_agu_ta)->get();

        if (empty($AguTa)) {
            $idTan = null;
        } else {
            $idTan = array();
            foreach ($AguTa as $key => $value) {
                $idTan[$key] = array(
                    'id'             => $value->id == null ? null : (int) $value->id,
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
                        'agunan_bag_depan'      => $value->agunan_bag_depan,
                        'agunan_bag_jalan'      => $value->agunan_bag_jalan,
                        'agunan_bag_ruangtamu'  => $value->agunan_bag_ruangtamu,
                        'agunan_bag_kamarmandi' => $value->agunan_bag_kamarmandi,
                        'agunan_bag_dapur'      => $value->agunan_bag_dapur
                    ]
                );
            }
        }

        $id_agu_ke = explode(",", $check_ao->id_agunan_kendaraan);
        $AguKe = AgunanKendaraan::whereIn('id', $id_agu_ke)->get();

        if (empty($AguKe)) {
            $idKen = null;
        } else {
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
        } elseif ($check_ca->status_ca == 2) {
            $status_ca = 'not recommend';
        } else {
            $status_ca = 'waiting';
        }
        $mutasi = MutasiBank::whereIn('id', explode(",", $check_ca->id_mutasi_bank))->get()->toArray();

        if ($mutasi != []) {
            foreach ($mutasi as $i => $mut) {
                $doub[$i] = array_slice($mut, 0, 5);
            }

            // $arr = array();
            foreach ($mutasi as $i => $mut) {
                $slice[$i] = array_slice($mut, 5);
                foreach ($slice as $key => $val) {
                    foreach ($val as $row => $col) {
                        $arr[$i][$row] = explode(";", $col);
                    }
                }
            }

            // $dataMut = array();
            foreach ($arr as $key => $subarr) {
                foreach ($subarr as $subkey => $subvalue) {
                    foreach ($subvalue as $childkey => $childvalue) {
                        $out[$key][$childkey][$subkey] = ($childvalue);
                    }

                    $dataMutasi[$key] = array_merge($doub[$key], array('table' => $out[$key]));
                }
            }
        } else {
            $dataMutasi = null;
        }
        //  dd($check_ca);
        $jaminan_keb = DB::connection('web')->table('asuransi_jaminan_kebakaran')->where('id', $check_ca->id_asuransi_jaminan_kebakaran)->first();

        $jaminan_ken = DB::connection('web')->table('asuransi_jaminan_kendaraan')->where('id', $check_ca->id_asuransi_jaminan_kendaraan)->first();

        if ($jaminan_keb == null) {
            $asuransi_jaminan_keb = null;
        } else {
            $jamkeb = array(
                'id'                    => $jaminan_keb->id,
                'nama_asuransi'         => $jaminan_keb->nama_asuransi,
                'jangka_waktu'          => $jaminan_keb->jangka_waktu,
                'nilai_pertanggungan'   => $jaminan_keb->nilai_pertanggungan,
                'jatuh_tempo'           => $jaminan_keb->jatuh_tempo
            );
        }

        if ($jaminan_ken == null) {
            $asuransi_jaminan_ken = null;
        } else {
            $jamken = array(
                'id'                    => $jaminan_ken->id,
                'nama_asuransi'         => $jaminan_ken->nama_asuransi,
                'jangka_waktu'          => $jaminan_ken->jangka_waktu,
                'nilai_pertanggungan'   => $jaminan_ken->nilai_pertanggungan,
                'jatuh_tempo'           => $jaminan_ken->jatuh_tempo
            );
        }

        $penj = Penjamin::whereIn('id', explode(",", $check_so->id_penjamin))->get();

        if (!$penj) {
            $penjamin = null;
        } else {
            $penjamin = array();
            foreach ($penj as $pen) {
                $penjamin[] = [
                    'id'                => $pen->id,
                    'nama_ktp'          => $pen->nama_ktp,
                    'nama_ibu_kandung'  => $pen->nama_ibu_kandung,
                    'no_ktp'            => $pen->no_ktp,
                    'no_npwp'           => $pen->no_npwp,
                    'tempat_lahir'      => $pen->tempat_lahir,
                    'tgl_lahir'         => $pen->tgl_lahir,
                    'jenis_kelamin'     => $pen->jenis_kelamin,
                    'alamat_ktp'        => $pen->alamat_ktp,
                    'no_telp'           => $pen->no_telp,
                    'hubungan_debitur'  => $pen->hubungan_debitur,

                    "pekerjaan" => [
                        "nama_pekerjaan"        => $pen->pekerjaan,
                        "posisi_pekerjaan"      => $pen->posisi_pekerjaan,
                        "nama_tempat_kerja"     => $pen->nama_tempat_kerja,
                        "jenis_pekerjaan"       => $pen->jenis_pekerjaan,
                        "tgl_mulai_kerja"       => $pen->tgl_mulai_kerja,
                        "no_telp_tempat_kerja"  => $pen->no_telp_tempat_kerja,
                        'alamat' => [
                            'alamat_singkat' => $pen->alamat_tempat_kerja,
                            'rt'             => $pen->rt_tempat_kerja,
                            'rw'             => $pen->rw_tempat_kerja,
                            'kelurahan' => [
                                'id'    => $pen->id_kel_tempat_kerja,
                                'nama'  => $pen->kel_kerja['nama']
                            ],
                            'kecamatan' => [
                                'id'    => $pen->id_kec_tempat_kerja,
                                'nama'  => $pen->kec_kerja['nama']
                            ],
                            'kabupaten' => [
                                'id'    => $pen->id_kab_tempat_kerja,
                                'nama'  => $pen->kab_kerja['nama'],
                            ],
                            'provinsi'  => [
                                'id'    => $pen->id_prov_tempat_kerja,
                                'nama'  => $pen->prov_kerja['nama'],
                            ],
                            'kode_pos'  => $pen->kel_kerja['kode_pos'] == null ? null : (int) $pen->kel_kerja['kode_pos']
                        ]
                    ],

                    'lampiran' => [
                        'lamp_ktp' => $pen->lamp_ktp,
                        'lamp_ktp_pasangan' => $pen->lamp_ktp_pasangan,
                        'lamp_kk' => $pen->lamp_kk,
                        'lamp_buku_nikah' => $pen->lamp_buku_nikah
                    ]
                ];
            }
        }
        $cek_agu_tanah = DB::connection('web')->table('periksa_agunan_tanah')->where('id_agunan_tanah', $check_ao->id_agunan_tanah)->get();
        $cek_agu_ken = DB::connection('web')->table('periksa_agunan_kendaraan')->where('id_agunan_kendaraan', $check_ao->id_agunan_kendaraan)->get();

        $data = array(
            'status_revisi' => $check_ca->revisi >= 1 ? 'Y' : 'N',
            'id_trans_so' => $check_so->id == null ? null : (int) $check_so->id,
            'status_ca'   => $status_ca,
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
            'asaldata' => $check_so->asaldata,

            'data_debitur' => [
                'id'                    => $check_so->id_calon_debitur,
                'nama_lengkap'          => $check_so->debt['nama_lengkap'],
                'gelar_keagamaan'       => $check_so->debt['gelar_keagamaan'],
                'gelar_pendidikan'      => $check_so->debt['gelar_pendidikan'],
                'jenis_kelamin'         => $check_so->debt['jenis_kelamin'],
                'status_nikah'          => $check_so->debt['status_nikah'],
                'ibu_kandung'           => $check_so->debt['ibu_kandung'],
                'tinggi_badan'          => $check_so->debt['tinggi_badan'],
                'berat_badan'           => $check_so->debt['berat_badan'],
                'no_ktp'                => $check_so->debt['no_ktp'],
                'no_ktp_kk'             => $check_so->debt['no_ktp_kk'],
                'no_kk'                 => $check_so->debt['no_kk'],
                'no_npwp'               => $check_so->debt['no_npwp'],
                'tempat_lahir'          => $check_so->debt['tempat_lahir'],
                'tgl_lahir'             => $check_so->debt['tgl_lahir'],
                'agama'                 => $check_so->debt['agama'],
                'alamat_ktp' => [
                    'alamat_singkat' => $check_so->debt['alamat_ktp'],
                    'rt'     => $check_so->debt['rt_ktp'] == null ? null : (int) $check_so->debt['rt_ktp'],
                    'rw'     => $check_so->debt['rw_ktp'] == null ? null : (int) $check_so->debt['rw_ktp'],
                    'kelurahan' => [
                        'id'    => $check_so->debt['id_kel_ktp'] == null ? null : (int) $check_so->debt['id_kel_ktp'],
                        'nama'  => $check_so->debt['kel_ktp']['nama']
                    ],
                    'kecamatan' => [
                        'id'    => $check_so->debt['id_kec_ktp'] == null ? null : (int) $check_so->debt['id_kec_ktp'],
                        'nama'  => $check_so->debt['kec_ktp']['nama']
                    ],
                    'kabupaten' => [
                        'id'    => $check_so->debt['id_kab_ktp'] == null ? null : (int) $check_so->debt['id_kab_ktp'],
                        'nama'  => $check_so->debt['kab_ktp']['nama'],
                    ],
                    'provinsi'  => [
                        'id'   => $check_so->debt['id_prov_ktp'] == null ? null : (int) $check_so->debt['id_prov_ktp'],
                        'nama' => $check_so->debt['prov_ktp']['nama'],
                    ],
                    'kode_pos' => $check_so->debt['kel_ktp']['kode_pos'] == null ? null : (int) $check_so->debt['kel_ktp']['kode_pos']
                ],
                'alamat_domisili' => [
                    'alamat_singkat' => $check_so->debt['alamat_domisili'],
                    'rt'             => $check_so->debt['rt_domisili'] == null ? null : (int) $check_so->debt['rt_domisili'],
                    'rw'             => $check_so->debt['rw_domisili'] == null ? null : (int) $check_so->debt['rw_domisili'],
                    'kelurahan' => [
                        'id'    => $check_so->debt['id_kel_domisili'] == null ? null : (int) $check_so->debt['id_kel_domisili'],
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
                "pekerjaan" => [
                    "nama_pekerjaan"        => $check_so->debt['pekerjaan'],
                    "posisi_pekerjaan"      => $check_so->debt['posisi_pekerjaan'],
                    "nama_tempat_kerja"     => $check_so->debt['nama_tempat_kerja'],
                    "jenis_pekerjaan"       => $check_so->debt['jenis_pekerjaan'],
                    "tgl_mulai_kerja"       => $check_so->debt['tgl_mulai_kerja'], //Carbon::parse($val->tgl_mulai_kerja)->format('d-m-Y'),
                    "no_telp_tempat_kerja"  => $check_so->debt['no_telp_tempat_kerja'],
                    'alamat' => [
                        'alamat_singkat' => $check_so->debt['alamat_tempat_kerja'],
                        'rt'             => $check_so->debt['rt_tempat_kerja'] == null ? null : (int) $check_so->debt['rt_tempat_kerja'],
                        'rw'             => $check_so->debt['rw_tempat_kerja'] == null ? null : (int) $check_so->debt['rw_tempat_kerja'],
                        'kelurahan' => [
                            'id'    => $check_so->debt['id_kel_tempat_kerja'] == null ? null : (int) $check_so->debt['id_kel_tempat_kerja'],
                            'nama'  => $check_so->debt['kel_kerja']['nama']
                        ],
                        'kecamatan' => [
                            'id'    => $check_so->debt['id_kec_tempat_kerja'] == null ? null : (int) $check_so->debt['id_kec_tempat_kerja'],
                            'nama'  => $check_so->debt['kec_kerja']['nama']
                        ],
                        'kabupaten' => [
                            'id'    => $check_so->debt['id_kab_tempat_kerja'] == null ? null : (int) $check_so->debt['id_kab_tempat_kerja'],
                            'nama'  => $check_so->debt['kab_kerja']['nama'],
                        ],
                        'provinsi'  => [
                            'id'    => $check_so->debt['id_prov_tempat_kerja'] == null ? null : (int) $check_so->debt['id_prov_tempat_kerja'],
                            'nama'  => $check_so->debt['prov_kerja']['nama'],
                        ],
                        'kode_pos'  => $check_so->debt['kel_kerja']['kode_pos'] == null ? null : (int) $check_so->debt['kel_kerja']['kode_pos']
                    ]
                ],
                'pendidikan_terakhir'   => $check_so->debt['pendidikan_terakhir'],
                'jumlah_tanggungan'     => $check_so->debt['jumlah_tanggungan'],
                'no_telp'               => $check_so->debt['no_telp'],
                'no_hp'                 => $check_so->debt['no_hp'],
                'alamat_surat'          => $check_so->debt['alamat_surat'],
                'lampiran' => [
                    'lamp_ktp'              => $check_so->debt['lamp_ktp'],
                    'lamp_kk'               => $check_so->debt['lamp_kk'],
                    'lamp_buku_tabungan'    => $check_so->debt['lamp_buku_tabungan'],
                    'lamp_sertifikat'       => $check_so->debt['lamp_sertifikat'],
                    'lamp_sttp_pbb'         => $check_so->debt['lamp_sttp_pbb'],
                    'lamp_imb'              => $check_so->debt['lamp_imb'],
                    'foto_agunan_rumah'     => $check_so->debt['foto_agunan_rumah']
                ]
            ],

            'data_pasangan' => [
                'id'                    => $check_so->id_pasangan,
                'nama_lengkap'          => $check_so->pas['nama_lengkap'],
                'nama_ibu_kandung'      => $check_so->pas['nama_ibu_kandung'],
                'gelar_keagamaan'       => $check_so->pas['gelar_keagamaan'],
                'gelar_pendidikan'      => $check_so->pas['gelar_pendidikan'],
                'jenis_kelamin'         => $check_so->pas['jenis_kelamin'],
                'no_ktp'                => $check_so->pas['no_ktp'],
                'no_ktp_kk'             => $check_so->pas['no_ktp_kk'],
                'no_npwp'               => $check_so->pas['no_npwp'],
                'tempat_lahir'          => $check_so->pas['tempat_lahir'],
                'tgl_lahir'             => $check_so->pas['tgl_lahir'],
                'alamat_ktp'            => $check_so->pas['alamat_ktp'],
                'no_telp'               => $check_so->pas['no_telp'],

                "pekerjaan" => [
                    "nama_pekerjaan"        => $check_so->pas['pekerjaan'],
                    "posisi_pekerjaan"      => $check_so->pas['posisi_pekerjaan'],
                    "nama_tempat_kerja"     => $check_so->pas['nama_tempat_kerja'],
                    "jenis_pekerjaan"       => $check_so->pas['jenis_pekerjaan'],
                    "tgl_mulai_kerja"       => $check_so->pas['tgl_mulai_kerja'],
                    "no_telp_tempat_kerja"  => $check_so->pas['no_telp_tempat_kerja'],
                    'alamat' => [
                        'alamat_singkat' => $check_so->pas['alamat_tempat_kerja'],
                        'rt'             => $check_so->pas['rt_tempat_kerja'] == null ? null : (int) $check_so->pas['rt_tempat_kerja'],
                        'rw'             => $check_so->pas['rw_tempat_kerja'] == null ? null : (int) $check_so->pas['rw_tempat_kerja'],
                        'kelurahan' => [
                            'id'    => $check_so->pas['id_kel_tempat_kerja'] == null ? null : (int) $check_so->pas['id_kel_tempat_kerja'],
                            'nama'  => $check_so->pas['kel_kerja']['nama']
                        ],
                        'kecamatan' => [
                            'id'    => $check_so->pas['id_kec_tempat_kerja'] == null ? null : (int) $check_so->pas['id_kec_tempat_kerja'],
                            'nama'  => $check_so->pas['kec_kerja']['nama']
                        ],
                        'kabupaten' => [
                            'id'    => $check_so->pas['id_kab_tempat_kerja'] == null ? null : (int) $check_so->pas['id_kab_tempat_kerja'],
                            'nama'  => $check_so->pas['kab_kerja']['nama'],
                        ],
                        'provinsi'  => [
                            'id'    => $check_so->pas['id_prov_tempat_kerja'] == null ? null : (int) $check_so->pas['id_prov_tempat_kerja'],
                            'nama'  => $check_so->pas['prov_kerja']['nama'],
                        ],
                        'kode_pos'  => $check_so->pas['kel_kerja']['kode_pos'] == null ? null : (int) $check_so->pas['kel_kerja']['kode_pos']
                    ]
                ],
                'lampiran' => [
                    'lamp_ktp'        => $check_so->pas['lamp_ktp'],
                    'lamp_buku_nikah' => $check_so->pas['lamp_buku_nikah']
                ]
            ],

            'data_penjamin' => $penjamin,

            'data_agunan' => [
                'agunan_tanah'     => $idTan,
                'agunan_kendaraan' => $idKen
            ],
            'pemeriksaan' => [
                'agunan_tanah' => empty($cek_agu_tanah) ? $cek_agu_tanah : null,
                'agunan_kendaraan' => empty($cek_agu_ken) ? $cek_agu_ken : null,
            ],
            'verifikasi'    => DB::connection('web')->table('tb_verifikasi')->where('id', $check_ao->id_verifikasi)->first(),
            'validasi'      => DB::connection('web')->table('tb_validasi')->where('id', $check_ao->id_validasi)->first(),
            'pendapatan_usaha' => [
                'id'        => $check_ca->id_pendapatan_usaha == null ? null : (int) $check_ao->id_pendapatan_usaha,
                'pemasukan' => array(
                    'tunai' => $check_ca->usaha['pemasukan_tunai'],
                    'kredit' => $check_ca->usaha['pemasukan_kredit'],
                    'total' => $check_ca->usaha['total_pemasukan']
                ),
                'pengeluaran' => array(
                    'biaya_sewa'           => $check_ca->usaha['biaya_sewa'],
                    'biaya_gaji_pegawai'   => $check_ca->usaha['biaya_gaji_pegawai'],
                    'biaya_belanja_brg'    => $check_ca->usaha['biaya_belanja_brg'],
                    'biaya_telp_listr_air' => $check_ca->usaha['biaya_telp_listr_air'],
                    'biaya_sampah_kemanan' => $check_ca->usaha['biaya_sampah_kemanan'],
                    'biaya_kirim_barang'   => $check_ca->usaha['biaya_kirim_barang'],
                    'biaya_hutang_dagang'  => $check_ca->usaha['biaya_hutang_dagang'],
                    'angsuran'             => $check_ca->usaha['biaya_angsuran'],
                    'lain_lain'            => $check_ca->usaha['biaya_lain_lain'],
                    'total'                => $check_ca->usaha['total_pengeluaran']
                ),
                'penghasilan_bersih' => $check_ca->usaha['laba_usaha']
            ],
            'pengajuan' => $check_so->faspin,
            'rekomendasi_ao' => $check_ao->recom_ao,
            'rekomendasi_ca' => array($check_ca->recom_ca, $check_catatan),
            'rekomendasi_pinjaman' => $check_ca->recom_pin,
            'kapasitas_bulanan' => $check_ca->kapbul,
            // 'data_biaya' => [
            //     'reguler' => $reguler = array(
            //         'biaya_provisi'         => (int) $check_ca->recom_ca['biaya_provisi'],
            //         'biaya_administrasi'    => (int) $check_ca->recom_ca['biaya_administrasi'],
            //         'biaya_credit_checking' => (int) $check_ca->recom_ca['biaya_credit_checking'],
            //         'biaya_premi' => [
            //             'asuransi_jiwa'     => (int) $check_ca->recom_ca['biaya_asuransi_jiwa'],
            //             'asuransi_jaminan'  => (int) $check_ca->recom_ca['biaya_asuransi_jaminan']
            //         ],
            //         'biaya_tabungan'                    => (int) $check_ca->recom_ca['biaya_tabungan'],
            //         'biaya_notaris'                     => (int) $check_ca->recom_ca['notaris'],
            //         'angsuran_pertama_bungan_berjalan'  => (int) $check_ca->recom_ca['angs_pertama_bunga_berjalan'],
            //         'pelunasan_nasabah_ro'              => (int) $check_ca->recom_ca['pelunasan_nasabah_ro']
            //     ),

            //     'hold_dana' => $hold_dana = array(
            //         'pelunasan_tempat_lain'         => (int) $check_ca->recom_ca['pelunasan_tempat_lain'],
            //         'blokir' => [
            //             'tempat_lain'               => (int) $check_ca->recom_ca['blokir_dana'],
            //             'dua_kali_angsuran_kredit'  => (int) $check_ca->recom_ca['blokir_angs_kredit']
            //         ]
            //     ),

            //     'total' => array(
            //         'biaya_reguler'     => $ttl1 = array_sum($reguler + $reguler['biaya_premi']),
            //         'biaya_hold_dana'   => $ttl2 = array_sum($hold_dana + $hold_dana['blokir']),
            //         'jml_total'         => $ttl1 + $ttl2
            //     )
            // ],
            'info_analisa_cc' => [
                'count_table'              => count($infoCC),
                'ttl_plafon'               => array_sum(array_column($infoCC, 'plafon')),
                'ttl_debet'                => array_sum(array_column($infoCC, 'baki_debet')),
                'ttl_angsuran'             => array_sum(array_column($infoCC, 'angsuran')),
                'collectabilitas_terendah' => max(array_column($infoCC, 'collectabilitas')),
                'table'                    => $infoCC
            ],
            'mutasi_bank' => $dataMutasi,
            'data_keuangan' => DB::connection('web')->table('log_tabungan_debt')->where('id', $check_ca->id_log_tabungan)->get(),
            'ringkasan_analisa' => $check_ca->ringkasan,
            'asuransi_jiwa'   => DB::connection('web')->table('asuransi_jiwa')->where('id', $check_ca->id_asuransi_jiwa)->first(),
            'asuransi_jaminan_kebakaran' => $jaminan_keb,
            'asuransi_jaminan_kendaraan' => $jaminan_ken,
            'tgl_transaksi' => $check_ca->updated_at
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

    public function detail($id, Request $req)
    {
        $pic = $req->pic; // From PIC middleware

        $arr = array();
        $i=0;
        foreach ($pic as $val) {
            $arr[] = $val['id_area'];
          $i++;
        }   

        $arrr = array();
        foreach ($pic as $val) {
            $arrr[] = $val['id_cabang'];
          $i++;
        }   
        $arrrr = array();
        foreach ($pic as $val) {
            $arrrr[] = $val['jpic']['cakupan'];
          $i++;
        }  
          //  dd($arr);
        $id_area   = $arr;
        $id_cabang = $arrr;
       // dd($id_cabang);
        $scope     = $arrrr;

        $check_so = TransSO::where('id', $id)->where('status_das', 1)->where('status_hm', 1)->first();

        if (!$check_so) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id ' . $id . ' belum ada di SO atau belum komplit saat pemeriksaan DAS da HM'
            ], 404);
        }
        $check_ao = TransAO::where('id_trans_so', $id)->where('status_ao', 1)->first();
        $check_ao_cat = TransAO::where('id_trans_so', $id)->where('status_ao', 1)->first();
        // dd($check_ao_cat);

        $check_penyimpangan = Penyimpangan::where('id_trans_so', $id)->first();
        // dd($check_penyimpangan);
        if (!$check_ao) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id ' . $id . ' belum sampai ke AO'
            ], 404);
        }

        $check_ca = TransCA::where('id_trans_so', $id)->where('status_ca', 1)->first();
        if (!$check_ca) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id ' . $id . ' belum sampai ke CA'
            ], 404);
        }

        $query_dir = TransCAA::with('so', 'pic', 'cabang')->where('id_trans_so', $id);

        $caa = Helper::checkDir($scope, $query_dir, $id_area, $id_cabang);
        $check_caa = $caa->first();

        if (empty($check_caa)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id ' . $id . ' belum di cek dan di kalkulasi oleh caa'
            ], 404);
        }

        $id_agu_ta = explode(",", $check_ao->id_agunan_tanah);

        $AguTa = AgunanTanah::whereIn('id', $id_agu_ta)->get();
        //  dd($AguTa);
        if (empty($AguTa)) {
            $idTan = null;
        } else {
            $idTan = array();
            foreach ($AguTa as $key => $value) {
 $kecamatan = Kecamatan::select('nama')->where('id',$value->id_kecamatan)->pluck('nama')->first();
               $kabupaten = Kabupaten::select('nama')->where('id',$value->id_kabupaten)->pluck('nama')->first();
               $provinsi = Provinsi::select('nama')->where('id',$value->id_provinsi)->pluck('nama')->first();
                $idTan[$key] = array(
                    'id'             => $value->id == null ? null : (int) $value->id,
                    'jenis'          => $value->jenis_sertifikat,
                    'tipe_lokasi'    => $value->tipe_lokasi,
'tipe_lokasi'    => $value->tipe_lokasi,
                    'alamat' => $value->alamat,
                    'rt'  => $value->rt,
                    'rw' => $value->rw,
                   'kelurahan' => Kelurahan::select('nama')->where('id',$value->id_kelurahan)->first(),
                   'kecamatan' => $kecamatan,
                   'kabupaten' => $kabupaten,
                   'provinsi' => $provinsi,
                    'luas' => [
                        'tanah'    => $value->luas_tanah,
                        'bangunan' => $value->luas_bangunan
                    ],
                    'tgl_berlaku_shgb'        => Carbon::parse($value->tgl_berlaku_shgb)->format("d-m-Y"),
                    'nama_pemilik_sertifikat' => $value->nama_pemilik_sertifikat,
                    'no_sertifikat'             => $value->no_sertifikat,
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


        $id_agu_ke = explode(",", $check_ao->id_agunan_kendaraan);

        $AguKe = AgunanKendaraan::whereIn('id', $id_agu_ke)->get();

        if (empty($AguKe)) {
            $idKen = null;
        } else {
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
        // dd($check_caa);
        // $jpic = JPIC::with('pic')->whereIn('id', explode(",", $check_caa->pic_team_caa))->select('urutan_jabatan')->first();
        // dd($jpic);
        $get_pic = PIC::with('jpic')->whereIn('id', explode(",", $check_caa->pic_team_caa))->orderBy('id_mj_pic', 'ASC')->get();

        if (empty($get_pic)) {
            $ptc = null;
        } else {
            $ptc = array();
            for ($i = 0; $i < count($get_pic); $i++) {
                $ptc[] = [
                    'id_pic'    => $get_pic[$i]['id'] == null ? null : (int) $get_pic[$i]['id'],
                    'nama'      => $get_pic[$i]['nama'],
'tgl_sk'      => $get_pic[$i]['tgl_sk'],
                    'jabatan'   => $get_pic[$i]['jpic']['nama_jenis'],
                    'urutan_jabatan'   => $get_pic[$i]['jpic']['urutan_jabatan'],
                    'user_id'   => $get_pic[$i]['user_id'] == null ? null : (int) $get_pic[$i]['user_id']
                ];
            }
        }

        $penyimpangan = TransCAA::where('id_trans_so',$id)->first();

    //    dd($penyimpangan->penyimpangan);
        //  dd($ptc);
        if ($check_caa->status_caa == 1) {
            $status_caa = 'recommend';
        } elseif ($check_caa->status_caa == 2) {
            $status_caa = 'not recommend';
        } else {
            $status_caa = 'waiting';
        }
 $app_date = Approval::where('id_trans_so',$id)->first();
        $data = array(
            'id_trans_so' => $check_so->id == null ? null : (int) $check_so->id,
            'data_penyimpangan' => $penyimpangan->penyimpangan,
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
            'status_caa'    => $status_caa,
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
                    'kredit' => $check_ao->usaha['pemasukan_kredit'],
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
                'suku_bunga'       => $check_ao->recom_ao['suku_bunga'],
                'pembayaran_bunga' => $check_ao->recom_ao['pembayaran_bunga'],
                'catatan'          => $check_ao_cat->recom_ao,
            ],
            'rekomendasi_ca' => [
                'id'                   => $check_ca->id_recom_ca == null ? null : (int) $check_ca->id_recom_ca,
                'produk'               => $check_ca->recom_ca['produk'],
                'plafon'               => $check_ca->recom_ca['plafon_kredit'],
                'tenor'                => $check_ca->recom_ca['jangka_waktu'],
                'suku_bunga'           => $check_ca->recom_ca['suku_bunga'],
                'pembayaran_bunga'     => $check_ca->recom_ca['pembayaran_bunga'],
                'rekomendasi_angsuran' => $check_ca->recom_ca['rekom_angsuran'],
                'catatan'              => $check_ca->catatan_ca
            ],
            'rekomendasi_pinjaman'     => $check_ca->recom_pin,
            'data_biaya' => [
                'reguler' => $reguler = array(
                    'biaya_provisi'         => $check_ca->recom_ca['biaya_provisi'],
                    'biaya_administrasi'    => $check_ca->recom_ca['biaya_administrasi'],
                    'biaya_credit_checking' => $check_ca->recom_ca['biaya_credit_checking'],
                    'biaya_premi' => [
                        'asuransi_jiwa'     => $check_ca->recom_ca['biaya_asuransi_jiwa'],
                        'asuransi_kebakaran'  => $check_ca->recom_ca['biaya_asuransi_jaminan_kebakaran'],
                        'asuransi_kendaraan'  => $check_ca->recom_ca['biaya_asuransi_jaminan_kendaraan'],
                    ],
                    'biaya_tabungan'                    => $check_ca->recom_ca['biaya_tabungan'],
                    'biaya_notaris'                     => $check_ca->recom_ca['notaris'],
                    'angsuran_pertama_bungan_berjalan'  => $check_ca->recom_ca['angs_pertama_bunga_berjalan'],
                    'pelunasan_nasabah_ro'              => $check_ca->recom_ca['pelunasan_nasabah_ro']
                ),

                'hold_dana' => $hold_dana = array(
                    'pelunasan_tempat_lain'         => $check_ca->recom_ca['pelunasan_tempat_lain'],
                    'blokir' => [
                        'tempat_lain'               => $check_ca->recom_ca['blokir_dana'],
                        'dua_kali_angsuran_kredit'  => $check_ca->recom_ca['blokir_angs_kredit']
                    ]
                ),

                'total' => array(
                    'biaya_reguler'     => $ttl1 = array_sum($reguler + $reguler['biaya_premi']),
                    'biaya_hold_dana'   => $ttl2 = array_sum($hold_dana + $hold_dana['blokir']),
                    'jml_total'         => $ttl1 + $ttl2
                )
            ],

            'penyimpangan' => $check_penyimpangan,
            'lampiran' => [
                'file_report_mao'     => $check_caa->file_report_mao,
                'file_report_mca'     => $check_caa->file_report_mca,
                'file_agunan'         => empty($check_caa->file_agunan) ? null : explode(";", $check_caa->file_agunan),
                'file_usaha'          => empty($check_caa->file_usaha) ? null : explode(";", $check_caa->file_usaha),
                'file_tempat_tinggal' => $check_caa->file_tempat_tinggal,
                'file_lain'           => empty($check_caa->file_lain) ? null : explode(";", $check_caa->file_lain)
            ],
            'rincian'       => $check_caa->rincian,
            'tgl_transaksi' => $check_caa->created_at,
 'tgl_approval' => date('d M Y',strtotime($app_date->updated_at))

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
        $pic = $req->pic; // From PIC middleware

        $column = array(
            'id', 'nomor_ca', 'user_id', 'id_trans_so', 'id_pic', 'id_area', 'id_cabang', 'id_mutasi_bank', 'id_log_tabungan', 'id_info_analisa_cc', 'id_ringkasan_analisa', 'id_recom_ca', 'id_rekomendasi_pinjaman', 'id_asuransi_jiwa', 'id_asuransi_jaminan', 'id_kapasitas_bulanan', 'id_pendapatan_usaha', 'catatan_ca', 'status_ca', 'revisi'
        );

        if ($param != 'filter' && $param != 'search') {
            return response()->json([
                'code'    => 412,
                'status'  => 'not valid',
                'message' => 'gunakan parameter yang valid diantara berikut: filter, search'
            ], 412);
        }

        if (in_array($key, $column) == false) {
            return response()->json([
                'code'    => 412,
                'status'  => 'not valid',
                'message' => 'gunakan key yang valid diantara berikut: ' . implode(",", $column)
            ], 412);
        }

        if (in_array($orderBy, $column) == false) {
            return response()->json([
                'code'    => 412,
                'status'  => 'not valid',
                'message' => 'gunakan order by yang valid diantara berikut: ' . implode(",", $column)
            ], 412);
        }

        if ($param == 'search') {
            $operator   = "like";
            $func_value = "%{$value}%";
        } else {
            $operator   = "=";
            $func_value = "{$value}";
        }

        $arr = array();
        $i=0;
        foreach ($pic as $val) {
            $arr[] = $val['id_area'];
          $i++;
        }   

        $arrr = array();
        foreach ($pic as $val) {
            $arrr[] = $val['id_cabang'];
          $i++;
        }   
        $arrrr = array();
        foreach ($pic as $val) {
            $arrrr[] = $val['jpic']['cakupan'];
          $i++;
        }  
          //  dd($arr);
        $id_area   = $arr;
        $id_cabang = $arrr;
       // dd($id_cabang);
        $scope     = $arrrr;

        $query_dir = TransCA::with('pic', 'cabang')
            ->where('status_ca', 1)
            ->where('flg_aktif', $status)
            ->orderBy($orderBy, $orderVal);

        $query = Helper::checkDir($scope, $query_dir, $id_area, $id_cabang);

        if ($value == 'default') {
            $res = $query;
        } else {
            $res = $query->where($key, $operator, $func_value);
        }

        if ($limit == 'default') {
            $result = $res;
        } else {
            $result = $res->limit($limit);
        }

        if ($result == '[]') {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $data = array();
        foreach ($result as $key => $val) {

            if ($val->status_ca == 1) {
                $status_ca = 'recommend';
            } elseif ($val->status_ca == 2) {
                $status_ca = 'not recommend';
            } else {
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

    public function filter($year, $month, Request $req)
    {
        $pic = $req->pic; // From PIC middleware

        $arr = array();
        $i=0;
        foreach ($pic as $val) {
            $arr[] = $val['id_area'];
          $i++;
        }   

        $arrr = array();
        foreach ($pic as $val) {
            $arrr[] = $val['id_cabang'];
          $i++;
        }   
        $arrrr = array();
        foreach ($pic as $val) {
            $arrrr[] = $val['jpic']['cakupan'];
          $i++;
        }  
          //  dd($arr);
        $id_area   = $arr;
        $id_cabang = $arrr;
       // dd($id_cabang);
        $scope     = $arrrr;
        if ($month == null) {
            $query_dir = TransCA::with('so', 'pic', 'cabang')->where('status_ca', 1)
                ->whereYear('created_at', '=', $year)
                ->orderBy('created_at', 'desc');
        } else {

            $query_dir = TransCA::with('so', 'pic', 'cabang')->where('status_ca', 1)
                ->whereYear('created_at', '=', $year)
                ->whereMonth('created_at', '=', $month)
                ->orderBy('created_at', 'desc');
        }

        $query = Helper::checkDir($scope, $query_dir, $id_area, $id_cabang);

        if ($query == '[]') {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        $data = array();
        foreach ($query as $key => $val) {

            if ($val->status_ca == 1) {
                $status_ca = 'recommend';
            } elseif ($val->status_ca == 2) {
                $status_ca = 'not recommend';
            } else {
                $status_ca = 'waiting';
            }

            if ($val->so['caa']['status_caa'] == 0) {
                $status_caa = 'waiting';
            } elseif ($val->so['caa']['status_caa'] == 1) {
                $status_caa = 'recommend';
            } elseif ($val->so['caa']['status_caa'] == 2) {
                $status_caa = 'not recommend';
            } elseif ($val->so['caa']['status_caa'] == null || $val->so['caa']['status_caa'] == "") {
                $status_caa = 'null';
            }

            $id_agu_ta = explode(",", $val->so['ao']['id_agunan_tanah']);
            $AguTa = AgunanTanah::whereIn('id', $id_agu_ta)->get();

            $Tan = array();
            foreach ($AguTa as $key => $value) {
                $Tan[$key] = array(
                    'id'    => $id_agu_ta[$key] == null ? null : (int) $id_agu_ta[$key],
                    'jenis' => $value->jenis_sertifikat
                );
            }

            $id_agu_ke = explode(",", $val->so['ao']['id_agunan_kendaraan']);
            $AguKe = AgunanKendaraan::whereIn('id', $id_agu_ke)->get();

            if ($AguKe == '[]') {
                $Ken = null;
            } else {
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
                ->select("id_pic", "id", "plafon", "tenor", "rincian", "status", "updated_at as tgl_approve")
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
