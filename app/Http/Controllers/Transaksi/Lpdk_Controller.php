<?php

namespace App\Http\Controllers\Transaksi;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use App\Models\Transaksi\TransAO;
use App\Models\Transaksi\Lpdk;
use App\Models\Transaksi\Lpdk_Cek;
use App\Models\Transaksi\TransCA;
use Illuminate\Support\Facades\File;
use App\Models\Transaksi\TransSO;
use App\Models\Pengajuan\SO\Debitur;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Lpdk_Controller extends BaseController
{
    public function index()
    {
        $lpdk =  DB::connection('web')->table('view_approval_caa')->get();
        //$lpdk = Lpdk_Cek::get();
        //  $lpdk = Lpdk::paginate(10);
        if ($lpdk === null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data LPDK Kosong'
            ], 404);
        }
        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'  => $lpdk
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function indexOnprogress()
    {
        // $lpdk =  DB::connection('web')->table('vw_memo_ca_approve')->get();
        $lpdk = Lpdk_Cek::first();
        //  $lpdk = Lpdk::paginate(10);
        //   dd($lpdk);
        if ($lpdk === null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data LPDK Kosong'
            ], 404);
        }
        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'  => $lpdk
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function indexRealisasi()
    {
        // $lpdk =  DB::connection('web')->table('vw_memo_ca_approve')->get();
        $lpdk = Lpdk::where('status_kredit', 'REALISASI')->get();
        //  $lpdk = Lpdk::paginate(10);
        //   dd($lpdk);
        if ($lpdk === '[]') {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data LPDK Kosong'
            ], 404);
        }
        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'  => $lpdk
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function show($id)
    {
        $lpdk =  DB::connection('web')->table('view_approval_caa')->where('id_trans_so', $id)->first();
        //   $lpdk = Lpdk::where('id_calon_debitur', $id)->first();
        if ($lpdk === null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Tidak Ada Data Lpdk dengan id =' . $id
            ], 404);
        }
        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'  => $lpdk
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function store($id, Request $req)
    {

        $pic     = $req->pic; // From PIC middleware
        $user_id = $req->auth->user_id;
        //   dd($pic->nama);

        $cek_lpdk =  DB::connection('web')->table('view_approval_caa')->where('id_trans_so', $id)->first();
        //  dd($cek_lpdk);

        if ($cek_lpdk === null) {
            return response()->json([
                'code'  => 402,
                'message'  => 'data transaksi SO dengan id =' . ' ' . $id . ' ' . 'tidak ditemukan'
            ]);
        }

        $check_debt_ktp = Debitur::where('id', $id)->first();
        //  $check_lpdk = Lpdk::where('id', $id)
        $check_debt = DB::connection('web')->table('view_approval_caa')->where('id_trans_so', $id)->first();

        // dd($check_debt);

        // $path = 'public/' . $check_debt_ktp->no_ktp . '/debitur/lpdk/debitur';

        $check_lamp_ktp             = $check_debt->lampiran_ktp_deb;
        $check_lamp_ktppas              = $check_debt->lampiran_ktp_pasangan;
        $check_lamp_ktppen             = $check_debt->lampiran_ktp_penjamin;
        // $check_lamp_npwp             = $check_debt->lampiran_npwp;
        $check_lamp_sertifikat      = $check_debt->lampiran_sertifikat;
        $check_lamp_sttp_pbb        = $check_debt->lampiran_pbb;
        $check_lamp_imb             = $check_debt->lampiran_imb;
        // $check_lamp_skk             = $check_debt->lampiran_skk;
        // $check_lamp_sku             = $check_debt->lampiran_sku;
        // $check_lamp_slip_gaji       = $check_debt->lampiran_slipgaji;
        // $check_lamp_kk              = $check_debt->lamp_kk;
        // $check_foto_agunan_rumah    = $check_debt->foto_agunan_rumah;
        // $check_lamp_buku_tabungan   = $check_debt->lamp_buku_tabungan;
        // $check_foto_pembukuan_usaha = $check_debt->foto_pembukuan_usaha;
        // $check_lamp_foto_usaha      = $check_debt->lamp_foto_usaha;
        // $check_lamp_surat_cerai     = $check_debt->lamp_surat_cerai;
        // $check_lamp_tempat_tinggal  = $check_debt->lamp_tempat_tinggal;


        // //lampiran 

        // if ($file = $req->file('lampiran_ktp_deb')) {
        //     $name = 'ktp.';
        //     $check = $check_lamp_ktp;

        //     $lamp_ktp = Helper::uploadImg($check, $file, $path, $name);
        // } else {
        //     $lamp_ktp = $check_lamp_ktp;
        // }


        // if ($file = $req->file('lampiran_ktp_pasangan')) {
        //     $name = 'ktp_pas.';
        //     $check = $check_lamp_ktppas;

        //     $lamp_ktp_pas = Helper::uploadImg($check, $file, $path, $name);
        // } else {
        //     $lamp_ktp_pas = $check_lamp_ktppas;
        // }

        // if ($file = $req->file('lampiran_npwp')) {
        //     $name = 'npwp.';
        //     $check = $check_lamp_npwp;

        //     $lamp_npwp = Helper::uploadImg($check, $file, $path, $name);
        // } else {
        //     $lamp_npwp = $check_lamp_npwp;
        // }

        // if ($file = $req->file('lampiran_ktp_penjamin')) {
        //     $name = 'ktp_pen.';
        //     $check = $check_lamp_ktppen;

        //     $lamp_ktp_pen = Helper::uploadImg($check, $file, $path, $name);
        // } else {
        //     $lamp_ktp_pen = $check_lamp_ktppen;
        // }


        // if ($file = $req->file('lampiran_sertifikat')) {
        //     $name = 'sertifikat.';
        //     $check = $check_lamp_sertifikat;

        //     $lamp_sertifikat = Helper::uploadImg($check, $file, $path, $name);
        // } else {
        //     $lamp_sertifikat = $check_lamp_sertifikat;
        // }


        // if ($file = $req->file('lampiran_pbb')) {
        //     $name = 'pbb.';
        //     $check = $check_lamp_sttp_pbb;

        //     $lamp_sttp_pbb = Helper::uploadImg($check, $file, $path, $name);
        // } else {
        //     $lamp_sttp_pbb = $check_lamp_sttp_pbb;
        // }

        // if ($file = $req->file('lampiran_imb')) {
        //     $name = 'imb.';
        //     $check = $check_lamp_imb;

        //     $lamp_imb = Helper::uploadImg($check, $file, $path, $name);
        // } else {
        //     $lamp_imb = $check_lamp_imb;
        // }

        // if ($file = $req->file('lampiran_skk')) {
        //     $name = 'lamp_skk.';
        //     $check = $check_lamp_skk;

        //     $lamp_skk = Helper::uploadImg($check, $file, $path, $name);
        // } else {
        //     $lamp_skk = $check_lamp_skk;
        // }

        // if ($files = $req->file('lampiran_sku')) {
        //     $name = 'lamp_sku.';
        //     $check = $check_lamp_sku;

        //     $arrayPath = array();
        //     foreach ($files as $file) {
        //         $arrayPath[] = Helper::uploadImg($check, $file, $path, $name);
        //     }

        //     $lamp_sku = implode(";", $arrayPath);
        // } else {
        //     $lamp_sku = $check_lamp_sku;
        // }


        // if ($file = $req->file('lampiran_slipgaji')) {
        //     $name = 'lamp_slip_gaji.';
        //     $check = $check_lamp_slip_gaji;

        //     $lamp_slip_gaji = Helper::uploadImg($check, $file, $path, $name);
        // } else {
        //     $lamp_slip_gaji = $check_lamp_slip_gaji;
        // }

        $data = array(
            'trans_so'  => $cek_lpdk->id_trans_so,
            'nomor_so' => $cek_lpdk->nomor_so,
            'nama_so' => $cek_lpdk->nama_so,
            'asal_data' => $cek_lpdk->asal_data,
            'nama_marketing' => empty($req->input('nama_marketing')) ? $cek_lpdk->nama_marketing :   $req->input('nama_marketing'),
            'area_kerja' => empty($req->input('area_kerja')) ? $cek_lpdk->area_kerja :   $req->input('area_kerja'),
            'plafon' => $cek_lpdk->plafon,
            'tenor' => $cek_lpdk->tenor,
            'nama_debitur' => empty($req->input('nama_debitur')) ? $cek_lpdk->nama_debitur :   $req->input('nama_debitur'),
            'nama_pasangan' => empty($req->input('nama_pasangan')) ? $cek_lpdk->nama_pasangan :  $req->input('nama_pasangan'),
            'nama_penjamin' => empty($req->input('nama_penjamin')) ? $cek_lpdk->nama_penjamin : $req->input('nama_penjamin'),
            'ibu_kandung_penjamin' => empty($req->input('ibu_kandung_penjamin')) ? $cek_lpdk->ibu_kandung_penjamin : $req->input('ibu_kandung_penjamin'),
            'status_nikah' => empty($req->input('status_nikah')) ? $cek_lpdk->status_nikah : $req->input('status_nikah'),
            'produk' => empty($req->input('produk')) ? $cek_lpdk->produk : $req->input('produk'),
            'no_sertifikat' => empty($req->input('no_sertifikat')) ? $cek_lpdk->no_sertifikat : $req->input('no_sertifikat'),
            'nama_sertifikat' => empty($req->input('nama_sertifikat')) ? $cek_lpdk->nama_sertifikat : $req->input('nama_sertifikat'),
            'status_sertifikat' => empty($req->input('status_sertifikat')) ? $cek_lpdk->status_sertifikat : $req->input('status_sertifikat'),
            'nama_pas_sertifikat' => empty($req->input('nama_pas_sertifikat')) ? $cek_lpdk->nama_pas_sertifikat : $req->input('nama_pas_sertifikat'),
            'status_pas_sertifikat' => empty($req->input('status_pas_sertifikat')) ? $cek_lpdk->status_pas_sertifikat : $req->input('status_pas_sertifikat'),
            'hub_cadeb' => empty($req->input('hub_cadeb')) ? $cek_lpdk->hub_cadeb : $req->input('hub_cadeb'),
            'lampiran_ktp_deb' =>  $check_lamp_ktp,
            'lampiran_ktp_pasangan' =>  $check_lamp_ktppas,
            'lampiran_ktp_penjamin' =>  $check_lamp_ktppen,
            // 'lampiran_npwp' => $lamp_npwp,
            'lampiran_sertifikat' =>  $check_lamp_sertifikat,
            'lampiran_pbb' => $check_lamp_sttp_pbb,
            'lampiran_imb' =>  $check_lamp_imb,
            // 'lampiran_skk' => $lamp_skk,
            // 'lampiran_sku' => $lamp_sku,
            // 'lampiran_slipgaji' => $lamp_slip_gaji,
            'notes' => $pic->nama . " : " . $req->input('notes'),
            'status_kredit' => 'ON-PROGRESS'
        );
        //   dd($data);

        if ($data === null) {
            return response()->json([
                'code'  => 403,
                'message'   => 'data harus di input',
                'data'  => $data
            ]);
        }

        //  dd(Lpdk::where('nomor_so', $cek_lpdk->nomor_so)->first());

        if (Lpdk_Cek::where('nomor_so', $cek_lpdk->nomor_so)->exists()) {
            return response()->json([
                "Code"      => 409,
                "Status"    => "Conflict",
                "Message"   => "Nomor SO pengajuan LPDK Sudah Ada"
            ], 409);
        }



        try {

            Lpdk_Cek::create($data);

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'data' => $data
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

    public function updateLampiran($id, Request $req)
    {
        // $coba = $req->input('coba');
        // dd($coba);

        $pic     = $req->pic; // From PIC middleware
        $user_id = $req->auth->user_id;

        $get_lpdk = Lpdk::get();

        // if ($get_lpdk === null) {
        //     return response()->json([
        //         'code'    => 404,
        //         'status'  => 'not found',
        //         'message' => 'data lpdk kosong'
        //     ], 404);
        // }
        $cek_lpdk = Lpdk_Cek::where('trans_so', $id)->first();
        //dd($cek_lpdk);

        if ($cek_lpdk === null) {
            return response()->json([
                'code'  => 402,
                'message'  => 'data transaksi SO dengan id =' . ' ' . $id . ' ' . 'tidak ditemukan'
            ]);
        }

        $check_debt_ktp = Debitur::where('id', $id)->first();
        //  $check_lpdk = Lpdk::where('id', $id)
        $check_debt = Lpdk_Cek::where('trans_so', $id)->first();

        // dd($check_debt->lampiran_ktp_deb);
        //    dd($check_debt);

        $path = 'public/' . $check_debt_ktp->no_ktp . '/debitur/lpdk';
        $check_lamp_ktp             = $check_debt->lampiran_ktp_deb;
        $check_lamp_ktppas              = $check_debt->lampiran_ktp_pasangan;
        $check_lamp_ktppen             = $check_debt->lampiran_ktp_penjamin;
        $check_lamp_npwp             = $check_debt->lampiran_npwp;
        $check_lamp_surat_kematian = $check_debt->lampiran_surat_kematian;
        $check_lamp_sk_desa = $check_debt->lampiran_sk_desa;
        $check_lamp_ajb = $check_debt->lampiran_ajb;
        $check_lamp_ahliwaris = $check_debt->lampiran_ahliwaris;
        $check_lamp_aktahibah = $check_debt->lampiran_aktahibah;

        $check_lamp_sertifikat      = $check_debt->lampiran_sertifikat;
        $check_lamp_sttp_pbb        = $check_debt->lampiran_pbb;
        $check_lamp_imb             = $check_debt->lampiran_imb;
        $check_lamp_skk             = $check_debt->lampiran_skk;
        $check_lamp_sku             = $check_debt->lampiran_sku;
        $check_lamp_slip_gaji       = $check_debt->lampiran_slipgaji;
        $check_lamp_kk              = $check_debt->lamp_kk;
        $check_foto_agunan_rumah    = $check_debt->foto_agunan_rumah;
        $check_lamp_buku_tabungan   = $check_debt->lamp_buku_tabungan;
        $check_foto_pembukuan_usaha = $check_debt->foto_pembukuan_usaha;
        $check_lamp_foto_usaha      = $check_debt->lamp_foto_usaha;
        $check_lamp_surat_cerai     = $check_debt->lamp_surat_cerai;
        $check_lamp_tempat_tinggal  = $check_debt->lamp_tempat_tinggal;

        //$dd = $req->input('hub_cadeb');
        //lampiran 
        //dd($dd);
        if ($file = $req->file('lampiran_ktp_deb')) {
            $name = 'ktp.';
            $check = $check_lamp_ktp;

            $lamp_ktp = Helper::uploadImg($check, $file, $path, $name);
        } else {
            $lamp_ktp = $check_lamp_ktp;
        }


        if ($file = $req->file('lampiran_ktp_pasangan')) {
            $name = 'ktp_pas.';
            $check = $check_lamp_ktppas;

            $lamp_ktp_pas = Helper::uploadImg($check, $file, $path, $name);
        } else {
            $lamp_ktp_pas = $check_lamp_ktppas;
        }

        if ($file = $req->file('lampiran_npwp')) {
            $name = 'npwp.';
            $check = $check_lamp_npwp;

            $lamp_npwp = Helper::uploadImg($check, $file, $path, $name);
        } else {
            $lamp_npwp = $check_lamp_npwp;
        }

        if ($file = $req->file('lampiran_ktp_penjamin')) {
            $name = 'ktp_pen.';
            $check = $check_lamp_ktppen;

            $lamp_ktp_pen = Helper::uploadImg($check, $file, $path, $name);
        } else {
            $lamp_ktp_pen = $check_lamp_ktppen;
        }


        if ($file = $req->file('lampiran_sertifikat')) {
            $name = 'sertifikat.';
            $check = $check_lamp_sertifikat;

            $lamp_sertifikat = Helper::uploadImg($check, $file, $path, $name);
        } else {
            $lamp_sertifikat = $check_lamp_sertifikat;
        }


        if ($file = $req->file('lampiran_pbb')) {
            $name = 'pbb.';
            $check = $check_lamp_sttp_pbb;

            $lamp_sttp_pbb = Helper::uploadImg($check, $file, $path, $name);
        } else {
            $lamp_sttp_pbb = $check_lamp_sttp_pbb;
        }

        if ($file = $req->file('lampiran_imb')) {
            $name = 'imb.';
            $check = $check_lamp_imb;

            $lamp_imb = Helper::uploadImg($check, $file, $path, $name);
        } else {
            $lamp_imb = $check_lamp_imb;
        }

        if ($file = $req->file('lampiran_skk')) {
            $name = 'lamp_skk.';
            $check = $check_lamp_skk;

            $lamp_skk = Helper::uploadImg($check, $file, $path, $name);
        } else {
            $lamp_skk = $check_lamp_skk;
        }

        if ($files = $req->file('lampiran_sku')) {
            $name = 'lamp_sku.';
            $check = $check_lamp_sku;

            $lamp_sku = Helper::uploadImg($check, $file, $path, $name);
        } else {
            $lamp_sku = $check_lamp_sku;
        }


        if ($file = $req->file('lampiran_slipgaji')) {
            $name = 'lamp_slip_gaji.';
            $check = $check_lamp_slip_gaji;

            $lamp_slip_gaji = Helper::uploadImg($check, $file, $path, $name);
        } else {
            $lamp_slip_gaji = $check_lamp_slip_gaji;
        }


        if ($file = $req->file('lampiran_surat_kematian')) {
            $name = 'lamp_surat_kematian.';
            $check = $check_lamp_surat_kematian;

            $lamp_sk_kematian = Helper::uploadImg($check, $file, $path, $name);
        } else {
            $lamp_sk_kematian = $check_lamp_surat_kematian;
        }

        if ($file = $req->file('lampiran_sk_desa')) {
            $name = 'lamp_sk_desa.';
            $check = $check_lamp_sk_desa;

            $lamp_sk_desa = Helper::uploadImg($check, $file, $path, $name);
        } else {
            $lamp_sk_desa = $check_lamp_sk_desa;
        }

        if ($file = $req->file('lampiran_ajb')) {
            $name = 'lamp_ajb.';
            $check = $check_lamp_ajb;

            $lamp_ajb = Helper::uploadImg($check, $file, $path, $name);
        } else {
            $lamp_ajb = $check_lamp_ajb;
        }

        if ($file = $req->file('lampiran_ahliwaris')) {
            $name = 'lamp_ahliwaris.';
            $check = $check_lamp_ahliwaris;

            $lamp_ahliwaris = Helper::uploadImg($check, $file, $path, $name);
        } else {
            $lamp_ahliwaris = $check_lamp_ahliwaris;
        }

        if ($file = $req->file('lampiran_aktahibah')) {
            $name = 'lamp_aktahibah.';
            $check = $check_lamp_aktahibah;

            $lamp_aktahibah = Helper::uploadImg($check, $file, $path, $name);
        } else {
            $lamp_aktahibah = $check_lamp_aktahibah;
        }

        $note_cek = Lpdk_Cek::where('trans_so', $id)->first();

        DB::connection('web')->beginTransaction();
        $data = array(
            'trans_so'  => $id,
            'nomor_so' => $cek_lpdk->nomor_so,
            'nama_so' => $cek_lpdk->nama_so,
            'asal_data' => $cek_lpdk->asal_data,
            'nama_marketing' => $cek_lpdk->nama_marketing,
            'plafon' => $cek_lpdk->plafon,
            'tenor' => $cek_lpdk->tenor,
            'area_kerja' => empty($req->input('area_kerja')) ? $cek_lpdk->area_kerja :   $req->input('area_kerja'),
            'nama_debitur' => empty($req->input('nama_debitur')) ? $cek_lpdk->nama_debitur :   $req->input('nama_debitur'),
            'nama_pasangan' => empty($req->input('nama_pasangan')) ? $cek_lpdk->nama_pasangan :  $req->input('nama_pasangan'),
            'nama_penjamin' => empty($req->input('nama_penjamin')) ? $cek_lpdk->nama_penjamin : $req->input('nama_penjamin'),
            'ibu_kandung_penjamin' => empty($req->input('ibu_kandung_penjamin')) ? $cek_lpdk->ibu_kandung_penjamin : $req->input('ibu_kandung_penjamin'),
            'status_nikah' => empty($req->input('status_nikah')) ? $cek_lpdk->status_nikah : $req->input('status_nikah'),
            'produk' => empty($req->input('produk')) ? $cek_lpdk->produk : $req->input('produk'),
            'no_sertifikat' => empty($req->input('no_sertifikat')) ? $cek_lpdk->no_sertifikat : $req->input('no_sertifikat'),
            'nama_sertifikat' => empty($req->input('nama_sertifikat')) ? $cek_lpdk->nama_sertifikat : $req->input('nama_sertifikat'),
            'status_sertifikat' => empty($req->input('status_sertifikat')) ? $cek_lpdk->status_sertifikat : $req->input('status_sertifikat'),
            'nama_pas_sertifikat' => empty($req->input('nama_pas_sertifikat')) ? $cek_lpdk->nama_pas_sertifikat : $req->input('nama_pas_sertifikat'),
            'status_pas_sertifikat' => empty($req->input('status_pas_sertifikat')) ? $cek_lpdk->status_pas_sertifikat : $req->input('status_pas_sertifikat'),
            'hub_cadeb' => empty($req->input('hub_cadeb')) ? $cek_lpdk->hub_cadeb : $req->input('hub_cadeb'),
            'lampiran_ktp_deb' => $lamp_ktp,
            'lampiran_ktp_pasangan' => $lamp_ktp_pas,
            'lampiran_ktp_penjamin' => $lamp_ktp_pen,
            'lampiran_npwp' => $lamp_npwp,
            'lampiran_sertifikat' => $lamp_sertifikat,
            'lampiran_pbb' => $lamp_sttp_pbb,
            'lampiran_imb' => $lamp_imb,
            'lampiran_skk' => $lamp_skk,
            'lampiran_sku' => $lamp_sku,
            'lampiran_slipgaji' => $lamp_slip_gaji,
            'lampiran_surat_kematian' => $lamp_sk_kematian,
            'lampiran_sk_desa'  => $lamp_sk_desa,
            'lampiran_ajb' => $lamp_ajb,
            'lampiran_ahliwaris' => $lamp_ahliwaris,
            'lampiran_aktahibah'   => $lamp_aktahibah,
            'notes' => $note_cek->notes . ", " . $pic->nama . " : " . $req->input('notes'),
            'status_kredit' => $req->input('status_kredit')
        );

        // dd($data['status_kredit']);
        // if ($data['status_kredit'] === 'CANCEL') {
        //     Lpdk::where('trans_so', $id)->update($data);
        //     return response()->json([
        //         'code' => 200,
        //         'status' => 'return',
        //         'message' => $data['notes']
        //     ]);
        // }

        if ($data === null) {
            return response()->json([
                'code'  => 403,
                'message'   => 'data harus di input',
                'data'  => $data
            ]);
        }

        //  dd(Lpdk::where('nomor_so', $cek_lpdk->nomor_so)->first());

        // if (Lpdk::where('nomor_so', $cek_lpdk->nomor_so)->exists()) {
        //     return response()->json([
        //         "Code"      => 409,
        //         "Status"    => "Conflict",
        //         "Message"   => "Nomor SO pengajuan LPDK Sudah Ada"
        //     ], 409);
        // }



        try {

            DB::connection('web')->commit();

            Lpdk::create($data);
            Lpdk_Cek::where('trans_so', $id)->delete();

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'data' => $data
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
}
