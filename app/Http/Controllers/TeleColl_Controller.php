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
use App\Models\Transaksi\LogRekomCA;
use App\Models\Transaksi\LogRingAnalisa;
use App\Models\v2\Target_lending;
use App\Models\master_nilai;
use App\Models\master_transaksi;
use Image;

use App\Models\TeleColl;

// use Intervention\Image\ImageManagerStatic as Image;


use App\Models\MasterActivity\Activity;
use App\Models\MasterActivity\TargetPeriodik;
use App\Models\MasterActivity\TargetApproval;
use Illuminate\Support\Facades\DB;


class TeleColl_Controller extends BaseController
{

    public function storetelecoll(Request $req)
    {
 // $pic = $req->pic; // From PIC middleware
        $user_id = $req->auth->user_id;
$cabang = $req->auth;


 //$id_pic = array();
   //     $i = 0;
    //    foreach ($pic as $val) {
   //         $id_pic[] = $val['id'];
      //      $i++;
     //   }
        $data = array(
            "total_call"    => $req->input('total_call'),
            "tanggal_telpon" => $req->input('tgl_telepon'),
            "nomor_kontrak" => $req->input('no_kontrak'),
            "nama_debitur" => $req->input('nama_deb'),
            "usia_debitur" => $req->input('umur'),
            "no_telp_1" => $req->input('no_telp_1'),
            "no_telp_2" => $req->input('no_telp_2'),
            "no_telp_3" => $req->input('update_telp'),
            "tanggal_lahir" => $req->input('tgl_lahir_deb'),
            "sisa_angsuran" => $req->input('sisa_angsuran'),
            "tgl_kredit_tabungan" => $req->input('tgl_kredit'),
            "total_denda" => $req->input('total_denda'),
            "angsuran_ke" => $req->input('angsuran_ke'),
            "pastdue" => $req->input('pastdue'),
            "nominal_angsuran" => $req->input('nominal_angsuran'),
            "baki_debet" => $req->input('baki_debet'),
            "tgl_jatuh_tempo" => $req->input('tgl_tempo'),
            "total_pelunasan" => $req->input('total_pelunasan'),
            "karakter_debitur" => $req->input('karakter_deb'),
            "kondisi_kerja" => $req->input('kondisi_pekerjaan'),
            "update_pekerjaan" => $req->input('update_pekerjaan'),
            "update_penghasilan" => $req->input('update_penghasilan'),
            "contacted" => $req->input('contacted'),
            "uncontacted" => $req->input('uncontacted'),
            "unconnected" => $req->input('unconnected'),
            "tgl_janji_bayar" => $req->input('tgl_janji_bayar'),
            "metode_pembayaran" => $req->input('metode_bayar'),
            "note_tele" => $req->input('note_tele_col'),
	    "id_pic" => $user_id,
"kode_kantor" => $cabang->kd_cabang
        );

        if (empty($data)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Aktivitas kosong'
            ], 404);
        }

        try {
            $tele_coll = TeleColl::create($data);
            return response()->json([
                // 'code'   => 200,
                // 'status' => 'success',
                // 'count'  => sizeof($cek_sertifikat),
                'data'   => $tele_coll
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }
 public function viewKredit()
    {

  // $data = DB::connection('web')->table('view_browse_kre_tele');
//         $data = DB::connection('web')->select("SELECT
//         `a`.`no_rekening`     AS `no_kontrak`,
//         `a`.`nama_nasabah`    AS `nama_debitur`,
//         `b`.`TGLLAHIR`        AS `tgl_lahir`,
//         (YEAR(CURDATE()) - YEAR(`b`.`TGLLAHIR`)) AS `umur`,
//         `b`.`HP`              AS `no_telp`,
//         `a`.`jml_angsuran`    AS `angsuran_ke`,
//         `a`.`tgl_jatuh_tempo` AS `tgl_jatuh_tempo`,
//         `a`.`ft_hari`         AS `pastdue`,
//         `a`.`jumlah_angsuran` AS `nominal_angsuran`,
//         `a`.`baki_debet`      AS `baki_debet`,
//         `a`.`pelunasan`       AS `total_pelunasan`,
//         `a`.`alamat`          AS `alamat`,
//         `a`.`jml_pinjaman`    AS `plafon_awal`,
//         (SELECT
//            `dpm_online`.`kre_kode_jenis_agunan`.`DESKRIPSI_JENIS_AGUNAN`
//          FROM `dpm_online`.`kre_kode_jenis_agunan`
//          WHERE (`dpm_online`.`kre_kode_jenis_agunan`.`KODE_JENIS_AGUNAN` = `a`.`jenis_agunan`)) AS `jenis_agunan`,
//         `a`.`ft_hari`         AS `max_pastdue`,
//         `d`.`tgl_jt_shgb`     AS `shgb_expired`,
//         `a`.`nasabah_id`      AS `nasabah_id`
//       FROM (((`dpm_online`.`kre_nominatif` `a`
//           LEFT JOIN `dpm_online`.`nasabah` `b`
//              ON ((`a`.`nasabah_id` = `b`.`NASABAH_ID`)))
//          LEFT JOIN `dpm_online`.`kredit` `c`
//             ON ((`a`.`no_rekening` = `c`.`NO_REKENING`)))
//         LEFT JOIN `dpm_online`.`jaminan_dokument` `d`
//            ON ((`c`.`AGUNAN_ID1` = `d`.`agunan_id`)))
//       WHERE ((`a`.`baki_debet` > 0)
//            --  AND (`a`.`tgl_laporan` = CURDATE())
// )");

        $data = DB::connection('web')->table('view_browse_kre_tele')->get();

        if (empty($data)) {
            return response()->json([
                "message" => 'Data Tidak ditemukan'
            ]);
        }

        try {
            return response()->json([
                // 'code'   => 200,
                // 'status' => 'success',
                // 'count'  => sizeof($cek_sertifikat),
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
public function viewKreditDetail($nasabah_id, Request $req)
    {

       // $nasabah_id = $req->input('nasabah_id');

  $data = DB::connection('web')->select("SELECT
        `a`.`no_rekening`     AS `no_kontrak`,
        `a`.`nama_nasabah`    AS `nama_debitur`,
        `b`.`TGLLAHIR`        AS `tgl_lahir`,
        (YEAR(CURDATE()) - YEAR(`b`.`TGLLAHIR`)) AS `umur`,
        `b`.`HP`              AS `no_telp`,
        `a`.`jml_angsuran`    AS `angsuran_ke`,
        `a`.`tgl_jatuh_tempo` AS `tgl_jatuh_tempo`,
        `a`.`ft_hari`         AS `pastdue`,
        `a`.`jumlah_angsuran` AS `nominal_angsuran`,
        `a`.`baki_debet`      AS `baki_debet`,
        `a`.`pelunasan`       AS `total_pelunasan`,
        `a`.`alamat`          AS `alamat`,
        `a`.`jml_pinjaman`    AS `plafon_awal`,
        (SELECT
           `dpm_online`.`kre_kode_jenis_agunan`.`DESKRIPSI_JENIS_AGUNAN`
         FROM `dpm_online`.`kre_kode_jenis_agunan`
         WHERE (`dpm_online`.`kre_kode_jenis_agunan`.`KODE_JENIS_AGUNAN` = `a`.`jenis_agunan`)) AS `jenis_agunan`,
        `a`.`ft_hari`         AS `max_pastdue`,
        `d`.`tgl_jt_shgb`     AS `shgb_expired`,
        `a`.`nasabah_id`      AS `nasabah_id`
      FROM (((`dpm_online`.`kre_nominatif` `a`
           JOIN `dpm_online`.`nasabah` `b`
             ON ((`a`.`nasabah_id` = `b`.`NASABAH_ID`)))
          JOIN `dpm_online`.`kredit` `c`
            ON ((`a`.`no_rekening` = `c`.`NO_REKENING`)))
         JOIN `dpm_online`.`jaminan_dokument` `d`
           ON ((`c`.`AGUNAN_ID1` = `d`.`agunan_id`)))
      WHERE ((`a`.`baki_debet` > 0) AND (a.nasabah_id = '$nasabah_id')
             AND (`a`.`tgl_laporan` = CURDATE()))");
   //     $data = DB::connection('web')->table('view_browse_kre_tele')->where('nasabah_id', $nasabah_id)->first();

        if (empty($data)) {
            return response()->json([
                "message" => 'Data Tidak ditemukan'
            ]);
        }

        try {
            return response()->json([
                // 'code'   => 200,
                // 'status' => 'success',
                // 'count'  => sizeof($cek_sertifikat),
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
 public function getDetailTeleColl(Request $req, $id)
    {

        $data = TeleColl::where('id', $id)->first();
        //  $data = DB::connection('web')->table('view_browse_kre_tele')->get();

        if (empty($data)) {
            return response()->json([
                "message" => 'Data Tidak ditemukan'
            ]);
        }

        try {
            return response()->json([
                // 'code'   => 200,
                // 'status' => 'success',
                // 'count'  => sizeof($cek_sertifikat),
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
    public function getTeleColl(Request $req)
    {

 $user_id = $req->auth->user_id;
        $data = TeleColl::where('id_pic',$user_id)->orderBy('tanggal_telpon','DESC')->get();
        //  $data = DB::connection('web')->table('view_browse_kre_tele')->get();

        if (empty($data)) {
            return response()->json([
                "message" => 'Data Tidak ditemukan'
            ]);
        }

        try {
            return response()->json([
                // 'code'   => 200,
                // 'status' => 'success',
                // 'count'  => sizeof($cek_sertifikat),
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
