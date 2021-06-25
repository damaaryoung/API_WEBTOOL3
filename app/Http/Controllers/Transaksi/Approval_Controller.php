<?php

namespace App\Http\Controllers\Transaksi;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;

//Model
use App\Models\Pengajuan\AO\PemeriksaanAgunTan;
use App\Models\Pengajuan\AO\AgunanKendaraan;
use App\Models\Pengajuan\AO\PemeriksaanAgunKen;
use App\Http\Requests\Transaksi\ApprovalReq;
use App\Models\Pengajuan\CAA\Penyimpangan;
use App\Models\Pengajuan\AO\AgunanTanah;
use App\Http\Requests\Transaksi\BlankRequest;
use App\Models\Pengajuan\AO\PendapatanUsaha;
use App\Models\Pengajuan\AO\RekomendasiAO;
use App\Models\Pengajuan\CA\TabDebt;
use App\Models\Pengajuan\CA\MutasiBank;
use App\Models\Pengajuan\CA\InfoACC;
use App\Models\Pengajuan\CA\RingkasanAnalisa;
use App\Models\Pengajuan\CA\RekomendasiPinjaman;
use App\Models\Pengajuan\CA\AsuransiJiwa;
use App\Models\Pengajuan\CA\AsuransiJaminan;
use App\Models\Pengajuan\CA\RekomendasiCA;
use App\Models\Transaksi\LogRekomCA;
use App\Models\Pengajuan\CA\LogRingkasanAnalisa;
use App\Models\Pengajuan\CA\LogAsuransiJiwa;
use App\Models\Pengajuan\CA\LogAsuransiJaminan;
use App\Models\Pengajuan\CA\LogAsuransiJaminanKen;
// use Illuminate\Support\Facades\File;
use App\Models\Pengajuan\AO\KapBulanan;
use App\Models\Pengajuan\CA\AsuransiJaminanKen;
use App\Models\Transaksi\Approval;
use App\Models\Transaksi\TransCAA;
use App\Models\Transaksi\TransCA;
use App\Models\Transaksi\TransAO;
use App\Models\Transaksi\TransSO;
use App\Models\Pengajuan\AO\ValidModel;
use App\Models\Pengajuan\AO\VerifModel;
use App\Models\Pengajuan\SO\Debitur;
use App\Models\AreaKantor\JPIC;
use App\Models\AreaKantor\PIC;
use GuzzleHttp\Client;

//request
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Approval_Controller extends BaseController
{
    public function list_team(Request $req)
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
        $query = PIC::with(['jpic', 'area', 'cabang'])
            ->whereHas('jpic', function ($q) {
                // Query the name field in status table
                $q->where('bagian', '=', 'team_caa');
            })
            ->where('flg_aktif', 1);

        if ($scope == 'CABANG') {

            $parQuery = $query->whereHas('cabang', function ($q) use ($id_cabang) {
                $q->where('id', $id_cabang);
                $q->orWhere('nama', 'Pusat');
            })
                ->get()
                ->sortByDesc('jpic.urutan_jabatan');
        } elseif ($scope == 'AREA') {

            $parQuery = $query->whereHas('area', function ($q) use ($id_area) {
                $q->where('id', $id_area);
                $q->orWhere('nama', 'Pusat');
            })
                ->get()
                ->sortByDesc('jpic.urutan_jabatan');
        } else {

            $parQuery = $query->get()->sortByDesc('jpic.urutan_jabatan');
        }

        if ($parQuery == '[]') {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        $data = array();
        foreach ($parQuery as $key => $val) {

            if ($key == 0) {
                $checked = true;
            } else {
                $checked = false;
            }

            $data[] = array(
                "id"        => $val->id == null ? null : (int) $val->id,
                "user_id"   => $val->user_id,
                "plafon_max" => (int) $val->plafon_caa,
                "nama_area" => $val->area['nama'],
                "cabang"    => $val->cabang['nama'],
                "jabatan"   => $val->jpic['nama_jenis'],
                "nama"      => $val->nama,
                "email"     => $val->email,
"tgl_sk"     => $val->tgl_sk,
                 "flg_aktif" => (bool) $val->flg_aktif,
                "checked"   => $checked
            );
        }

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

    public function detail_team($id_team, Request $req)
    {
        $pic = $req->pic; // From PIC middleware

        $val = PIC::with(['jpic', 'area', 'cabang'])
            ->where('flg_aktif', 1)
            ->where('id', $id_team)
            ->first();

        if ($val == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }


        $data = array(
            "id"        => $val->id      == null ? null : (int) $val->id,
            "user_id"   => $val->user_id == null ? null : (int) $val->user_id,
            "id_area"   => $val->id_area == null ? null : (int) $val->id_area,
            "nama_area" => $val->area['nama'],
            "id_cabang" => $val->id_cabang == null ? null : (int) $val->id_cabang,
            "cabang"    => $val->cabang['nama'],
            "jabatan"   => $val->jpic['nama_jenis'],
            "nama"      => $val->nama,
            "email"     => $val->email,
            "plafon_max" => (int) $val->plafon_caa,
            "flg_aktif" => (bool) $val->flg_aktif
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

    public function index($id, Request $req)
    {
        $pic = $req->pic; // From PIC middleware

        $query = Approval::with('so', 'caa', 'pic')
            ->where('id_trans_so', $id)
            ->get()
            ->sortByDesc('pic.jpic.urutan_jabatan');

        if ($query == '[]') {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        // $data = array();
        foreach ($query as $key => $val) {

            if ($val->status) {
                $status = $val->status;
            } else {
                $status = 'waiting';
            }

            $data[] = [
                'id_approval'    => $val->id          == null ? null : (int) $val->id,
                'id_trans_so'    => $val->id_trans_so == null ? null : (int) $val->id_trans_so,
                'user_id'        => $val->user_id     == null ? null : (int) $val->user_id,
                'nomor_so'       => $val->so['nomor_so'],
                'nomor_ao'       => $val->so['ao']['nomor_ao'],
                'nomor_ca'       => $val->so['ca']['nomor_ca'],
                'nomor_caa'      => $val->caa['nomor_caa'],
                'id_pic'         => $val->id_pic == null ? null : (int) $val->id_pic,
                'batas_plafon'   => (int) $val->pic['plafon_caa'],
                'nama_pic'       => $val->pic['nama'],
                // 'id_jenis_pic'   => $val->pic['id_mj_pic'],
                'jabatan'        => $val->pic['jpic']['nama_jenis'],

                'pengajuan_so'   => [
                    'plafon'  => (int) $val->so['faspin']['plafon'],
                    'tenor'   => (int) $val->so['faspin']['tenor']
                ],
                'plafon'         => (int) $val->plafon,
                'tenor'          => (int) $val->tenor,
                'rincian'        => $val->rincian,
                'status_approval' => $status,
                'tanggal'        => empty($val->updated_at) ? null : Carbon::parse($val->updated_at)->format("d-m-Y H:i:s"),
            ];
        }

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'count'  => $query->count(),
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

    public function show($id, $id_approval, Request $req)
    {
        $check_caa = TransCAA::where('status_caa', 1)->where('id_trans_so', $id)->first();

        if ($check_caa == null) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => 'Transaksi dengan id ' . $id . ' belum sampai ke CAA'
            ], 404);
        }

        $val = Approval::where('id_trans_so', $id)->where('id', $id_approval)->first();

        if ($val == null) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => 'Transaksi dengan id ' . $id . ' belum sampai ke Approval'
            ], 404);
        }

        if ($val->status) {
            $status = $val->status;
        } else {
            $status = 'waiting';
        }

        if ($val->so['faspin']['plafon'] <= $val->pic['plafon_caa']) {

            $list_status = array('accept' => true, 'forward' => false, 'reject' => true, 'return' => true);
        } else {

            $list_status = array('accept' => false, 'forward' => true, 'reject' => true, 'return' => true);
        }

        $data = [
            'id_approval'    => $val->id          == null ? null : (int) $val->id,
            'id_trans_so'    => $val->id_trans_so == null ? null : (int) $val->id_trans_so,
            'user_id'        => $val->user_id     == null ? null : (int) $val->user_id,
            'nomor_so'       => $val->so['nomor_so'],
            'nomor_ao'       => $val->so['ao']['nomor_ao'],
            'nomor_ca'       => $val->so['ca']['nomor_ca'],
            'nomor_caa'      => $val->caa['nomor_caa'],
            'id_pic'         => $val->id_pic == null ? null : (int) $val->id_pic,
            'batas_plafon'   => (int) $val->pic['plafon_caa'],
            'nama_pic'       => $val->pic['nama'],
            'jabatan'        => $val->pic['jpic']['nama_jenis'],

            'pengajuan_so'   => [
                'plafon'  => (int) $val->so['faspin']['plafon'],
                'tenor'   => (int) $val->so['faspin']['tenor']
            ],
            'plafon'         => (int) $val->plafon,
            'tenor'          => (int) $val->tenor,
            'rincian'        => $val->rincian,
            'status_approval' => $status,
            'tanggal'        => empty($val->updated_at) ? null : Carbon::parse($val->updated_at)->format("d-m-Y H:i:s"),

            'list_status' => $list_status
        ];

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
    public function approve($id, $id_approval, Request $req, ApprovalReq $request)
    {
        $pic     = $req->pic; // From PIC middleware
        $user_id = $req->auth->user_id;

        $check_so = TransSO::where('id', $id)->first();

        if ($check_so == null) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Transaksi dengan id " . $id . " belum ada di SO"
            ], 404);
        }

        $check_ao = TransAO::where('id_trans_so', $id)->where('status_ao', 1)->first();

        if ($check_ao == null) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Transaksi dengan id " . $id . " belum sampai ke AO"
            ], 404);
        }

        $check_ca = TransCA::where('id_trans_so', $id)->where('status_ca', 1)->first();

        if ($check_ca == null) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Transaksi dengan id " . $id . " belum sampai ke ca"
            ], 404);
        }

        $check_caa = TransCAA::where('id_trans_so', $id)->where('status_caa', 1)->first();

        if ($check_caa == null) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Transaksi dengan id " . $id . " belum sampai ke caa"
            ], 404);
        }

        $check = Approval::where('id', $id_approval)->where('id_trans_so', $id)->first();

        if ($check == null) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Transaksi dengan id " . $id . " dan dengan id approval " . $id_approval . " tidak ada di daftar antrian Approval"
            ], 404);
        }

        if ($check->status != 'waiting') {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Transaksi dengan id `{$id}` dan dengan id approval `{$id_approval}` sudah sudah dalam proses dengan status `{$check->status}`"
            ], 404);
        }

        $forward_q = Approval::where('id_trans_so', $id)->where('id', '>', $id_approval)->first();

        if ($forward_q == null) {
            $to_forward = null;
        } else {
            $to_forward = $forward_q->id_pic;
        }

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

        $form = array(
            'user_id'       => $user_id,
            'id_area'       => $area[0],
            'id_cabang'     => $arrr[0],
            'plafon'        => $request->input('plafon'),
            'tenor'         => $request->input('tenor'),
            'rincian'       => $request->input('rincian'),
            'status'        => $st = $request->input('status'),
            'status_crm'    => $request->input('status_crm'),
            'tujuan_forward' => $st == 'forward' ? $to_forward : null //$request->input('tujuan_forward'),
            // 'tanggal'       => Carbon::now()->toDateTimeString()
        );
//dd($form);
        DB::connection('web')->beginTransaction();

// $lpdk_status = Lpdk::where('id',$id)->first();
   //     if ($lpdk_status !== NULL) {
// Lpdk::where('id',$id)->update([ 'plafon' => $form['plafon'] , 'tenor' => $form['tenor']]);
   //     }

    //    try {

// if($form['plafon'] == 0 ) {
// $form['status'] = 'forward';
// }

 if ($form['status'] == 'accept')
        {
            Approval::where('id_trans_so',$id)->where('id', '>', $id_approval)->delete();

            $team_caa = Approval::where('id_trans_so',$id)->pluck('id_pic');
            $str = str_replace("[","",$team_caa);
            $str2 = str_replace("]","",$str);
            TransCAA::where('id_trans_so',$id)->update(['pic_team_caa' => $str2]);

      //      $lpdk_status = Lpdk::where('id',$id)->first();
      // if (!empty($lpdk_status)) {
// Lpdk::where('id',$id)->update([ 'plafon' => $form['plafon'] , 'tenor' => $form['tenor']]);
   //     }
        }
$last_pic = TransCAA::where('id_trans_so', $id)->first();
$exp = explode(",",$last_pic->pic_team_caa);
$end = end($exp);
//dd($end);
if ($form['status_crm'] == 'Ya') {
    Approval::where('id_trans_so',$id)->where('id_pic',$end)->delete();
    $team_caa = Approval::where('id_trans_so',$id)->pluck('id_pic');
            $str = str_replace("[","",$team_caa);
            $str2 = str_replace("]","",$str);
            TransCAA::where('id_trans_so',$id)->update(['pic_team_caa' => $str2]);
    // $pic = TransCAA::where('id_trans_so', $id)->first();
    // TransCAA::where('id_trans_so', $id)->update(['pic_team_caa' => $pic->pic_team_caa]);
}

            if ($form['status'] == 'accept' || $form['status'] == 'reject' || $form['status'] == 'return') {
                $status = $form['status'] . " by picID {$id_pic[0]}";
                // TransCAA::where('id_trans_so', $id)->update(['status_team_caa' => $form['status'].' by user '.$user_id]);
            } elseif ($form['status'] == 'forward') {
                $status = $form['status'] . " by picID {$id_pic[0]} to picID {$form['tujuan_forward']}";

                $email = array(
                    'subyek' => $req->input('subyek'),
                    'tujuan' => $req->input('tujuan'),
                    'cc' => $req->input('cc'),
                    'pesan' => $req->input('pesan'),
                    // 'attach1' => $req->file('attach1')
                                );
                    
                             //   dd($email);
                                $client = new Client();
                                //   $request = $client->request('POST', 'https://kmi.jari.co.id/integration/task/bulk',  [
                                $request = $client->request('POST', 'http://103.31.232.149:3838/email',  [
                                    //$request = $client->request('POST', 'kmi.jari.co.id:8080/integration/task/bulkdraft',  [
                                   // 'headers'        => ['token' => $token],
                                    'Content-type' => 'application/x-www-form-urlencoded',
                                    'form_params'          => $email
                                ]);
                                $response = $request->getBody()->getContents();
                                $sendEmail = json_decode($response, true);
            }
           

            if ($form['status'] === 'return') {
                TransCAA::where('id_trans_so', $id)->where('id_trans_so', $id)->delete();
                Approval::where('id_trans_so', $id)->delete();

                $check_nst = Penyimpangan::where('id_trans_so', $id)->first();

                if ($check_nst != null) {
                    Penyimpangan::where('id_trans_so', $id)->delete();
                }
            }

            $trans_caa = TransCAA::where('id_trans_so', $check->id_trans_so)->update(['status_team_caa' => $status]);

            $approval = Approval::where('id', $id_approval)->update($form);

            DB::connection('web')->commit();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message' => 'Data untuk berhasil di - ' . $form['status'],
                'data'   => array(
                    'approval'  => $approval,
                    'transaksi' => $trans_caa
                )
            ], 200);
        // } catch (\Exception $e) {
        //     $err = DB::connection('web')->rollback();
        //     return response()->json([
        //         'code'    => 501,
        //         'status'  => 'error',
        //         'message' => $err
        //     ], 501);
        // }
    }

    // Team Caa
    public function report_approval($id)
    {

        $check_so = TransSO::where('id', $id)->first();

        if (empty($check_so)) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Transaksi dengan id " . $id . " belum ada di SO"
            ], 404);
        }

        $check_ao = TransAO::where('status_ao', 1)->where('id_trans_so', $id)->first();

        if (empty($check_ao)) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Transaksi dengan id " . $id . " belum sampai ke AO"
            ], 404);
        }

        $check_ca = TransCA::where('status_ca', 1)->where('id_trans_so', $id)->latest()->first();

        if (empty($check_ca)) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Transaksi dengan id " . $id . " belum sampai ke CA"
            ], 404);
        }

        $check_caa = TransCAA::where('status_caa', 1)->where('id_trans_so', $id)->first();

        if (empty($check_caa)) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Transaksi dengan id " . $id . " belum sampai ke CAA"
            ], 404);
        }


        $check_team = Approval::where('id_trans_so', $id)->whereIn('id_pic', explode(",", $check_caa->pic_team_caa))->get();

        if (empty($check_team)) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Transaksi dengan id " . $id . " belum sampai ke Approval"
            ], 404);
        }

        $data = array();;
        foreach ($check_team as $key => $val) {
            $data[] = [
                'jabatan' => $val->pic['jpic']['nama_jenis'],
                'id_pic'  => $val->id_pic  == null ? null : (int) $val->id_pic,
                'user_id' => $val->user_id == null ? null : (int) $val->user_id,
                'nama_pic' => $val->pic['nama'],
                'plafon'  => $val->plafon,
                'tenor'   => $val->tenor,
                'status'  => $val->status,
                'rincian' => $val->rincian,
                'tgl_approval' => Carbon::parse($val->updated_at)->format('d-m-Y')
            ];
        }

        $AguTa = AgunanTanah::whereIn('id', explode(",", $check_ao->id_agunan_tanah))->get();

        $idTan = array();
        foreach ($AguTa as $key => $value) {

            $idTan[$key] = $value->jenis_sertifikat . ' / ' . ($value->tgl_ukur_sertifikat == null ? 'null' : $value->tgl_ukur_sertifikat);
        }

        $imTan = implode("; ", $idTan);


        // Agunan Kendaraan
        $AguKe = AgunanKendaraan::whereIn('id', explode(",", $check_ao->id_agunan_kendaraan))->get();

        $idKen = array();
        foreach ($AguKe as $key => $value) {

            $idKen[$key] = 'BPKB / ' . ($value->no_bpkb == null ? 'null' : $value->no_bpkb);
        }

        $imKen = implode("; ", $idKen);


        if ($imTan == "" && $imKen == "") {
            $jaminan = null;
        } elseif ($imTan != "" && $imKen != "") {
            $jaminan = $imTan . '; ' . $imKen;
        } elseif ($imTan == "" && $imKen != "") {
            $jaminan = $imKen;
        } elseif ($imTan != "" && $imKen == "") {
            $jaminan = $imTan;
        }

        // $url_in_array = in_array('accept', $status_in_array);


        $num_sts = array_search('accept', array_column($data, 'status'), true);;

        if ($num_sts == false) {
            $tenor  = null;
            $plafon = null;
        } else {
            $tenor  = $data[$num_sts]['tenor'];
            $plafon = $data[$num_sts]['plafon'];
        }



        $result = array(
            'id_transaksi' => $check_caa->id_trans_so == null ? null : (int) $check_caa->id_trans_so,
            'debitur' => [
                'id'   => $check_so->id_calon_debitur == null ? null : (int) $check_so->id_calon_debitur,
                'nama' => $check_so->debt['nama_lengkap']
            ],
            'approved' => [
                'id_pic'  => $check_caa->id_pic  == null ? null : (int) $check_caa->id_pic,
                'user_id' => $check_caa->user_id == null ? null : (int) $check_caa->user_id,
                'nama_ca' => $check_caa->pic['nama'],
                'plafon'  => (int) $plafon,
                'tenor'   => (int) $tenor,
                'jaminan' => $jaminan
            ],
            'list_approver' => $data
        );


        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message' => $result
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

    public function update($id, Request $request, BlankRequest $req)
    {
        $pic     = $request->pic; // From PIC middleware

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
        $user_id = $request->auth->user_id;

        $countCA = TransCA::latest('id', 'nomor_ca')->first();

        if (!$countCA) {
            $lastNumb = 1;
        } else {
            $no = $countCA->nomor_ca;

            $arr = explode("-", $no, 5);

            $lastNumb = str_replace(" [revisi]", "", $arr[4]) + 1;
        }


        //Data Transaksi SO
        $nows  = Carbon::now();
        $year  = $nows->year;
        $month = $nows->month;

        $JPIC   = JPIC::whereIn('id', $mj)->first();

        //  ID-Cabang - AO / CA / SO - Bulan - Tahun - NO. Urut
        $nomor_ca = $arrr[0] . '-' . $JPIC->nama_jenis . '-' . $month . '-' . $year . '-' . $lastNumb;

        $check_so = TransSO::where('id', $id)->where('status_das', 1)->where('status_hm', 1)->first();

        if (!$check_so) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id ' . $id . ' belum ada di SO atau belum komplit saat pemeriksaan DAS dan HM'
            ], 404);
        }

        $check_ao = TransAO::where('id_trans_so', $id)->first();

        if (!$check_ao) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id ' . $id . ' belum sampai ke AO'
            ], 404);
        }

        $check_ca = TransCA::where('id_trans_so', $id)->first();
        // dd($check_ca->id_pendapatan_usaha);
        // if ($check_ca != null) {
        //     return response()->json([
        //         'code'    => 404,
        //         'status'  => 'not found',
        //         'message' => 'Transaksi dengan id ' . $id . ' sudah ada di CA'
        //     ], 404);
        // }

        $dataCA = RekomendasiCA::where('id', $check_ca->id_recom_ca)->first()->toArray();
        //dd($dataCA);
        $logRekom = LogRekomCA::create($dataCA);

        $dataRingAnalisa = RingkasanAnalisa::where('id', $check_ca->id_ringkasan_analisa)->first()->toArray();

        $logRingAnalisa = LogRingkasanAnalisa::create($dataRingAnalisa);

        $dataAsuransiJiwa = AsuransiJiwa::where('id', $check_ca->id_asuransi_jiwa)->first()->toArray();

        $logAsuransiJiwa = LogAsuransiJiwa::create($dataAsuransiJiwa);

        $dataAsuransiKebakaran = AsuransiJaminan::where('id', $check_ca->id_asuransi_jaminan_kebakaran)->first()->toArray();

        $logAsuransiKebakaran = LogAsuransiJaminan::create($dataAsuransiKebakaran);

        $dataAsuransiKendaraan = AsuransiJaminanKen::where('id', $check_ca->id_asuransi_jaminan_kendaraan)->first()->toArray();

        $logAsuransiKendaraan = LogAsuransiJaminanKen::create($dataAsuransiKendaraan);

        $transCA = array(
            'nomor_ca'    => $nomor_ca,
            'user_id'     => $user_id,
            'id_trans_so' =>$check_so->id,
            'id_pic'      => $id_pic[0],
            'id_area'     => $area[0],
            'id_cabang'   => $arrr[0],
            'catatan_ca'  => $check_ca->catatan_ca,
            'status_ca'   => $check_ca->status_ca
        );

        $pen = PendapatanUsaha::where('id', $check_ca->id_pendapatan_usaha)->first();

        // Pendapatan Usaha Cadebt
        $dataPendapatanUsaha = array(
            'id'                    => $pen->id,
            'pemasukan_tunai'      => $pen->pemasukan_tunai,
            'pemasukan_kredit'     => $pen->pemasukan_kredit,
            'biaya_sewa'           => $pen->biaya_sewa,
            'biaya_gaji_pegawai'   => $pen->biaya_gaji_pegawai,
            'biaya_belanja_brg'    => $pen->biaya_belanja_brg,
            'biaya_telp_listr_air' => $pen->biaya_telp_listr_air,
            'biaya_sampah_kemanan' => $pen->biaya_sampah_keamanan,
            'biaya_kirim_barang'   => $pen->biaya_kirim_barang,
            'biaya_hutang_dagang'  => $pen->biaya_hutang_dagang,
            'biaya_angsuran'       => $pen->biaya_angsuran,
            'biaya_lain_lain'      => $pen->biaya_lain_lain,
            'total_pemasukan'      => $pen->total_pemasukan,
            'total_pengeluaran'      => $pen->total_pengeluaran,
            'laba_usaha'      => $pen->laba_usaha,
        );


        // Start Kapasitas Bulanan
        $kap = KapBulanan::where('id', $check_ca->id_kapasitas_bulanan)->first();
        $KapBul = array(
            'id'    => $kap->id,
            'pemasukan_cadebt' => $kap->pemasukan_cadebt,

            'pemasukan_pasangan'
            => $kap->pemasukan_pasangan,

            'pemasukan_penjamin'
            => $kap->pemasukan_penjamin,

            'biaya_rumah_tangga'
            => $kap->biaya_rumah_tangga,

            'biaya_transport'
            => $kap->biaya_transport,

            'biaya_pendidikan'
            => $kap->biaya_pendidikan,

            'telp_listr_air'
            => $kap->telp_listr_air,

            'angsuran'
            => $kap->angsuran,

            'biaya_lain'
            => $kap->biaya_lain,


            'total_pemasukan'
            => $kap->total_pemasukan,

            'total_pengeluaran'
            => $kap->total_pengeluaran,

            'penghasilan_bersih'
            => $kap->penghasilan_bersih,

            'disposable_income'
            => $kap->disposable_income,

            'ao_ca'
            => $kap->ao_ca,

            // $total_KapBul = array(
            //     'total_pemasukan'    => $ttl1 = array_sum(array_slice($inputKapBul, 0, 3)),
            //     'total_pengeluaran'  => $ttl2 = array_sum(array_slice($inputKapBul, 3)),
            //     'penghasilan_bersih' => $ttl1 - $ttl2
        );

        $rekom = RekomendasiPinjaman::where('id', $check_ca->id_rekomendasi_pinjaman)->first();

        // Ceiling Recomendasi Pinjaman
        $rekomPinjaman = array(
            'id' => $rekom->id,
            'penyimpangan_struktur'
            => $rekom->penyimpangan_struktur,

            'penyimpangan_dokumen'
            => $rekom->penyimpangan_dokumen,

            'recom_nilai_pinjaman'
            => $rekom->recom_nilai_pinjaman,

            'recom_tenor'
            => $rekom->recom_tenor,

            'recom_angsuran'
            => $rekom->recom_angsuran,

            'recom_produk_kredit'
            => $rekom->recom_produk_kredit,

            'note_recom'
            => $rekom->note_recom,

            'bunga_pinjaman'
            => $rekom->bunga_pinjaman,

            'nama_ca'
            => $rekom->nama_ca,
        );

        // Rekomendasi Angsuran pada table rrekomendasi_pinjaman
        // $plafonCA = $rekomPinjaman['recom_nilai_pinjaman'] == null ? 0 : $rekomPinjaman['recom_nilai_pinjaman'];
        // $tenorCA  = $rekomPinjaman['recom_tenor']          == null ? 0 : $rekomPinjaman['recom_tenor'];
        // $bunga    = $rekomPinjaman['bunga_pinjaman']       == null ? 0 : ($rekomPinjaman['bunga_pinjaman'] / 100);

        // $rekomen_pendapatan  = $total_KapBul['total_pemasukan']   == null ? 0 : $total_KapBul['total_pemasukan'];
        // $rekomen_pengeluaran = $total_KapBul['total_pengeluaran'] == null ? 0 : $total_KapBul['total_pengeluaran'];
        // $rekomen_angsuran    = $inputKapBul['angsuran']           == null ? 0 : $inputKapBul['angsuran'];

        // if ($plafonCA == 0 && $tenorCA == 0 && $bunga == 0) {
        //     $recom_angs = 0;
        // } else {
        //     $recom_angs = Helper::recom_angs($plafonCA, $tenorCA, $bunga);
        // }

        // $rekomen_pend_bersih = $rekomen_pendapatan - $rekomen_pengeluaran;

        // $disposable_income   = $rekomen_pend_bersih - $recom_angs;

        // $kapBul = array_merge($inputKapBul, $total_KapBul, array('disposable_income'  => $disposable_income, 'ao_ca' => 'CA'));
        // // End Kapasitas Bulanan

        // // Check Pemeriksaan
        $id_pe_ta = $check_ao->id_periksa_agunan_tanah;

        if (empty($id_pe_ta)) {
            $PeriksaTanah = null;
        }

        $id_pe_ke = $check_ao->id_periksa_agunan_kendaraan;

        if ($id_pe_ke == null) {
            $PeriksaKenda = null;
        }

        $PeriksaTanah = PemeriksaanAgunTan::select('nilai_taksasi_agunan')->whereIn('id', explode(",", $id_pe_ta))->get()->toArray();

        // if (empty($PeriksaTanah)) {
        //     $sumTaksasiTan = 0;
        // } else {
        //     $sumTaksasiTan = array_sum(array_column($PeriksaTanah, 'nilai_taksasi_agunan')); //array_sum($PeriksaTanah);
        // }

        // $PeriksaKenda = PemeriksaanAgunTan::select('nilai_taksasi_agunan')->whereIn('id', explode(",", $id_pe_ta))->get()->toArray();

        // if ($PeriksaKenda == []) {
        //     $sumTaksasiKen = 0;
        // }else{
        //     $sumTaksasiKen = array_sum(array_column($PeriksaTanah,'nilai_taksasi_agunan')); //array_sum($PeriksaTanah);
        // }
        // $sumAllTaksasi = $sumTaksasiTan + $sumTaksasiKen; // Semua Nilai Taksasi dari semua agunan
        //   $sumAllTaksasi = $sumTaksasiTan; // Semua Nilai Taksasi dari semua agunan


        // $recom_ltv   = Helper::recom_ltv($plafonCA, $sumAllTaksasi);
        // $recom_idir  = Helper::recom_idir($recom_angs, $rekomen_pendapatan, $rekomen_pengeluaran);
        // $recom_dsr   = Helper::recom_dsr($recom_angs, $rekomen_pendapatan, $rekomen_angsuran);
        // $recom_hasil = Helper::recom_hasil($recom_dsr, $recom_ltv, $recom_idir);

        $kuantitatif = RingkasanAnalisa::where('id', $check_ca->id_ringkasan_analisa)->first();
        // Data Ringkasan Analisa CA
        $dataRingkasan = array(
            'kuantitatif_ttl_pendapatan'    => empty($req->input('kuantitatif_ttl_pendapatan')) ? $kuantitatif->kuantitatif_ttl_pendapatan : $req->input('kuantitatif_ttl_pendapatan'),
            'kuantitatif_ttl_pengeluaran'   => empty($req->input('kuantitatif_ttl_pengeluaran')) ? $kuantitatif->kuantitatif_ttl_pengeluaran : $req->input('kuantitatif_ttl_pengeluaran'),
            'kuantitatif_pendapatan_bersih' => empty($req->input('kuantitatif_pendapatan_bersih')) ? $kuantitatif->kuantitatif_pendapatan_bersih : $req->input('kuantitatif_pendapatan_bersih'),
            'kuantitatif_angsuran'          => empty($req->input('kuantitatif_angsuran')) ? $kuantitatif->kuantitatif_angsuran : $req->input('kuantitatif_angsuran'),
            // 'kuantitatif_ttl_pendapatan'    => $rekomen_pendapatan,
            // 'kuantitatif_ttl_pengeluaran'   => $rekomen_pengeluaran,
            // 'kuantitatif_pendapatan_bersih' => $rekomen_pend_bersih,
            // 'kuantitatif_angsuran'          => $recom_angs,
            'kuantitatif_ltv'               => empty($req->input('kuantitatif_ltv')) ? $kuantitatif->kuantitatif_ltv : $req->input('kuantitatif_ltv'),
            'kuantitatif_dsr'               => empty($req->input('kuantitatif_dsr')) ? $kuantitatif->kuantitatif_dsr : $req->input('kuantitatif_dsr'),
            'kuantitatif_idir'              => empty($req->input('kuantitatif_idir')) ? $kuantitatif->kuantitatif_idir : $req->input('kuantitatif_idir'),
            'kuantitatif_hasil'             => empty($req->input('kuantitatif_hasil')) ? $kuantitatif->kuantitatif_hasil : $req->input('kuantitatif_hasil'),


            'kualitatif_analisa'
            => empty($req->input('kualitatif_analisa')) ? $kuantitatif->kualitatif_analisa : $req->input('kualitatif_analisa'),

            'kualitatif_strenght'
            => empty($req->input('kualitatif_strenght')) ? $kuantitatif->kualitatif_strenght : $req->input('kualitatif_strenght'),

            'kualitatif_weakness'
            => empty($req->input('kualitatif_weakness')) ? $kuantitatif->kualitatif_weakness : $req->input('kualitatif_weakness'),

            'kualitatif_opportunity'
            => empty($req->input('kualitatif_opportunity')) ? $kuantitatif->kualitatif_opportunity : $req->input('kualitatif_opportunity'),

            'kualitatif_threatness'
            => empty($req->input('kualitatif_threatness')) ? $kuantitatif->kualitatif_threatness : $req->input('kualitatif_threatness'),
        );

        $mut = MutasiBank::where('id', $check_ca->id_mutasi_bank)->first();
        // Mutasi Bank
        // if (!empty($req->input('no_rekening_mutasi'))) {

        //     for ($i = 0; $i < count($req->input('no_rekening_mutasi')); $i++) {

        $dataMuBa = array(
            'urutan_mutasi'
            => $mut->urutan_mutasi,

            'nama_bank'
            => $mut->nama_bank,

            'no_rekening'
            => $mut->no_rekening,

            'nama_pemilik'
            => $mut->nama_pemilik,

            'periode'
            => $mut->periode,

            'frek_debet'
            => $mut->frek_debet,

            'nominal_debet'
            => $mut->nominal_debet,

            'frek_kredit'
            => $mut->frek_kredit,

            'nominal_kredit'
            => $mut->nominal_kredit,

            'saldo'
            => $mut->saldo,
        );


        $infoacc = InfoACC::where('id', $check_ca->id_info_analisa_cc)->first();
        //   if (!empty($req->input('nama_bank_acc'))) {
        //  for ($i = 0; $i < count($req->input('nama_bank_acc')); $i++) {
        $dataACC = array(
            'nama_bank'       => $infoacc->nama_bank,
            'plafon'          => $infoacc->plafon,
            'baki_debet'      => $infoacc->baki_debet,
            'angsuran'        => $infoacc->angsuran,
            'collectabilitas' => $infoacc->collectabilitas,
            'jenis_kredit'    => $infoacc->jenis_kredit,
        );
        // }
        //  }

        $tabtdebt = TabDebt::where('id', $check_ca->id_log_tabungan)->first();
        $dataTabUang = array(

            'no_rekening'
            => $tabtdebt->no_rekening,

            'nama_bank'
            => $tabtdebt->nama_bank,

            'tujuan_pembukaan_rek'
            => $tabtdebt->tujuan_pembukaan_rek,

            'penghasilan_per_tahun'
            => $tabtdebt->penghasilan_per_tahun,

            'sumber_penghasilan'
            => $tabtdebt->sumber_penghasilan,

            'pemasukan_per_bulan'
            => $tabtdebt->pemasukan_per_bulan,

            'frek_trans_pemasukan'
            => $tabtdebt->frek_trans_pemasukan,

            'pengeluaran_per_bulan'
            => $tabtdebt->pengeluaran_per_bulan,

            'frek_trans_pengeluaran'
            => $tabtdebt->frek_trans_pengeluaran,

            'sumber_dana_setoran'
            => $tabtdebt->sumber_dana_setoran,

            'tujuan_pengeluaran_dana'
            => $tabtdebt->tujuan_pengeluaran_dana,
        );

        $rekom = RekomendasiCA::where('id', $check_ca->id_recom_ca)->first();
        // dd($rekom->jangka_waktu);
        // Rekomendasi CA
        $recomCA = array(
            // 'id'                    => empty($req->input('id')) ? $rekom->produk : $req->input('id'),
            'produk'                => empty($req->input('produk')) ? $rekom->produk : $req->input('produk'),
            'plafon_kredit'         => empty($req->input('plafon_kredit')) ? $rekom->plafon_kredit : $req->input('plafon_kredit'),
            'jangka_waktu'          => empty($req->input('jangka_waktu')) ? $rekom->jangka_waktu : $req->input('jangka_waktu'),
            'suku_bunga'            => empty($req->input('suku_bunga')) ? $rekom->suku_bunga : $req->input('suku_bunga'),
            'pembayaran_bunga'      => empty($req->input('pembayaran_bunga')) ? $rekom->pembayaran_bunga : $req->input('pembayaran_bunga'),
            'akad_kredit'           => empty($req->input('akad_kredit')) ? $rekom->akad_kredit : $req->input('akad_kredit'),
            'ikatan_agunan'         => empty($req->input('ikatan_agunan')) ? $rekom->ikatan_agunan : $req->input('ikatan_agunan'),
            'biaya_provisi'         => empty($req->input('biaya_provisi')) ? $rekom->biaya_provisi : $req->input('biaya_provisi'),
            'biaya_administrasi'    => empty($req->input('biaya_administrasi')) ? $rekom->biaya_administrasi : $req->input('biaya_administrasi'),
            'biaya_credit_checking' => empty($req->input('biaya_credit_checking')) ? $rekom->biaya_credit_checking : $req->input('biaya_credit_checking'),
            'biaya_asuransi_jiwa'   => empty($req->input('biaya_asuransi_jiwa')) ? $rekom->biaya_asuransi_jiwa : $req->input('biaya_asuransi_jiwa'),
            'biaya_asuransi_jaminan_kebakaran' => empty($req->input('biaya_asuransi_jaminan_kebakaran')) ? $rekom->biaya_asuransi_jaminan_kebakaran : $req->input('biaya_asuransi_jaminan_kebakaran'),
            'biaya_asuransi_jaminan_kendaraan' => empty($req->input('biaya_asuransi_jaminan_kendaraan')) ? $rekom->biaya_asuransi_jaminan_kendaraan : $req->input('biaya_asuransi_jaminan_kendaraan'),
            'notaris'               => empty($req->input('notaris')) ? $rekom->notaris : $req->input('notaris'),
            'biaya_tabungan'        => empty($req->input('biaya_tabungan')) ? $rekom->biaya_tabungan : $req->input('biaya_tabungan'),


            // 'rekom_angsuran'        => $recom_angs,

            // 'angs_pertama_bunga_berjalan' => $req->input('angs_pertama_bunga_berjalan'),
            // 'pelunasan_nasabah_ro'        => $req->input('pelunasan_nasabah_ro'),
            // 'blokir_dana'                 => $req->input('blokir_dana'),
            // 'pelunasan_tempat_lain'       => $req->input('pelunasan_tempat_lain'),
            // 'blokir_angs_kredit'          => $req->input('blokir_angs_kredit')
        );
        //   dd($recomCA);
        $asJiwa = AsuransiJiwa::where('id', $check_ca->id_asuransi_jiwa)->first();
        $asuransiJiwa = array(
            'nama_asuransi'       => empty($req->input('nama_asuransi_jiwa')) ? $asJiwa->nama_asuransi : $req->input('nama_asuransi_jiwa'),
            'jangka_waktu'        => empty($req->input('jangka_waktu_jiwa')) ? $asJiwa->jangka_waktu : $req->input('jangka_waktu_jiwa'),
            'nilai_pertanggungan' => empty($req->input('nilai_pertanggungan')) ? $asJiwa->nilai_pertanggungan : $req->input('nilai_pertanggungan'),
            'jatuh_tempo'         => empty($req->input('jatuh_tempo')) ? Carbon::parse($asJiwa->jatuh_tempo)->format('Y-m-d') : Carbon::parse($req->input('jatuh_tempo'))->format('Y-m-d'),
            'berat_badan'         => empty($req->input('berat_badan')) ? $asJiwa->berat_badan : $req->input('berat_badan'),
            'tinggi_badan'        => empty($req->input('tinggi_badan')) ? $asJiwa->tinggi_badan : $req->input('tinggi_badan'),
            'umur_nasabah'        => empty($req->input('umur_nasabah')) ? $asJiwa->umur_nasabah : $req->input('umur_nasabah'),
        );

        $asKeb = AsuransiJaminan::where('id', $check_ca->id_asuransi_jaminan_kebakaran)->first();
        $asjaminanKeb = array(
            'nama_asuransi'       => empty($req->input('nama_asuransi_keb')) ? $asKeb->nama_asuransi : $req->input('nama_asuransi_keb'),
            'jangka_waktu'        => empty($req->input('jangka_waktu_asuransi_keb')) ? $asKeb->jangka_waktu : $req->input('jangka_waktu_asuransi_keb'),
            'nilai_pertanggungan' => empty($req->input('nilai_pertanggungan_keb')) ? $asKeb->nilai_pertanggungan : $req->input('nilai_pertanggungan_keb'),
            'jatuh_tempo'         => empty($req->input('jatuh_tempo_keb')) ? Carbon::parse($asKeb->jatuh_tempo)->format('Y-m-d') : Carbon::parse($req->input('jatuh_tempo_keb'))->format('Y-m-d'),
        );

        $asKen = AsuransiJaminanKen::where('id', $check_ca->id_asuransi_jaminan_kendaraan)->first();
        $asjaminanKen = array(
            'nama_asuransi'       => empty($req->input('nama_asuransi_ken')) ? $asKen->nama_asuransi : $req->input('nama_asuransi_ken'),
            'jangka_waktu'        => empty($req->input('jangka_waktu_asuransi_ken')) ?  $asKen->jangka_waktu : $req->input('jangka_waktu_asuransi_ken'),
            'nilai_pertanggungan' => empty($req->input('nilai_pertanggungan_ken')) ? $asKen->nilai_pertanggungan : $req->input('nilai_pertanggungan_ken'),
            'jatuh_tempo'         => empty($req->input('jatuh_tempo_ken')) ? Carbon::parse($asKen->jatuh_tempo)->format('Y-m-d') : Carbon::parse($req->input('jatuh_tempo_ken'))->format('Y-m-d'),
        );
        try {
            DB::connection('web')->beginTransaction();
            $so_trans = TransSO::select('nomor_so')->where('id', $id)->first();
            $rev = "Rev" . "-" . $so_trans->nomor_so;
            $dataID = array(
                'trans_ca'          => $transCA,
                'pendapatan_usaha'         => $dataPendapatanUsaha,
                'kapasitas_bulanan'      => $KapBul,
                'rekomendasi_pinjaman'    => $rekomPinjaman,
                'pemeriksaan_tanah'             => $PeriksaTanah,
                'data_ringkasan' => $dataRingkasan,
                'mutasi_bank'        => $dataMuBa,
                'data_acc'     => $dataACC,
                'dataTabUang'     => $dataTabUang,
                'recomCA'    => $recomCA,
                'asuransi_jiwa'     => $asuransiJiwa,
                'asuransi_kebakaran'     => $asjaminanKeb,
                'asuransi_kendaraan'     => $asjaminanKen,
                'revisi'                   => $rev,
            );


            // dd($recomCA->produk);
            // $newEditCA = array_merge($transCA, $dataPendapatanUsaha, $KapBul, $rekomPinjaman, $PeriksaTanah, $dataRingkasan, $dataMuBa, $dataACC, $dataTabUang, $recomCA, $asuransiJiwa, $asjaminanKeb, $asjaminanKen);
            //  dd($newEditCA);
            $CA = RekomendasiCA::where('id', $check_ca->id_recom_ca)
                ->update([
                    'produk'                => empty($req->input('produk')) ? 0 : $req->input('produk'),
                    'plafon_kredit'         => empty($req->input('plafon_kredit')) ? 0 : $req->input('plafon_kredit'),
                    'jangka_waktu'          => empty($req->input('jangka_waktu')) ? 0 : $req->input('jangka_waktu'),
                    'suku_bunga'            => empty($req->input('suku_bunga')) ? 0 : $req->input('suku_bunga'),
                    'pembayaran_bunga'      => empty($req->input('pembayaran_bunga')) ? 0 : $req->input('pembayaran_bunga'),
                    'akad_kredit'           => empty($req->input('akad_kredit')) ? 0 : $req->input('akad_kredit'),
                    'ikatan_agunan'         => empty($req->input('ikatan_agunan')) ? 0 : $req->input('ikatan_agunan'),
                    'biaya_provisi'         => empty($req->input('biaya_provisi')) ? 0 : $req->input('biaya_provisi'),
                    'biaya_administrasi'    => empty($req->input('biaya_administrasi')) ? 0 : $req->input('biaya_administrasi'),
                    'biaya_credit_checking' => empty($req->input('biaya_credit_checking')) ? 0 : $req->input('biaya_credit_checking'),
                     'biaya_asuransi_jiwa'   => $req->input('biaya_asuransi_jiwa'),
                     'biaya_asuransi_jaminan_kebakaran' => $req->input('biaya_asuransi_jaminan_kebakaran'),
'biaya_asuransi_jaminan_kendaraan' => $req->input('biaya_asuransi_jaminan_kendaraan'),
                    'notaris'               => empty($req->input('notaris')) ? 0 : $req->input('notaris'),
                    'biaya_tabungan'        => empty($req->input('biaya_tabungan')) ? 0 : $req->input('biaya_tabungan'),

                ]);

            RingkasanAnalisa::where('id', $check_ca->id_ringkasan_analisa)->update([
                'kuantitatif_ttl_pendapatan'    => empty($req->input('kuantitatif_ttl_pendapatan')) ? $kuantitatif->kuantitatif_ttl_pendapatan : $req->input('kuantitatif_ttl_pendapatan'),
                'kuantitatif_ttl_pengeluaran'   => empty($req->input('kuantitatif_ttl_pengeluaran')) ? $kuantitatif->kuantitatif_ttl_pengeluaran : $req->input('kuantitatif_ttl_pengeluaran'),
                'kuantitatif_pendapatan_bersih' => empty($req->input('kuantitatif_pendapatan_bersih')) ? $kuantitatif->kuantitatif_pendapatan_bersih : $req->input('kuantitatif_pendapatan_bersih'),
                'kuantitatif_angsuran'          => empty($req->input('kuantitatif_angsuran')) ? $kuantitatif->kuantitatif_angsuran : $req->input('kuantitatif_angsuran'),
                // 'kuantitatif_ttl_pendapatan'    => $rekomen_pendapatan,
                // 'kuantitatif_ttl_pengeluaran'   => $rekomen_pengeluaran,
                // 'kuantitatif_pendapatan_bersih' => $rekomen_pend_bersih,
                // 'kuantitatif_angsuran'          => $recom_angs,
                'kuantitatif_ltv'               => empty($req->input('kuantitatif_ltv')) ? $kuantitatif->kuantitatif_ltv : $req->input('kuantitatif_ltv'),
                'kuantitatif_dsr'               => empty($req->input('kuantitatif_dsr')) ? $kuantitatif->kuantitatif_dsr : $req->input('kuantitatif_dsr'),
                'kuantitatif_idir'              => empty($req->input('kuantitatif_idir')) ? $kuantitatif->kuantitatif_idir : $req->input('kuantitatif_idir'),
                'kuantitatif_hasil'             => empty($req->input('kuantitatif_hasil')) ? $kuantitatif->kuantitatif_hasil : $req->input('kuantitatif_hasil'),
            ]);

            AsuransiJiwa::where('id', $check_ca->id_asuransi_jiwa)->update([
                'nama_asuransi'       => empty($req->input('nama_asuransi')) ? $asJiwa->nama_asuransi : $req->input('nama_asuransi'),
                'jangka_waktu'        => empty($req->input('jangka_waktu')) ? $asJiwa->jangka_waktu : $req->input('jangka_waktu'),
                'nilai_pertanggungan' => empty($req->input('nilai_pertanggungan')) ? $asJiwa->nilai_pertanggungan : $req->input('nilai_pertanggungan'),
                'jatuh_tempo'         => empty($req->input('jatuh_tempo')) ? Carbon::parse($asJiwa->jatuh_tempo)->format('Y-m-d') : Carbon::parse($req->input('jatuh_tempo'))->format('Y-m-d'),
                'berat_badan'         => empty($req->input('berat_badan')) ? $asJiwa->berat_badan : $req->input('berat_badan'),
                'tinggi_badan'        => empty($req->input('tinggi_badan')) ? $asJiwa->tinggi_badan : $req->input('tinggi_badan'),
                'umur_nasabah'        => empty($req->input('umur_nasabah')) ? $asJiwa->umur_nasabah : $req->input('umur_nasabah'),
            ]);

            AsuransiJaminan::where('id', $check_ca->id_asuransi_jaminan_kebakaran)->update([
                'nama_asuransi'       => empty($req->input('nama_asuransi_keb')) ? $asKeb->nama_asuransi : $req->input('nama_asuransi_keb'),
                'jangka_waktu'        => empty($req->input('jangka_waktu_asuransi_keb')) ? $asKeb->jangka_waktu : $req->input('jangka_waktu_asuransi_keb'),
                'nilai_pertanggungan' => empty($req->input('nilai_pertanggungan_keb')) ? $asKeb->nilai_pertanggungan : $req->input('nilai_pertanggungan_keb'),
                'jatuh_tempo'         => empty($req->input('jatuh_tempo_keb')) ? Carbon::parse($asKeb->jatuh_tempo)->format('Y-m-d') : Carbon::parse($req->input('jatuh_tempo_keb'))->format('Y-m-d'),
            ]);

            AsuransiJaminanKen::where('id', $check_ca->id_asuransi_jaminan_kendaraan)->update([
                'nama_asuransi'       => empty($req->input('nama_asuransi_ken')) ? $asKen->nama_asuransi : $req->input('nama_asuransi_ken'),
                'jangka_waktu'        => empty($req->input('jangka_waktu_asuransi_ken')) ?  $asKen->jangka_waktu : $req->input('jangka_waktu_asuransi_ken'),
                'nilai_pertanggungan' => empty($req->input('nilai_pertanggungan_ken')) ? $asKen->nilai_pertanggungan : $req->input('nilai_pertanggungan_ken'),
                'jatuh_tempo'         => empty($req->input('jatuh_tempo_ken')) ? Carbon::parse($asKen->jatuh_tempo)->format('Y-m-d') : Carbon::parse($req->input('jatuh_tempo_ken'))->format('Y-m-d'),
            ]);
            TransCA::where('id_trans_so', $id)->update(['revisi' => $rev]);


            //  TransSO::where('id', $id)->update(['id_trans_ca' => $CA->id, 'norev_so' => $rev]);
            DB::connection('web')->commit();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message' => 'Data Revisi OL berhasil dikirim',
                'data'   => $dataID
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

    public function getRev($id, Request $req)
    {
        $getRev_first = TransCA::where('id_trans_so', $id)->first();
        $getRev = TransCA::where('id_trans_so', $id)->get();

        if ($getRev_first === null) {
            return response()->json([
                'code'   => 404,
                'status' => 'not found',
                'message' => 'data tidak di temukan',
            ], 404);
        }

        try {
            if (!$getRev_first->id_trans_so) {
                return response()->json([
                    'code'   => 403,
                    'status' => 'bad request',
                    'message' => 'Data Tidak valid',
                ], 403);
            }

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message' => 'List Data CA Revisi OL',
                'data'   => $getRev
            ], 200);
        } catch (\Exception $e) {
            // $err = DB::connection('web')->rollback();
            return response()->json([
                'code'    => 501,
                'status'  => 'error',
                'message' => $e
            ], 501);
        }
    }

    public function checkOL($id, Request $request, BlankRequest $req)
    {
        $pic     = $request->pic; // From PIC middleware
        $user_id = $request->auth->user_id;

        $countCA = TransCA::latest('id', 'nomor_ca')->first();

        if (!$countCA) {
            $lastNumb = 1;
        } else {
            $no = $countCA->nomor_ca;

            $arr = explode("-", $no, 5);

            $lastNumb = str_replace(" [revisi]", "", $arr[4]) + 1;
        }


        //Data Transaksi SO
        $nows  = Carbon::now();
        $year  = $nows->year;
        $month = $nows->month;

        $JPIC   = JPIC::where('id', $pic->id_mj_pic)->first();

        //  ID-Cabang - AO / CA / SO - Bulan - Tahun - NO. Urut
        $nomor_ca = $pic->id_cabang . '-' . $JPIC->nama_jenis . '-' . $month . '-' . $year . '-' . $lastNumb;

        $check_so = TransSO::where('id', $id)->where('status_das', 1)->where('status_hm', 1)->first();

        if (!$check_so) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id ' . $id . ' belum ada di SO atau belum komplit saat pemeriksaan DAS dan HM'
            ], 404);
        }

        $check_ao = TransAO::where('id_trans_so', $id)->first();

        if (!$check_ao) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id ' . $id . ' belum sampai ke AO'
            ], 404);
        }

        $check_ca = TransCA::where('id_trans_so', $id)->first();
        // dd($check_ca->id_pendapatan_usaha);
        // if ($check_ca != null) {
        //     return response()->json([
        //         'code'    => 404,
        //         'status'  => 'not found',
        //         'message' => 'Transaksi dengan id ' . $id . ' sudah ada di CA'
        //     ], 404);
        // }

        // $dataCA = RekomendasiCA::where('id', $check_ca->id_recom_ca)->first();
        // dd($dataCA);
        // $logRekom = LogRekomCA::create($dataCA);

        $transCA = array(
            'nomor_ca'    => $nomor_ca,
            'user_id'     => $user_id,
            'id_trans_so' => $id,
            'id_pic'      => $pic->id,
            'id_area'     => $pic->id_area,
            'id_cabang'   => $pic->id_cabang,
            'catatan_ca'  => $check_ca->catatan_ca,
            'status_ca'   => $check_ca->status_ca
        );

        $pen = PendapatanUsaha::where('id', $check_ca->id_pendapatan_usaha)->first();

        // Pendapatan Usaha Cadebt
        $dataPendapatanUsaha = array(
            'id'                    => $pen->id,
            'pemasukan_tunai'      => $pen->pemasukan_tunai,
            'pemasukan_kredit'     => $pen->pemasukan_kredit,
            'biaya_sewa'           => $pen->biaya_sewa,
            'biaya_gaji_pegawai'   => $pen->biaya_gaji_pegawai,
            'biaya_belanja_brg'    => $pen->biaya_belanja_brg,
            'biaya_telp_listr_air' => $pen->biaya_telp_listr_air,
            'biaya_sampah_kemanan' => $pen->biaya_sampah_keamanan,
            'biaya_kirim_barang'   => $pen->biaya_kirim_barang,
            'biaya_hutang_dagang'  => $pen->biaya_hutang_dagang,
            'biaya_angsuran'       => $pen->biaya_angsuran,
            'biaya_lain_lain'      => $pen->biaya_lain_lain,
            'total_pemasukan'      => $pen->total_pemasukan,
            'total_pengeluaran'      => $pen->total_pengeluaran,
            'laba_usaha'      => $pen->laba_usaha,
        );


        // Start Kapasitas Bulanan
        $kap = KapBulanan::where('id', $check_ca->id_kapasitas_bulanan)->first();
        $KapBul = array(
            'id'    => $kap->id,
            'pemasukan_cadebt' => $kap->pemasukan_cadebt,

            'pemasukan_pasangan'
            => $kap->pemasukan_pasangan,

            'pemasukan_penjamin'
            => $kap->pemasukan_penjamin,

            'biaya_rumah_tangga'
            => $kap->biaya_rumah_tangga,

            'biaya_transport'
            => $kap->biaya_transport,

            'biaya_pendidikan'
            => $kap->biaya_pendidikan,

            'telp_listr_air'
            => $kap->telp_listr_air,

            'angsuran'
            => $kap->angsuran,

            'biaya_lain'
            => $kap->biaya_lain,


            'total_pemasukan'
            => $kap->total_pemasukan,

            'total_pengeluaran'
            => $kap->total_pengeluaran,

            'penghasilan_bersih'
            => $kap->penghasilan_bersih,

            'disposable_income'
            => $kap->disposable_income,

            'ao_ca'
            => $kap->ao_ca,

            // $total_KapBul = array(
            //     'total_pemasukan'    => $ttl1 = array_sum(array_slice($inputKapBul, 0, 3)),
            //     'total_pengeluaran'  => $ttl2 = array_sum(array_slice($inputKapBul, 3)),
            //     'penghasilan_bersih' => $ttl1 - $ttl2
        );

        $rekom = RekomendasiPinjaman::where('id', $check_ca->id_rekomendasi_pinjaman)->first();

        // Ceiling Recomendasi Pinjaman
        $rekomPinjaman = array(
            'id' => $rekom->id,
            'penyimpangan_struktur'
            => $rekom->penyimpangan_struktur,

            'penyimpangan_dokumen'
            => $rekom->penyimpangan_dokumen,

            'recom_nilai_pinjaman'
            => $rekom->recom_nilai_pinjaman,

            'recom_tenor'
            => $rekom->recom_tenor,

            'recom_angsuran'
            => $rekom->recom_angsuran,

            'recom_produk_kredit'
            => $rekom->recom_produk_kredit,

            'note_recom'
            => $rekom->note_recom,

            'bunga_pinjaman'
            => $rekom->bunga_pinjaman,

            'nama_ca'
            => $rekom->nama_ca,
        );

        // Rekomendasi Angsuran pada table rrekomendasi_pinjaman
        // $plafonCA = $rekomPinjaman['recom_nilai_pinjaman'] == null ? 0 : $rekomPinjaman['recom_nilai_pinjaman'];
        // $tenorCA  = $rekomPinjaman['recom_tenor']          == null ? 0 : $rekomPinjaman['recom_tenor'];
        // $bunga    = $rekomPinjaman['bunga_pinjaman']       == null ? 0 : ($rekomPinjaman['bunga_pinjaman'] / 100);

        // $rekomen_pendapatan  = $total_KapBul['total_pemasukan']   == null ? 0 : $total_KapBul['total_pemasukan'];
        // $rekomen_pengeluaran = $total_KapBul['total_pengeluaran'] == null ? 0 : $total_KapBul['total_pengeluaran'];
        // $rekomen_angsuran    = $inputKapBul['angsuran']           == null ? 0 : $inputKapBul['angsuran'];

        // if ($plafonCA == 0 && $tenorCA == 0 && $bunga == 0) {
        //     $recom_angs = 0;
        // } else {
        //     $recom_angs = Helper::recom_angs($plafonCA, $tenorCA, $bunga);
        // }

        // $rekomen_pend_bersih = $rekomen_pendapatan - $rekomen_pengeluaran;

        // $disposable_income   = $rekomen_pend_bersih - $recom_angs;

        // $kapBul = array_merge($inputKapBul, $total_KapBul, array('disposable_income'  => $disposable_income, 'ao_ca' => 'CA'));
        // // End Kapasitas Bulanan

        // // Check Pemeriksaan
        $id_pe_ta = $check_ao->id_periksa_agunan_tanah;

        if (empty($id_pe_ta)) {
            $PeriksaTanah = null;
        }

        $id_pe_ke = $check_ao->id_periksa_agunan_kendaraan;

        if ($id_pe_ke == null) {
            $PeriksaKenda = null;
        }

        $PeriksaTanah = PemeriksaanAgunTan::select('nilai_taksasi_agunan')->whereIn('id', explode(",", $id_pe_ta))->get()->toArray();

        // if (empty($PeriksaTanah)) {
        //     $sumTaksasiTan = 0;
        // } else {
        //     $sumTaksasiTan = array_sum(array_column($PeriksaTanah, 'nilai_taksasi_agunan')); //array_sum($PeriksaTanah);
        // }

        // $PeriksaKenda = PemeriksaanAgunTan::select('nilai_taksasi_agunan')->whereIn('id', explode(",", $id_pe_ta))->get()->toArray();

        // if ($PeriksaKenda == []) {
        //     $sumTaksasiKen = 0;
        // }else{
        //     $sumTaksasiKen = array_sum(array_column($PeriksaTanah,'nilai_taksasi_agunan')); //array_sum($PeriksaTanah);
        // }
        // $sumAllTaksasi = $sumTaksasiTan + $sumTaksasiKen; // Semua Nilai Taksasi dari semua agunan
        //   $sumAllTaksasi = $sumTaksasiTan; // Semua Nilai Taksasi dari semua agunan


        // $recom_ltv   = Helper::recom_ltv($plafonCA, $sumAllTaksasi);
        // $recom_idir  = Helper::recom_idir($recom_angs, $rekomen_pendapatan, $rekomen_pengeluaran);
        // $recom_dsr   = Helper::recom_dsr($recom_angs, $rekomen_pendapatan, $rekomen_angsuran);
        // $recom_hasil = Helper::recom_hasil($recom_dsr, $recom_ltv, $recom_idir);

        $kuantitatif = RingkasanAnalisa::where('id', $check_ca->id_ringkasan_analisa)->first();
        // Data Ringkasan Analisa CA
        $dataRingkasan = array(
            'kuantitatif_ttl_pendapatan'    => $kuantitatif->kuantitatif_ttl_pendapatan,
            'kuantitatif_ttl_pengeluaran'   => $kuantitatif->kuantitatif_ttl_pengeluaran,
            'kuantitatif_pendapatan_bersih' => $kuantitatif->kuantitatif_pendapatan_bersih,
            'kuantitatif_angsuran'          => $kuantitatif->kuantitatif_angsuran,
            // 'kuantitatif_ttl_pendapatan'    => $rekomen_pendapatan,
            // 'kuantitatif_ttl_pengeluaran'   => $rekomen_pengeluaran,
            // 'kuantitatif_pendapatan_bersih' => $rekomen_pend_bersih,
            // 'kuantitatif_angsuran'          => $recom_angs,
            'kuantitatif_ltv'               => $kuantitatif->kuantitatif_ltv,
            'kuantitatif_dsr'               => $kuantitatif->kuantitatif_dsr,
            'kuantitatif_idir'              => $kuantitatif->kuantitatif_idir,
            'kuantitatif_hasil'             => $kuantitatif->kuantitatif_hasil,


            'kualitatif_analisa'
            => $kuantitatif->kualitatif_analisa,

            'kualitatif_strenght'
            => $kuantitatif->kualitatif_strenght,

            'kualitatif_weakness'
            => $kuantitatif->kualitatif_weakness,

            'kualitatif_opportunity'
            => $kuantitatif->kualitatif_opportunity,

            'kualitatif_threatness'
            => $kuantitatif->kualitatif_threatness,
        );

        $mut = MutasiBank::where('id', $check_ca->id_mutasi_bank)->first();
        // Mutasi Bank
        // if (!empty($req->input('no_rekening_mutasi'))) {

        //     for ($i = 0; $i < count($req->input('no_rekening_mutasi')); $i++) {

        $dataMuBa = array(
            'urutan_mutasi'
            => $mut->urutan_mutasi,

            'nama_bank'
            => $mut->nama_bank,

            'no_rekening'
            => $mut->no_rekening,

            'nama_pemilik'
            => $mut->nama_pemilik,

            'periode'
            => $mut->periode,

            'frek_debet'
            => $mut->frek_debet,

            'nominal_debet'
            => $mut->nominal_debet,

            'frek_kredit'
            => $mut->frek_kredit,

            'nominal_kredit'
            => $mut->nominal_kredit,

            'saldo'
            => $mut->saldo,
        );


        $infoacc = InfoACC::where('id', $check_ca->id_info_analisa_cc)->first();
        //   if (!empty($req->input('nama_bank_acc'))) {
        //  for ($i = 0; $i < count($req->input('nama_bank_acc')); $i++) {
        $dataACC = array(
            'nama_bank'       => $infoacc->nama_bank,
            'plafon'          => $infoacc->plafon,
            'baki_debet'      => $infoacc->baki_debet,
            'angsuran'        => $infoacc->angsuran,
            'collectabilitas' => $infoacc->collectabilitas,
            'jenis_kredit'    => $infoacc->jenis_kredit,
        );
        // }
        //  }

        $tabtdebt = TabDebt::where('id', $check_ca->id_log_tabungan)->first();
        $dataTabUang = array(

            'no_rekening'
            => $tabtdebt->no_rekening,

            'nama_bank'
            => $tabtdebt->nama_bank,

            'tujuan_pembukaan_rek'
            => $tabtdebt->tujuan_pembukaan_rek,

            'penghasilan_per_tahun'
            => $tabtdebt->penghasilan_per_tahun,

            'sumber_penghasilan'
            => $tabtdebt->sumber_penghasilan,

            'pemasukan_per_bulan'
            => $tabtdebt->pemasukan_per_bulan,

            'frek_trans_pemasukan'
            => $tabtdebt->frek_trans_pemasukan,

            'pengeluaran_per_bulan'
            => $tabtdebt->pengeluaran_per_bulan,

            'frek_trans_pengeluaran'
            => $tabtdebt->frek_trans_pengeluaran,

            'sumber_dana_setoran'
            => $tabtdebt->sumber_dana_setoran,

            'tujuan_pengeluaran_dana'
            => $tabtdebt->tujuan_pengeluaran_dana,
        );

        $rekom = RekomendasiCA::where('id', $check_ca->id_recom_ca)->first();
        // dd($rekom->jangka_waktu);
        // Rekomendasi CA
        $recomCA = array(
            'produk'                =>  $rekom->produk,
            'plafon_kredit'         => $rekom->plafon_kredit,
            'jangka_waktu'          => $rekom->jangka_waktu,
            'suku_bunga'            =>  $rekom->suku_bunga,
            'pembayaran_bunga'      =>  $rekom->pembayaran_bunga,
            'akad_kredit'           => $rekom->akad_kredit,
            'ikatan_agunan'         => $rekom->ikatan_agunan,
            'biaya_provisi'         => $rekom->biaya_provisi,
            'biaya_administrasi'    => $rekom->biaya_administrasi,
            'biaya_credit_checking' => $rekom->biaya_credit_checking,
            // 'biaya_asuransi_jiwa'   => $req->input('biaya_asuransi_jiwa'),
            // 'biaya_asuransi_jaminan' => $req->input('biaya_asuransi_jaminan'),
            'notaris'               => $rekom->notaris,
            'biaya_tabungan'        => $rekom->biaya_tabungan,


            // 'rekom_angsuran'        => $recom_angs,

            // 'angs_pertama_bunga_berjalan' => $req->input('angs_pertama_bunga_berjalan'),
            // 'pelunasan_nasabah_ro'        => $req->input('pelunasan_nasabah_ro'),
            // 'blokir_dana'                 => $req->input('blokir_dana'),
            // 'pelunasan_tempat_lain'       => $req->input('pelunasan_tempat_lain'),
            // 'blokir_angs_kredit'          => $req->input('blokir_angs_kredit')
        );
        //   dd($recomCA);
        $asJiwa = AsuransiJiwa::where('id', $check_ca->id_asuransi_jiwa)->first();
        $asuransiJiwa = array(
            'nama_asuransi'       => $asJiwa->nama_asuransi,
            'jangka_waktu'        => $asJiwa->jangka_waktu,
            'nilai_pertanggungan' => $asJiwa->nilai_pertanggungan,
            'jatuh_tempo'         => Carbon::parse($asJiwa->jatuh_tempo)->format('d-m-Y'),
            'berat_badan'         => $asJiwa->berat_badan,
            'tinggi_badan'        => $asJiwa->tinggi_badan,
            'umur_nasabah'        => $asJiwa->umur_nasabah,
        );

        $asKeb = AsuransiJaminan::where('id', $check_ca->id_asuransi_jaminan_kebakaran)->first();
        $asjaminanKeb = array(
            'nama_asuransi'       => $asKeb->nama_asuransi,
            'jangka_waktu'        => $asKeb->jangka_waktu,
            'nilai_pertanggungan' => $asKeb->nilai_pertanggungan,
            'jatuh_tempo'         => Carbon::parse($asKeb->jatuh_tempo)->format('d-m-Y'),
        );

        $asKen = AsuransiJaminanKen::where('id', $check_ca->id_asuransi_jaminan_kendaraan)->first();
        $asjaminanKen = array(
            'nama_asuransi'       => $asKen->nama_asuransi,
            'jangka_waktu'        => $asKen->jangka_waktu,
            'nilai_pertanggungan' => $asKen->nilai_pertanggungan,
            'jatuh_tempo'         => Carbon::parse($asKen->jatuh_tempo)->format('d-m-Y'),
        );
        try {
            DB::connection('web')->beginTransaction();
            $so_trans = TransSO::select('nomor_so')->where('id', $id)->first();
            $rev = "Rev" . "-" . $so_trans->nomor_so;
            $dataID = array(
                'trans_ca'          => $transCA,
                'pendapatan_usaha'         => $dataPendapatanUsaha,
                'kapasitas_bulanan'      => $KapBul,
                'rekomendasi_pinjaman'    => $rekomPinjaman,
                'pemeriksaan_tanah'             => $PeriksaTanah,
                'data_ringkasan' => $dataRingkasan,
                'mutasi_bank'        => $dataMuBa,
                'data_acc'     => $dataACC,
                'dataTabUang'     => $dataTabUang,
                'recomCA'    => $recomCA,
                'asuransi_jiwa'     => $asuransiJiwa,
                'asuransi_kebakaran'     => $asjaminanKeb,
                'asuransi_kendaraan'     => $asjaminanKen,
                'asuransi_jiwa'     => $asuransiJiwa,
                'revisi'                   => $rev,
            );


            // dd($dataID);
            // $newEditCA = array_merge($transCA, $dataPendapatanUsaha, $KapBul, $rekomPinjaman, $PeriksaTanah, $dataRingkasan, $dataMuBa, $dataACC, $dataTabUang, $recomCA, $asuransiJiwa, $asjaminanKeb, $asjaminanKen);
            //  dd($newEditCA);
            //   $CA = TransCA::update([$recomCA]);


            // TransSO::where('id', $id)->update(['id_trans_ca' => $CA->id, 'norev_so' => $rev]);
            DB::connection('web')->commit();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message' => 'Data Revisi OL',
                'data'   => $dataID
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
}
