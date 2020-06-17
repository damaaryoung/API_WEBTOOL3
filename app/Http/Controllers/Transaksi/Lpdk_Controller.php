<?php

namespace App\Http\Controllers\Transaksi;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use App\Models\Pengajuan\AO\AgunanTanah;
use App\Models\Transaksi\TransAO;
use App\Models\Transaksi\Lpdk;
use App\Models\Transaksi\Lpdk_lampiran;
use App\Models\Transaksi\Lpdk_sertifikat;
use App\Models\Transaksi\Lpdk_penjamin;
use App\Models\Pengajuan\SO\Penjamin;
use App\Models\Pengajuan\AO\AgunanKendaraan;
use App\Models\Transaksi\Lpdk_kendaraan;
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
        $arrData = array();
        foreach ($lpdk as $key => $val) {
            $arrData[$key]['id_trans_so']       = $val->id_trans_so;
            $arrData[$key]['nomor_so']       = $val->nomor_so;
            $arrData[$key]['nama_so']       = $val->nama_so;
            $arrData[$key]['asal_data']       = $val->asal_data;
            $arrData[$key]['area_kerja']       = $val->area_kerja;
            $arrData[$key]['nama_marketing']       = $val->nama_marketing;
            $arrData[$key]['plafon']       = $val->plafon;
            $arrData[$key]['tenor']       = $val->tenor;
            $arrData[$key]['nama_debitur']       = $val->nama_debitur;
            $arrData[$key]['nama_pasangan']       = $val->nama_pasangan;
            $det = explode(',', $val->id_penjamin);
            $arrData[$key]['penjamin']       = Penjamin::select('nama_ktp', 'nama_ibu_kandung', 'no_ktp', 'no_npwp', 'tempat_lahir', 'tgl_lahir', 'jenis_kelamin', 'lamp_ktp')->whereIn('id', $det)->get();
            $arrData[$key]['status_nikah']       = $val->status_nikah;
            $arrData[$key]['produk']       = $val->produk;
            $ser = explode(',', $val->id_agunan_tanah);
            $arrData[$key]['sertifikat']       = AgunanTanah::select('nama_pemilik_sertifikat', 'jenis_sertifikat', 'no_sertifikat', 'tgl_berlaku_shgb', 'lamp_sertifikat', 'lamp_imb', 'lamp_pbb')->whereIn('id', $ser)->get();
            $ken = explode(',', $val->id_agunan_kendaraan);
            $arrData[$key]['kendaraan']       = AgunanKendaraan::select('no_bpkb', 'nama_pemilik', 'alamat_pemilik', 'merk', 'jenis', 'no_rangka', 'no_mesin', 'warna', 'tahun', 'no_polisi', 'no_stnk', 'tgl_kadaluarsa_pajak', 'tgl_kadaluarsa_stnk', 'no_faktur', 'lamp_agunan_depan', 'lamp_agunan_kanan', 'lamp_agunan_kiri', 'lamp_agunan_belakang', 'lamp_agunan_dalam')->whereIn('id', $ken)->get();

            $arrData[$key]['lampiran_kk']       = $val->lampiran_kk;
            $arrData[$key]['lampiran_npwp']       = NULL;
            $arrData[$key]['lampiran_ktp_deb']       = $val->lampiran_ktp_deb;
            $arrData[$key]['lampiran_ktp_pasangan']       = $val->lampiran_ktp_pasangan;
            $arrData[$key]['lamp_buku_nikah']       = $val->lamp_buku_nikah;
            $arrData[$key]['lampiran_surat_cerai']       = $val->lampiran_surat_cerai;
            $arrData[$key]['lampiran_surat_lahir']       = NULL;
            $arrData[$key]['lampiran_surat_kematian']       = NULL;
            $arrData[$key]['lampiran_surat_keterangan_desa']       = NULL;
            $arrData[$key]['lampiran_surat_ajb_ppjb']       = NULL;
            $arrData[$key]['lampiran_ahliwaris']       = NULL;
            $arrData[$key]['lampiran_akta_hibah']       = NULL;
        }
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
                'data'  => $arrData
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function getDetailLpdk($id)
    {
        // $lpdk =  DB::connection('web')->table('vw_memo_ca_approve')->get();
        $lpdk = Lpdk::where('trans_so', $id)->get();
        $penjamin = Lpdk_penjamin::where('trans_so', $id)->get();
        $lampiran = Lpdk_penjamin::where('trans_so', $id)->get();
        $sertifikat = Lpdk_sertifikat::where('trans_so', $id)->get();
        //  $lpdk = Lpdk::paginate(10);


        $arrData = array();
        foreach ($lpdk as $key => $val) {
            $arrData[$key]['id']       = $val->id;
            $arrData[$key]['trans_so']   = $val->trans_so;
            $arrData[$key]['nomor_so']    = $val->nomor_so;
            $arrData[$key]['nama_so'] = $val->nama_so;
            $arrData[$key]['asal_data']   = $val->asal_data;
            $arrData[$key]['nama_marketing']     = $val->nama_marketing;
            $arrData[$key]['area_kerja']       = $val->area_kerja;
            $arrData[$key]['plafon']   = $val->plafon;
            $arrData[$key]['tenor']    = $val->tenor;
            $arrData[$key]['nama_debitur'] = $val->nama_debitur;
            $arrData[$key]['nama_pasangan']   = $val->nama_pasangan;
            $arrData[$key]['status_nikah']     = $val->status_nikah;
            $arrData[$key]['produk']       = $val->produk;
            $arrData[$key]['alamat_ktp_vs_jaminan']   = $val->alamat_ktp_vs_jaminan;
            $arrData[$key]['hubcadeb']    = $val->hubcadeb;
            $arrData[$key]['akta_notaris'] = $val->akta_notaris;
            $arrData[$key]['status_kredit']   = $val->status_kredit;
            $arrData[$key]['notes_progress']     = $val->notes_progress;
            $arrData[$key]['notes_counter']     = $val->notes_counter;
            $arrData[$key]['penjamin'] = Lpdk_penjamin::select(
                'nama_penjamin',
                'ibu_kandung_penjamin',
                'pasangan_penjamin',
                'lampiran_ktp_penjamin'
            )->where('trans_so', $id)->get();
            $arrData[$key]['sertifikat'] = Lpdk_sertifikat::select(
                'nama_sertifikat',
                'status_sertifikat',
                'nama_pas_sertifikat',
                'status_pas_sertifikat',
                'no_sertifikat',
                'jenis_sertifikat',
                'tgl_berlaku_shgb',
                'lampiran_sertifikat',
                'lampiran_pbb',
                'lampiran_imb'
            )->where('trans_so', $id)->get();
            $arrData[$key]['lampiran'] = Lpdk_lampiran::select(
                'lampiran_ktp_deb',
                'lampiran_ktp_pasangan',
                'lampiran_npwp',
                'lampiran_surat_kematian',
                'lampiran_sk_desa',
                'lampiran_ktp_penjamin',
                'lampiran_pbb',
                'lampiran_imb',
                'lampiran_ajb',
                'lampiran_ahliwaris',
                'lampiran_aktahibah',
                'lampiran_skk',
                'lampiran_sku',
                'lampiran_slipgaji',
                'lampiran_kk',
                'lampiran_surat_lahir',
                'lampiran_surat_nikah',
                'lampiran_surat_cerai',
                'lampiran_ktp_pemilik_sertifikat',
                'lampiran_ktp_pasangan_sertifikat'
            )->where('trans_so', $id)->get();
            $arrData[$key]['kendaraan'] = Lpdk_kendaraan::select(
                'trans_so',
                'no_bpkb',
                'nama_pemilik',
                'alamat_pemilik',
                'merk',
                'jenis',
                'no_rangka',
                'no_mesin',
                'warna',
                'tahun',
                'no_polisi',
                'no_stnk',
                'tgl_kadaluarsa_pajak',
                'tgl_kadaluarsa_stnk',
                'no_faktur',
                'lamp_agunan_depan',
                'lamp_agunan_kanan',
                'lamp_agunan_kiri',
                'lamp_agunan_belakang',
                'lamp_agunan_dalam'
            )->where('trans_so', $id)->get();


            $arrData[$key]['created_at']     = $val->created_at;
            $arrData[$key]['updated_at']     = $val->updated_at;
        }

        // dd(empty($arrData));
        if (empty($arrData)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data LPDK id transaksi ' . $id . ' Kosong'
            ], 404);
        }
        //  $data = array();

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'  =>  $arrData
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
        $lpdk = Lpdk::where('status_kredit', '=', 'ON-PROGRESS')->get();
        //  $lpdk = Lpdk::paginate(10);
        if ($lpdk === NULL) {
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
        $lpdk = Lpdk::where('status_kredit', '=', 'REALISASI')->get();
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
        $lpdk =  DB::connection('web')->table('view_approval_caa')->where('id_trans_so', $id)->get();
        //   dd($lpdk);
        $arrData = array();
        foreach ($lpdk as $key => $val) {
            $arrData[$key]['id_trans_so']       = $val->id_trans_so;
            $arrData[$key]['nomor_so']       = $val->nomor_so;
            $arrData[$key]['nama_so']       = $val->nama_so;
            $arrData[$key]['asal_data']       = $val->asal_data;
            $arrData[$key]['area_kerja']       = $val->area_kerja;
            $arrData[$key]['nama_marketing']       = $val->nama_marketing;
            //$arrData[$key]['request_by']       = $val->request_by;
            $arrData[$key]['plafon']       = $val->plafon;
            $arrData[$key]['tenor']       = $val->tenor;
            $arrData[$key]['nama_debitur']       = $val->nama_debitur;
            $arrData[$key]['nama_pasangan']       = $val->nama_pasangan;
            $det = explode(',', $val->id_penjamin);
            $arrData[$key]['penjamin']       = Penjamin::select('nama_ktp', 'nama_ibu_kandung', 'no_ktp', 'no_npwp', 'tempat_lahir', 'tgl_lahir', 'jenis_kelamin', 'lamp_ktp')->whereIn('id', $det)->get();

            $arrData[$key]['status_nikah']       = $val->status_nikah;
            $arrData[$key]['produk']       = $val->produk;
            $ser = explode(',', $val->id_agunan_tanah);
            $arrData[$key]['sertifikat']       = AgunanTanah::select('nama_pemilik_sertifikat', 'jenis_sertifikat', 'no_sertifikat', 'tgl_berlaku_shgb', 'lamp_sertifikat', 'lamp_imb', 'lamp_pbb')->whereIn('id', $ser)->get();
            $ken = explode(',', $val->id_agunan_kendaraan);
            $arrData[$key]['kendaraan']       = AgunanKendaraan::select('no_bpkb', 'nama_pemilik', 'alamat_pemilik', 'merk', 'jenis', 'no_rangka', 'no_mesin', 'warna', 'tahun', 'no_polisi', 'no_stnk', 'tgl_kadaluarsa_pajak', 'tgl_kadaluarsa_stnk', 'no_faktur', 'lamp_agunan_depan', 'lamp_agunan_kanan', 'lamp_agunan_kiri', 'lamp_agunan_belakang', 'lamp_agunan_dalam')->whereIn('id', $ken)->get();
            $arrData[$key]['lampiran_kk']       = $val->lampiran_kk;
            $arrData[$key]['lampiran_npwp']       = NULL;
            $arrData[$key]['lampiran_ktp_deb']       = $val->lampiran_ktp_deb;
            $arrData[$key]['lampiran_ktp_pasangan']       = $val->lampiran_ktp_pasangan;
            $arrData[$key]['lamp_buku_nikah']       = $val->lamp_buku_nikah;
            $arrData[$key]['lampiran_surat_cerai']       = $val->lampiran_surat_cerai;
            $arrData[$key]['lampiran_surat_lahir']       = NULL;
            $arrData[$key]['lampiran_surat_kematian']       = NULL;
            $arrData[$key]['lampiran_surat_keterangan_desa']       = NULL;
            $arrData[$key]['lampiran_surat_ajb_ppjb']       = NULL;
            $arrData[$key]['lampiran_ahliwaris']       = NULL;
            $arrData[$key]['lampiran_akta_hibah']       = NULL;
        }
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
                'data'  => $arrData
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
        $user_nama = $req->auth->user;
        //   dd($pic->nama);

        $cek_lpdk =  DB::connection('web')->table('view_approval_caa')->where('id_trans_so', $id)->first();
        $explode = explode(',', $cek_lpdk->id_agunan_tanah);
        $explode_penjamin = explode(',', $cek_lpdk->id_penjamin);
        // $in = in_array($explode);
        $getsertifikat = AgunanTanah::whereIn('id', $explode)->get();
        $getpenjamin = Penjamin::whereIn('id', $explode_penjamin)->get();
        // dd($getsertifikat);



        //  dd($arrData);
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

        $check_lamp_kk             = $check_debt->lampiran_kk;
        $check_lamp_ktp             = $check_debt->lampiran_ktp_deb;
        $check_lamp_suratcerai             = $check_debt->lampiran_surat_cerai;
        $check_lamp_ktppas              = $check_debt->lampiran_ktp_pasangan;
        $check_lamp_bukunikah             = $check_debt->lamp_buku_nikah;
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


        $data_sertifikat = array(
            'trans_so'  => $cek_lpdk->id_trans_so,
            'no_sertifikat' => empty($req->input('no_sertifikat')) ? $cek_lpdk->no_sertifikat : $req->input('no_sertifikat'),
            'nama_sertifikat' => empty($req->input('nama_sertifikat')) ? $cek_lpdk->nama_sertifikat : $req->input('nama_sertifikat'),

            'jenis_sertifikat' => empty($req->input('jenis_sertifikat')) ? $cek_lpdk->jenis_sertifikat : $req->input('jenis_sertifikat'),
            'tgl_berlaku_shgb' => empty($req->input('tgl_berlaku_shgb')) ? $cek_lpdk->tgl_berlaku_shgb : $req->input('tgl_berlaku_shgb'),
        );

        //    dd($data_sertifikat['status_sertifikat']);
        $penjamin = array(
            'trans_so'  => $cek_lpdk->id_trans_so,
            'nama_penjamin' => empty($req->input('nama_penjamin')) ? $cek_lpdk->nama_penjamin : $req->input('nama_penjamin'),
            'ibu_kandung_penjamin' => empty($req->input('ibu_kandung_penjamin')) ? $cek_lpdk->ibu_kandung_penjamin : $req->input('ibu_kandung_penjamin')
        );

        $data_lpdk = array(
            'trans_so'  => $cek_lpdk->id_trans_so,
            'nomor_so' => $cek_lpdk->nomor_so,
            'nama_so' => $cek_lpdk->nama_so,
            'asal_data' => $cek_lpdk->asal_data,
            'request_by' => $user_nama,
            'nama_marketing' => empty($req->input('nama_marketing')) ? $cek_lpdk->nama_marketing :   $req->input('nama_marketing'),
            'area_kerja' => empty($req->input('area_kerja')) ? $cek_lpdk->area_kerja :   $req->input('area_kerja'),
            'plafon' => $cek_lpdk->plafon,
            'tenor' => $cek_lpdk->tenor,
            'nama_debitur' => empty($req->input('nama_debitur')) ? $cek_lpdk->nama_debitur :   $req->input('nama_debitur'),
            'nama_pasangan' => empty($req->input('nama_pasangan')) ? $cek_lpdk->nama_pasangan :  $req->input('nama_pasangan'),

            'status_nikah' => empty($req->input('status_nikah')) ? $cek_lpdk->status_nikah : $req->input('status_nikah'),
            'produk' => empty($req->input('produk')) ? $cek_lpdk->produk : $req->input('produk'),
            // 'hub_cadeb' => empty($req->input('hub_cadeb')) ? $cek_lpdk->hub_cadeb : $req->input('hub_cadeb'),
            // 'alamat_ktp_vs_jaminan' => empty($req->input('alamat_ktp_vs_jaminan')) ? $cek_lpdk->alamat_ktp_vs_jaminan : $req->input('alamat_ktp_vs_jaminan'),
            // 'lampiran_skk' => $lamp_skk,
            // 'lampiran_sku' => $lamp_sku,
            // 'lampiran_slipgaji' => $lamp_slip_gaji,
            //   'notes_progress' => $pic->nama . " : " . $req->input('notes_progress'),
            'status_kredit' => 'ON-PROGRESS'
        );

        // dd(array_merge($data_lpdk, $data_sertifikat));
        $lampiran = array(
            'trans_so'  => $cek_lpdk->id_trans_so,
            'lampiran_ktp_deb' =>  $check_lamp_ktp,
            'lampiran_ktp_pasangan' =>  $check_lamp_ktppas,
            'lampiran_ktp_penjamin' =>  $check_lamp_ktppen,
            // 'lampiran_npwp' => $lamp_npwp,
            'lampiran_sertifikat' =>  $check_lamp_sertifikat,
            'lampiran_pbb' => $check_lamp_sttp_pbb,
            'lampiran_imb' =>  $check_lamp_imb,
            'lampiran_kk' => $check_lamp_kk,
            'lampiran_surat_cerai' => $check_lamp_suratcerai,
            'lampiran_surat_nikah' => $check_lamp_bukunikah
        );
        //   dd($data);

        // if ($data_lpdk || $data_sertifikat === null) {
        //     return response()->json([
        //         'code'  => 403,
        //         'message'   => 'data harus di input',
        //         'data'  => $data_lpdk
        //     ]);
        // }

        //  dd(Lpdk::where('nomor_so', $cek_lpdk->nomor_so)->first());
        //  dd(Lpdk::get());

        if (Lpdk::where('nomor_so', $cek_lpdk->nomor_so)->exists()) {
            return response()->json([
                "Code"      => 409,
                "Status"    => "Conflict",
                "Message"   => "Nomor SO pengajuan LPDK Sudah Ada"
            ], 409);
        }
        $arrData = array();
        $i = 0;
        foreach ($getsertifikat as $key => $val) {
            $arrData[$key][$i]['nama_pemilik_sertifikat']       = $val->nama_pemilik_sertifikat;
            $arrData[$key][$i]['jenis_sertifikat']   = $val->jenis_sertifikat;
            $arrData[$key][$i]['no_sertifikat']    = $val->no_sertifikat;
            // $arrData[$key]['tgl_berlaku_shgb'] = $val->tgl_berlaku_shgb;
            $arrData[$key][$i]['lamp_sertifikat']   = $val->lamp_sertifikat;
            $arrData[$key][$i]['lamp_imb']     = $val->lamp_imb;
            $arrData[$key][$i]['lamp_pbb']     = $val->lamp_pbb;

            //  dd($arrData);




            //}
            // dd($arrData);

            $sertifikat = Lpdk_sertifikat::create([
                'trans_so' => $id,
                'nama_sertifikat' => $val['nama_pemilik_sertifikat'],
                'jenis_sertifikat' => $val['jenis_sertifikat'],
                'no_sertifikat' => $val['no_sertifikat'],
                'tgl_berlaku_shgb' => $val['tgl_berlaku_shgb'],
                'lampiran_sertifikat' => $val['lamp_sertifikat'],
                'lamp_imb' => $val['lamp_imb'],
                'lamp_pbb' => $val['lamp_pbb']
            ]);
        }

        $get_id = Lpdk_sertifikat::select('id')->where('trans_so', $id)->get();

        $str1 = str_replace('[', '', $get_id);
        $str2 = str_replace('{', '', $str1);
        $str3 = str_replace('"', '', $str2);
        $str4 = str_replace('}', '', $str3);
        $str5 = str_replace(']', '', $str4);
        $str6 = str_replace('id:', '', $str5);

        $arrpenj = array();
        $i = 0;
        foreach ($getpenjamin as $key => $val) {
            $arrpenj[$key][$i]['trans_so']       = $val->trans_so;
            $arrpenj[$key][$i]['nama_penjamin']   = $val->nama_penjamin;
            $arrpenj[$key][$i]['ibu_kandung_penjamin']    = $val->ibu_kandung_penjamin;
            $arrpenj[$key][$i]['pasangan_penjamin']   = $val->pasangan_penjamin;
            $arrpenj[$key][$i]['lampiran_ktp_penjamin']     = $val->lampiran_ktp_penjamin;




            $penj = Lpdk_penjamin::create([
                'trans_so'       => $val['trans_so'],
                'nama_penjamin'   => $val['nama_penjamin'],
                'ibu_kandung_penjamin'    => $val['ibu_kandung_penjamin'],
                'pasangan_penjamin'   => $val['pasangan_penjamin'],
                'lampiran_ktp_penjamin'     => $val['lampiran_ktp_penjamin']
            ]);
        }
        $get_pen = Lpdk_penjamin::select('id')->where('trans_so', $id)->get();

        $str1 = str_replace('[', '', $get_pen);
        $str2 = str_replace('{', '', $str1);
        $str3 = str_replace('"', '', $str2);
        $str4 = str_replace('}', '', $str3);
        $str5 = str_replace(']', '', $str4);
        $strpen = str_replace('id:', '', $str5);

        $lamp = Lpdk_lampiran::create($lampiran);

        Lpdk::create($data_lpdk)->update(['id_sertifikat' => $str6, 'id_lampiran' => $lamp->id, 'id_penjamin' => $strpen]);

        try {
            return response()->json([
                'code' => 200,
                'status' => 'success',
                'data' => array_merge($data_lpdk, $data_sertifikat, $penjamin, $lampiran)
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
        $cek_lpdk = Lpdk::where('trans_so', $id)->first();

        $cek_sertif = Lpdk_sertifikat::where('trans_so', $id)->first();
        //dd($cek_lpdk);

        if ($cek_lpdk === null) {
            return response()->json([
                'code'  => 402,
                'message'  => 'data transaksi SO dengan id =' . ' ' . $id . ' ' . 'tidak ditemukan'
            ]);
        }

        $check_debt_ktp = Debitur::where('id', $id)->first();
        //  $check_lpdk = Lpdk::where('id', $id)
        $check_debt = Lpdk::where('trans_so', $id)->first();

        $check_lamp = Lpdk_lampiran::where('trans_so', $id)->first();

        $check_ktp_pen = Lpdk_penjamin::where('trans_so', $id)->first();
        // dd($check_debt->lampiran_ktp_deb);
        //    dd($check_debt);

        $path = 'public/' . $check_debt_ktp->no_ktp . '/debitur/lpdk';
        $check_lamp_ktp             = $check_lamp->lampiran_ktp_deb;
        $check_lamp_ktppas              = $check_lamp->lampiran_ktp_pasangan;
        $check_lamp_ktppen             = $check_lamp->lampiran_ktp_penjamin;
        $check_lamp_npwp             = $check_lamp->lampiran_npwp;
        $check_lamp_surat_kematian = $check_lamp->lampiran_surat_kematian;
        $check_lamp_sk_desa = $check_lamp->lampiran_sk_desa;
        $check_lamp_ajb = $check_lamp->lampiran_ajb;
        $check_lamp_ahliwaris = $check_lamp->lampiran_ahliwaris;
        $check_lamp_aktahibah = $check_lamp->lampiran_aktahibah;

        $check_lamp_sertifikat      = $check_lamp->lampiran_sertifikat;
        $check_lamp_sttp_pbb        = $check_lamp->lampiran_pbb;
        $check_lamp_imb             = $check_lamp->lampiran_imb;
        $check_lamp_skk             = $check_lamp->lampiran_skk;
        $check_lamp_sku             = $check_lamp->lampiran_sku;
        $check_lamp_slip_gaji       = $check_lamp->lampiran_slipgaji;
        $check_lamp_kk              = $check_lamp->lampiran_kk;
        $check_surat_lahir    = $check_lamp->lampiran_surat_lahir;
        $check_surat_nikah    = $check_lamp->lampiran_surat_nikah;
        $check_surat_cerai    = $check_lamp->lampiran_surat_cerai;
        $check_lamp_ktp_pemilik_sert   = $check_lamp->lampiran_ktp_pemilik_sertifikat;
        $check_lamp_ktp_pasangan_sert   = $check_lamp->lampiran_ktp_pasangan_sertifikat;
        $check_lamp_ktp_pasangan_penjamin   = $check_ktp_pen->lampiran_ktp_penjamin;
        // $check_foto_pembukuan_usaha = $check_lamp->foto_pembukuan_usaha;
        // $check_lamp_foto_usaha      = $check_lamp->lamp_foto_usaha;
        // $check_lamp_surat_cerai     = $check_lamp->lamp_surat_cerai;
        // $check_lamp_tempat_tinggal  = $check_lamp->lamp_tempat_tinggal;

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

        if ($file = $req->file('lampiran_kk')) {
            $name = 'lamp_kk.';
            $check = $check_lamp_kk;

            $lamp_kk = Helper::uploadImg($check, $file, $path, $name);
        } else {
            $lamp_kk = $check_lamp_kk;
        }

        if ($file = $req->file('lampiran_surat_lahir')) {
            $name = 'lamp_suratlahir.';
            $check = $check_surat_lahir;

            $lamp_suratlahir = Helper::uploadImg($check, $file, $path, $name);
        } else {
            $lamp_suratlahir = $check_surat_lahir;
        }

        if ($file = $req->file('lampiran_surat_nikah')) {
            $name = 'lamp_suratnikah.';
            $check = $check_surat_nikah;

            $lamp_suratnikah = Helper::uploadImg($check, $file, $path, $name);
        } else {
            $lamp_suratnikah = $check_surat_nikah;
        }

        if ($file = $req->file('lampiran_surat_cerai')) {
            $name = 'lamp_suratcerai.';
            $check = $check_surat_cerai;

            $lamp_suratcerai = Helper::uploadImg($check, $file, $path, $name);
        } else {
            $lamp_suratcerai = $check_surat_cerai;
        }


        if ($file = $req->file('lampiran_ktp_pem_sertifikat')) {
            $name = 'lamp_ktppemiliksertifikat.';
            $check = $check_lamp_ktp_pemilik_sert;

            $lamp_ktppemsert = Helper::uploadImg($check, $file, $path, $name);
        } else {
            $lamp_ktppemsert = $check_lamp_ktp_pemilik_sert;
        }

        if ($file = $req->file('lampiran_ktp_pas_sertifikat')) {
            $name = 'lamp_ktppasangansertifikat.';
            $check = $check_lamp_ktp_pasangan_sert;

            $lamp_ktppassert = Helper::uploadImg($check, $file, $path, $name);
        } else {
            $lamp_ktppassert = $check_lamp_ktp_pasangan_sert;
        }

        if ($file = $req->file('lampiran_ktp_penjamin')) {
            $name = 'lamp_ktppenjamin.';
            $check = $check_lamp_ktp_pasangan_penjamin;

            $lamp_ktppenj = Helper::uploadImg($check, $file, $path, $name);
        } else {
            $lamp_ktppenj = $check_lamp_ktp_pasangan_penjamin;
        }

        $note_cek = Lpdk::where('trans_so', $id)->first();

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
            'status_nikah' => empty($req->input('status_nikah')) ? $cek_lpdk->status_nikah : $req->input('status_nikah'),

            'produk' => empty($req->input('produk')) ? $cek_lpdk->produk : $req->input('produk'),

            'alamat_ktp_vs_jaminan' => empty($req->input('alamat_ktp_vs_jaminan')) ? $cek_lpdk->alamat_ktp_vs_jaminan : $req->input('alamat_ktp_vs_jaminan'),

            'hub_cadeb' => empty($req->input('hub_cadeb')) ? $cek_lpdk->hub_cadeb : $req->input('hub_cadeb'),
            'akta_notaris' => empty($req->input('akta_notaris')) ? $cek_lpdk->akta_notaris : $req->input('akta_notaris'),
            'notes_progress' => empty($req->input('notes_progress')) ? $note_cek->notes_progress : $pic->nama . " : " . $req->input('notes_progress'),
            'notes_counter' => $pic->nama . " : " . $req->input('notes_counter') . ',' . $note_cek->notes_counter,
            'status_kredit' => $req->input('status_kredit'),

        );

        // $data_sert = array(
        //     'trans_so' => $id,
        //     'no_sertifikat' => empty($req->input('no_sertifikat')) ? $cek_sertif->no_sertifikat : $req->input('no_sertifikat'),
        //     'nama_sertifikat' => empty($req->input('nama_sertifikat')) ? $cek_sertif->nama_sertifikat : $req->input('nama_sertifikat'),
        //     'status_sertifikat' => empty($req->input('status_sertifikat')) ? $cek_sertif->status_sertifikat : $req->input('status_sertifikat'),
        //     'jenis_sertifikat' => empty($req->input('jenis_sertifikat')) ? $cek_sertif->jenis_sertifikat : $req->input('jenis_sertifikat'),
        //     'tgl_berlaku_shgb' => empty($req->input('tgl_berlaku_shgb')) ? $cek_sertif->tgl_berlaku_shgb : $req->input('tgl_berlaku_shgb'),
        //     'nama_pas_sertifikat' => empty($req->input('nama_pas_sertifikat')) ? $cek_sertif->nama_pas_sertifikat : $req->input('nama_pas_sertifikat'),
        //     'status_pas_sertifikat' => empty($req->input('status_pas_sertifikat')) ? $cek_sertif->status_pas_sertifikat : $req->input('status_pas_sertifikat'),
        //     'lampiran_sertifikat' => $lamp_sertifikat

        // );

        $data_lamp = array(
            'trans_so' => $id,
            'lampiran_ktp_deb' => $lamp_ktp,
            'lampiran_ktp_pasangan' => $lamp_ktp_pas,
            'lampiran_ktp_penjamin' => $lamp_ktp_pen,
            'lampiran_npwp' => $lamp_npwp,
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
            'lampiran_kk'       => $lamp_kk,
            'lampiran_surat_lahir'  => $lamp_suratlahir,
            'lampiran_surat_nikah'  => $lamp_suratnikah,
            'lampiran_surat_cerai'  => $lamp_suratcerai,
            'lampiran_ktp_pemilik_sertifikat' => $lamp_ktppemsert,
            'lampiran_ktp_pasangan_sertifikat' => $lamp_ktppassert,
        );

        // $data_penj = array(
        //     'trans_so' => $id,
        //     'nama_penjamin' => empty($req->input('nama_penjamin')) ? $cek_lpdk->nama_penjamin : $req->input('nama_penjamin'),
        //     'ibu_kandung_penjamin' => empty($req->input('ibu_kandung_penjamin')) ? $cek_lpdk->ibu_kandung_penjamin : $req->input('ibu_kandung_penjamin'),
        //     'pasangan_penjamin' => empty($req->input('pasangan_penjamin')) ? $cek_lpdk->pasangan_penjamin : $req->input('pasangan_penjamin'),
        //     'lampiran_ktp_penjamin'       => $lamp_ktppenj,
        // );

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
            //  $data_sertifikat = Lpdk_sertifikat::where('trans_so', $id)->update($data_sert);
            $upd_lamp = Lpdk_lampiran::where('trans_so', $id)->update($data_lamp);
            //    $upd_penj = Lpdk_penjamin::where('trans_so', $id)->update($data_penj);
            Lpdk::where('trans_so', $id)->update($data);


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

    public function tambahSertifikat($id_trans, Request $req)
    {
        //$coba = $req->input('coba');
        //dd($coba);

        $pic     = $req->pic; // From PIC middleware
        $user_id = $req->auth->user_id;
        //  dd($user_id);
        $get_lpdk = Lpdk::get();

        $cek_lpdk = Lpdk::where('trans_so', $id_trans)->first();

        // $cek_sertif = Lpdk_sertifikat::where('trans_so', $id_trans)->first();
        //dd($cek_lpdk);

        if ($cek_lpdk === null) {
            return response()->json([
                'code'  => 402,
                'message'  => 'data transaksi SO dengan id =' . ' ' . $id_trans . ' ' . 'tidak ditemukan'
            ]);
        }

        $check_debt_ktp = Debitur::where('id', $id_trans)->first();
        //  $check_lpdk = Lpdk::where('id', $id_trans)
        $check_debt = Lpdk::where('trans_so', $id_trans)->first();

        $check_lamp = Lpdk_lampiran::where('trans_so', $id_trans)->first();


        $path = 'public/' . $check_debt_ktp->no_ktp . '/debitur/lpdk/sertifikat';

        $check_lamp_sertifikat      = $check_lamp->lampiran_sertifikat;


        if ($files = $req->file('lampiran_sertifikat')) {
            $name = 'sertifikat.';
            $check = $check_lamp_sertifikat;
            $arrayPath = array();
            foreach ($files as $file) {
                $arrayPath[] = Helper::uploadImg($check, $file, $path, $name);
            }

            $lamp_sertifikat = $arrayPath;
        } else {
            $lamp_sertifikat = $check_lamp_sertifikat;
        }



        $note_cek = Lpdk::where('trans_so', $id_trans)->first();

        // DB::connection('web')->beginTransaction();

        if (!empty($req->input('no_sertifikat'))) {
            for ($i = 0; $i < count($req->input('no_sertifikat')); $i++) {


                $data_sert[] = array(
                    'trans_so' => $id_trans,
                    'no_sertifikat' => empty($req->input('no_sertifikat')[$i]) ? NULL : $req->input('no_sertifikat')[$i],
                    'nama_sertifikat' => empty($req->input('nama_sertifikat')[$i]) ? NULL : $req->input('nama_sertifikat')[$i],
                    'status_sertifikat' => empty($req->input('status_sertifikat')[$i]) ? NULL : $req->input('status_sertifikat')[$i],
                    'jenis_sertifikat' => empty($req->input('jenis_sertifikat')[$i]) ? NULL : $req->input('jenis_sertifikat')[$i],
                    'tgl_berlaku_shgb' => empty($req->input('tgl_berlaku_shgb')[$i]) ? NULL : $req->input('tgl_berlaku_shgb')[$i],
                    'nama_pas_sertifikat' => empty($req->input('nama_pas_sertifikat')[$i]) ? NULL : $req->input('nama_pas_sertifikat')[$i],
                    'status_pas_sertifikat' => empty($req->input('status_pas_sertifikat')[$i]) ? NULL : $req->input('status_pas_sertifikat')[$i],
                    'lampiran_sertifikat' => $lamp_sertifikat[$i],

                );
            }
        }


        if (!empty($req->input('no_sertifikat'))) {
            for ($j = 0; $j < count($data_sert); $j++) {
                $sert = Lpdk_sertifikat::create($data_sert[$j]);
                $id_sert['id'][$j] = $sert->id;
            }
            $serID = implode(",", $id_sert['id']);
        } else {
            $serID = null;
        }
        // }


        // try {

        $cek = Lpdk::where('trans_so', $id_trans)->first();

        Lpdk::where('trans_so', $id_trans)->update(['id_sertifikat' => $cek->id_sertifikat . "," . $serID]);


        return response()->json([
            'code' => 200,
            'status' => 'success',
            'data' => $data_sert
        ]);
        // } catch (\Exception $e) {
        //     $err = DB::connection('web')->rollback();
        //     return response()->json([
        //         'code'    => 501,
        //         'status'  => 'error',
        //         'message' => $err
        //     ], 501);
        // }
    }
    public function tambahPenjamin($id_trans, Request $req)
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
        $cek_lpdk = Lpdk::where('trans_so', $id_trans)->first();

        $cek_sertif = Lpdk_penjamin::where('trans_so', $id_trans)->first();
        //dd($cek_lpdk);

        if ($cek_lpdk === null) {
            return response()->json([
                'code'  => 402,
                'message'  => 'data transaksi SO dengan id =' . ' ' . $id_trans . ' ' . 'tidak ditemukan'
            ]);
        }

        $check_debt_ktp = Debitur::where('id', $id_trans)->first();
        //  $check_lpdk = Lpdk::where('id', $id_trans)
        $check_debt = Lpdk::where('trans_so', $id_trans)->first();

        $check_ktp_pen = Lpdk_penjamin::where('trans_so', $id_trans)->first();


        $path = 'public/' . $check_debt_ktp->no_ktp . '/debitur/lpdk/penjamin';

        $check_lamp_ktp_pasangan_penjamin   = $check_ktp_pen->lampiran_ktp_penjamin;



        $note_cek = Lpdk::where('trans_so', $id_trans)->first();


        if ($files = $req->file('lampiran_ktp_penjamin')) {
            $name = 'lamp_ktppenjamin.';
            $check = $check_lamp_ktp_pasangan_penjamin;
            $arrayPath = array();
            foreach ($files as $file) {

                $arrayPath[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $lamp_ktppenj = $arrayPath;
        } else {
            $lamp_ktppenj = $check_lamp_ktp_pasangan_penjamin;
        }



        if (!empty($req->input('nama_penjamin'))) {
            for ($i = 0; $i < count($req->input('nama_penjamin')); $i++) {

                $data_penj[] = array(
                    'trans_so' => $id_trans,
                    'nama_penjamin' => empty($req->input('nama_penjamin')[$i]) ? NULL : $req->input('nama_penjamin')[$i],
                    'ibu_kandung_penjamin' => empty($req->input('ibu_kandung_penjamin')[$i]) ? NULL : $req->input('ibu_kandung_penjamin')[$i],
                    'pasangan_penjamin' => empty($req->input('pasangan_penjamin')[$i]) ? NULL : $req->input('pasangan_penjamin')[$i],
                    'lampiran_ktp_penjamin'       => $lamp_ktppenj[$i],
                );
            }
        }

        if (!empty($req->input('nama_penjamin'))) {
            for ($j = 0; $j < count($data_penj); $j++) {
                $penj = Lpdk_penjamin::create($data_penj[$j]);
                $id_penjamin['id'][$j] = $penj->id;
            }
            $penID = implode(",", $id_penjamin['id']);
        } else {
            //  $penID = null;
        }
        try {
            $cek = Lpdk::where('trans_so', $id_trans)->first();
            //dd($cek->id_penjamin);
            //  DB::connection('web')->commit();
            // $data_penjamin = Lpdk_penjamin::create($data_penj);
            // $upd_lamp = Lpdk_lampiran::where('trans_so', $id)->update($data_lamp);
            //$upd_penj = Lpdk_penjamin::where('trans_so', $id)->update($data_penj);
            Lpdk::where('trans_so', $id_trans)->update(['id_penjamin' => $cek->id_penjamin . "," . $penID]);


            return response()->json([
                'code' => 200,
                'status' => 'success',
                'data' => $data_penj
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

    public function EditSertifikat($id_trans, $id, Request $req)
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
        $cek_lpdk = Lpdk::where('trans_so', $id_trans)->first();

        $cek_sertif = Lpdk_sertifikat::where('id', $id)
            ->where('trans_so', $id_trans)->first();
        //dd($cek_lpdk);

        if ($cek_lpdk === null) {
            return response()->json([
                'code'  => 402,
                'message'  => 'data transaksi SO dengan id =' . ' ' . $id_trans . ' ' . 'tidak ditemukan'
            ]);
        }

        $check_debt_ktp = Debitur::where('id', $id_trans)->first();
        //  $check_lpdk = Lpdk::where('id', $id_trans)
        $check_debt = Lpdk::where('trans_so', $id_trans)->first();

        $check_lamp = Lpdk_lampiran::where('trans_so', $id_trans)->first();


        $path = 'public/' . $check_debt_ktp->no_ktp . '/debitur/lpdk';

        $check_lamp_sertifikat      = $check_lamp->lampiran_sertifikat;


        if ($file = $req->file('lampiran_sertifikat')) {
            $name = 'sertifikat.';
            $check = $check_lamp_sertifikat;

            $lamp_sertifikat = Helper::uploadImg($check, $file, $path, $name);
        } else {
            $lamp_sertifikat = $check_lamp_sertifikat;
        }



        $note_cek = Lpdk::where('trans_so', $id_trans)->first();

        DB::connection('web')->beginTransaction();

        $data_sert = array(
            'trans_so' => $id_trans,
            'no_sertifikat' => empty($req->input('no_sertifikat')) ? $cek_sertif->no_sertifikat : $req->input('no_sertifikat'),
            'nama_sertifikat' => empty($req->input('nama_sertifikat')) ? $cek_sertif->nama_sertifikat : $req->input('nama_sertifikat'),
            'status_sertifikat' => empty($req->input('status_sertifikat')) ? $cek_sertif->status_sertifikat : $req->input('status_sertifikat'),
            'jenis_sertifikat' => empty($req->input('jenis_sertifikat')) ? $cek_sertif->jenis_sertifikat : $req->input('jenis_sertifikat'),
            'tgl_berlaku_shgb' => empty($req->input('tgl_berlaku_shgb')) ? $cek_sertif->tgl_berlaku_shgb : $req->input('tgl_berlaku_shgb'),
            'nama_pas_sertifikat' => empty($req->input('nama_pas_sertifikat')) ? $cek_sertif->nama_pas_sertifikat : $req->input('nama_pas_sertifikat'),
            'status_pas_sertifikat' => empty($req->input('status_pas_sertifikat')) ? $cek_sertif->status_pas_sertifikat : $req->input('status_pas_sertifikat'),
            'lampiran_sertifikat' => $lamp_sertifikat,

        );

        try {
            $cek = Lpdk::where('trans_so', $id_trans)->first();
            DB::connection('web')->commit();
            $data_sertifikat = Lpdk_sertifikat::where('id', $id)->where('trans_so', $id_trans)->update($data_sert);
            // $upd_lamp = Lpdk_lampiran::where('trans_so', $id)->update($data_lamp);
            //$upd_penj = Lpdk_penjamin::where('trans_so', $id)->update($data_penj);
            Lpdk::where('trans_so', $id_trans)->update(['id_sertifikat' => $cek->id_sertifikat . "," . $data_sertifikat->id]);


            return response()->json([
                'code' => 200,
                'status' => 'success',
                'data' => $data_sertifikat
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

    public function EditPenjamin($id_trans, $id, Request $req)
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
        $cek_lpdk = Lpdk::where('trans_so', $id_trans)->first();

        $cek_sertif = Lpdk_penjamin::where('trans_so', $id_trans)->where('id', $id)->first();
        //dd($cek_lpdk);

        if ($cek_lpdk === null) {
            return response()->json([
                'code'  => 402,
                'message'  => 'data transaksi SO dengan id =' . ' ' . $id_trans . ' ' . 'tidak ditemukan'
            ]);
        }

        $check_debt_ktp = Debitur::where('id', $id_trans)->first();
        //  $check_lpdk = Lpdk::where('id', $id_trans)
        $check_debt = Lpdk::where('trans_so', $id_trans)->first();

        $check_ktp_pen = Lpdk_penjamin::where('trans_so', $id_trans)->first();


        $path = 'public/' . $check_debt_ktp->no_ktp . '/debitur/lpdk';

        $check_lamp_ktp_pasangan_penjamin   = $check_ktp_pen->lampiran_ktp_penjamin;




        $note_cek = Lpdk::where('trans_so', $id_trans)->first();

        DB::connection('web')->beginTransaction();

        if ($file = $req->file('lampiran_ktp_penjamin')) {
            $name = 'lamp_ktppenjamin.';
            $check = $check_lamp_ktp_pasangan_penjamin;

            $lamp_ktppenj = Helper::uploadImg($check, $file, $path, $name);
        } else {
            $lamp_ktppenj = $check_lamp_ktp_pasangan_penjamin;
        }

        $data_penj = array(
            'trans_so' => $id_trans,
            'nama_penjamin' => empty($req->input('nama_penjamin')) ? $cek_lpdk->nama_penjamin : $req->input('nama_penjamin'),
            'ibu_kandung_penjamin' => empty($req->input('ibu_kandung_penjamin')) ? $cek_lpdk->ibu_kandung_penjamin : $req->input('ibu_kandung_penjamin'),
            'pasangan_penjamin' => empty($req->input('pasangan_penjamin')) ? $cek_lpdk->pasangan_penjamin : $req->input('pasangan_penjamin'),
            'lampiran_ktp_penjamin'       => $lamp_ktppenj,
        );


        try {
            $cek = Lpdk::where('trans_so', $id_trans)->first();
            //dd($cek->id_penjamin);
            DB::connection('web')->commit();
            $data_penjamin = Lpdk_penjamin::where('id', $id)->where('trans_so', $id_trans)->update($data_penj);
            // $upd_lamp = Lpdk_lampiran::where('trans_so', $id)->update($data_lamp);
            //$upd_penj = Lpdk_penjamin::where('trans_so', $id)->update($data_penj);
            Lpdk::where('trans_so', $id_trans)->update(['id_penjamin' => $cek->id_penjamin . "," . $data_penjamin->id]);


            return response()->json([
                'code' => 200,
                'status' => 'success',
                'data' => $data_penj
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
