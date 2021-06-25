<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use App\Http\Requests\Transaksi\BlankRequest;
use App\Models\Pengajuan\CAA\Penyimpangan;
use App\Models\Pengajuan\AO\AgunanKendaraan;
use App\Models\Pengajuan\AO\AgunanTanah;
use App\Models\Pengajuan\AO\PemeriksaanAgunTan;
use App\Models\Pengajuan\AO\PemeriksaanAgunKen;
use App\Models\Pengajuan\SO\Penjamin;
use App\Models\Pengajuan\SO\Debitur;
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
use App\Models\ActivitySo;
use App\Models\Efilling\Bi_checking;
use App\Models\Efilling\Efilling;
use App\Models\Efilling\Efilling_asset;
use App\Models\Efilling\Efilling_bi;
use App\Models\Efilling\Efilling_ca;
use App\Models\Efilling\Efilling_foto;
use App\Models\Efilling\Efilling_legal;
use App\Models\Efilling\Efilling_spkndk;
use App\Models\Efilling\EfillingJaminan;
use App\Models\Transaksi\LogRekomCA;
use App\Models\Transaksi\LogRingAnalisa;
use App\Models\v2\Target_lending;
use App\Models\master_nilai;
use App\Models\master_transaksi;
use Image;
use App\Models\Efilling\EfillingNasabah;
use App\Models\Efilling\EfillingPermohonan;
// use Intervention\Image\Image;

// use Intervention\Image\ImageManagerStatic as Image;


use App\Models\MasterActivity\Activity;
use App\Models\MasterActivity\TargetPeriodik;
use App\Models\MasterActivity\TargetApproval;
use App\Models\Transaksi\Lpdk_lampiran;
use Illuminate\Support\Facades\DB;
use Exception;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;


class EfillingController extends BaseController
{
     public function index(Request $req)
    {
        $user_id = $req->auth;
        $pic = $req->pic;
        // dd($user_id->kd_cabang);

        if ($user_id->kd_cabang === '00' || $user_id->initial === 'HO') {
            $data = DB::connection('centro')->table('view_efiling_header')->paginate(10);
        } else {
            $data = DB::connection('centro')->table('view_efiling_header')->where('kode_kantor', $user_id->kd_cabang)->paginate(10);
        }

        if ($data === null) {
            return response()->json([
                "code" => 404,
                "message" => "Data Tidak Ditemukan"
            ], 404);
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

    public function showViewHeaderEfilling(Request $req)
    {

        $kode_kantor = $req->input('kode_kantor');
        $baki_debet = $req->input('baki_debet');
        $status_verifikasi = $req->input('status_verifikasi');
        $no_rekening = $req->input('no_rekening');
        // DB::connection('centro')->statement(DB::connection('centro')->raw("SET @kode_kantor = '$kode_kantor'"));
        // DB::connection('centro')->statement(DB::connection('centro')->raw("SET @kode_kantor = '$kode_kantor'"));
        // DB::connection('centro')->statement(DB::connection('centro')->raw("SET @status_verifikasi = '$status_verifikasi'"));
        // DB::connection('centro')->statement(DB::connection('centro')->raw("SET @no_rekening = '$no_rekening'"));
        //  dd($set_kantor, $set_status);
        //   $set = DB::connection('centro')->select(DB::raw("SET @kode_kantor = '04'"));
        //  dd($set);
        // dd($kode_kantor, $baki_debet, $status_verifikasi, $no_rekening);
        // $db = DB::connection('centro')->select("SELECT * FROM `view_efiling_header` WHERE no_rekening=@no_rekening");
        //dd($kode_kantor, $baki_debet, $status_verifikasi, $no_rekening);
$jenis = Efilling::where('no_rekening', $no_rekening)->first();

if($jenis->is_jenis === "2") {
 $db = DB::connection('centro')->select("SELECT * FROM `view_efiling_header` WHERE 
         IF('$kode_kantor' = 'all', 1, 
            kode_kantor= '$kode_kantor'
          )
          AND
         IF('$baki_debet' = 'all',1,
            baki_debet='0' AND status_dokument='KELUAR'
         )
         AND
         IF('$status_verifikasi' = 'all', 1,
             status_verifikasi = '$status_verifikasi'
         )
         AND
         IF('$no_rekening' <> '', (no_rekening LIKE '$no_rekening%'), 1)
         ORDER BY tgl_realisasi_eng DESC LIMIT 10 OFFSET 0
         ");
} else {
$db = DB::connection('centro')->select("SELECT * FROM `view_efilling_sefin_header` WHERE 
         IF('$kode_kantor' = 'all', 1, 
            kode_kantor= '$kode_kantor'
          )
          AND
         IF('$baki_debet' = 'all',1,
            baki_debet='0' AND status_dokument='KELUAR'
         )
         AND
         IF('$status_verifikasi' = 'all', 1,
             status_verifikasi = '$status_verifikasi'
         )
         AND
         IF('$no_rekening' <> '', (no_rekening LIKE '$no_rekening%'), 1)
         ORDER BY tgl_realisasi_eng DESC LIMIT 10 OFFSET 0
         ");
}
       
        //   dd($db);
        try {
            if (empty($db)) {
                return response()->json([
                    "code" => 404,
                    "message" => "data tidak ditemukan"
                ], 404);
            }

            return response()->json([
                'data'   => $db
            ]);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function paginate($items, $perPage = 5, $page = null, $options = [])
    {

        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }


    public function show($no_kontrak)
    {
 function multiexplode($delimiters, $string)
        {

            $ready = str_replace($delimiters, $delimiters[0], $string);
            $launch = explode($delimiters[0], $ready);
            return  $launch;
        }
        $efillingnasabah = EfillingNasabah::where('no_rekening', $no_kontrak)->first();
        $efillingjaminan = EfillingJaminan::where('no_rekening', $no_kontrak)->first();
        $efillingpermohonan = EfillingPermohonan::where('no_rekening', $no_kontrak)->first();
        $efillingfoto = Efilling_foto::where('no_rekening', $no_kontrak)->first();
        $efilling_aset = Efilling_asset::where('no_rekening', $no_kontrak)->first();
        $spkndk = Efilling_spkndk::where('no_rekening', $no_kontrak)->first();



        $efilling_legal = Efilling_legal::where('no_rekening', $no_kontrak)->first();

        $efilling = Efilling::where('no_rekening', $no_kontrak)->first();
        // $bichecking = Bi_checking::where('no_rekening', $no_kontrak)->first();
        $efilling_bichecking = Efilling_bi::where('no_rekening', $no_kontrak)->first();
        $effiling_ca = Efilling_ca::where('no_rekening', $no_kontrak)->first();

        if (empty($efillingnasabah)) {
            $efillingnasabah =  EfillingNasabah::where('no_rekening', $no_kontrak)->create(['no_rekening' => $no_kontrak]);
        }
        if (empty($efillingpermohonan)) {
            $efillingpermohonan = EfillingPermohonan::where('no_rekening', $no_kontrak)->create(['no_rekening' => $no_kontrak]);
        }
        if (empty($efillingjaminan)) {
            $efillingjaminan = EfillingJaminan::where('no_rekening', $no_kontrak)->create(['no_rekening' => $no_kontrak]);
        }
        if (empty($efillingfoto)) {
            $efillingfoto = Efilling_foto::where('no_rekening', $no_kontrak)->create(['no_rekening' => $no_kontrak]);
        }
        if (empty($efilling_aset)) {
            $efilling_aset = Efilling_asset::where('no_rekening', $no_kontrak)->create(['no_rekening' => $no_kontrak]);
        }
        if (empty($efilling_legal)) {
            $efilling_legal =  Efilling_legal::where('no_rekening', $no_kontrak)->create(['no_rekening' => $no_kontrak]);
        }
        if (empty($efilling)) {
            $efilling = Efilling::where('no_rekening', $no_kontrak)->create(['no_rekening' => $no_kontrak]);
        }
        if (empty($spkndk)) {
            $spkndk = Efilling_spkndk::where('no_rekening', $no_kontrak)->create(['no_rekening' => $no_kontrak]);
        }
        // if (empty($bichecking)) {
        //     $bichecking = Bi_checking::where('no_rekening', $no_kontrak)->create(['no_rekening' => $no_kontrak]);
        // }
        if (empty($efilling_bichecking)) {
            $efilling_bichecking = Efilling_bi::where('no_rekening', $no_kontrak)->create(['no_rekening' => $no_kontrak]);
        }
        if (empty($effiling_ca)) {
            $effiling_ca = Efilling_ca::where('no_rekening', $no_kontrak)->create(['no_rekening' => $no_kontrak]);
        }
        $no_ktp = $efillingnasabah->ktp2;
        ##############################################################################################
        $memo_ao = array(explode(",", $effiling_ca->memo_ao));
        $memo_ca = array(explode(",", $effiling_ca->memo_ca));
        $offering_letter = array(explode(",", $effiling_ca->offering_letter));
        $penilaian_jaminan = array(explode(",", $effiling_ca->penilaian_jaminan));
        $cheklist_survey = array(explode(",", $effiling_ca->cheklist_survey));
        $persetujuan_kredit = array(explode(",", $effiling_ca->persetujuan_kredit));

        $help_memo_ao = Helper::subsCA($memo_ao, $no_ktp);
        $help_memo_ao = array('memo_ao' => $effiling_ca->memo_ao, 'memo_ao_nama' => json_encode($help_memo_ao));
        $help_memo_ca = Helper::subsCA($memo_ca, $no_ktp);
        $help_memo_ca = array('memo_ca' => $effiling_ca->memo_ca, 'memo_ca_nama' => json_encode($help_memo_ca));
        $help_offering_letter = Helper::subsCA($offering_letter, $no_ktp);
        $help_offering_letter = array('offering_letter' => $effiling_ca->offering_letter, 'offering_letter_nama' => json_encode($help_offering_letter));
        $help_penilaian_jaminan = Helper::subsCA($penilaian_jaminan, $no_ktp);
        $help_penilaian_jaminan = array('penilaian_jaminan' => $effiling_ca->penilaian_jaminan, 'penilaian_jaminan_nama' => json_encode($help_penilaian_jaminan));

        $help_cheklist_survey = Helper::subsCA($cheklist_survey, $no_ktp);
        $help_cheklist_survey = array('cheklist_survey' => $effiling_ca->cheklist_survey, 'cheklist_survey_nama' => json_encode($help_cheklist_survey));

        $help_persetujuan_kredit = Helper::subsCA($persetujuan_kredit, $no_ktp);
        $help_persetujuan_kredit = array('persetujuan_kredit' => $effiling_ca->persetujuan_kredit, 'persetujuan_kredit_nama' => json_encode($help_persetujuan_kredit));

        $ca_merge = array_merge($help_memo_ao, $help_memo_ca, $help_offering_letter, $help_penilaian_jaminan, $help_cheklist_survey, $help_persetujuan_kredit);

        #####################################################################################################
        $pengajuan_bi = array(explode(",", $efilling_bichecking->pengajuan_bi));
        $persetujuan = array(explode(",", $efilling_bichecking->persetujuan));
        $hasil = array(explode(",", $efilling_bichecking->hasil));


        $help_pengajuan_bi = Helper::subsBI($pengajuan_bi, $no_ktp);
        $help_pengajuan_bi = array('pengajuan_bi' => $efilling_bichecking->pengajuan_bi, 'pengajuan_bi_nama' => json_encode($help_pengajuan_bi));
        $help_persetujuan = Helper::subsBI($persetujuan, $no_ktp);
        $help_persetujuan = array('persetujuan' => $efilling_bichecking->persetujuan, 'persetujuan_nama' => json_encode($help_persetujuan));
        $help_hasil = Helper::subsBI($hasil, $no_ktp);
        $help_hasil = array('hasil' => $efilling_bichecking->hasil, 'hasil_nama' => json_encode($help_hasil));


        $bi_merge = array_merge($help_pengajuan_bi, $help_persetujuan, $help_hasil);
        ################################################################################################
        $ft_jaminan = $efillingfoto->ft_jaminan;
        $ft_pengikatan = $efillingfoto->ft_pengikatan;
        $ft_domisili = $efillingfoto->ft_domisili;
        $ft_usaha = $efillingfoto->ft_usaha;


        $help_ft_jaminan = Helper::subsFoto($ft_jaminan);
        $help_ft_jaminan = array('ft_jaminan' => $efillingfoto->ft_jaminan, 'ft_jaminan_nama' => json_encode(array($help_ft_jaminan)));
        $help_ft_pengikatan = Helper::subsFoto($ft_pengikatan, $no_ktp);
        $help_ft_pengikatan = array('ft_pengikatan' => $efillingfoto->ft_pengikatan, 'ft_pengikatan_nama' => json_encode(array($help_ft_pengikatan)));
        $help_ft_domisili = Helper::subsFoto($ft_domisili);
        $help_ft_domisili = array('ft_domisili' => $efillingfoto->ft_domisili, 'ft_domisili_nama' => json_encode(array($help_ft_domisili)));

        $help_ft_usaha = Helper::subsFoto($ft_usaha);
        $help_ft_usaha = array('ft_usaha' => $efillingfoto->ft_usaha, 'ft_usaha_nama' => json_encode(array($help_ft_usaha)));


        $foto_merge = array_merge($help_ft_jaminan, $help_ft_pengikatan, $help_ft_domisili, $help_ft_usaha);
        ################################################################################################
         $sertifikat = $efillingjaminan->sertifikat;
        $skmht = $efillingjaminan->skmht;
        $apht = $efillingjaminan->apht;
        $cabut_roya = $efillingjaminan->cabut_roya;
        $sht = $efillingjaminan->sht;
        $pbb = $efillingjaminan->pbb;
        $imb = $efillingjaminan->imb;
        $ajb = $efillingjaminan->ajb;
        $bpkb = $efillingjaminan->bpkb;
        $ahli_waris = $efillingjaminan->ahli_waris;
        $pengakuan_hutang = $efillingjaminan->pengakuan_hutang;
        $akta_pengakuan_hak_bersama = $efillingjaminan->akta_pengakuan_hak_bersama;
        $adendum = $efillingjaminan->adendum;
        $fidusia = $efillingjaminan->fidusia;


       $help_sertifikat = Helper::subsJaminan($sertifikat);
        $help_sertifikat = array('sertifikat' => $efillingjaminan->sertifikat, 'sertifikat_nama' => json_encode(array($help_sertifikat)));
        $help_skmht = Helper::subsJaminan($skmht);
        $help_skmht = array('skmht' => $efillingjaminan->skmht, 'skmht_nama' => json_encode(array($help_skmht)));
        $help_apht = Helper::subsJaminan($apht);
        $help_apht = array('apht' => $efillingjaminan->apht, 'apht_nama' => json_encode(array($help_apht)));
        $help_cabut_roya = Helper::subsJaminan($cabut_roya);
        $help_cabut_roya = array('cabut_roya' => $efillingjaminan->cabut_roya, 'cabut_roya_nama' => json_encode(array($help_cabut_roya)));
        $help_sht = Helper::subsJaminan($sht);
        $help_sht = array('sht' => $efillingjaminan->sht, 'sht_nama' => json_encode(array($help_sht)));
        $help_pbb = Helper::subsJaminan($pbb);
        //  dd($help_pbb);
        $help_pbb = array('pbb' => $efillingjaminan->pbb, 'pbb_nama' => json_encode(array($help_pbb)));
        $help_imb = Helper::subsJaminan($imb);
        $help_imb = array('imb' => $efillingjaminan->imb, 'imb_nama' => json_encode(array($help_imb)));
        $help_ajb = Helper::subsJaminan($ajb);
        $help_ajb = array('ajb' => $efillingjaminan->ajb, 'ajb_nama' => json_encode(array($help_ajb)));
        $help_bpkb = Helper::subsJaminan($bpkb);
        $help_bpkb = array('bpkb' => $efillingjaminan->bpkb, 'bpkb_nama' => json_encode(array($help_bpkb)));
        $help_ahli_waris = Helper::subsJaminan($ahli_waris);
        $help_ahli_waris = array('ahli_waris' => $efillingjaminan->ahli_waris, 'ahli_waris_nama' => json_encode(array($help_ahli_waris)));
        $help_pengakuan_hutang = Helper::subsJaminan($pengakuan_hutang);
        $help_pengakuan_hutang = array('pengakuan_hutang' => $efillingjaminan->pengakuan_hutang, 'pengakuan_hutang_nama' => json_encode(array($help_pengakuan_hutang)));
        $help_akta_pengakuan_hak_bersama = Helper::subsJaminan($akta_pengakuan_hak_bersama);
        $help_akta_pengakuan_hak_bersama = array('akta_pengakuan_hak_bersama' => $efillingjaminan->akta_pengakuan_hak_bersama, 'akta_pengakuan_hak_bersama_nama' => json_encode(array($help_akta_pengakuan_hak_bersama)));
        $help_adendum = Helper::subsJaminan($adendum);
        $help_adendum = array('adendum' => $efillingjaminan->adendum, 'adendum_nama' => json_encode(array($help_adendum)));
        $help_fidusia = Helper::subsJaminan($fidusia);
        $help_fidusia = array('fidusia' => $efillingjaminan->fidusia, 'fidusia_nama' => json_encode(array($help_fidusia)));


        $jaminan_merge = array_merge($help_sertifikat, $help_skmht, $help_apht, $help_cabut_roya, $help_sht, $help_pbb, $help_imb, $help_ajb, $help_bpkb, $help_ahli_waris, $help_pengakuan_hutang, $help_akta_pengakuan_hak_bersama, $help_adendum, $help_fidusia);
        ########################################################################################################
        $pengajuan_lpdk = array(explode(",", $efilling_legal->pengajuan_lpdk));
        $lpdk = array(explode(",", $efilling_legal->lpdk));
        $cheklist_pengikatan = array(explode(",", $efilling_legal->cheklist_pengikatan));
        $order_pengikatan = array(explode(",", $efilling_legal->order_pengikatan));


        $help_pengajuan_lpdk = Helper::subsLegal($pengajuan_lpdk, $no_ktp);
        $help_pengajuan_lpdk = array('pengajuan_lpdk' => $efilling_legal->pengajuan_lpdk, 'pengajuan_lpdk_nama' => json_encode($help_pengajuan_lpdk));
        $help_lpdk = Helper::subsLegal($lpdk, $no_ktp);
        $help_lpdk = array('lpdk' => $efilling_legal->lpdk, 'lpdk_nama' => json_encode($help_lpdk));
        $help_cheklist_pengikatan = Helper::subsLegal($cheklist_pengikatan, $no_ktp);
        $help_cheklist_pengikatan = array('cheklist_pengikatan' => $efilling_legal->cheklist_pengikatan, 'cheklist_pengikatan_nama' => json_encode($help_cheklist_pengikatan));

        $help_order_pengikatan = Helper::subsLegal($order_pengikatan, $no_ktp);
        $help_order_pengikatan = array('order_pengikatan' => $efilling_legal->order_pengikatan, 'order_pengikatan_nama' => json_encode($help_order_pengikatan));


        $legal_merge = array_merge($help_pengajuan_lpdk, $help_lpdk, $help_cheklist_pengikatan, $help_order_pengikatan);
        ########################################################################################################
        $aplikasi = array(explode(",", $efillingpermohonan->aplikasi));
        $denah_lokasi = array(explode(",", $efillingpermohonan->denah_lokasi));
        $checklist_kelengkapan = array(explode(",", $efillingpermohonan->checklist_kelengkapan));


        $help_aplikasi = Helper::subsPermohonan($aplikasi, $no_ktp);
        $help_aplikasi = array('aplikasi' => $efillingpermohonan->aplikasi, 'aplikasi_nama' => json_encode($help_aplikasi));
        $help_denah_lokasi = Helper::subsPermohonan($denah_lokasi, $no_ktp);
        $help_denah_lokasi = array('denah_lokasi' => $efillingpermohonan->denah_lokasi, 'denah_lokasi_nama' => json_encode($help_denah_lokasi));
        $help_checklist_kelengkapan = Helper::subsPermohonan($checklist_kelengkapan, $no_ktp);
        $help_checklist_kelengkapan = array('checklist_kelengkapan' => $efillingpermohonan->checklist_kelengkapan, 'checklist_kelengkapan_nama' => json_encode($help_checklist_kelengkapan));


        $permohonan_merge = array_merge($help_aplikasi, $help_denah_lokasi, $help_checklist_kelengkapan);

        ################################################################################################
        $ra_tanda_terima = array(explode(",", $efilling_aset->ra_tanda_terima));
        $ra_surat_kuasa = array(explode(",", $efilling_aset->ra_surat_kuasa));
        $ra_identitas_pengambilan = array(explode(",", $efilling_aset->ra_identitas_pengambilan));
        $ra_lainnya = array(explode(",", $efilling_aset->ra_lainnya));
        $ra_serah_terima = array(explode(",", $efilling_aset->ra_serah_terima));

        $help_ra_tanda_terima = Helper::subsAsset($ra_tanda_terima, $no_ktp);
        $help_ra_tanda_terima = array('ra_tanda_terima' => $efilling_aset->ra_tanda_terima, 'ra_tanda_terima_nama' => json_encode($help_ra_tanda_terima));
        $help_ra_surat_kuasa = Helper::subsAsset($ra_surat_kuasa, $no_ktp);
        $help_ra_surat_kuasa = array('ra_surat_kuasa' => $efilling_aset->ra_surat_kuasa, 'ra_surat_kuasa_nama' => json_encode($help_ra_surat_kuasa));
        $help_ra_identitas_pengambilan = Helper::subsAsset($ra_identitas_pengambilan, $no_ktp);
        $help_ra_identitas_pengambilan = array('ra_identitas_pengambilan' => $efilling_aset->ra_identitas_pengambilan, 'ra_identitas_pengambilan_nama' => json_encode($help_ra_identitas_pengambilan));
        $help_ra_lainnya = Helper::subsAsset($ra_lainnya, $no_ktp);
        $help_ra_lainnya = array('ra_lainnya' => $efilling_aset->ra_lainnya, 'ra_lainnya_nama' => json_encode($help_ra_lainnya));
        $help_ra_serah_terima = Helper::subsAsset($ra_serah_terima, $no_ktp);
        $help_ra_serah_terima = array('ra_serah_terima' => $efilling_aset->ra_serah_terima, 'ra_serah_terima_nama' => json_encode($help_ra_serah_terima));


        $asset_merge = array_merge($help_ra_tanda_terima, $help_ra_surat_kuasa, $help_ra_identitas_pengambilan, $help_ra_lainnya, $help_ra_serah_terima);
        #######################################################################################################
        $spk_ndk = array(explode(",", $spkndk->spk_ndk));
        $asuransi = array(explode(",", $spkndk->asuransi));
        $sp_no_imb = array(explode(",", $spkndk->sp_no_imb));
        $jadwal_angsuran = array(explode(",", $spkndk->jadwal_angsuran));
        $personal_guarantee = array(explode(",", $spkndk->personal_guarantee));
        $hold_dana = array(explode(",", $spkndk->hold_dana));
        $surat_transfer = array(explode(",", $spkndk->surat_transfer));
        $keabsahan_data = array(explode(",", $spkndk->keabsahan_data));
        $sp_beda_jt_tempo = array(explode(",", $spkndk->sp_beda_jt_tempo));
        $sp_authentic = array(explode(",", $spkndk->sp_authentic));
        $sp_penyerahan_jaminan = array(explode(",", $spkndk->sp_penyerahan_jaminan));
        $surat_aksep = array(explode(",", $spkndk->surat_aksep));
        $tt_uang = array(explode(",", $spkndk->tt_uang));
        $sp_pendebetan_rekening = array(explode(",", $spkndk->sp_pendebetan_rekening));
        $sp_plang = array(explode(",", $spkndk->sp_plang));
        $hal_penting = array(explode(",", $spkndk->hal_penting));
        $restruktur_bunga_denda = array(explode(",", $spkndk->restruktur_bunga_denda));
        $spajk_spa_fpk = array(explode(",", $spkndk->spajk_spa_fpk));

        $help_spk_ndk = Helper::subsSpk($spk_ndk, $no_ktp);
        $help_spk_ndk = array('spk_ndk' => $spkndk->spk_ndk, 'spk_ndk_nama' => json_encode($help_spk_ndk));
        $help_asuransi = Helper::subsSpk($asuransi, $no_ktp);
        $help_asuransi = array('asuransi' => $spkndk->asuransi, 'asuransi_nama' => json_encode($help_asuransi));
        $help_sp_no_imb = Helper::subsSpk($sp_no_imb, $no_ktp);
        $help_sp_no_imb = array('sp_no_imb' => $spkndk->sp_no_imb, 'sp_no_imb_nama' => json_encode($help_sp_no_imb));
        $help_jadwal_angsuran = Helper::subsSpk($jadwal_angsuran, $no_ktp);
        $help_jadwal_angsuran = array('jadwal_angsuran' => $spkndk->jadwal_angsuran, 'jadwal_angsuran_nama' => json_encode($help_jadwal_angsuran));
        $help_personal_guarantee = Helper::subsSpk($personal_guarantee, $no_ktp);
        $help_personal_guarantee = array('personal_guarantee' => $spkndk->personal_guarantee, 'personal_guarantee_nama' => json_encode($help_personal_guarantee));

        $help_hold_dana = Helper::subsSpk($hold_dana, $no_ktp);
        $help_hold_dana = array('hold_dana' => $spkndk->hold_dana, 'hold_dana_nama' => json_encode($help_hold_dana));

        $help_surat_transfer = Helper::subsSpk($surat_transfer, $no_ktp);
        $help_surat_transfer = array('surat_transfer' => $spkndk->surat_transfer, 'surat_transfer_nama' => json_encode($help_surat_transfer));

        $help_keabsahan_data = Helper::subsSpk($keabsahan_data, $no_ktp);
        $help_keabsahan_data = array('keabsahan_data' => $spkndk->keabsahan_data, 'keabsahan_data_nama' => json_encode($help_keabsahan_data));

        $help_sp_beda_jt_tempo = Helper::subsSpk($sp_beda_jt_tempo, $no_ktp);
        $help_sp_beda_jt_tempo = array('sp_beda_jt_tempo' => $spkndk->sp_beda_jt_tempo, 'sp_beda_jt_tempo_nama' => json_encode($help_sp_beda_jt_tempo));

        $help_sp_authentic = Helper::subsSpk($sp_authentic, $no_ktp);
        $help_sp_authentic = array('sp_authentic' => $spkndk->sp_authentic, 'sp_authentic_nama' => json_encode($help_sp_authentic));

        $help_sp_penyerahan_jaminan = Helper::subsSpk($sp_penyerahan_jaminan, $no_ktp);
        $help_sp_penyerahan_jaminan = array('sp_penyerahan_jaminan' => $spkndk->sp_penyerahan_jaminan, 'sp_penyerahan_jaminan_nama' => json_encode($help_sp_penyerahan_jaminan));

        $help_surat_aksep = Helper::subsSpk($surat_aksep, $no_ktp);
        $help_surat_aksep = array('surat_aksep' => $spkndk->surat_aksep, 'surat_aksep_nama' => json_encode($help_surat_aksep));

        $help_tt_uang = Helper::subsSpk($tt_uang, $no_ktp);
        $help_tt_uang = array('tt_uang' => $spkndk->tt_uang, 'tt_uang_nama' => json_encode($help_tt_uang));

        $help_sp_pendebetan_rekening = Helper::subsSpk($sp_pendebetan_rekening, $no_ktp);
        $help_sp_pendebetan_rekening = array('sp_pendebetan_rekening' => $spkndk->sp_pendebetan_rekening, 'sp_pendebetan_rekening_nama' => json_encode($help_sp_pendebetan_rekening));

        $help_sp_plang = Helper::subsSpk($sp_plang, $no_ktp);
        $help_sp_plang = array('sp_plang' => $spkndk->sp_plang, 'sp_plang_nama' => json_encode($help_sp_plang));

        $help_hal_penting = Helper::subsSpk($hal_penting, $no_ktp);
        $help_hal_penting = array('hal_penting' => $spkndk->hal_penting, 'hal_penting_nama' => json_encode($help_hal_penting));

        $help_restruktur_bunga_denda = Helper::subsSpk($restruktur_bunga_denda, $no_ktp);
        $help_restruktur_bunga_denda = array('restruktur_bunga_denda' => $spkndk->restruktur_bunga_denda, 'restruktur_bunga_denda_nama' => json_encode($help_restruktur_bunga_denda));

        $help_spajk_spa_fpk = Helper::subsSpk($spajk_spa_fpk, $no_ktp);
        $help_spajk_spa_fpk = array('spajk_spa_fpk' => $spkndk->spajk_spa_fpk, 'spajk_spa_fpk_nama' => json_encode($help_spajk_spa_fpk));


        $spk_merge = array_merge($help_spk_ndk, $help_asuransi, $help_sp_no_imb, $help_jadwal_angsuran, $help_personal_guarantee, $help_hold_dana, $help_surat_transfer, $help_keabsahan_data, $help_sp_beda_jt_tempo, $help_sp_authentic, $help_sp_penyerahan_jaminan, $help_surat_aksep, $help_tt_uang, $help_sp_pendebetan_rekening, $help_sp_plang, $help_hal_penting, $help_restruktur_bunga_denda, $help_spajk_spa_fpk);

        ################################################################################################



       $ktp = $efillingnasabah->ktp;
        $npwp = $efillingnasabah->npwp;
        $kk = $efillingnasabah->kk;
        $domisili = $efillingnasabah->domisili;
        $surat_nikah = $efillingnasabah->surat_nikah;
        $surat_cerai = $efillingnasabah->surat_cerai;
        $surat_lahir = $efillingnasabah->surat_lahir;
        $surat_kematian = $efillingnasabah->surat_kematian;
        $skd = $efillingnasabah->skd;
        $slip_gaji = $efillingnasabah->slip_gaji;
        $take_over = $efillingnasabah->take_over;
        $sk_kerja = $efillingnasabah->sk_kerja;
        $sk_usaha = $efillingnasabah->sk_usaha;
        $rek_koran = $efillingnasabah->rek_koran;
        $tdp = $efillingnasabah->tdp;
        $bon_usaha = $efillingnasabah->bon_usaha;

      $help_ktp = Helper::fcn($ktp);
        $help_ktp = array('ktp' => $efillingnasabah->ktp, 'ktp_nama' => json_encode(array($help_ktp)));
        // dd($npwp);
        $help_npwp = Helper::fcn($npwp);
        $help_npwp = array('npwp' => $efillingnasabah->npwp, 'npwp_nama' => json_encode(array($help_npwp)));
        $help_kk = Helper::fcn($kk);
        $help_kk = array('kk' => $efillingnasabah->kk, 'kk_nama' => json_encode(array($help_kk)));
        $help_domisili = Helper::fcn($domisili);
        $help_domisili = array('domisili' => $efillingnasabah->domisili, 'domisili_nama' => json_encode(array($help_domisili)));
        $help_surat_nikah = Helper::fcn($surat_nikah);
        $help_surat_nikah = array('surat_nikah' => $efillingnasabah->surat_nikah, 'surat_nikah_nama' => json_encode(array($help_surat_nikah)));
        $help_surat_cerai = Helper::fcn($surat_cerai);
        $help_surat_cerai = array('surat_cerai' => $efillingnasabah->surat_cerai, 'surat_cerai_nama' => json_encode(array($help_surat_cerai)));
        $help_surat_lahir = Helper::fcn($surat_lahir);
        $help_surat_lahir = array('surat_lahir' => $efillingnasabah->surat_lahir, 'surat_lahir_nama' => json_encode(array($help_surat_lahir)));
        $help_surat_kematian = Helper::fcn($surat_kematian);
        $help_surat_kematian = array('surat_kematian' => $efillingnasabah->surat_kematian, 'surat_kematian_nama' => json_encode(array($help_surat_kematian)));
        $help_skd = Helper::fcn($skd);
        $help_skd = array('skd' => $efillingnasabah->skd, 'skd_nama' => json_encode(array($help_skd)));
        $help_slip_gaji = Helper::fcn($slip_gaji);
        $help_slip_gaji = array('slip_gaji' => $efillingnasabah->slip_gaji, 'slip_gaji_nama' => json_encode(array($help_slip_gaji)));
        $help_take_over = Helper::fcn($take_over);
        $help_take_over = array('take_over' => $efillingnasabah->take_over, 'take_over_nama' => json_encode(array($help_take_over)));
        $help_sk_kerja = Helper::fcn($sk_kerja);
        $help_sk_kerja = array('sk_kerja' => $efillingnasabah->sk_kerja, 'sk_kerja_nama' => json_encode(array($help_sk_kerja)));
        $help_sk_usaha = Helper::fcn($sk_usaha);
        $help_sk_usaha = array('sk_usaha' => $efillingnasabah->sk_usaha, 'sk_usaha_nama' => json_encode(array($help_sk_usaha)));
        $help_rek_koran = Helper::fcn($rek_koran);
        $help_rek_koran = array('rek_koran' => $efillingnasabah->rek_koran, 'rek_koran_nama' => json_encode(array($help_rek_koran)));
        $help_tdp = Helper::fcn($tdp);
        $help_tdp = array('tdp' => $efillingnasabah->tdp, 'tdp_nama' => json_encode(array($help_tdp)));
        $help_bon_usaha = Helper::fcn($bon_usaha);
        $help_bon_usaha = array('bon_usaha' => $efillingnasabah->bon_usaha, 'bon_usaha_nama' => json_encode(array($help_bon_usaha)));

        $nas_merge = array_merge($help_ktp, $help_npwp, $help_kk, $help_domisili, $help_surat_nikah, $help_surat_cerai, $help_surat_lahir, $help_surat_kematian, $help_skd, $help_slip_gaji, $help_take_over, $help_sk_kerja, $help_sk_usaha, $help_rek_koran, $help_tdp, $help_bon_usaha);
        //dd(array_merge($help_ktp, $help_npwp));
        ######################################################################################################
        $array_verif_bichecking = array(
            "verifikasi_bichecking" => $efilling_bichecking->verifikasi,
            "notes_bichecking" => $efilling_bichecking->notes
        );
        $array_verif_ca = array(
            "verifikasi_ca" => $effiling_ca->verifikasi,
            "notes_ca" => $effiling_ca->notes
        );
        $array_verif_nasabah = array(
            "verifikasi_nasabah" => $efillingnasabah->verifikasi,
            "notes_nasabah" => $efillingnasabah->notes
        );
        $array_verif_jaminan = array(
            "verifikasi_jaminan" => $efillingjaminan->verifikasi,
            "notes_jaminan" => $efillingjaminan->notes
        );
        $array_verif_permohonan = array(
            "verifikasi_permohonan" => $efillingpermohonan->verifikasi,
            "notes_permohonan" => $efillingpermohonan->notes
        );
        $array_verif_foto = array(
            "verifikasi_foto" => $efillingfoto->verifikasi,
            "notes_foto" => $efillingfoto->notes
        );
        $array_verif_legal = array(
            "verifikasi_legal" => $efilling_legal->verifikasi,
            "notes_legal" => $efilling_legal->notes
        );
        $array_verif_asset = array(
            "verifikasi_asset" => $efilling_aset->verifikasi,
            "notes_asset" => $efilling_aset->notes
        );
        $array_verif_spkndk = array(
            "verifikasi_spkndk" => $spkndk->verifikasi,
            "notes_spkndk" => $spkndk->notes
        );
        // dd($array_verif);



        $db = DB::connection('centro')->table('view_efiling_header')->where('no_rekening', $no_kontrak)->first();
        //  $get = DB::connection('centro')->table('efiling')->where('no_rekening', $no_kontrak);
        // dd($get);
        return response()->json([
            'data'   =>
            array(
                "header_efiling" => $db, "efilling" => $efilling,
                // "bichecking" => $bichecking,
                "efilling_bichecking" => array_merge($bi_merge, $array_verif_bichecking), "efilling_ca" => array_merge($ca_merge, $array_verif_ca), "efilling_legal" => array_merge($legal_merge, $array_verif_legal), "efilling_nasabah" => array_merge($nas_merge, $array_verif_nasabah), "efilling_jaminan" => array_merge($jaminan_merge, $array_verif_jaminan), "efilling_foto" => array_merge($foto_merge, $array_verif_foto), "efilling_permohonan" => array_merge($permohonan_merge, $array_verif_permohonan), "efilling_aset" => array_merge($asset_merge, $array_verif_asset), "efilling_spkndk" => array_merge($spk_merge, $array_verif_spkndk)
            )
        ], 200);
    }

    public function update($no_kontrak, Request $req)
    {
        $pic = $req->pic;

        $get_ef = Efilling::where('no_rekening', $no_kontrak)->first();
        // dd($get_ef);
        $get_nas = EfillingNasabah::where('no_rekening', $no_kontrak)->first();
        $get_kre = EfillingPermohonan::where('no_rekening', $no_kontrak)->first();
        $get_by = Efilling_bi::where('no_rekening', $no_kontrak)->first();

        $check_ktp_deb = Debitur::select('calon_debitur.no_ktp')->join('trans_so', 'trans_so.id_calon_debitur', 'calon_debitur.id')->where('trans_so.no_rekening_kre', $no_kontrak)->first();
        //dd($check_ktp->no_ktp);
        //start GET Data Debitur
        $check_ktp      = $get_nas->ktp;
        $str1 = str_replace('["', '', $check_ktp);
        $str2 = str_replace('"]', '', $str1);

        $check_npwp      = $get_nas->npwp;
        $np1 = str_replace('["', '', $check_npwp);
        $np2 = str_replace('"]', '', $np1);
        $check_kk      = $get_nas->kk;
        $k1 = str_replace('["', '', $check_kk);
        $k2 = str_replace('"]', '', $k1);

        $check_domisili = $get_nas->domisili;
        $dom1 = str_replace('["', '', $check_domisili);
        $dom2 = str_replace('"]', '', $dom1);

        $check_surat_nikah      = $get_nas->surat_nikah;
        $nik1 = str_replace('["', '', $check_surat_nikah);
        $nik2 = str_replace('"]', '', $nik1);

        $check_surat_lahir      = $get_nas->surat_lahir;
        $lah1 = str_replace('["', '', $check_surat_lahir);
        $lah2 = str_replace('"]', '', $lah1);

        $check_surat_cerai      = $get_nas->surat_cerai;
        $cer1 = str_replace('["', '', $check_surat_cerai);
        $cer2 = str_replace('"]', '', $cer1);

        $check_surat_kematian = $get_nas->surat_kematian;
        $kem1 = str_replace('["', '', $check_surat_kematian);
        $kem2 = str_replace('"]', '', $kem1);

        $check_skd = $get_nas->skd;
        $skd1 = str_replace('["', '', $check_skd);
        $skd2 = str_replace('"]', '', $skd1);

        $check_takeover = $get_nas->take_over;
        $tak1 = str_replace('["', '', $check_takeover);
        $tak2 = str_replace('"]', '', $tak1);

        $check_rekeningkoran = $get_nas->rek_koran;
        $rek1 = str_replace('["', '', $check_rekeningkoran);
        $rek2 = str_replace('"]', '', $rek1);

        $check_tdp = $get_nas->tdp;
        $td1 = str_replace('["', '', $check_tdp);
        $td2 = str_replace('"]', '', $td1);

        $check_bon = $get_nas->bon_usaha;
        $bon1 = str_replace('["', '', $check_bon);
        $bon2 = str_replace('"]', '', $bon1);

        $check_slip = $get_nas->slip_gaji;
        $sli1 = str_replace('["', '', $check_slip);
        $sli2 = str_replace('"]', '', $sli1);

        $check_skk = $get_nas->sk_kerja;
        $skk1 = str_replace('["', '', $check_skk);
        $skk2 = str_replace('"]', '', $skk1);

        $check_sku = $get_nas->sk_usaha;
        $sku1 = str_replace('["', '', $check_sku);
        $sku2 = str_replace('"]', '', $sku1);

        //str_replace(',','',$check_ktp);
        //   dd($check_ktp_deb->no_ktp);

        //end GET Data Debitur


        ////////////////////////////////////////////////////////////////////////////////////////////////
        //KELENGKAPAN NASABAH
        // if ($file = $req->file('lampiran_ktp')) {
        //     $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur';
        //     $name       = 'ktp.';
        //     $check_file = 'null';

        //     $ktp = Helper::uploadImg($check_file, $file, $path, $name);
        // } else {
        //     $ktp = null;
        // }
        if ($files = $req->file('lampiran_ktp')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur';
            $name = 'E-FILLING_KTP_ALL';
            //. '-' . Carbon::now();
            $check = $str2;
            foreach ($files as $file) {
                $arrayPath[] = Helper::uploadImg($check, $file, $path, $name);
            }


            $imp = implode("", $arrayPath);
            //  dd($str2, $imp);
if(empty($str2)) {
  $siku = array($imp);
} else {
  $siku = array($str2,$imp);
}
           

            $search = "\\";
            $replace = '';
            array_walk(
                $siku,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );

            $ktp = str_replace("[", "", $siku);
        } else {
            $ktp = str_replace("\\", "", $check_ktp);
            //$ktp = $check_ktp;
        }
        //dd($ktp);
        if ($files = $req->file('lampiran_npwp')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur';
            $name = 'E-FILLING_NPWP';
            $check = $np2;
            foreach ($files as $file) {
                $arrayPath2[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp2 = implode("", $arrayPath2);
            // dd($np2, $imp2);
if(empty($np2)) {
  $siku2 = array($imp2);
} else {
   $siku2 = array($np2, $imp2);
}
           

            $search = "\\";
            $replace = '';
            array_walk(
                $siku2,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $npwp = str_replace("\\", "", $siku2);
        } else {
            $npwp = str_replace("\\", "", $check_npwp);
        }
        //  dd($npwp);
        if ($files = $req->file('lampiran_kk')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur';
            $name = 'E-FILLING_KK';
            $check = $k2;
            foreach ($files as $file) {
                $arrayPath3[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp3 = implode("", $arrayPath3);
if(empty($k2)) {
  $siku3 = array($imp3);
} else {
   $siku3 = array($k2, $imp3);
}
            $search = "\\";
            $replace = '';
            array_walk(
                $siku3,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $kk = str_replace("\\", "", $siku3);
        } else {
            $kk = str_replace("\\", "", $check_kk);
        }

        if ($files = $req->file('surat_nikah')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur';
            $name = 'E-FILLING_SURATNIKAH';
            $check = $nik2;
            foreach ($files as $file) {
                $arrayPath4[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp4 = implode("", $arrayPath4);
if(empty($nik2)) {
  $siku4 = array($imp4);
} else {
   $siku4 = array($nik2, $imp4);
}
            $search = "\\";
            $replace = '';
            array_walk(
                $siku4,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $nikah = str_replace("\\", "", $siku4);
        } else {
            $nikah = str_replace("\\", "", $check_surat_nikah);
        }

        if ($files = $req->file('surat_lahir')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur';
            $name = 'E-FILLING_SURATLAHIR';
            $check = $lah2;
            foreach ($files as $file) {
                $arrayPath5[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp5 = implode("", $arrayPath5);
if(empty($lah2)) {
  $siku5 = array($imp2);
} else {
   $siku5 = array($lah2, $imp5);
}
            $search = "\\";
            $replace = '';
            array_walk(
                $siku5,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $lahir = str_replace("\\", "", $siku5);
        } else {
            $lahir = str_replace("\\", "", $check_surat_lahir);
        }
        if ($files = $req->file('surat_kematian')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur';
            $name = 'E-FILLING_SURATKEMATIAN';
            $check = $kem2;
            foreach ($files as $file) {
                $arrayPath6[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp6 = implode("", $arrayPath6);
if(empty($kem2)) {
 $siku6 = array($imp6);
} else {
 $siku6 = array($kem2,$imp6);
}
           
            $search = "\\";
            $replace = '';
            array_walk(
                $siku6,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $kematian = str_replace("\\", "", $siku6);
        } else {
            $kematian = str_replace("\\", "", $check_surat_kematian);
        }

        if ($files = $req->file('slipgaji')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur';
            $name = 'E-FILLING_SLIPGAJI';
            $check = $sli2;
            foreach ($files as $file) {
                $arrayPath7[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp7 = implode("", $arrayPath7);
if(empty($sli2)) {
  $siku7 = array($imp7);
} else {
   $siku7 = array($sli2, $imp7);
}
            $search = "\\";
            $replace = '';
            array_walk(
                $siku7,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $slipgaji = str_replace("\\", "", $siku7);
        } else {
            $slipgaji = str_replace("\\", "", $check_slip);
        }
        if ($files = $req->file('skk')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur';
            $name = 'E-FILLING_SKK';
            $check = $skk2;
            foreach ($files as $file) {
                $arrayPath8[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp8 = implode("", $arrayPath8);
if(empty($skk2)) {
  $siku8 = array($imp8);
} else {
   $siku8 = array($skk2, $imp8);
}
            $search = "\\";
            $replace = '';
            array_walk(
                $siku8,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $skk = str_replace("\\", "", $siku8);
        } else {
            $skk = str_replace("\\", "", $check_skk);
        }

        if ($files = $req->file('sku')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur';
            $name = 'E-FILLING_SKU';
            $check = $sku2;
            foreach ($files as $file) {
                $arrayPath9[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp9 = implode("", $arrayPath9);
if(empty($sku2)) {
  $siku9 = array($imp9);
} else {
   $siku9 = array($sku2, $imp9);
}
           
            $search = "\\";
            $replace = '';
            array_walk(
                $siku9,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $sku = str_replace("\\", "", $siku9);
        } else {
            $sku = str_replace("\\", "", $check_sku);
        }

        if ($files = $req->file('skd')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur';
            $name = 'E-FILLING_SKD';
            $check = $skd2;
            foreach ($files as $file) {
                $arrayPath10[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp10 = implode("", $arrayPath10);
if(empty($skd2)) {
  $siku10 = array($imp10);
} else {
   $siku10 = array($skd2, $imp10);
}
            $search = "\\";
            $replace = '';
            array_walk(
                $siku10,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $skd = str_replace("\\", "", $siku10);
        } else {
            $skd = str_replace("\\", "", $check_skd);
        }

        if ($files = $req->file('take_over')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur';
            $name = 'E-FILLING_TAKEOVER';
            $check = $tak2;
            foreach ($files as $file) {
                $arrayPath11[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp11 = implode("", $arrayPath11);
if(empty($tak2)) {
  $siku11 = array($imp11);
} else {
   $siku11 = array($tak2, $imp11);
}
           
            $search = "\\";
            $replace = '';
            array_walk(
                $siku11,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $takeover = str_replace("\\", "", $siku11);
        } else {
            $takeover = str_replace("\\", "", $check_takeover);
        }

        if ($files = $req->file('domisili')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur';
            $name = 'E-FILLING_DOMISILI';
            $check = $dom2;
            foreach ($files as $file) {
                $arrayPath12[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp12 = implode("", $arrayPath12);
if(empty($dom2)) {
  $siku12 = array($imp12);
} else {
   $siku12 = array($dom2, $imp12);
}
           
            $search = "\\";
            $replace = '';
            array_walk(
                $siku12,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $domisili = str_replace("\\", "", $siku12);
        } else {
            $domisili = str_replace("\\", "", $check_domisili);
        }

        if ($files = $req->file('rek_koran')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur';
            $name = 'E-FILLING_REKENINGKORAN';
            $check = $rek2;
            foreach ($files as $file) {
                $arrayPath13[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp13 = implode("", $arrayPath13);
if(empty($rek2)) {
  $siku13 = array($imp13);
} else {
   $siku13 = array($rek2, $imp13);
}
          
            $search = "\\";
            $replace = '';
            array_walk(
                $siku13,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $rekeningkoran = str_replace("\\", "", $siku13);
        } else {
            $rekeningkoran = str_replace("\\", "", $check_rekeningkoran);
        }

        if ($files = $req->file('tdp')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur';
            $name = 'E-FILLING_TDP';
            $check = $td2;
            foreach ($files as $file) {
                $arrayPath14[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp14 = implode("", $arrayPath14);
if(empty($td2)) {
  $siku14 = array($imp14);
} else {
   $siku14 = array($td2, $imp14);
}
            
            $search = "\\";
            $replace = '';
            array_walk(
                $siku14,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $tdp = str_replace("\\", "", $siku14);
        } else {
            $tdp = str_replace("\\", "", $check_tdp);
        }

        if ($files = $req->file('bon_usaha')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur';
            $name = 'E-FILLING_BONUSAHA';
            $check = $bon2;
            foreach ($files as $file) {
                $arrayPath15[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp15 = implode("", $arrayPath15);
if(empty($bon2)) {
  $siku15 = array($imp15);
} else {
   $siku15 = array($bon2, $imp15);
}
           
            $search = "\\";
            $replace = '';
            array_walk(
                $siku15,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $bon = str_replace("\\", "", $siku15);
        } else {
            $bon = str_replace("\\", "", $check_bon);
        }

        if ($files = $req->file('surat_cerai')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur';
            $name = 'E-FILLING_SURATCERAI';
            $check = $cer2;
            foreach ($files as $file) {
                $arrayPath16[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp16 = implode("", $arrayPath16);
if(empty($cer2)) {
  $siku16 = array($imp16);
} else {
   $siku16 = array($cer2, $imp16);
}
            
            $search = "\\";
            $replace = '';
            array_walk(
                $siku16,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $cerai = str_replace("\\", "", $siku16);
        } else {
            $cerai = str_replace("\\", "", $check_surat_cerai);
        }


        ///////////////////////////////////////////////////////////////////////////////////////////////////
        //PERMOHONAN KREDIT
        $check_apskre = $get_kre->aplikasi;
        $ap1 = str_replace('["', '', $check_apskre);
        $aps2 = str_replace('"]', '', $ap1);

        $check_denah = $get_kre->denah_lokasi;
        $den1 = str_replace('["', '', $check_denah);
        $den2 = str_replace('"]', '', $den1);

        $check_cekliskelengkapan = $get_kre->checklist_kelengkapan;
        $kel1 = str_replace('["', '', $check_cekliskelengkapan);
        $kel2 = str_replace('"]', '', $kel1);

        if ($files = $req->file('aplikasi_kredit')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur/kredit';
            $name = 'E-FILLING_APLIKASIKREDIT';
            $check = $aps2;
            foreach ($files as $file) {
                $arrayPath17[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp17 = implode("", $arrayPath17);
            $siku17 = array($aps2, $imp17);
            $search = "\\";
            $replace = '';
            array_walk(
                $siku17,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $aps = str_replace("\\", "", $siku17);
        } else {
            $aps = str_replace("\\", "", $check_apskre);
        }

        if ($files = $req->file('denah_lokasi')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur/kredit';
            $name = 'E-FILLING_DENAHLOKASI';
            $check = $den2;
            foreach ($files as $file) {
                $arrayPath18[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp18 = implode("", $arrayPath18);
            $siku18 = array($den2, $imp18);
            $search = "\\";
            $replace = '';
            array_walk(
                $siku18,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $denah = str_replace("\\", "", $siku18);
        } else {
            $denah = str_replace("\\", "", $check_denah);
        }

        if ($files = $req->file('checklist_kelengkapan')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur/kredit';
            $name = 'E-FILLING_CHECKLISTKELENGKAPAN';
            $check = $kel2;
            foreach ($files as $file) {
                $arrayPath19[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp19 = implode("", $arrayPath19);
            $siku19 = array($kel2, $imp19);
            $search = "\\";
            $replace = '';
            array_walk(
                $siku19,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $kelengkapan = str_replace("\\", "", $siku19);
        } else {
            $kelengkapan = str_replace("\\", "", $check_cekliskelengkapan);
        }
        ###############################################################################################
       
        ###############################################################################################
        $get_bi_pengajuan = Efilling_bi::where('no_rekening', $no_kontrak)->first();

        $check_pengajuanbi      = $get_bi_pengajuan->pengajuan_bi;
        $pengbi1 = str_replace('["', '', $check_pengajuanbi);
        $pengbi2 = str_replace('"]', '', $pengbi1);

        $check_persetujuanbi      = $get_bi_pengajuan->persetujuan;
        $persetujuanbi1 = str_replace('["', '', $check_persetujuanbi);
        $persetujuanbi2 = str_replace('"]', '', $persetujuanbi1);

        $check_hasilbi      = $get_bi_pengajuan->pengajuan_bi;
        $hasilbi1 = str_replace('["', '', $check_hasilbi);
        $hasilbi2 = str_replace('"]', '', $hasilbi1);

        if ($files = $req->file('lampiran_pengajuan_bi')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur/PengajuanBI';
            $name = 'E-FILLING_PENGAJUAN_BI';
            $check = $pengbi2;
            foreach ($files as $file) {
                $arrayPath26[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp26 = implode("", $arrayPath26);
            $siku26 = array($pengbi2, $imp26);
            $search = "\\";
            $replace = '';
            array_walk(
                $siku26,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $lampiran_pengajuan_bi = str_replace("\\", "", $siku26);
        } else {
            $lampiran_pengajuan_bi = str_replace("\\", "", $check_pengajuanbi);
        }

        if ($files = $req->file('lampiran_persetujuan_bi')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur/PengajuanBI';
            $name = 'E-FILLING_PERSETUJUAN_BI';
            $check = $persetujuanbi2;
            foreach ($files as $file) {
                $arrayPath27[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp27 = implode("", $arrayPath27);
            $siku27 = array($persetujuanbi2, $imp27);
            $search = "\\";
            $replace = '';
            array_walk(
                $siku27,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $lampiran_persetujuanbi = str_replace("\\", "", $siku27);
        } else {
            $lampiran_persetujuanbi = str_replace("\\", "", $check_persetujuanbi);
        }

        if ($files = $req->file('lampiran_hasil_bi')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur/PengajuanBI';
            $name = 'E-FILLING_HASIL_BI';
            $check = $hasilbi2;
            foreach ($files as $file) {
                $arrayPath28[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp28 = implode("", $arrayPath28);
            $siku28 = array($hasilbi2, $imp28);
            $search = "\\";
            $replace = '';
            array_walk(
                $siku28,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $lampiran_hasilbi = str_replace("\\", "", $siku28);
        } else {
            $lampiran_hasilbi = str_replace("\\", "", $check_hasilbi);
        }
        ###############################################################################################
        $get_caa = Efilling_ca::where('no_rekening', $no_kontrak)->first();

        $check_memoao      = $get_caa->memo_ao;
        $memoao1 = str_replace('["', '', $check_memoao);
        $memoao2 = str_replace('"]', '', $memoao1);

        $check_memoca      = $get_caa->memo_ca;
        $memoca1 = str_replace('["', '', $check_memoca);
        $memoca2 = str_replace('"]', '', $memoca1);

        $check_ol      = $get_caa->offering_letter;
        $ol1 = str_replace('["', '', $check_ol);
        $ol2 = str_replace('"]', '', $ol1);

        $check_nilaijaminan      = $get_caa->penilaian_jaminan;
        $nilaijaminan1 = str_replace('["', '', $check_nilaijaminan);
        $nilaijaminan2 = str_replace('"]', '', $nilaijaminan1);

        $check_cheklistsurvey      = $get_caa->cheklist_survey;
        $cheklistsurvey = str_replace('["', '', $check_cheklistsurvey);
        $cheklistsurvey2 = str_replace('"]', '', $cheklistsurvey);

        $check_appkredit      = $get_caa->persetujuan_kredit;
        $appkredit = str_replace('["', '', $check_appkredit);
        $appkredit2 = str_replace('"]', '', $appkredit);


        if ($files = $req->file('memo_ao')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur/CreditAnalist';
            $name = 'E-FILLING_MEMOAO';
            $check = $memoao2;
            foreach ($files as $file) {
                $arrayPath29[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp29 = implode("", $arrayPath29);
            $siku29 = array($memoao2, $imp29);
            $search = "\\";
            $replace = '';
            array_walk(
                $siku29,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $lampiran_memoao = str_replace("\\", "", $siku29);
        } else {
            $lampiran_memoao = str_replace("\\", "", $check_memoao);
        }

        if ($files = $req->file('memo_ca')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur/CreditAnalist';
            $name = 'E-FILLING_MEMOCA';
            $check = $memoca2;
            foreach ($files as $file) {
                $arrayPath30[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp30 = implode("", $arrayPath30);
            $siku30 = array($memoca2, $imp30);
            $search = "\\";
            $replace = '';
            array_walk(
                $siku30,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $lampiran_memoca = str_replace("\\", "", $siku30);
        } else {
            $lampiran_memoca = str_replace("\\", "", $check_memoca);
        }

        if ($files = $req->file('offering_letter')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur/CreditAnalist';
            $name = 'E-FILLING_OFFERINGLETTER';
            $check = $ol2;
            foreach ($files as $file) {
                $arrayPath31[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp31 = implode("", $arrayPath31);
            $siku31 = array($ol2, $imp31);
            $search = "\\";
            $replace = '';
            array_walk(
                $siku31,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $lampiran_ol = str_replace("\\", "", $siku31);
        } else {
            $lampiran_ol = str_replace("\\", "", $check_ol);
        }

        if ($files = $req->file('penilaian_jaminan')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur/CreditAnalist';
            $name = 'E-FILLING_NILAIJAMINAN';
            $check = $nilaijaminan2;
            foreach ($files as $file) {
                $arrayPath32[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp32 = implode("", $arrayPath32);
            $siku32 = array($nilaijaminan2, $imp32);
            $search = "\\";
            $replace = '';
            array_walk(
                $siku32,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $lampiran_nilaijaminan = str_replace("\\", "", $siku32);
        } else {
            $lampiran_nilaijaminan = str_replace("\\", "", $check_nilaijaminan);
        }

        if ($files = $req->file('cheklist_survey')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur/CreditAnalist';
            $name = 'E-FILLING_CHECKLISTSURVEY';
            $check = $cheklistsurvey2;
            foreach ($files as $file) {
                $arrayPath33[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp33 = implode("", $arrayPath33);
            $siku33 = array($cheklistsurvey2, $imp33);
            $search = "\\";
            $replace = '';
            array_walk(
                $siku33,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $lampiran_checksurvey = str_replace("\\", "", $siku33);
        } else {
            $lampiran_checksurvey = str_replace("\\", "", $check_cheklistsurvey);
        }

        if ($files = $req->file('persetujuan_kredit')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur/CreditAnalist';
            $name = 'E-FILLING_PERSETUJUANKREDIT';
            $check = $appkredit2;
            foreach ($files as $file) {
                $arrayPath34[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp34 = implode("", $arrayPath34);
            $siku34 = array($appkredit2, $imp34);
            $search = "\\";
            $replace = '';
            array_walk(
                $siku34,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $lampiran_appkredit = str_replace("\\", "", $siku34);
        } else {
            $lampiran_appkredit = str_replace("\\", "", $check_appkredit);
        }
        ###############################################################################################
        $get_foto = Efilling_foto::where('no_rekening', $no_kontrak)->first();

        $check_ftjaminan      = $get_foto->ft_jaminan;
        $ftjaminan1 = str_replace('["', '', $check_ftjaminan);
        $ftjaminan2 = str_replace('"]', '', $ftjaminan1);

        $check_ftpengikatan      = $get_foto->ft_pengikatan;
        $ftpengikatan1 = str_replace('["', '', $check_ftpengikatan);
        $ftpengikatan2 = str_replace('"]', '', $ftpengikatan1);

        $check_ftdomisili      = $get_foto->ft_domisili;
        $ftdomisili1 = str_replace('["', '', $check_ftdomisili);
        $ftdomisili2 = str_replace('"]', '', $ftdomisili1);

        $check_ftusaha      = $get_foto->ft_usaha;
        $ftusaha1 = str_replace('["', '', $check_ftusaha);
        $ftusaha2 = str_replace('"]', '', $ftusaha1);

        if ($files = $req->file('ft_jaminan')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur';
            $name = 'E-FILLING_FTJAMINAN';
            $check = $ftjaminan2;
            foreach ($files as $file) {
                $arrayPath35[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp35 = implode("", $arrayPath35);
            $siku35 = array($ftjaminan2, $imp35);
            $search = "\\";
            $replace = '';
            array_walk(
                $siku35,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $lampiran_ftjaminan = str_replace("\\", "", $siku35);
        } else {
            $lampiran_ftjaminan = str_replace("\\", "", $check_ftjaminan);
        }

        if ($files = $req->file('ft_pengikatan')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur';
            $name = 'E-FILLING_FTPENGIKATAN';
            $check = $ftpengikatan2;
            foreach ($files as $file) {
                $arrayPath36[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp36 = implode("", $arrayPath36);
            $siku36 = array($ftpengikatan2, $imp36);
            $search = "\\";
            $replace = '';
            array_walk(
                $siku36,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $lampiran_ftpengikatan = str_replace("\\", "", $siku36);
        } else {
            $lampiran_ftpengikatan = str_replace("\\", "", $check_ftpengikatan);
        }

        if ($files = $req->file('ft_domisili')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur';
            $name = 'E-FILLING_FTDOMISILI';
            $check = $ftdomisili2;
            foreach ($files as $file) {
                $arrayPath37[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp37 = implode("", $arrayPath37);
            $siku37 = array($ftdomisili2, $imp37);
            $search = "\\";
            $replace = '';
            array_walk(
                $siku37,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $lampiran_ftdomisili = str_replace("\\", "", $siku37);
        } else {
            $lampiran_ftdomisili = str_replace("\\", "", $check_ftdomisili);
        }

        if ($files = $req->file('ft_usaha')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur';
            $name = 'E-FILLING_FTUSAHA';
            $check = $ftusaha2;
            foreach ($files as $file) {
                $arrayPath38[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp38 = implode("", $arrayPath38);
            $siku38 = array($ftusaha2, $imp38);
            $search = "\\";
            $replace = '';
            array_walk(
                $siku38,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $lampiran_ftusaha = str_replace("\\", "", $siku38);
        } else {
            $lampiran_ftusaha = str_replace("\\", "", $check_ftusaha);
        }

        ################################################################################################
        $get_jaminan = EfillingJaminan::where('no_rekening', $no_kontrak)->first();

        $check_sertifikat      = $get_jaminan->sertifikat;
        $sertifikat1 = str_replace('["', '', $check_sertifikat);
        $sertifikat2 = str_replace('"]', '', $sertifikat1);

        $check_skmht      = $get_jaminan->skmht;
        $skmht1 = str_replace('["', '', $check_skmht);
        $skmht2 = str_replace('"]', '', $skmht1);

        $check_apht      = $get_jaminan->apht;
        $apht1 = str_replace('["', '', $check_apht);
        $apht2 = str_replace('"]', '', $apht1);

        $check_cabut_roya      = $get_jaminan->cabut_roya;
        $cabut_roya1 = str_replace('["', '', $check_cabut_roya);
        $cabut_roya2 = str_replace('"]', '', $cabut_roya1);

        $check_sht      = $get_jaminan->sht;
        $sht1 = str_replace('["', '', $check_sht);
        $sht2 = str_replace('"]', '', $sht1);

        $check_pbb      = $get_jaminan->pbb;
        $pbb1 = str_replace('["', '', $check_pbb);
        $pbb2 = str_replace('"]', '', $pbb1);

        $check_imb      = $get_jaminan->imb;
        $imb1 = str_replace('["', '', $check_imb);
        $imb2 = str_replace('"]', '', $imb1);

        $check_ajb      = $get_jaminan->ajb;
        $ajb1 = str_replace('["', '', $check_ajb);
        $ajb2 = str_replace('"]', '', $ajb1);

        $check_bpkb      = $get_jaminan->bpkb;
        $bpkb1 = str_replace('["', '', $check_bpkb);
        $bpkb2 = str_replace('"]', '', $bpkb1);

        $check_ahli_waris      = $get_jaminan->ahli_waris;
        $ahli_waris1 = str_replace('["', '', $check_ahli_waris);
        $ahli_waris2 = str_replace('"]', '', $ahli_waris1);

        $check_pengakuan_hutang      = $get_jaminan->pengakuan_hutang;
        $pengakuan_hutang1 = str_replace('["', '', $check_pengakuan_hutang);
        $pengakuan_hutang2 = str_replace('"]', '', $pengakuan_hutang1);

        $check_akta_pengakuan_hak_bersama      = $get_jaminan->akta_pengakuan_hak_bersama;
        $akta_pengakuan_hak_bersama1 = str_replace('["', '', $check_akta_pengakuan_hak_bersama);
        $akta_pengakuan_hak_bersama2 = str_replace('"]', '', $akta_pengakuan_hak_bersama1);

        $check_adendum      = $get_jaminan->adendum;
        $adendum1 = str_replace('["', '', $check_adendum);
        $adendum2 = str_replace('"]', '', $adendum1);

        $check_fidusia      = $get_jaminan->fidusia;
        $fidusia1 = str_replace('["', '', $check_fidusia);
        $fidusia2 = str_replace('"]', '', $fidusia1);

        if ($files = $req->file('sertifikat')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur/lpdk/lampiran/EFILLINGSertifikat';
            $name = 'E-FILLING_SERTIFIKAT';
            $check = $sertifikat2;
            foreach ($files as $file) {
                $arrayPath39[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp39 = implode("", $arrayPath39);
if(empty($sertifikat2)) {
 $siku39 = array($imp39);
} else {
 $siku39 = array($sertifikat2,$imp39);
}
           
            $search = "\\";
            $replace = '';
            array_walk(
                $siku39,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $lampiran_sertifikat = str_replace("\\", "", $siku39);
        } else {
            $lampiran_sertifikat = str_replace("\\", "", $check_sertifikat);
        }

        if ($files = $req->file('skmht')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur/lpdk/lampiran/EFILLINGSertifikat';
            $name = 'E-FILLING_SKMHT';
            $check = $skmht2;
            foreach ($files as $file) {
                $arrayPath100[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp100 = implode("", $arrayPath100);
if(empty($skmht2)) {
 $siku100 = array($imp100);
} else {
 $siku100 = array($skmht2,$imp100);
}
           
            $search = "\\";
            $replace = '';
            array_walk(
                $siku100,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $lampiran_skmht = str_replace("\\", "", $siku100);
        } else {
            $lampiran_skmht = str_replace("\\", "", $check_skmht);
        }

        if ($files = $req->file('apht')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur/lpdk/lampiran/EFILLINGSertifikat';
            $name = 'E-FILLING_APHT';
            $check = $apht2;
            foreach ($files as $file) {
                $arrayPath40[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp40 = implode("", $arrayPath40);
if(empty($apht2)) {
 $siku40 = array($imp40);
} else {
 $siku40 = array($apht2,$imp40);
}
           
            $search = "\\";
            $replace = '';
            array_walk(
                $siku40,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $lampiran_apht = str_replace("\\", "", $siku40);
        } else {
            $lampiran_apht = str_replace("\\", "", $check_apht);
        }

        if ($files = $req->file('cabut_roya')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur/lpdk/lampiran/EFILLINGSertifikat';
            $name = 'E-FILLING_CABUTROYA';
            $check = $cabut_roya2;
            foreach ($files as $file) {
                $arrayPath41[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp41 = implode("", $arrayPath41);
if(empty($cabut_roya2)) {
 $siku41 = array($imp41);
} else {
 $siku41 = array($cabut_roya2,$imp41);
}
          
            $search = "\\";
            $replace = '';
            array_walk(
                $siku41,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $lampiran_cabut_roya = str_replace("\\", "", $siku41);
        } else {
            $lampiran_cabut_roya = str_replace("\\", "", $check_cabut_roya);
        }

        if ($files = $req->file('sht')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur/lpdk/lampiran/EFILLINGSertifikat';
            $name = 'E-FILLING_SHT';
            $check = $sht2;
            foreach ($files as $file) {
                $arrayPath42[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp42 = implode("", $arrayPath42);
if(empty($sht2)) {
 $siku42 = array($imp42);
} else {
 $siku42 = array($sht2,$imp42);
}
           
            $search = "\\";
            $replace = '';
            array_walk(
                $siku42,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $lampiran_sht = str_replace("\\", "", $siku42);
        } else {
            $lampiran_sht = str_replace("\\", "", $check_sht);
        }

        if ($files = $req->file('pbb')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur/lpdk/lampiran/EFILLINGSertifikat';
            $name = 'E-FILLING_PBB';
            $check = $pbb2;
            foreach ($files as $file) {
                $arrayPath43[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp43 = implode("", $arrayPath43);
if(empty($pbb2)) {
 $siku43 = array($imp43);
} else {
 $siku43 = array($pbb2,$imp43);
}
           
            $search = "\\";
            $replace = '';
            array_walk(
                $siku43,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $lampiran_pbb = str_replace("\\", "", $siku43);
        } else {
            $lampiran_pbb = str_replace("\\", "", $check_pbb);
        }

        if ($files = $req->file('imb')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur/lpdk/lampiran/EFILLINGSertifikat';
            $name = 'E-FILLING_IMB';
            $check = $imb2;
            foreach ($files as $file) {
                $arrayPath44[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp44 = implode("", $arrayPath44);
if(empty($imb2)) {
 $siku44 = array($imp44);
} else {
 $siku44 = array($imb2,$imp44);
}
           
            $search = "\\";
            $replace = '';
            array_walk(
                $siku44,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $lampiran_imb = str_replace("\\", "", $siku44);
        } else {
            $lampiran_imb = str_replace("\\", "", $check_imb);
        }

        if ($files = $req->file('ajb')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur/lpdk/lampiran/EFILLINGSertifikat';
            $name = 'E-FILLING_AJB';
            $check = $ajb2;
            foreach ($files as $file) {
                $arrayPath45[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp45 = implode("", $arrayPath45);
if(empty($ajb2)) {
 $siku45 = array($imp45);
} else {
 $siku45 = array($ajb2,$imp45);
}
           
            $search = "\\";
            $replace = '';
            array_walk(
                $siku45,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $lampiran_ajb = str_replace("\\", "", $siku45);
        } else {
            $lampiran_ajb = str_replace("\\", "", $check_ajb);
        }

        if ($files = $req->file('bpkb')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur/lpdk/lampiran/EFILLINGSertifikat';
            $name = 'E-FILLING_BPKB';
            $check = $bpkb2;
            foreach ($files as $file) {
                $arrayPath46[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp46 = implode("", $arrayPath46);
if(empty($bpkb2)) {
 $siku46 = array($imp46);
} else {
 $siku46 = array($bpkb2,$imp46);
}
           
            $search = "\\";
            $replace = '';
            array_walk(
                $siku46,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $lampiran_bpkb = str_replace("\\", "", $siku46);
        } else {
            $lampiran_bpkb = str_replace("\\", "", $check_bpkb);
        }

        if ($files = $req->file('ahli_waris')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur/lpdk/lampiran/EFILLINGSertifikat';
            $name = 'E-FILLING_AHLIWARIS';
            $check = $ahli_waris2;
            foreach ($files as $file) {
                $arrayPath47[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp47 = implode("", $arrayPath47);
if(empty($ahli_waris2)) {
 $siku47 = array($imp47);
} else {
 $siku47 = array($ahli_waris2,$imp47);
}
            
            $search = "\\";
            $replace = '';
            array_walk(
                $siku47,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $lampiran_ahli_waris = str_replace("\\", "", $siku47);
        } else {
            $lampiran_ahli_waris = str_replace("\\", "", $check_ahli_waris);
        }

        if ($files = $req->file('pengakuan_hutang')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur/lpdk/lampiran/EFILLINGSertifikat';
            $name = 'E-FILLING_PENGAKUANHUTANG';
            $check = $pengakuan_hutang2;
            foreach ($files as $file) {
                $arrayPath48[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp48 = implode("", $arrayPath48);
if(empty($pengakuan_hutang2)) {
 $siku48 = array($imp48);
} else {
 $siku48 = array($pengakuan_hutang2,$imp48);
}
           
            $search = "\\";
            $replace = '';
            array_walk(
                $siku48,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $lampiran_pengakuan_hutang = str_replace("\\", "", $siku48);
        } else {
            $lampiran_pengakuan_hutang = str_replace("\\", "", $check_pengakuan_hutang);
        }

        if ($files = $req->file('akta_pengakuan_hak_bersama')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur/lpdk/lampiran/EFILLINGSertifikat';
            $name = 'E-FILLING_AKTAPENGAKUANHAKBERSAMA';
            $check = $akta_pengakuan_hak_bersama2;
            foreach ($files as $file) {
                $arrayPath49[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp49 = implode("", $arrayPath49);
if(empty($akta_pengakuan_hak_bersama2)) {
 $siku49 = array($imp49);
} else {
 $siku49 = array($akta_pengakuan_hak_bersama2,$imp49);
}
            
            $search = "\\";
            $replace = '';
            array_walk(
                $siku49,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $lampiran_akta_pengakuan_hak_bersama = str_replace("\\", "", $siku49);
        } else {
            $lampiran_akta_pengakuan_hak_bersama = str_replace("\\", "", $check_akta_pengakuan_hak_bersama);
        }

        if ($files = $req->file('adendum')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur/lpdk/lampiran/EFILLINGSertifikat';
            $name = 'E-FILLING_ADENDUM';
            $check = $adendum2;
            foreach ($files as $file) {
                $arrayPath50[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp50 = implode("", $arrayPath50);
if(empty($adendum2)) {
 $siku50 = array($imp50);
} else {
 $siku50 = array($adendum2,$imp50);
}
           
            $search = "\\";
            $replace = '';
            array_walk(
                $siku50,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $lampiran_adendum = str_replace("\\", "", $siku50);
        } else {
            $lampiran_adendum = str_replace("\\", "", $check_adendum);
        }

        if ($files = $req->file('fidusia')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur/lpdk/lampiran/EFILLINGSertifikat';
            $name = 'E-FILLING_FIDUSIA';
            $check = $fidusia2;
            foreach ($files as $file) {
                $arrayPath51[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp51 = implode("", $arrayPath51);
if(empty($fidusia2)) {
 $siku51 = array($imp51);
} else {
 $siku51 = array($fidusia2,$imp51);
}
            
            $search = "\\";
            $replace = '';
            array_walk(
                $siku51,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $lampiran_fidusia = str_replace("\\", "", $siku51);
        } else {
            $lampiran_fidusia = str_replace("\\", "", $check_fidusia);
        }
        ################################################################################################
        $get_legal = Efilling_legal::where('no_rekening', $no_kontrak)->first();

        $check_pengajuan_lpdk      = $get_legal->pengajuan_lpdk;
        $pengajuan_lpdk1 = str_replace('["', '', $check_pengajuan_lpdk);
        $pengajuan_lpdk2 = str_replace('"]', '', $pengajuan_lpdk1);

        $check_lpdk      = $get_legal->lpdk;
        $lpdk1 = str_replace('["', '', $check_lpdk);
        $lpdk2 = str_replace('"]', '', $lpdk1);

        $check_cheklist_pengikatan      = $get_legal->cheklist_pengikatan;
        $cheklist_pengikatan1 = str_replace('["', '', $check_cheklist_pengikatan);
        $cheklist_pengikatan2 = str_replace('"]', '', $cheklist_pengikatan1);

        $check_order_pengikatan      = $get_legal->order_pengikatan;
        $order_pengikatan1 = str_replace('["', '', $check_order_pengikatan);
        $order_pengikatan2 = str_replace('"]', '', $order_pengikatan1);

        if ($files = $req->file('pengajuan_lpdk')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur/EFILLINGLEGAL';
            $name = 'E-FILLING_PENGAJUAN_LPDK';
            $check = $pengajuan_lpdk2;
            foreach ($files as $file) {
                $arrayPath52[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp52 = implode("", $arrayPath52);
            $siku52 = array($pengajuan_lpdk2, $imp52);
            $search = "\\";
            $replace = '';
            array_walk(
                $siku52,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $lampiran_pengajuan_lpdk = str_replace("\\", "", $siku52);
        } else {
            $lampiran_pengajuan_lpdk = str_replace("\\", "", $check_pengajuan_lpdk);
        }

        if ($files = $req->file('lpdk')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur/EFILLINGLEGAL';
            $name = 'E-FILLING_LPDK';
            $check = $lpdk2;
            foreach ($files as $file) {
                $arrayPath53[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp53 = implode("", $arrayPath53);
            $siku53 = array($lpdk2, $imp53);
            $search = "\\";
            $replace = '';
            array_walk(
                $siku53,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $lampiran_lpdk = str_replace("\\", "", $siku53);
        } else {
            $lampiran_lpdk = str_replace("\\", "", $check_lpdk);
        }

        if ($files = $req->file('cheklist_pengikatan')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur/EFILLINGLEGAL';
            $name = 'E-FILLING_cheklist_pengikatan';
            $check = $cheklist_pengikatan2;
            foreach ($files as $file) {
                $arrayPath54[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp54 = implode("", $arrayPath54);
            $siku54 = array($cheklist_pengikatan2, $imp54);
            $search = "\\";
            $replace = '';
            array_walk(
                $siku54,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $lampiran_cheklist_pengikatan = str_replace("\\", "", $siku54);
        } else {
            $lampiran_cheklist_pengikatan = str_replace("\\", "", $check_cheklist_pengikatan);
        }

        if ($files = $req->file('order_pengikatan')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur/EFILLINGLEGAL';
            $name = 'E-FILLING_order_pengikatan';
            $check = $order_pengikatan2;
            foreach ($files as $file) {
                $arrayPath55[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp55 = implode("", $arrayPath55);
            $siku55 = array($order_pengikatan2, $imp55);
            $search = "\\";
            $replace = '';
            array_walk(
                $siku55,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $lampiran_order_pengikatan = str_replace("\\", "", $siku55);
        } else {
            $lampiran_order_pengikatan = str_replace("\\", "", $check_order_pengikatan);
        }
        #######################################################################################################
        $get_asset = Efilling_asset::where('no_rekening', $no_kontrak)->first();

        $check_ra_tanda_terima      = $get_asset->ra_tanda_terima;
        $ra_tanda_terima1 = str_replace('["', '', $check_ra_tanda_terima);
        $ra_tanda_terima2 = str_replace('"]', '', $ra_tanda_terima1);

        $check_ra_surat_kuasa      = $get_asset->ra_surat_kuasa;
        $ra_surat_kuasa1 = str_replace('["', '', $check_ra_surat_kuasa);
        $ra_surat_kuasa2 = str_replace('"]', '', $ra_surat_kuasa1);

        $check_ra_identitas_pengambilan      = $get_asset->ra_identitas_pengambilan;
        $ra_identitas_pengambilan1 = str_replace('["', '', $check_ra_identitas_pengambilan);
        $ra_identitas_pengambilan2 = str_replace('"]', '', $ra_identitas_pengambilan1);

        $check_ra_lainnya      = $get_asset->ra_lainnya;
        $ra_lainnya1 = str_replace('["', '', $check_ra_lainnya);
        $ra_lainnya2 = str_replace('"]', '', $ra_lainnya1);

        $check_ra_serah_terima      = $get_asset->ra_serah_terima;
        $ra_serah_terima1 = str_replace('["', '', $check_ra_serah_terima);
        $ra_serah_terima2 = str_replace('"]', '', $ra_serah_terima1);

        if ($files = $req->file('ra_tanda_terima')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur/EFILLINGASSET';

            $name = 'E-FILLING_TANDATERIMA';
            $check = $ra_tanda_terima2;
            foreach ($files as $file) {
                $arrayPath56[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp56 = implode("", $arrayPath56);
            $siku56 = array($ra_tanda_terima2, $imp56);
            $search = "\\";
            $replace = '';
            array_walk(
                $siku56,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $lampiran_ra_tanda_terima = str_replace("\\", "", $siku56);
        } else {
            $lampiran_ra_tanda_terima = str_replace("\\", "", $check_ra_tanda_terima);
        }

        if ($files = $req->file('ra_surat_kuasa')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur/EFILLINGASSET';
            $name = 'E-FILLING_SURATKUASA';
            $check = $ra_surat_kuasa2;
            foreach ($files as $file) {
                $arrayPath57[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp57 = implode("", $arrayPath57);
            $siku57 = array($ra_surat_kuasa2, $imp57);
            $search = "\\";
            $replace = '';
            array_walk(
                $siku57,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $lampiran_ra_surat_kuasa = str_replace("\\", "", $siku57);
        } else {
            $lampiran_ra_surat_kuasa = str_replace("\\", "", $check_ra_surat_kuasa);
        }

        if ($files = $req->file('ra_identitas_pengambilan')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur/EFILLINGASSET';
            $name = 'E-FILLING_IDENTITASPENGAMBILAN';
            $check = $ra_identitas_pengambilan2;
            foreach ($files as $file) {
                $arrayPath58[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp58 = implode("", $arrayPath58);
            $siku58 = array($ra_identitas_pengambilan2, $imp58);
            $search = "\\";
            $replace = '';
            array_walk(
                $siku58,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $lampiran_ra_identitas_pengambilan = str_replace("\\", "", $siku58);
        } else {
            $lampiran_ra_identitas_pengambilan = str_replace("\\", "", $check_ra_identitas_pengambilan);
        }

        if ($files = $req->file('ra_lainnya')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur/EFILLINGASSET';
            $name = 'E-FILLING_LAINNYA';
            $check = $ra_lainnya2;
            foreach ($files as $file) {
                $arrayPath59[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp59 = implode("", $arrayPath59);
            $siku59 = array($ra_lainnya2, $imp59);
            $search = "\\";
            $replace = '';
            array_walk(
                $siku59,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $lampiran_ra_lainnya = str_replace("\\", "", $siku59);
        } else {
            $lampiran_ra_lainnya = str_replace("\\", "", $check_ra_lainnya);
        }

        if ($files = $req->file('ra_serah_terima')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur/EFILLINGASSET';
            $name = 'E-FILLING_SERAHTERIMA';
            $check = $ra_serah_terima2;
            foreach ($files as $file) {
                $arrayPath60[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp60 = implode("", $arrayPath60);
            $siku60 = array($ra_serah_terima2, $imp60);
            $search = "\\";
            $replace = '';
            array_walk(
                $siku60,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $lampiran_ra_serah_terima = str_replace("\\", "", $siku60);
        } else {
            $lampiran_ra_serah_terima = str_replace("\\", "", $check_ra_serah_terima);
        }
        ###############################################################################################
        $get_spkndk = Efilling_spkndk::where('no_rekening', $no_kontrak)->first();

        $check_spk_ndk      = $get_spkndk->spk_ndk;
        $spk_ndk1 = str_replace('["', '', $check_spk_ndk);
        $spk_ndk2 = str_replace('"]', '', $spk_ndk1);

        $check_asuransi      = $get_spkndk->asuransi;
        $asuransi1 = str_replace('["', '', $check_asuransi);
        $asuransi2 = str_replace('"]', '', $asuransi1);

        $check_sp_no_imb      = $get_spkndk->sp_no_imb;
        $sp_no_imb1 = str_replace('["', '', $check_sp_no_imb);
        $sp_no_imb2 = str_replace('"]', '', $sp_no_imb1);

        $check_jadwal_angsuran      = $get_spkndk->jadwal_angsuran;
        $jadwal_angsuran1 = str_replace('["', '', $check_jadwal_angsuran);
        $jadwal_angsuran2 = str_replace('"]', '', $jadwal_angsuran1);

        $check_personal_guarantee      = $get_spkndk->personal_guarantee;
        $personal_guarantee1 = str_replace('["', '', $check_personal_guarantee);
        $personal_guarantee2 = str_replace('"]', '', $personal_guarantee1);


        $check_hold_dana      = $get_spkndk->hold_dana;
        $hold_dana1 = str_replace('["', '', $check_hold_dana);
        $hold_dana2 = str_replace('"]', '', $hold_dana1);

        $check_surat_transfer      = $get_spkndk->surat_transfer;
        $surat_transfer1 = str_replace('["', '', $check_surat_transfer);
        $surat_transfer2 = str_replace('"]', '', $surat_transfer1);

        $check_keabsahan_data      = $get_spkndk->keabsahan_data;
        $keabsahan_data1 = str_replace('["', '', $check_keabsahan_data);
        $keabsahan_data2 = str_replace('"]', '', $keabsahan_data1);

        $check_sp_beda_jt_tempo      = $get_spkndk->sp_beda_jt_tempo;
        $sp_beda_jt_tempo1 = str_replace('["', '', $check_sp_beda_jt_tempo);
        $sp_beda_jt_tempo2 = str_replace('"]', '', $sp_beda_jt_tempo1);

        $check_sp_authentic      = $get_spkndk->sp_authentic;
        $sp_authentic1 = str_replace('["', '', $check_sp_authentic);
        $sp_authentic2 = str_replace('"]', '', $sp_authentic1);

        $check_sp_penyerahan_jaminan      = $get_spkndk->sp_penyerahan_jaminan;
        $sp_penyerahan_jaminan1 = str_replace('["', '', $check_sp_penyerahan_jaminan);
        $sp_penyerahan_jaminan2 = str_replace('"]', '', $sp_penyerahan_jaminan1);

        $check_surat_aksep      = $get_spkndk->surat_aksep;
        $surat_aksep1 = str_replace('["', '', $check_surat_aksep);
        $surat_aksep2 = str_replace('"]', '', $surat_aksep1);

        $check_tt_uang      = $get_spkndk->tt_uang;
        $tt_uang1 = str_replace('["', '', $check_tt_uang);
        $tt_uang2 = str_replace('"]', '', $tt_uang1);

        $check_sp_pendebetan_rekening      = $get_spkndk->sp_pendebetan_rekening;
        $sp_pendebetan_rekening1 = str_replace('["', '', $check_sp_pendebetan_rekening);
        $sp_pendebetan_rekening2 = str_replace('"]', '', $sp_pendebetan_rekening1);

        $check_sp_plang      = $get_spkndk->sp_plang;
        $sp_plang1 = str_replace('["', '', $check_sp_plang);
        $sp_plang2 = str_replace('"]', '', $sp_plang1);

        $check_hal_penting      = $get_spkndk->hal_penting;
        $hal_penting1 = str_replace('["', '', $check_hal_penting);
        $hal_penting2 = str_replace('"]', '', $hal_penting1);

        $check_restruktur_bunga_denda      = $get_spkndk->restruktur_bunga_denda;
        $restruktur_bunga_denda1 = str_replace('["', '', $check_restruktur_bunga_denda);
        $restruktur_bunga_denda2 = str_replace('"]', '', $restruktur_bunga_denda1);

        $check_spajk_spa_fpk      = $get_spkndk->spajk_spa_fpk;
        $spajk_spa_fpk1 = str_replace('["', '', $check_spajk_spa_fpk);
        $spajk_spa_fpk2 = str_replace('"]', '', $spajk_spa_fpk1);

        if ($files = $req->file('spk_ndk')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur/EFILLINGSPKNDK';
            $name = 'E-FILLING_SPKNDK';
            $check = $spk_ndk2;
            foreach ($files as $file) {
                $arrayPath61[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp61 = implode("", $arrayPath61);
            $siku61 = array($spk_ndk2, $imp61);
            $search = "\\";
            $replace = '';
            array_walk(
                $siku61,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $lampiran_spk_ndk = str_replace("\\", "", $siku61);
        } else {
            $lampiran_spk_ndk = str_replace("\\", "", $check_spk_ndk);
        }

        if ($files = $req->file('asuransi')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur/EFILLINGSPKNDK';
            $name = 'E-FILLING_ASURANSI';
            $check = $asuransi2;
            foreach ($files as $file) {
                $arrayPath62[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp62 = implode("", $arrayPath62);
            $siku62 = array($asuransi2, $imp62);
            $search = "\\";
            $replace = '';
            array_walk(
                $siku62,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $lampiran_asuransi = str_replace("\\", "", $siku62);
        } else {
            $lampiran_asuransi = str_replace("\\", "", $check_asuransi);
        }

        if ($files = $req->file('sp_no_imb')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur/EFILLINGSPKNDK';
            $name = 'E-FILLING_SPNOIMB';
            $check = $sp_no_imb2;
            foreach ($files as $file) {
                $arrayPath63[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp63 = implode("", $arrayPath63);
            $siku63 = array($sp_no_imb2, $imp63);
            $search = "\\";
            $replace = '';
            array_walk(
                $siku63,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $lampiran_sp_no_imb = str_replace("\\", "", $siku63);
        } else {
            $lampiran_sp_no_imb = str_replace("\\", "", $check_sp_no_imb);
        }

        if ($files = $req->file('jadwal_angsuran')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur/EFILLINGSPKNDK';
            $name = 'E-FILLING_JADWALANGSURAN';
            $check = $jadwal_angsuran2;
            foreach ($files as $file) {
                $arrayPath64[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp64 = implode("", $arrayPath64);
            $siku64 = array($jadwal_angsuran2, $imp64);
            $search = "\\";
            $replace = '';
            array_walk(
                $siku64,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $lampiran_jadwal_angsuran = str_replace("\\", "", $siku64);
        } else {
            $lampiran_jadwal_angsuran = str_replace("\\", "", $check_jadwal_angsuran);
        }

        if ($files = $req->file('personal_guarantee')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur/EFILLINGSPKNDK';
            $name = 'E-FILLING_GUARANTEE';
            $check = $personal_guarantee2;
            foreach ($files as $file) {
                $arrayPath65[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp65 = implode("", $arrayPath65);
            $siku65 = array($personal_guarantee2, $imp65);
            $search = "\\";
            $replace = '';
            array_walk(
                $siku65,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $lampiran_personal_guarantee = str_replace("\\", "", $siku65);
        } else {
            $lampiran_personal_guarantee = str_replace("\\", "", $check_personal_guarantee);
        }

        if ($files = $req->file('hold_dana')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur/EFILLINGSPKNDK';
            $name = 'E-FILLING_HOLDDANA';
            $check = $hold_dana2;
            foreach ($files as $file) {
                $arrayPath66[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp66 = implode("", $arrayPath66);
            $siku66 = array($hold_dana2, $imp66);
            $search = "\\";
            $replace = '';
            array_walk(
                $siku66,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $lampiran_hold_dana = str_replace("\\", "", $siku66);
        } else {
            $lampiran_hold_dana = str_replace("\\", "", $check_hold_dana);
        }

        if ($files = $req->file('surat_transfer')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur/EFILLINGSPKNDK';
            $name = 'E-FILLING_SURATTRANSFER';
            $check = $surat_transfer2;
            foreach ($files as $file) {
                $arrayPath67[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp67 = implode("", $arrayPath67);
            $siku67 = array($surat_transfer2, $imp67);
            $search = "\\";
            $replace = '';
            array_walk(
                $siku67,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $lampiran_surat_transfer = str_replace("\\", "", $siku67);
        } else {
            $lampiran_surat_transfer = str_replace("\\", "", $check_surat_transfer);
        }

        if ($files = $req->file('keabsahan_data')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur/EFILLINGSPKNDK';
            $name = 'E-FILLING_KEABSAHANDATA';
            $check = $keabsahan_data2;
            foreach ($files as $file) {
                $arrayPath68[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp68 = implode("", $arrayPath68);
            $siku68 = array($keabsahan_data2, $imp68);
            $search = "\\";
            $replace = '';
            array_walk(
                $siku68,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $lampiran_keabsahan_data = str_replace("\\", "", $siku68);
        } else {
            $lampiran_keabsahan_data = str_replace("\\", "", $check_keabsahan_data);
        }

        if ($files = $req->file('sp_beda_jt_tempo')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur/EFILLINGSPKNDK';
            $name = 'E-FILLING_SPJTTEMPO';
            $check = $sp_beda_jt_tempo2;
            foreach ($files as $file) {
                $arrayPath69[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp69 = implode("", $arrayPath69);
            $siku69 = array($sp_beda_jt_tempo2, $imp69);
            $search = "\\";
            $replace = '';
            array_walk(
                $siku69,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $lampiran_sp_beda_jt_tempo = str_replace("\\", "", $siku69);
        } else {
            $lampiran_sp_beda_jt_tempo = str_replace("\\", "", $check_sp_beda_jt_tempo);
        }

        if ($files = $req->file('sp_authentic')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur/EFILLINGSPKNDK';
            $name = 'E-FILLING_SPAUTHENTIC';
            $check = $sp_authentic2;
            foreach ($files as $file) {
                $arrayPath70[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp70 = implode("", $arrayPath70);
            $siku70 = array($sp_authentic2, $imp70);
            $search = "\\";
            $replace = '';
            array_walk(
                $siku70,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $lampiran_sp_authentic = str_replace("\\", "", $siku70);
        } else {
            $lampiran_sp_authentic = str_replace("\\", "", $check_sp_authentic);
        }

        if ($files = $req->file('sp_penyerahan_jaminan')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur/EFILLINGSPKNDK';
            $name = 'E-FILLING_PENYERAHANJAMINAN';
            $check = $sp_penyerahan_jaminan2;
            foreach ($files as $file) {
                $arrayPath71[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp71 = implode("", $arrayPath71);
            $siku71 = array($sp_penyerahan_jaminan2, $imp71);
            $search = "\\";
            $replace = '';
            array_walk(
                $siku71,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $lampiran_sp_penyerahan_jaminan = str_replace("\\", "", $siku71);
        } else {
            $lampiran_sp_penyerahan_jaminan = str_replace("\\", "", $check_sp_penyerahan_jaminan);
        }

        if ($files = $req->file('surat_aksep')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur/EFILLINGSPKNDK';
            $name = 'E-FILLING_AKSEP';
            $check = $surat_aksep2;
            foreach ($files as $file) {
                $arrayPath72[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp72 = implode("", $arrayPath72);
            $siku72 = array($surat_aksep2, $imp72);
            $search = "\\";
            $replace = '';
            array_walk(
                $siku72,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $lampiran_surat_aksep = str_replace("\\", "", $siku72);
        } else {
            $lampiran_surat_aksep = str_replace("\\", "", $check_surat_aksep);
        }

        if ($files = $req->file('tt_uang')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur/EFILLINGSPKNDK';
            $name = 'E-FILLING_TTUANG';
            $check = $tt_uang2;
            foreach ($files as $file) {
                $arrayPath73[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp73 = implode("", $arrayPath73);
            $siku73 = array($tt_uang2, $imp73);
            $search = "\\";
            $replace = '';
            array_walk(
                $siku73,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $lampiran_tt_uang = str_replace("\\", "", $siku73);
        } else {
            $lampiran_tt_uang = str_replace("\\", "", $check_tt_uang);
        }

        if ($files = $req->file('sp_pendebetan_rekening')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur/EFILLINGSPKNDK';
            $name = 'E-FILLING_DEBETREKENING';
            $check = $sp_pendebetan_rekening2;
            foreach ($files as $file) {
                $arrayPath74[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp74 = implode("", $arrayPath74);
            $siku74 = array($sp_pendebetan_rekening2, $imp74);
            $search = "\\";
            $replace = '';
            array_walk(
                $siku74,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $lampiran_sp_pendebetan_rekening = str_replace("\\", "", $siku74);
        } else {
            $lampiran_sp_pendebetan_rekening = str_replace("\\", "", $check_sp_pendebetan_rekening);
        }

        if ($files = $req->file('sp_plang')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur/EFILLINGSPKNDK';
            $name = 'E-FILLING_PLANG';
            $check = $sp_plang2;
            foreach ($files as $file) {
                $arrayPath75[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp75 = implode("", $arrayPath75);
            $siku75 = array($sp_plang2, $imp75);
            $search = "\\";
            $replace = '';
            array_walk(
                $siku75,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $lampiran_sp_plang = str_replace("\\", "", $siku75);
        } else {
            $lampiran_sp_plang = str_replace("\\", "", $check_sp_plang);
        }

        if ($files = $req->file('hal_penting')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur/EFILLINGSPKNDK';
            $name = 'E-FILLING_HALPENTING';
            $check = $hal_penting2;
            foreach ($files as $file) {
                $arrayPath76[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp76 = implode("", $arrayPath76);
            $siku76 = array($hal_penting2, $imp76);
            $search = "\\";
            $replace = '';
            array_walk(
                $siku76,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $lampiran_hal_penting = str_replace("\\", "", $siku76);
        } else {
            $lampiran_hal_penting = str_replace("\\", "", $check_hal_penting);
        }

        if ($files = $req->file('restruktur_bunga_denda')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur/EFILLINGSPKNDK';
            $name = 'E-FILLING_RESTRUKTUR';
            $check = $restruktur_bunga_denda2;
            foreach ($files as $file) {
                $arrayPath77[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp77 = implode("", $arrayPath77);
            $siku77 = array($restruktur_bunga_denda2, $imp77);
            $search = "\\";
            $replace = '';
            array_walk(
                $siku77,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $lampiran_restruktur_bunga_denda = str_replace("\\", "", $siku77);
        } else {
            $lampiran_restruktur_bunga_denda = str_replace("\\", "", $check_restruktur_bunga_denda);
        }

        if ($files = $req->file('spajk_spa_fpk')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur/EFILLINGSPKNDK';
            $name = 'E-FILLING_SPAJK';
            $check = $spajk_spa_fpk2;
            foreach ($files as $file) {
                $arrayPath78[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $imp78 = implode("", $arrayPath78);
            $siku78 = array($spajk_spa_fpk2, $imp78);
            $search = "\\";
            $replace = '';
            array_walk(
                $siku78,
                function (&$v) use ($search, $replace) {
                    $v = str_replace($search, $replace, $v);
                }
            );
            $lampiran_spajk_spa_fpk = str_replace("\\", "", $siku78);
        } else {
            $lampiran_spajk_spa_fpk = str_replace("\\", "", $check_spajk_spa_fpk);
        }



        ################################################################################################
        // $efilling_updt = array(
        //     "user_id" => empty($req->input('user_id')) ? $get_ef->user_id : $req->input('user_id'),
        //     "user_verif" => empty($req->input('user_verif')) ? $get_ef->user_verif : $req->input('user_verif'),
        //     "tgl_buat" => empty($req->input('tgl_buat')) ? $get_ef->tgl_buat : $req->input('tgl_buat'),
        //     "tgl_verif" => empty($req->input('tgl_verif')) ? $get_ef->tgl_verif : $req->input('tgl_verif'),
        //     "tgl_update" => Carbon::now(),
        //     "status_dokument" => empty($req->input('status_dokument')) ? $get_ef->status_dokument : $req->input('status_dokument')
        // );
        // dd($efilling_updt);
        $nas = array(
            //.'no_rekening' => $no_kontrak,
            'ktp'   => $ktp,
            'npwp' => $npwp,
            'kk' => $kk,
            'surat_nikah' => $nikah,
            'surat_cerai' => $cerai,
            'surat_lahir' => $lahir,
            'domisili' => $domisili,
            'surat_kematian' => $kematian,
            'slip_gaji' => $slipgaji,
            'sk_kerja' => $skk,
            'sk_usaha' => $sku,
            //data baru
            'skd' => $skd,
            'take_over' => $takeover,
            'rek_koran' => $rekeningkoran,
            'tdp' => $tdp,
            'bon_usaha' => $bon,
            // 'verifikasi' => empty($req->input('verifikasi_nasabah')) ? $get_nas->verifikasi : $req->input('verifikasi_nasabah'),
            'verifikasi' => empty($req->input('verifikasi_nasabah')) ? null : $req->input('verifikasi_nasabah'),
            'notes' => empty($req->input('notes_nasabah')) ? $get_nas->notes : $req->input('notes_nasabah')
        );

        $permohonan = array(
            //  'no_rekening' => $no_kontrak,
            'aplikasi'   => $aps,
            'denah_lokasi'   => $denah,
            'checklist_kelengkapan'   => $kelengkapan,
            'verifikasi' => empty($req->input('verifikasi_permohonan')) ? null : $req->input('verifikasi_permohonan'),
            'notes' => empty($req->input('notes_permohonan')) ? $get_kre->notes : $req->input('notes_permohonan')
        );

        $pengajuan_bi = array(
            'pengajuan_bi' => $lampiran_pengajuan_bi,
            'persetujuan' => $lampiran_persetujuanbi,
            'hasil' => $lampiran_hasilbi,
            'verifikasi' => empty($req->input('verifikasi_bi')) ? null : $req->input('verifikasi_bi'),
            'notes' => empty($req->input('notes_bi')) ? $get_bi_pengajuan->notes : $req->input('notes_bi')
        );

        $creditanalist = array(
            'memo_ao' => $lampiran_memoao,
            'memo_ca' => $lampiran_memoca,
            'offering_letter' => $lampiran_ol,
            'penilaian_jaminan' => $lampiran_nilaijaminan,
            'cheklist_survey' => $lampiran_checksurvey,
            'persetujuan_kredit' => $lampiran_appkredit,
            'verifikasi' => empty($req->input('verifikasi_ca')) ? null : $req->input('verifikasi_ca'),
            'notes' => empty($req->input('notes_ca')) ? $get_caa->notes : $req->input('notes_ca')
        );

        $foto = array(
            'ft_jaminan' => $lampiran_ftjaminan,
            'ft_pengikatan' => $lampiran_ftpengikatan,
            'ft_domisili' => $lampiran_ftdomisili,
            'ft_usaha'  => $lampiran_ftusaha,
            'verifikasi' => empty($req->input('verifikasi_foto')) ? null : $req->input('verifikasi_foto'),
            'notes' => empty($req->input('notes_foto')) ? $get_foto->notes : $req->input('notes_foto')
        );

        $jaminan = array(
            'no_rekening' => $no_kontrak,
            'sertifikat'   => $lampiran_sertifikat,
            'skmht'   => $lampiran_skmht,
            'apht'   => $lampiran_apht,
            'cabut_roya'   => $lampiran_cabut_roya,
            'sht'   => $lampiran_sht,
            'pbb'   => $lampiran_pbb,
            'imb'   => $lampiran_imb,
            'ajb'   => $lampiran_ajb,
            'bpkb'   => $lampiran_bpkb,
            'ahli_waris'   => $lampiran_ahli_waris,
            'pengakuan_hutang'   => $lampiran_pengakuan_hutang,
            'akta_pengakuan_hak_bersama'   => $lampiran_akta_pengakuan_hak_bersama,
            'adendum'   => $lampiran_adendum,
            'fidusia'   => $lampiran_fidusia,
            'verifikasi' => empty($req->input('verifikasi_jaminan')) ? null : $req->input('verifikasi_jaminan'),
            'notes' => empty($req->input('notes_jaminan')) ? $get_jaminan->notes : $req->input('notes_jaminan')
        );

        $legal = array(
            'pengajuan_lpdk' => $lampiran_pengajuan_lpdk,
            'lpdk' => $lampiran_lpdk,
            'cheklist_pengikatan' => $lampiran_cheklist_pengikatan,
            'order_pengikatan'  => $lampiran_order_pengikatan,
            'verifikasi' => empty($req->input('verifikasi_legal')) ? null : $req->input('verifikasi_legal'),
            'notes' => empty($req->input('notes_legal')) ? $get_legal->notes : $req->input('notes_legal')
        );

        $asset = array(
            'ra_tanda_terima' => $lampiran_ra_tanda_terima,
            'ra_surat_kuasa' => $lampiran_ra_surat_kuasa,
            'ra_identitas_pengambilan' => $lampiran_ra_identitas_pengambilan,
            'ra_lainnya'  => $lampiran_ra_lainnya,
            'ra_serah_terima'  => $lampiran_ra_serah_terima,
            'verifikasi' => empty($req->input('verifikasi_asset')) ? null : $req->input('verifikasi_asset'),
            'notes' => empty($req->input('notes_asset')) ? $get_asset->notes : $req->input('notes_asset')
        );

        $spkndk = array(
            'spk_ndk'   => $lampiran_spk_ndk,
            'asuransi'   => $lampiran_asuransi,
            'sp_no_imb'   => $lampiran_sp_no_imb,
            'jadwal_angsuran'   => $lampiran_jadwal_angsuran,
            'personal_guarantee'   => $lampiran_personal_guarantee,
            'hold_dana'   => $lampiran_hold_dana,
            'surat_transfer'   => $lampiran_surat_transfer,
            'keabsahan_data'   => $lampiran_keabsahan_data,
            'sp_beda_jt_tempo'   => $lampiran_sp_beda_jt_tempo,
            'sp_authentic'   => $lampiran_sp_authentic,
            'sp_penyerahan_jaminan'   => $lampiran_sp_penyerahan_jaminan,
            'surat_aksep'   => $lampiran_surat_aksep,
            'tt_uang'   => $lampiran_tt_uang,
            'sp_pendebetan_rekening'   => $lampiran_sp_pendebetan_rekening,
            'sp_plang'   => $lampiran_sp_plang,
            'hal_penting'   => $lampiran_hal_penting,
            'restruktur_bunga_denda'   => $lampiran_restruktur_bunga_denda,
            'spajk_spa_fpk'   => $lampiran_spajk_spa_fpk,
            'verifikasi' => empty($req->input('verifikasi_spkndk')) ? null : $req->input('verifikasi_spkndk'),
            'notes' => empty($req->input('notes_spkndk')) ? $get_spkndk->notes : $req->input('notes_spkndk')
        );

        $get_empty_verif = array(
            $req->input('verifikasi_nasabah'), $req->input('verifikasi_permohonan'), $req->input('verifikasi_bi'), $req->input('verifikasi_ca'), $req->input('verifikasi_foto'), $req->input('verifikasi_jaminan'), $req->input('verifikasi_legal'), $req->input('verifikasi_asset'), $req->input('verifikasi_spkndk')

        );

        $get_status_verif = DB::connection('centro')->select("SELECT * FROM view_verifikasi_efilling2 WHERE 2 IN(verif_bi,verif_ca,verif_foto,verif_jaminan,verif_legal,verif_nasabah,verif_permohonan_kredit,verif_ra,verif_spk) AND no_rekening = '$no_kontrak'");



        $data_nas =  EfillingNasabah::where('no_rekening', $no_kontrak)->update($nas);
        $data_per =  EfillingPermohonan::where('no_rekening', $no_kontrak)->update($permohonan);
        
        $data_pengajuan_bi = Efilling_bi::where('no_rekening', $no_kontrak)->update($pengajuan_bi);
        $data_creditanalist = Efilling_ca::where('no_rekening', $no_kontrak)->update($creditanalist);
        $data_foto = Efilling_foto::where('no_rekening', $no_kontrak)->update($foto);
        $data_jaminan = EfillingJaminan::where('no_rekening', $no_kontrak)->update($jaminan);
        $data_legal = Efilling_legal::where('no_rekening', $no_kontrak)->update($legal);
        $data_asset = Efilling_asset::where('no_rekening', $no_kontrak)->update($asset);
        $data_spkndk = Efilling_spkndk::where('no_rekening', $no_kontrak)->update($spkndk);

        $nasabah = EfillingNasabah::where('no_rekening', $no_kontrak)->first();
        $permohonan_nas = EfillingPermohonan::where('no_rekening', $no_kontrak)->first();
      
        $pengajuan_bi = Efilling_bi::select(
            'pengajuan_bi',
            'persetujuan',
            'hasil'
        )->where('no_rekening', $no_kontrak)->first();
        $ca = Efilling_ca::select(
            'memo_ao',
            'memo_ca',
            'offering_letter',
            'penilaian_jaminan',
            'cheklist_survey',
            'persetujuan_kredit'
        )->where('no_rekening', $no_kontrak)->first();

        $foto_efilling = Efilling_foto::select(
            'ft_jaminan',
            'ft_pengikatan',
            'ft_domisili',
            'ft_usaha'
        )->where('no_rekening', $no_kontrak)->first();
        $jaminan_efilling = EfillingJaminan::select(
            'sertifikat',
            'skmht',
            'apht',
            'cabut_roya',
            'sht',
            'pbb',
            'imb',
            'ajb',
            'bpkb',
            'ahli_waris',
            'pengakuan_hutang',
            'akta_pengakuan_hak_bersama',
            'adendum',
            'fidusia'
        )->where('no_rekening', $no_kontrak)->first();

        $legal_efilling = Efilling_legal::select(
            'pengajuan_lpdk',
            'lpdk',
            'cheklist_pengikatan',
            'order_pengikatan'
        )->where('no_rekening', $no_kontrak)->first();

        $asset_efilling = Efilling_asset::select(
            'ra_tanda_terima',
            'ra_surat_kuasa',
            'ra_identitas_pengambilan',
            'ra_lainnya',
            'ra_serah_terima',
            'verifikasi',
            'notes'
        )->where('no_rekening', $no_kontrak)->first();

        $spk_efilling = Efilling_spkndk::select(
            'spk_ndk',
            'asuransi',
            'sp_no_imb',
            'jadwal_angsuran',
            'personal_guarantee',
            'hold_dana',
            'surat_transfer',
            'keabsahan_data',
            'sp_beda_jt_tempo',
            'sp_authentic',
            'sp_penyerahan_jaminan',
            'surat_aksep',
            'tt_uang',
            'sp_pendebetan_rekening',
            'sp_plang',
            'hal_penting',
            'restruktur_bunga_denda',
            'spajk_spa_fpk',
            'verifikasi',
            'notes'
        )->where('no_rekening', $no_kontrak)->first();

         // if (empty($efillingnasabah)) {
        //     EfillingNasabah::where('no_rekening', $no_kontrak)->create(['no_rekening' => $no_kontrak]);
        // }
        // if (empty($efillingpermohonan)) {
        //     EfillingPermohonan::where('no_rekening', $no_kontrak)->create(['no_rekening' => $no_kontrak]);
        // }
        // if (empty($jaminan_efilling)) {
        //     EfillingJaminan::where('no_rekening', $no_kontrak)->create(['no_rekening' => $no_kontrak]);
        // }
        // if (empty($foto_efilling)) {
        //     Efilling_foto::where('no_rekening', $no_kontrak)->create(['no_rekening' => $no_kontrak]);
        // }
        // if (empty($asset_efilling)) {
        //     Efilling_asset::where('no_rekening', $no_kontrak)->create(['no_rekening' => $no_kontrak]);
        // }
        // if (empty($legal_efilling)) {
        //     Efilling_legal::where('no_rekening', $no_kontrak)->create(['no_rekening' => $no_kontrak]);
        // }
        // if (empty($efilling)) {
        //     Efilling::where('no_rekening', $no_kontrak)->create(['no_rekening' => $no_kontrak]);
        // }
        // if (empty($spk_efilling)) {
        //     Efilling_spkndk::where('no_rekening', $no_kontrak)->create(['no_rekening' => $no_kontrak]);
        // }
        // // if (empty($bichecking)) {
        // //     Bi_checking::where('no_rekening', $no_kontrak)->create(['no_rekening' => $no_kontrak]);
        // // }
        // if (empty($pengajuan_bi)) {
        //     Efilling_bi::where('no_rekening', $no_kontrak)->create(['no_rekening' => $no_kontrak]);
        // }
        // if (empty($ca)) {
        //     Efilling_ca::where('no_rekening', $no_kontrak)->create(['no_rekening' => $no_kontrak]);
        // }


        if ($nas === null) {
            return response()->json([
                "code" => 401,
                "message" => "Data Kosong"
            ], 401);
        }

        $efilling_updt = array(
            "user_id" => empty($req->input('user_id')) ? $get_ef->user_id : $req->input('user_id'),
            "user_verif" => empty($req->input('user_verif')) ? $get_ef->user_verif : $req->input('user_verif'),
            "tgl_buat" => empty($req->input('tgl_buat')) ? $get_ef->tgl_buat : $req->input('tgl_buat'),
            "tgl_verif" => empty($req->input('tgl_verif')) ? $get_ef->tgl_verif : $req->input('tgl_verif'),
            "tgl_update" => Carbon::now(),
            "status_dokument" => empty($req->input('status_dokument')) ? $get_ef->status_dokument : $req->input('status_dokument'),
            "status_verif" => $get_ef->status_verif
        );

        if ($get_ef->status_verif === "2" && !array_filter($get_empty_verif)) {
            $efilling_updt = array(
                "user_id" => empty($req->input('user_id')) ? $get_ef->user_id : $req->input('user_id'),
                "user_verif" => empty($req->input('user_verif')) ? $get_ef->user_verif : $req->input('user_verif'),
                "tgl_buat" => empty($req->input('tgl_buat')) ? $get_ef->tgl_buat : $req->input('tgl_buat'),
                "tgl_verif" => empty($req->input('tgl_verif')) ? $get_ef->tgl_verif : $req->input('tgl_verif'),
                "tgl_update" => Carbon::now(),
                "status_dokument" => empty($req->input('status_dokument')) ? $get_ef->status_dokument : $req->input('status_dokument'),
                "status_verif" => "3"
            );
        } elseif (!array_filter($get_empty_verif)) {
            $efilling_updt = array(
                "user_id" => empty($req->input('user_id')) ? $get_ef->user_id : $req->input('user_id'),
                "user_verif" => empty($req->input('user_verif')) ? $get_ef->user_verif : $req->input('user_verif'),
                "tgl_buat" => empty($req->input('tgl_buat')) ? $get_ef->tgl_buat : $req->input('tgl_buat'),
                "tgl_verif" => empty($req->input('tgl_verif')) ? $get_ef->tgl_verif : $req->input('tgl_verif'),
                "tgl_update" => Carbon::now(),
                "status_dokument" => empty($req->input('status_dokument')) ? $get_ef->status_dokument : $req->input('status_dokument'),
                "status_verif" => $get_ef->status_verif
            );
        } elseif ($get_status_verif) {
            $efilling_updt = array(
                "user_id" => empty($req->input('user_id')) ? $get_ef->user_id : $req->input('user_id'),
                "user_verif" => empty($req->input('user_verif')) ? $get_ef->user_verif : $req->input('user_verif'),
                "tgl_buat" => empty($req->input('tgl_buat')) ? $get_ef->tgl_buat : $req->input('tgl_buat'),
                "tgl_verif" => empty($req->input('tgl_verif')) ? $get_ef->tgl_verif : $req->input('tgl_verif'),
                "tgl_update" => Carbon::now(),
                "status_dokument" => empty($req->input('status_dokument')) ? $get_ef->status_dokument : $req->input('status_dokument'),
                "status_verif" => "2"
            );
        } elseif (empty($get_status_verif)) {
            $efilling_updt = array(
                "user_id" => empty($req->input('user_id')) ? $get_ef->user_id : $req->input('user_id'),
                "user_verif" => empty($req->input('user_verif')) ? $get_ef->user_verif : $req->input('user_verif'),
                "tgl_buat" => empty($req->input('tgl_buat')) ? $get_ef->tgl_buat : $req->input('tgl_buat'),
                "tgl_verif" => empty($req->input('tgl_verif')) ? $get_ef->tgl_verif : $req->input('tgl_verif'),
                "tgl_update" => Carbon::now(),
                "status_dokument" => empty($req->input('status_dokument')) ? $get_ef->status_dokument : $req->input('status_dokument'),
                "status_verif" => "1"
            );
        } else {
            $efilling_updt = array(
                "user_id" => empty($req->input('user_id')) ? $get_ef->user_id : $req->input('user_id'),
                "user_verif" => empty($req->input('user_verif')) ? $get_ef->user_verif : $req->input('user_verif'),
                "tgl_buat" => empty($req->input('tgl_buat')) ? $get_ef->tgl_buat : $req->input('tgl_buat'),
                "tgl_verif" => empty($req->input('tgl_verif')) ? $get_ef->tgl_verif : $req->input('tgl_verif'),
                "tgl_update" => Carbon::now(),
                "status_dokument" => empty($req->input('status_dokument')) ? $get_ef->status_dokument : $req->input('status_dokument'),
                "status_verif" => $get_ef->status_verif
            );
        }
        $data_efilling =  Efilling::where('no_rekening', $no_kontrak)->update($efilling_updt);
        try {

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'data' => array($nasabah, $permohonan_nas, $pengajuan_bi, $ca, $foto_efilling, $jaminan_efilling, $legal_efilling, $asset_efilling, $spk_efilling)
            ]);
        } catch (\Exception $e) {
            $err = DB::connection('web')->rollback();
            return response()->json([
                'code'    => 501,
                'status'  => 'error',
                'message' => $err
            ], 501);
        }
    }
    public function delete(Request $req, $no_kontrak)
    {
        $jenis = $req->input('jenis');
        $body = $req->input('files');
        $column = $req->input('column');

        $nasabah = EfillingNasabah::where('no_rekening', $no_kontrak)->first();
        $permohonan_nas = EfillingPermohonan::where('no_rekening', $no_kontrak)->first();
        
        $pengajuan_bi = Efilling_bi::where('no_rekening', $no_kontrak)->first();
        $ca = Efilling_ca::where('no_rekening', $no_kontrak)->first();

        $foto_efilling = Efilling_foto::where('no_rekening', $no_kontrak)->first();
        $jaminan_efilling = EfillingJaminan::where('no_rekening', $no_kontrak)->first();

        $legal_efilling = Efilling_legal::where('no_rekening', $no_kontrak)->first();

        $asset_efilling = Efilling_asset::where('no_rekening', $no_kontrak)->first();

        $spk_efilling = Efilling_spkndk::where('no_rekening', $no_kontrak)->first();
        if ($jenis === 'efiling_nasabah') {
            $cek =  EfillingNasabah::select($column)->where('no_rekening', $no_kontrak)->first();
            $exp = explode(",", $cek->$column);
            $exp = str_replace("[", "", $exp);
            $exp = str_replace("]", "", $exp);
            $exp = str_replace('"', '', $exp);
            //dd($jenis, $body, $column)
            function array_search_partial($arr, $keyword)
            {
                foreach ($arr as $index => $string) {
                    if (strpos($string, $keyword) !== FALSE)
                        return $index;
                }
            }

            if ($body === "") {
                return response()->json([
                    'code' => 404,
                    'message' => 'input file tidak boleh kosong'
                ], 404);
            } else if (array_search_partial($exp, $body) === null) {
                return response()->json([
                    'code' => 404,
                    'message' => 'file tidak ditemukan'
                ], 404);
            } else {
                $got = array_search_partial($exp, $body);
                unset($exp[$got]);
            }
            $exp = array_values($exp);
            $exp = json_encode($exp, JSON_UNESCAPED_SLASHES);
            //  dd($exp);
            if ($exp === array('')) {
                $exp = null;
                $simpan = EfillingNasabah::where('no_rekening', $no_kontrak)->update([$column => $exp]);
            } else {
                $simpan = EfillingNasabah::where('no_rekening', $no_kontrak)->update([$column => $exp]);
            }
            return response()->json([
                'code' => 200,
                'data' => $exp
            ], 200);
        }
        if ($jenis === 'efiling_bi_checking') {
            $cek =  Efilling_bi::select($column)->where('no_rekening', $no_kontrak)->first();
            $exp = explode(",", $cek->$column);
            $exp = str_replace("[", "", $exp);
            $exp = str_replace("]", "", $exp);
            $exp = str_replace('"', '', $exp);
            //dd($jenis, $body, $column)
            function array_search_partial($arr, $keyword)
            {
                foreach ($arr as $index => $string) {
                    if (strpos($string, $keyword) !== FALSE)
                        return $index;
                }
            }

            if ($body === "") {
                return response()->json([
                    'code' => 404,
                    'message' => 'input file tidak boleh kosong'
                ], 404);
            } else if (array_search_partial($exp, $body) === null) {
                return response()->json([
                    'code' => 404,
                    'message' => 'file tidak ditemukan'
                ], 404);
            } else {
                $got = array_search_partial($exp, $body);
                unset($exp[$got]);
            }
            $exp = array_values($exp);
            $exp = json_encode($exp, JSON_UNESCAPED_SLASHES);
            if ($exp === array('')) {
                $exp = null;
                $simpan = Efilling_bi::where('no_rekening', $no_kontrak)->update([$column => $exp]);
            } else {
                $simpan = Efilling_bi::where('no_rekening', $no_kontrak)->update([$column => $exp]);
            }
            return response()->json([
                'code' => 200,
                'data' => $exp
            ], 200);
        }
        if ($jenis === 'efiling') {
            $cek =  Efilling::select($column)->where('no_rekening', $no_kontrak)->first();
            $exp = explode(",", $cek->$column);
            $exp = str_replace("[", "", $exp);
            $exp = str_replace("]", "", $exp);
            $exp = str_replace('"', '', $exp);
            //dd($jenis, $body, $column)
            function array_search_partial($arr, $keyword)
            {
                foreach ($arr as $index => $string) {
                    if (strpos($string, $keyword) !== FALSE)
                        return $index;
                }
            }

            if ($body === "") {
                return response()->json([
                    'code' => 404,
                    'message' => 'input file tidak boleh kosong'
                ], 404);
            } else if (array_search_partial($exp, $body) === null) {
                return response()->json([
                    'code' => 404,
                    'message' => 'file tidak ditemukan'
                ], 404);
            } else {
                $got = array_search_partial($exp, $body);
                unset($exp[$got]);
            }
            $exp = array_values($exp);
            $exp = json_encode($exp, JSON_UNESCAPED_SLASHES);
            if ($exp === array('')) {
                $exp = null;
                $simpan = Efilling::where('no_rekening', $no_kontrak)->update([$column => $exp]);
            } else {
                $simpan = Efilling::where('no_rekening', $no_kontrak)->update([$column => $exp]);
            }
            return response()->json([
                'code' => 200,
                'data' => $exp
            ], 200);
        }
        if ($jenis === 'efiling_credit_analist') {
            $cek =  Efilling_ca::select($column)->where('no_rekening', $no_kontrak)->first();
            $exp = explode(",", $cek->$column);
            $exp = str_replace("[", "", $exp);
            $exp = str_replace("]", "", $exp);
            $exp = str_replace('"', '', $exp);
            //dd($jenis, $body, $column)
            function array_search_partial($arr, $keyword)
            {
                foreach ($arr as $index => $string) {
                    if (strpos($string, $keyword) !== FALSE)
                        return $index;
                }
            }

            if ($body === "") {
                return response()->json([
                    'code' => 404,
                    'message' => 'input file tidak boleh kosong'
                ], 404);
            } else if (array_search_partial($exp, $body) === null) {
                return response()->json([
                    'code' => 404,
                    'message' => 'file tidak ditemukan'
                ], 404);
            } else {
                $got = array_search_partial($exp, $body);
                unset($exp[$got]);
            }
            $exp = array_values($exp);
            $exp = json_encode($exp, JSON_UNESCAPED_SLASHES);
            if ($exp === array('')) {
                $exp = null;
                $simpan = Efilling_ca::where('no_rekening', $no_kontrak)->update([$column => $exp]);
            } else {
                $simpan = Efilling_ca::where('no_rekening', $no_kontrak)->update([$column => $exp]);
            }
            return response()->json([
                'code' => 200,
                'data' => $exp
            ], 200);
        }
        if ($jenis === 'efiling_foto') {
            $cek =  Efilling_foto::select($column)->where('no_rekening', $no_kontrak)->first();
            $exp = explode(",", $cek->$column);
            $exp = str_replace("[", "", $exp);
            $exp = str_replace("]", "", $exp);
            $exp = str_replace('"', '', $exp);
            //dd($jenis, $body, $column)
            function array_search_partial($arr, $keyword)
            {
                foreach ($arr as $index => $string) {
                    if (strpos($string, $keyword) !== FALSE)
                        return $index;
                }
            }

            if ($body === "") {
                return response()->json([
                    'code' => 404,
                    'message' => 'input file tidak boleh kosong'
                ], 404);
            } else if (array_search_partial($exp, $body) === null) {
                return response()->json([
                    'code' => 404,
                    'message' => 'file tidak ditemukan'
                ], 404);
            } else {
                $got = array_search_partial($exp, $body);
                unset($exp[$got]);
            }
            $exp = array_values($exp);
            $exp = json_encode($exp, JSON_UNESCAPED_SLASHES);

            if ($exp === array('')) {
                $exp = null;
                $simpan = Efilling_foto::where('no_rekening', $no_kontrak)->update([$column => $exp]);
            } else {
                $simpan = Efilling_foto::where('no_rekening', $no_kontrak)->update([$column => $exp]);
            }
            return response()->json([
                'code' => 200,
                'data' => $exp
            ], 200);
        }
        if ($jenis === 'efiling_jaminan') {
            $cek =  EfillingJaminan::select($column)->where('no_rekening', $no_kontrak)->first();
            $exp = explode(",", $cek->$column);
            $exp = str_replace("[", "", $exp);
            $exp = str_replace("]", "", $exp);
            $exp = str_replace('"', '', $exp);
            //dd($jenis, $body, $column)
            function array_search_partial($arr, $keyword)
            {
                foreach ($arr as $index => $string) {
                    if (strpos($string, $keyword) !== FALSE)
                        return $index;
                }
            }

            if ($body === "") {
                return response()->json([
                    'code' => 404,
                    'message' => 'input file tidak boleh kosong'
                ], 404);
            } else if (array_search_partial($exp, $body) === null) {
                return response()->json([
                    'code' => 404,
                    'message' => 'file tidak ditemukan'
                ], 404);
            } else {
                $got = array_search_partial($exp, $body);
                unset($exp[$got]);
            }
            $exp = array_values($exp);
            $exp = json_encode($exp, JSON_UNESCAPED_SLASHES);

            if ($exp === array('')) {
                $exp = null;
                $simpan = EfillingJaminan::where('no_rekening', $no_kontrak)->update([$column => $exp]);
            } else {
                $simpan = EfillingJaminan::where('no_rekening', $no_kontrak)->update([$column => $exp]);
            }
            return response()->json([
                'code' => 200,
                'data' => $exp
            ], 200);
        }
        if ($jenis === 'efiling_legal') {
            $cek =  Efilling_legal::select($column)->where('no_rekening', $no_kontrak)->first();
            $exp = explode(",", $cek->$column);
            $exp = str_replace("[", "", $exp);
            $exp = str_replace("]", "", $exp);
            $exp = str_replace('"', '', $exp);
            //dd($jenis, $body, $column)
            function array_search_partial($arr, $keyword)
            {
                foreach ($arr as $index => $string) {
                    if (strpos($string, $keyword) !== FALSE)
                        return $index;
                }
            }

            if ($body === "") {
                return response()->json([
                    'code' => 404,
                    'message' => 'input file tidak boleh kosong'
                ], 404);
            } else if (array_search_partial($exp, $body) === null) {
                return response()->json([
                    'code' => 404,
                    'message' => 'file tidak ditemukan'
                ], 404);
            } else {
                $got = array_search_partial($exp, $body);
                unset($exp[$got]);
            }
            $exp = array_values($exp);
            $exp = json_encode($exp, JSON_UNESCAPED_SLASHES);
            if ($exp === array('')) {
                $exp = null;
                $simpan = Efilling_legal::where('no_rekening', $no_kontrak)->update([$column => $exp]);
            } else {
                $simpan = Efilling_legal::where('no_rekening', $no_kontrak)->update([$column => $exp]);
            }
            return response()->json([
                'code' => 200,
                'data' => $exp
            ], 200);
        }
        if ($jenis === 'efiling_permohonan_kredit') {
            $cek =  EfillingPermohonan::select($column)->where('no_rekening', $no_kontrak)->first();
            $exp = explode(",", $cek->$column);
            $exp = str_replace("[", "", $exp);
            $exp = str_replace("]", "", $exp);
            $exp = str_replace('"', '', $exp);
            //dd($jenis, $body, $column)
            function array_search_partial($arr, $keyword)
            {
                foreach ($arr as $index => $string) {
                    if (strpos($string, $keyword) !== FALSE)
                        return $index;
                }
            }

            if ($body === "") {
                return response()->json([
                    'code' => 404,
                    'message' => 'input file tidak boleh kosong'
                ], 404);
            } else if (array_search_partial($exp, $body) === null) {
                return response()->json([
                    'code' => 404,
                    'message' => 'file tidak ditemukan'
                ], 404);
            } else {
                $got = array_search_partial($exp, $body);
                unset($exp[$got]);
            }
            $exp = array_values($exp);
            $exp = json_encode($exp, JSON_UNESCAPED_SLASHES);
            if ($exp === array('')) {
                $exp = null;
                $simpan = EfillingPermohonan::where('no_rekening', $no_kontrak)->update([$column => $exp]);
            } else {
                $simpan = EfillingPermohonan::where('no_rekening', $no_kontrak)->update([$column => $exp]);
            }
            return response()->json([
                'code' => 200,
                'data' => $exp
            ], 200);
        }
        if ($jenis === 'efiling_release_aset') {
            $cek =  Efilling_asset::select($column)->where('no_rekening', $no_kontrak)->first();
            $exp = explode(",", $cek->$column);
            $exp = str_replace("[", "", $exp);
            $exp = str_replace("]", "", $exp);
            $exp = str_replace('"', '', $exp);
            //dd($jenis, $body, $column)
            function array_search_partial($arr, $keyword)
            {
                foreach ($arr as $index => $string) {
                    if (strpos($string, $keyword) !== FALSE)
                        return $index;
                }
            }

            if ($body === "") {
                return response()->json([
                    'code' => 404,
                    'message' => 'input file tidak boleh kosong'
                ], 404);
            } else if (array_search_partial($exp, $body) === null) {
                return response()->json([
                    'code' => 404,
                    'message' => 'file tidak ditemukan'
                ], 404);
            } else {
                $got = array_search_partial($exp, $body);
                unset($exp[$got]);
            }
            $exp = array_values($exp);
            $exp = json_encode($exp, JSON_UNESCAPED_SLASHES);
            if ($exp === array('')) {
                $exp = null;
                $simpan = Efilling_asset::where('no_rekening', $no_kontrak)->update([$column => $exp]);
            } else {
                $simpan = Efilling_asset::where('no_rekening', $no_kontrak)->update([$column => $exp]);
            }
            return response()->json([
                'code' => 200,
                'data' => $exp
            ], 200);
        }
        if ($jenis === 'efiling_spk_ndk') {
            $cek =  Efilling_spkndk::select($column)->where('no_rekening', $no_kontrak)->first();
            $exp = explode(",", $cek->$column);
            $exp = str_replace("[", "", $exp);
            $exp = str_replace("]", "", $exp);
            $exp = str_replace('"', '', $exp);
            //dd($jenis, $body, $column)
            function array_search_partial($arr, $keyword)
            {
                foreach ($arr as $index => $string) {
                    if (strpos($string, $keyword) !== FALSE)
                        return $index;
                }
            }

            if ($body === "") {
                return response()->json([
                    'code' => 404,
                    'message' => 'input file tidak boleh kosong'
                ], 404);
            } else if (array_search_partial($exp, $body) === null) {
                return response()->json([
                    'code' => 404,
                    'message' => 'file tidak ditemukan'
                ], 404);
            } else {
                $got = array_search_partial($exp, $body);
                unset($exp[$got]);
            }
            $exp = array_values($exp);
            $exp = json_encode($exp, JSON_UNESCAPED_SLASHES);
            if ($exp === array('')) {
                $exp = null;
                $simpan = Efilling_spkndk::where('no_rekening', $no_kontrak)->update([$column => $exp]);
            } else {
                $simpan = Efilling_spkndk::where('no_rekening', $no_kontrak)->update([$column => $exp]);
            }
            return response()->json([
                'code' => 200,
                'data' => $exp
            ], 200);
        }

        //$get_nas = EfillingNasabah::where('no_rekening', $no_kontrak)->first();

    }
}
