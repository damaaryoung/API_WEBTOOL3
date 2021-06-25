<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use App\Http\Requests\Transaksi\BlankRequest;
use App\Http\Requests\Pengajuan\TeleAssignRequest;
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

use App\Models\TeleSales;
use App\Models\Activityhmhb;
use App\Models\TeleAssign;

// use Intervention\Image\ImageManagerStatic as Image;


use App\Models\MasterActivity\Activity;
use App\Models\MasterActivity\TargetPeriodik;
use App\Models\MasterActivity\TargetApproval;
use Illuminate\Support\Facades\DB;


class TeleSales_Controller extends BaseController
{

    public function storetelesales(Request $req)
    {
$user_id = $req->auth->user_id;
$cabang = $req->auth;
        $data = array(
          //  "total call"    => $req->input('total_call'),
            "tgl_telp" => Carbon::now(),
            "no_kontrak" => $req->input('no_kontrak'),
            "nama_debitur" => $req->input('nama_deb'),
            "tanggal_lahir" => $req->input('tgl_lahir_deb'),
            "usia_debitur" => $req->input('umur'),
            "no_telp_1" => $req->input('no_telp_1'),
            "no_telp_2" => $req->input('no_telp_2'),
            "no_telp_3" => $req->input('update_telp'),
            "alamat_domisili" => $req->input('alamat'),
            "update_pekerjaan" => $req->input('update_pekerjaan'),
            "update_penghasilan" => $req->input('update_penghasilan'),
            "plafon_awal" => $req->input('plafon_awal'),
            "angsuran_ke" => $req->input('angsuran_ke'),
            "sisa_angsuran" => $req->input('sisa_angsuran'),
            "max_pastdue" => $req->input('max_pastdue'),
            "nominal_angsuran" => $req->input('nominal_angsuran'),
            "taksasi_agunan" => $req->input('taksasi_agunan'),
            "baki_debet" => $req->input('baki_debet'),
            "jenis_agunan" => $req->input('jenis_agunan'),
            "shgb_expired" => $req->input('tgl_shgb'),
            "total_pelunasan" => $req->input('total_pelunasan'),
            "pengajuan_ro" => $req->input('pengajuan_ro'),
            "tenor" => $req->input('tenor'),
            "produk_kredit" => $req->input('produk_kredit'),
            "rate_bulan" => $req->input('rate'),
            "angsuran" => $req->input('angsuran'),
            "biaya_provisi" => $req->input('biaya_provisi'),
            "biaya_adm" => $req->input('biaya_admin'),
            "biaya_cc" => $req->input('biaya_cc'),
            "dsr" => $req->input('dsr'),
            "idir" => $req->input('idir'),
            "ltv" => $req->input('ltv'),
            "total_pencairan" => $req->input('total_pencairan'),

            "result_contacted" => $req->input('contacted'),
            "result_uncontacted" => $req->input('uncontacted'),
            "result_unconnected" => $req->input('unconnected'),
            "tgl_janji_bayar" => $req->input('tgl_janji_bayar'),
            "metode_pembayaran" => $req->input('metode_bayar'),
            "note_tele_sales" => $req->input('note_tele_sales'),
	     'id_pic' => $user_id,
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
            $tele_sales = TeleSales::create($data);
            return response()->json([
                // 'code'   => 200,
                // 'status' => 'success',
                // 'count'  => sizeof($cek_sertifikat),
                'data'   => $tele_sales
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function viewNasabahMikro(Request $req)
    {


        $get_data = DB::connection('web')->table('view_mikro_browse_credit')->select('no_rekening AS nomor_kontrak', 'nama_nasabah AS nama_debitur', 'alamat AS alamat_domisili')->paginate(10);



        if (empty($get_data)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Aktivitas kosong'
            ], 404);
        }

        try {
            return response()->json([
                // 'code'   => 200,
                // 'status' => 'success',
                // 'count'  => sizeof($cek_sertifikat),
                'data'   => $get_data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }
 public function getDetailTeleSales(Request $req, $id)
    {

        $data = TeleSales::where('id', $id)->first();
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
    public function getTeleSales(Request $req)
    {

 $user_id = $req->auth->user_id;
        $data = TeleSales::where('id_pic',$user_id)->orderBy('tgl_telp','DESC')->get();
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

  public function indexActivityHmHb(Request $req)
    {
        $jenis_pic = $req->input("jenis_pic");
        $jenis_aktivitas = $req->input("jenis_aktivitas");
        $pic = $req->pic; // From PIC middleware
        $user_id = $req->auth;
        $cabang = array();
        $i = 0;
        foreach ($pic as $val) {
            $cabang[] = $val['id_cabang'];
            $i++;
        }
        //   dd($jenis_pic, $jenis_aktivitas);
        if ($jenis_pic === 'AO' && $jenis_aktivitas === 'TELESALES') {
            $data = Activityhmhb::where('jenis_pic', $jenis_pic)->where('activity', 'TELESALES')->paginate(10);
        } else {
            Activityhmhb::paginate(10);
        }

        if (empty($data)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Trans So Kosong'
            ], 404);
        }
        try {
            return response()->json([
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
 public function assignTele(Request $req, TeleAssignRequest $request)
    {
        $user_id = $req->auth->user_id;
        $pic = $req->pic; // From PIC middleware

        $arr = array();
        $i = 0;
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
        $id_pic = array();
        $i = 0;
        foreach ($pic as $val) {
            $id_pic[] = $val['id'];
            $i++;
        }
        //  dd($arr);
        $id_area   = $arr;
        $id_cabang = $arrr;

        // dd($id_cabang);
        $scope     = $arrrr;
        $assignList = TeleAssign::orderBy('created_at', 'desc');
        if (empty($assignList)) {
            return response()->json([
                "code" => 404,
                "message" => "Data Assignment Kosong"
            ], 404);
        }

        $data = array(
            'nama_debitur' => $req->input('nama_debitur'),
'no_rekening' => $req->input('no_rekening'),
            'no_hp' => $request->input('no_hp'),
            'no_hp2' => $request->input('no_hp2'),
            'produk' => $request->input('produk'),
            'new_plafond' => $request->input('new_plafond'),
            'new_angsuran' => $request->input('new_angsuran'),
            'new_tenor' => $request->input('new_tenor'),
            'baki_debet' => $request->input('baki_debet'),
'kode_cabang' => $request->input('kode_cabang'),
            'notes' => $req->input('notes'),
            'user_id' => $user_id,
            'id_pic' => $id_pic[0],
            'id_area' => $id_area[0],
            'id_cabang' => $id_cabang[0],
'created_at' => Carbon::now()
        );

        if (empty($data)) {
            return response()->json([
                "code" => 404,
                "message" => "Data Inputan Kosong"
            ], 404);
        }

        $save = TeleAssign::create($data);

        try {
            return response()->json([
                "code" => 200,
                "data" => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }
   public function assignTeleIndex(Request $req)
    {
        $user_id = $req->auth->user_id;
        $pic = $req->pic; // From PIC middleware
        $arr = array();
        $i = 0;
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
        $query_dir = TeleAssign::select(
            'id',
            'no_rekening',
            'source',
            'nama_debitur',
            'no_hp',
            'no_hp2',
            'produk',
            'new_plafond',
            'new_angsuran',
            'new_tenor',
            'baki_debet',
            'notes',
            'user_id',
            'id_pic',
            'id_area',
            DB::connection('web')->raw("(SELECT nama FROM mk_cabang
            WHERE id = kode_cabang) as nama_cabang"),
            'created_at',
            'updated_at'
        )->where('user_id', $user_id)->orderBy('created_at', 'desc')->get();
        // $query = Helper::checkDir($scope, $query_dir, $id_area, $id_cabang);
        if (empty($query_dir)) {
            return response()->json([
                "code" => 404,
                "message" => "Data Assignment Kosong"
            ], 404);
        }
        //dd($query);
        $arrayData = array();
        //  foreach ($query as $key => $val) {
        //     $arrayData[$key]['source'] = $val->source;
        //     $arrayData[$key]['nama_debitur'] = $val->nama_debitur;
        //      $arrayData[$key]['no_hp'] = $val->no_hp;
        //      $arrayData[$key]['no_hp2'] = $val->no_hp2;
        //       $arrayData[$key]['produk'] = $val->produk;
        //       $arrayData[$key]['new_plafond'] = $val->new_plafond;
        //      $arrayData[$key]['new_angsuran'] = $val->new_angsuran;
        //      $arrayData[$key]['new_tenor'] = $val->new_tenor;
        //      $arrayData[$key]['baki_debet'] = $val->baki_debet;
        //      $arrayData[$key]['notes'] = $val->notes;
        //     }


        try {
            return response()->json([
                "code" => 200,
                "data" => $query_dir
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }
 public function assignTeleShow(Request $req, $id)
    {
        $user_id = $req->auth->user_id;
        $pic = $req->pic; // From PIC middleware
        $arr = array();
        $i = 0;
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
        $show = TeleAssign::where('id', $id)->first();

        if (empty($show)) {
            return response()->json([
                "code" => 404,
                "message" => "Data Assignment Kosong"
            ], 404);
        }
        //dd($query);

        try {
            return response()->json([
                "code" => 200,
                "data" => $show
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

  public function assignTeleIndexHMHB(Request $req)
    {
        $user_id = $req->auth->user_id;
        $pic = $req->pic; // From PIC middleware
        $arr = array();
        $i = 0;
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
        $query_dir = TeleAssign::select(
            'id',
            'no_rekening',
            'source',
            'nama_debitur',
            'no_hp',
            'no_hp2',
            'produk',
            'new_plafond',
            'new_angsuran',
            'new_tenor',
            'baki_debet',
            'notes',
            'user_id',
            // DB::connection('web')->raw('(SELECT nama FROM dpm_online.user WHERE user_id = "user_id") as nama_user'),
            DB::connection('web')->raw('(SELECT nama FROM m_pic WHERE id = id_pic) as nama_user'),
            'id_pic',
            'id_area',
            DB::connection('web')->raw("(SELECT nama FROM mk_cabang
            WHERE id = kode_cabang) as nama_cabang"),
            'created_at',
            'updated_at'
        )->orderBy('created_at', 'desc')->get();

        
        // $query = Helper::checkDir($scope, $query_dir, $id_area, $id_cabang);
        if (empty($query_dir)) {
            return response()->json([
                "code" => 404,
                "message" => "Data Assignment Kosong"
            ], 404);
        }
        //dd($query);
        $arrayData = array();
        //  foreach ($query as $key => $val) {
        //     $arrayData[$key]['source'] = $val->source;
        //     $arrayData[$key]['nama_debitur'] = $val->nama_debitur;
        //      $arrayData[$key]['no_hp'] = $val->no_hp;
        //      $arrayData[$key]['no_hp2'] = $val->no_hp2;
        //       $arrayData[$key]['produk'] = $val->produk;
        //       $arrayData[$key]['new_plafond'] = $val->new_plafond;
        //      $arrayData[$key]['new_angsuran'] = $val->new_angsuran;
        //      $arrayData[$key]['new_tenor'] = $val->new_tenor;
        //      $arrayData[$key]['baki_debet'] = $val->baki_debet;
        //      $arrayData[$key]['notes'] = $val->notes;
        //     }


        try {
            return response()->json([
                "code" => 200,
                "data" => $query_dir
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
