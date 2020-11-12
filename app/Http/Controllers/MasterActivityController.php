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
use App\Models\Transaksi\LogRekomCA;
use App\Models\Transaksi\LogRingAnalisa;
use App\Models\v2\Target_lending;
use App\Models\master_nilai;
use App\Models\master_transaksi;
use Image;

use App\Models\Activity;
use App\Models\TargetPeriodik;
use App\Models\TargetApproval;
use Illuminate\Support\Facades\DB;


class MasterActivityController extends BaseController
{
    public function storeTargetApprovalPeriodik (Request $req) {
 $pic = $req->pic;

     $data = array(
     "bulan" => $req->input("bulan"),
      "tahun" => $req->input("tahun"),
     "periode" => $req->input("periode"),
     "target_persen" => $req->input("target_persen"),
 "hk" => $req->input("hk"),
     "tgl" => Carbon::parse($req->input("tgl"))->format('Y-m-d')

     );

$validate = TargetApproval::where('bulan',$data['bulan'])->where('tahun',$data['tahun'])->where('periode',$data['periode'])->first();

       if ($data === null) {
            return response()->json([
                'code'  => 402,
                'message'  => 'Silahkan isi Data Terlebih Dahulu'
            ]);
        }

      if ($data['bulan'] === TargetApproval::where('bulan',$data['bulan'])->first() || $data['tahun'] === TargetApproval::where('tahun',$data['tahun'])->first() || $data['periode'] === TargetApproval::where('tahun',$data['tahun'])->first()) {
            return response()->json([
                'code'  => 402,
                'message'  => 'Data Sudah Ada'
            ]);
        }

TargetApproval::create($data);

          try {
            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Data Aktivitas Berhasil Di Input',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            // $err = DB::connection('web')->rollback();
            return response()->json([
                'code'    => 501,
                'status'  => 'error',
                'message' => $e
            ], 501);
        }

    }

     public function showApprovalperiodik (Request $req)
     {
 $pic = $req->pic;

   $data = TargetPeriodik::paginate(10);
          try {
            return response()->json([
                'data' => $data
            ]);
        } catch (\Exception $e) {
            // $err = DB::connection('web')->rollback();
            return response()->json([
                'code'    => 501,
                'status'  => 'error',
                'message' => $e
            ], 501);
        }
}
      public function storeTargetPeriodik (Request $req) {
 $pic = $req->pic;


     $data = array(
     "bulan" => $req->input("bulan"),
      "tahun" => $req->input("tahun"),
     "periode" => $req->input("periode"),
     "target_persen" => $req->input("target_persen"),
 "hk" => $req->input("hk"),
     "tgl" => Carbon::parse($req->input("tgl"))->format('Y-m-d')

     );

$validate = TargetPeriodik::where('bulan',$data['bulan'])->where('tahun',$data['tahun'])->where('periode',$data['periode'])->first();

       if ($data === null) {
            return response()->json([
                'code'  => 402,
                'message'  => 'Silahkan isi Data Terlebih Dahulu'
            ]);
        }

      if ($data['bulan'] === TargetPeriodik::where('bulan',$data['bulan'])->first() || $data['tahun'] === TargetPeriodik::where('tahun',$data['tahun'])->first() || $data['periode'] === TargetPeriodik::where('tahun',$data['tahun'])->first()) {
            return response()->json([
                'code'  => 402,
                'message'  => 'Data Sudah Ada'
            ]);
        }

TargetPeriodik::create($data);

          try {
            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Data Aktivitas Berhasil Di Input',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            // $err = DB::connection('web')->rollback();
            return response()->json([
                'code'    => 501,
                'status'  => 'error',
                'message' => $e
            ], 501);
        }

    }

    public function showTargetPeriodik (Request $req)
     {
 $pic = $req->pic;

   $data = TargetPeriodik::select('periode','bulan','tahun','target_persen')
->distinct('periode','bulan','tahun','target_persen')
->get();
          try {
            return response()->json([
                'data' => $data
            ]);
        } catch (\Exception $e) {
            // $err = DB::connection('web')->rollback();
            return response()->json([
                'code'    => 501,
                'status'  => 'error',
                'message' => $e
            ], 501);
        }
}

 public function deleteTargetPeriodik ($bulan,$tahun,$periode,Request $req)
     {
 $pic = $req->pic;

   $data = TargetPeriodik::where('bulan',$bulan)->where('tahun',$tahun)->where('periode',$periode)->delete();
          try {
            return response()->json([
              'message' => 'Data Berhasil di Hapus'
               // 'data' => $data
            ]);
        } catch (\Exception $e) {
            // $err = DB::connection('web')->rollback();
            return response()->json([
                'code'    => 501,
                'status'  => 'error',
                'message' => $e
            ], 501);
        }
}

    public function storeAktivitas (Request $req)
     {
 $pic = $req->pic;

 	$data = array(
 	"nama_jenis" => $req->input("pic"),
 	"nama_aktivitas" => $req->input("nama_aktivitas"),
 	"target_aktivitas" => $req->input("target_aktivitas"),
 	"durasi_aktivitas" => $req->input("durasi_aktivitas")
 	);


 	  if ($data === null) {
            return response()->json([
                'code'  => 402,
                'message'  => 'Silahkan isi Data Terlebih Dahulu'
            ]);
        }

        Activity::create($data);

          try {
            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Data Aktivitas Berhasil Di Input',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            // $err = DB::connection('web')->rollback();
            return response()->json([
                'code'    => 501,
                'status'  => 'error',
                'message' => $e
            ], 501);
        }
}

 public function showAktivitas (Request $req)
     {
 $pic = $req->pic;

   $data = Activity::paginate(10);
          try {
            return response()->json([
                'data' => $data
            ]);
        } catch (\Exception $e) {
            // $err = DB::connection('web')->rollback();
            return response()->json([
                'code'    => 501,
                'status'  => 'error',
                'message' => $e
            ], 501);
        }
}

public function showdetailAktivitas ($id,Request $req)
     {
 $pic = $req->pic;

   $data = Activity::where('id',$id)->first();

 if ($data === null) {
            return response()->json([
                'code'  => 402,
                'message'  => 'Data Aktivitas dengan id = '.$id.'tidak ditemukan'
            ]);
        }


          try {
            return response()->json([
                'data' => $data
            ]);
        } catch (\Exception $e) {
            // $err = DB::connection('web')->rollback();
            return response()->json([
                'code'    => 501,
                'status'  => 'error',
                'message' => $e
            ], 501);
        }
}

public function putAktivitas ($id,Request $req)
     {
 $pic = $req->pic;

   $data = Activity::where('id',$id)->first();

  $dataAktivitas = array(
            'nama_jenis'      => empty($req->input('nama_jenis'))     ? $data->nama_jenis : $req->input('nama_jenis'),
             'nama_aktivitas'      => empty($req->input('nama_aktivitas'))     ? $data->nama_aktivitas : $req->input('nama_aktivitas'),
 'target_aktivitas'      => empty($req->input('target_aktivitas'))     ? $data->target_aktivitas : $req->input('target_aktivitas'),
  'durasi_aktivitas'      => empty($req->input('durasi_aktivitas'))     ? $data->durasi_aktivitas : $req->input('durasi_aktivitas')
           
        );
 if ($data === null) {
            return response()->json([
                'code'  => 402,
                'message'  => 'Data Aktivitas dengan id = '.$id.'tidak ditemukan'
            ]);
        }



  $data = Activity::where('id',$id)->update($dataAktivitas);

          try {
            return response()->json([
              'data'  => $dataAktivitas,
                'message' => 'data berhasil terupdate'
            ]);
        } catch (\Exception $e) {
            // $err = DB::connection('web')->rollback();
            return response()->json([
                'code'    => 501,
                'status'  => 'error',
                'message' => $e
            ], 501);
        }
}

public function deletedetailAktivitas ($id,Request $req)
     {
 $pic = $req->pic;

   $data = Activity::where('id',$id)->first();

 if ($data === null) {
            return response()->json([
                'code'  => 402,
                'message'  => 'Data Aktivitas dengan id = '.$id.'tidak ditemukan'
            ]);
        }

  $data = Activity::where('id',$id)->delete();

          try {
            return response()->json([
                'message' => 'data berhasil terhapus'
            ]);
        } catch (\Exception $e) {
            // $err = DB::connection('web')->rollback();
            return response()->json([
                'code'    => 501,
                'status'  => 'error',
                'message' => $e
            ], 501);
        }
}

public function filterAktivitas(Request $req) {


       $jenis_pic = $req->input('pic');
    //    $range_start = $req->input('range_start');
      //  $range_end = $req->input('range_end');
   // dd($nomor_so);
  
if (!empty($jenis_pic)) {
$filter = Activity::where('nama_jenis','=',$jenis_pic)
->paginate(10);
 } elseif ($jenis_pic === "") {
$filter = Activity::paginate(10);
 } else {
    $jenis_pic = null;
 }

 if (empty($filter)) {
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



// }
// }
