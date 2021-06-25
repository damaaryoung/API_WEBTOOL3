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
use App\Models\Transaksi\LogRekomCA;
use App\Models\Transaksi\LogRingAnalisa;
use Image;
use Illuminate\Support\Facades\DB;

class SertifikatController extends BaseController
{

     public function index(Request $req)
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

        $query_dir = TransAO::with('so', 'pic', 'cabang');

        $query = Helper::checkDir($scope, $query_dir, $id_area, $id_cabang);

$trans_so = TransSO::select('trans_so.id','trans_so.nomor_so','mk_cabang.nama AS nama_cabang','calon_debitur.nama_lengkap','fasilitas_pinjaman.plafon','agunan_tanah.status AS status_sertifikat','agunan_tanah.plan_akad')->join('calon_debitur','trans_so.id_calon_debitur','=','calon_debitur.id')->join('fasilitas_pinjaman','trans_so.id_fasilitas_pinjaman','=','fasilitas_pinjaman.id')->join('mk_cabang','trans_so.id_cabang','=','mk_cabang.id')->join('trans_ao','trans_ao.id_trans_so','=','trans_so.id')->join('agunan_tanah','trans_ao.id_agunan_tanah','=','agunan_tanah.id')
->orderBy('trans_so.created_at','DESC')
->paginate(10);
        // $TransAO = TransAO::select('trans_so.id',
        //      // 'calon_debitur.nama_lengkap',
        //    // 'calon_debitur.alamat_ktp',
        //     'agunan_tanah.no_sertifikat','agunan_tanah.tgl_ukur_sertifikat','agunan_tanah.nama_pemilik_sertifikat','agunan_tanah.alamat',
        //     // 'agunan_tanah.tanggal_sertifikat',
        //     'agunan_tanah.luas_tanah',
        //     'trans_so.nomor_so',
        //     // 'trans_so.cabang',
        //     // 'trans_so.plafon',
        //     // 'agunan_tanah.status'
        // )->join('agunan_tanah','trans_ao.id_agunan_tanah','=','agunan_tanah.id')->join('trans_so','trans_ao.id_trans_so','=','trans_so.id')->get();

 // dd($trans_so);
        if (empty($query)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data di Agunan Tanah masih kosong'
            ], 404);
        }

//          foreach ($query as $key => $val) {
// $debitur = TransSO::select('calon_debitur.nama_lengkap','calon_debitur.alamat_ktp')->join('calon_debitur','trans_so.id_calon_debitur','=','calon_debitur.id')->join('trans_ao','trans_ao.id_trans_so','=','trans_so.id')->where('trans_ao.id_agunan_tanah',$val->id_agunan_tanah)->get();
// dd($val->id_agunan_tanah);
//  $data[$key] = [
//                 'id_trans_so'    => $val->id_trans_so == null ? null : (int) $val->id_trans_so,
//                 'nama_debitur'       => $debitur->nama_lengkap,
//                 'alamat_debitur'       => $debitur->alamat_ktp,
//                 'no_shm'       => $debitur->no_sertifikat,
//                 'no_suratukur'  => $debitur->tgl_ukur_sertifikat,
//                 'nama_pemilik_sertifikat'  => $debitur->nama_pemilik_sertifikat,
//                   'alamat_sertifikat'  => $debitur->alamat,
//                   'tanggal_sertifikat'  => $debitur->tanggal_sertifikat,
//                   'luas_tanah' => $debitur->luas_tanah,
//                 // 'nomor_so'        => $val->so['nomor_so'],
//                 // 'cabang'        => $val->so['cabang'],
//                 'plafon' => $val->so['plafon'],
//                 'status'  => $debitur->alamat
//             ];
//          }
  try {
            return response()->json([
                // 'code'   => 200,
                // 'status' => 'success',
                // 'count'  => sizeof($trans_so),
                 'data'   => $trans_so
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }


    }


 public function show($id, Request $req)
    {
      $pic = $req->pic;

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

$cek_sertifikat = Debitur::select('trans_so.id AS id','trans_so.nomor_so as no_rekening'  ,'calon_debitur.nama_lengkap','calon_debitur.alamat_ktp','agunan_tanah.no_sertifikat AS no_shm','jenis_sertifikat AS jenis_jaminan','agunan_tanah.tgl_sertifikat AS tgl_sertifikat','agunan_tanah.tgl_ukur_sertifikat AS nomor_surat_ukur','mk_cabang.nama AS nama_kota','agunan_tanah.nama_pemilik_sertifikat AS nama_pemilik_sertifikat','agunan_tanah.alamat','agunan_tanah.luas_tanah',
    'agunan_tanah.asli_ajb','agunan_tanah.asli_imb','agunan_tanah.asli_sppt','agunan_tanah.asli_sppt','agunan_tanah.asli_imb','asli_skmht','agunan_tanah.asli_gambar_denah','agunan_tanah.asli_imb','agunan_tanah.asli_surat_roya','agunan_tanah.asli_sht','agunan_tanah.asli_stts','agunan_tanah.asli_ssb','agunan_tanah.ajb',
    'agunan_tanah.imb','agunan_tanah.sppt','agunan_tanah.no_sppt','agunan_tanah.sppt_tahun','agunan_tanah.skmht','agunan_tanah.gambar_denah','agunan_tanah.surat_roya','agunan_tanah.sht','agunan_tanah.no_sht','agunan_tanah.sht_propinsi','agunan_tanah.sht_kota','agunan_tanah.stts','agunan_tanah.stts_tahun','agunan_tanah.ssb','agunan_tanah.ssb_atas_nama','agunan_tanah.ssb_tahun','agunan_tanah.lain_lain','agunan_tanah.no_ajb','agunan_tanah.no_imb','agunan_tanah.no_sppt','agunan_tanah.tgl_ajb',
'agunan_tanah.status','agunan_tanah.plan_akad')->join('trans_so','trans_so.id_calon_debitur','=','calon_debitur.id')->join('trans_ao','trans_ao.id_trans_so','=','trans_so.id')->join('agunan_tanah','trans_ao.id_agunan_tanah','=','agunan_tanah.id')->join('mk_cabang','trans_so.id_cabang','=','mk_cabang.id')
 ->where('trans_so.id',$id)
->get();

 if (empty($cek_sertifikat)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Sertifikat kosong'
            ], 404);
        }

try {
            return response()->json([
                // 'code'   => 200,
                // 'status' => 'success',
                // 'count'  => sizeof($cek_sertifikat),
                'data'   => $cek_sertifikat
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }



    }

    public function update($id,Request $req)
    {
        $pic = $req->pic; // From PIC middleware

        $ser = AgunanTanah::where('id_trans_so',$id)->first();
        
        $data = array(
          'nama_pemilik_sertifikat'  => empty($req->input('nama'))     ? $ser->nama_pemilik_sertifikat : $req->input('nama'),
          'alamat'  => empty($req->input('alamat'))     ? $ser->alamat : $req->input('alamat'),
          'no_sertifikat'  => empty($req->input('no_shm'))     ? $ser->no_sertifikat : $req->input('no_shm'),
          'tgl_ukur_sertifikat'  => empty($req->input('nomor_surat_ukur'))     ? $ser->nomor_surat_ukur : $req->input('nomor_surat_ukur'),
          'tgl_sertifikat'  => empty($req->input('tgl_sertifikat'))     ? $ser->tgl_sertifikat : $req->input('tgl_sertifikat'),
          'luas_tanah'  => empty($req->input('luas_tanah'))     ? $ser->luas_tanah : $req->input('luas_tanah'),
'lain_lain'  => empty($req->input('lain_lain'))     ? $ser->lain_lain : $req->input('lain_lain'),
           'status'  => empty($req->input('status'))     ? 'WAITING' : $req->input('status'),
           'asli_ajb'  => empty($req->input('asli_ajb'))     ? $ser->asli_ajb : $req->input('asli_ajb'),
           'asli_imb'  => empty($req->input('asli_imb'))     ? $ser->asli_imb : $req->input('asli_imb'),
           'asli_sppt'  => empty($req->input('asli_sppt'))     ? $ser->asli_sppt : $req->input('asli_sppt'),
           'asli_skmht'  => empty($req->input('asli_skmht'))     ? $ser->asli_skmht : $req->input('asli_skmht'),
           'asli_gambar_denah'  => empty($req->input('asli_gambar_denah'))     ? $ser->asli_gambar_denah : $req->input('asli_gambar_denah'),
           'asli_surat_roya'  => empty($req->input('asli_surat_roya'))     ? $ser->asli_surat_roya : $req->input('asli_surat_roya'),
           'asli_sht'  => empty($req->input('asli_sht'))     ? $ser->asli_sht : $req->input('asli_sht'),
           'asli_stts'  => empty($req->input('asli_stts'))     ? $ser->asli_stts : $req->input('asli_stts'),
           'asli_ssb'  => empty($req->input('asli_ssb'))     ? $ser->asli_ssb : $req->input('asli_ssb'),
            'ajb'  => empty($req->input('ajb'))     ? $ser->ajb : $req->input('ajb'),
           'imb'  => empty($req->input('imb'))     ? $ser->imb : $req->input('imb'),
           'sppt'  => empty($req->input('sppt'))     ? $ser->sppt : $req->input('sppt'),
           'no_sppt'  => empty($req->input('no_sppt'))     ? $ser->no_sppt : $req->input('no_sppt'),
           'sppt_tahun'  => empty($req->input('sppt_tahun'))     ? $ser->sppt_tahun : $req->input('sppt_tahun'),
           'skmht'  => empty($req->input('skmht'))     ? $ser->skmht : $req->input('skmht'),
           'gambar_denah'  => empty($req->input('gambar_denah'))     ? $ser->gambar_denah : $req->input('gambar_denah'),
           'surat_roya'  => empty($req->input('surat_roya'))     ? $ser->surat_roya : $req->input('surat_roya'),
           'sht'  => empty($req->input('sht'))     ? $ser->sht : $req->input('sht'),
           'no_sht'  => empty($req->input('no_sht'))     ? $ser->no_sht : $req->input('no_sht'),
           'sht_propinsi'  => empty($req->input('sht_propinsi'))     ? $ser->sht_propinsi : $req->input('sht_propinsi'),
           'sht_kota'  => empty($req->input('sht_kota'))     ? $ser->sht_kota : $req->input('sht_kota'),
           'stts'  => empty($req->input('stts'))     ? $ser->stts : $req->input('stts'),
           'stts_tahun'  => empty($req->input('stts_tahun'))     ? $ser->stts_tahun : $req->input('stts_tahun'),
           'ssb'  => empty($req->input('ssb'))     ? $ser->ssb : $req->input('ssb'),
           'ssb_atas_nama'  => empty($req->input('ssb_atas_nama'))     ? $ser->ssb_atas_nama : $req->input('ssb_atas_nama'),
'ssb_tahun'  => empty($req->input('ssb_tahun'))     ? $ser->ssb_tahun : $req->input('ssb_tahun'),
           'lain_lain'  => empty($req->input('lain_lain'))     ? $ser->lain_lain : $req->input('lain_lain'),
            'no_ajb'  => empty($req->input('no_ajb'))     ? $ser->no_ajb : $req->input('no_ajb'),
             'no_imb'  => empty($req->input('no_imb'))     ? $ser->no_imb : $req->input('no_imb'),
              'no_sppt'  => empty($req->input('no_sppt'))     ? $ser->no_sppt : $req->input('no_sppt'),
               'tgl_ajb'  => empty($req->input('tgl_ajb'))     ? $ser->tgl_ajb : $req->input('tgl_ajb'),
'plan_akad'  => empty(Carbon::parse($req->input('plan_akad'))->format('Y-m-d'))     ? $ser->tgl_ajb : Carbon::parse($req->input('plan_akad'))->format('Y-m-d')
        );

AgunanTanah::where('id_trans_so',$id)->update($data);

       
        try {
            return response()->json([
                // 'code'   => 200,
                // 'status' => 'success',
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

 public function filter(Request $req) {


       $nomor_so = $req->input('nomor_so');
        $range_start = $req->input('range_start');
        $range_end = $req->input('range_end');
   // dd($nomor_so);
    if ($req->has('nomor_so') || empty($req->has('range_start')) && empty($req->has('range_end')) ) {
$filter = TransSO::select('trans_so.id','trans_so.nomor_so','mk_cabang.nama AS nama_cabang','calon_debitur.nama_lengkap','fasilitas_pinjaman.plafon','agunan_tanah.status AS status_sertifikat')->join('calon_debitur','trans_so.id_calon_debitur','=','calon_debitur.id')->join('fasilitas_pinjaman','trans_so.id_fasilitas_pinjaman','=','fasilitas_pinjaman.id')->join('mk_cabang','trans_so.id_cabang','=','mk_cabang.id')->join('trans_ao','trans_ao.id_trans_so','=','trans_so.id')->join('agunan_tanah','trans_ao.id_agunan_tanah','=','agunan_tanah.id')->where('trans_so.nomor_so','LIKE',"%{$nomor_so}")
//->orWhere('mk_cabang.nama','LIKE',"%{$nomor_so}%")
//->orWhere('calon_debitur.nama_lengkap','LIKE',"%{$nomor_so}%")
// ->orWhere('calon_debitur.nama_lengkap','LIKE','%{$nama}%')
//->orWhere('nama_cabang','LIKE','%{$cabang}%')
//->orWhere('status_sertifikat','LIKE','%{$status}%')
->paginate(10)->appends($req->input());

} elseif ( empty($nomor_so) || $req->has('range_start') && $req->has('range_end')) {
    $filter = TransSO::select('trans_so.id','trans_so.nomor_so','mk_cabang.nama AS nama_cabang','calon_debitur.nama_lengkap','fasilitas_pinjaman.plafon','agunan_tanah.status AS status_sertifikat')->join('calon_debitur','trans_so.id_calon_debitur','=','calon_debitur.id')->join('fasilitas_pinjaman','trans_so.id_fasilitas_pinjaman','=','fasilitas_pinjaman.id')->join('mk_cabang','trans_so.id_cabang','=','mk_cabang.id')->join('trans_ao','trans_ao.id_trans_so','=','trans_so.id')->join('agunan_tanah','trans_ao.id_agunan_tanah','=','agunan_tanah.id')->whereBetween('trans_ao.created_at',[$range_start,$range_end])->paginate(10)->appends($req->input());

} elseif ( $nomor_so = "" || $range_start = "" || $range_end = "") {
   $filter = TransSO::select('trans_so.id','trans_so.nomor_so','mk_cabang.nama AS nama_cabang','calon_debitur.nama_lengkap','fasilitas_pinjaman.plafon','agunan_tanah.status AS status_sertifikat')->join('calon_debitur','trans_so.id_calon_debitur','=','calon_debitur.id')->join('fasilitas_pinjaman','trans_so.id_fasilitas_pinjaman','=','fasilitas_pinjaman.id')->join('mk_cabang','trans_so.id_cabang','=','mk_cabang.id')->join('trans_ao','trans_ao.id_trans_so','=','trans_so.id')->join('agunan_tanah','trans_ao.id_agunan_tanah','=','agunan_tanah.id')->paginate(10)->appends($req->input());

   // $users->appends(request()->input())->links();
} else {
   $filter = TransSO::select('trans_so.id','trans_so.nomor_so','mk_cabang.nama AS nama_cabang','calon_debitur.nama_lengkap','fasilitas_pinjaman.plafon','agunan_tanah.status AS status_sertifikat')->join('calon_debitur','trans_so.id_calon_debitur','=','calon_debitur.id')->join('fasilitas_pinjaman','trans_so.id_fasilitas_pinjaman','=','fasilitas_pinjaman.id')->join('mk_cabang','trans_so.id_cabang','=','mk_cabang.id')->join('trans_ao','trans_ao.id_trans_so','=','trans_so.id')->join('agunan_tanah','trans_ao.id_agunan_tanah','=','agunan_tanah.id')->paginate(10)->appends($req->input());
}

 // if ($value == 'default') {
 //            $res = $filter;
 //        } else {
 //            $res = $filter->where($key, $operator, $func_value);
 //        }

 //        if ($limit == 'default') {
 //            $result = $res;
 //        } else {
 //            $result = $res->limit($limit);
 //        }

 if (empty($filter)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Sertifikat kosong'
            ], 404);
        }

try {
            return response()->json([
                // 'code'   => 200,
                // 'status' => 'success',
                // 'count'  => sizeof($cek_sertifikat),
                'data'   => $filter
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
