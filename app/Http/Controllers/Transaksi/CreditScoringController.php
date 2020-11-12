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
use App\Models\v2\Target_lending;
use App\Models\master_nilai;
use App\Models\master_transaksi;
use Image;
use Illuminate\Support\Facades\DB;


class CreditScoringController extends BaseController
{

    public function store ($req Request) {
 $pic = $req->pic;

 $getcredit = DB::connection::('web')->table('view_creditscoring')->get();


 $data = array();

 foreach ($getcredit as $key => $val) {
  $data[$key]['created_at'] = $val->created_at;
  $data[$key]['id_trans_so'] = $val->id_trans_so;
  $data[$key]['nomor_so'] = $val->nomor_so;
  $data[$key]['nama_lengkap'] = $val->nama_lengkap;
  $data[$key]['nama_area'] = $val->nama_area;
  $data[$key]['nama_cabang'] = $val->nama_cabang;
  $data[$key]['nama_so'] = $val->nama_so;
  $data[$key]['nama_ao'] = $val->nama_ao;
  $data[$key]['umur'] = $val->umur;
  $data[$key]['jumlah_tanggungan'] = $val->jumlah_tanggungan;
  $data[$key]['pendidikan_terakhir'] = $val->pendidikan_terakhir;
  $data[$key]['ltv'] = $val->ltv;
  $data[$key]['dsr'] = $val->dsr;
  $data[$key]['idir'] = $val->idir;
  $data[$key]['tenor'] = $val->tenor;
  $data[$key]['foto_pembukuan_usaha'] = $val->foto_pembukuan_usaha;
  $data[$key]['lamp_buku_tabungan'] = $val->lamp_buku_tabungan;
$data[$key]['lamp_foto_usaha'] = $val->lamp_foto_usaha;
$data[$key]['lamp_imb'] = $val->lamp_imb;
$data[$key]['lamp_skk'] = $val->lamp_skk;
$data[$key]['lamp_sku'] = $val->lamp_sku;
$data[$key]['lamp_slipgaji'] = $val->lamp_slipgaji;






    }
}
