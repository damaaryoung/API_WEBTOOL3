<?php

namespace App\Http\Controllers\Pengajuan;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Requests\Debt\DebtPenjaminRequest;
use App\Http\Requests\Debt\DebtPasanganRequest;
use App\Http\Controllers\Controller as Helper;
use App\Models\Wilayah\Kabupaten;
use App\Models\Wilayah\Kecamatan;
use App\Models\Wilayah\Kelurahan;
use App\Models\Wilayah\Provinsi;
use App\Models\Bisnis\TransSo;
use Illuminate\Http\Request;
use App\Models\CC\Penjamin;
use App\Http\Requests;
use App\Models\User;
use Carbon\Carbon;
// use Image;
use DB;

class HMController extends BaseController
{
    public function index(Request $req){
        // $user_id = $req->auth->user_id;

        $kode_kantor = $req->auth->kd_cabang;
        $query = TransSo::where('kode_kantor', $kode_kantor)->get();

        if ($query == '[]') {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        $data = array();
        foreach ($query as $key => $val) {

            if ($val->status_das == 0) {
                $status_das = 'waiting';
            }elseif($val->status_das == 1){
                $status_das = 'complete';
            }else{
                $status_das = 'not complete';
            }

            if ($val->status_hm == 0) {
                $status_hm = 'waiting';
            }elseif ($val->status_hm == 1) {
                $status_hm = 'complete';
            }else{
                $status_hm = 'not complete';
            }

            $data[$key] = [
                'id'             => $val->id,
                'nomor_so'       => $val->nomor_so,
                'kode_kantor'    => $val->kode_kantor,
                'asal_data'      => $val->asaldata['nama'],
                'nama_marketing' => $val->nama_marketing,
                'nama_so'        => $val->nama_so,
                'nama_debitur'   => $val->debt['nama_lengkap'],
                'plafon'         => (int) $val->faspin->plafon,
                'tenor'          => (int) $val->faspin->tenor,
                'das_status'     => $status_das,
                'das_note'       => $val->catatan_das,
                'hm_status'      => $status_das,
                'hm_note'        => $val->catatan_hm
            ];
        }

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => $data
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    // public function whereKode($kode, Request $req){
    //     $query = TransSo::where('kode_kantor', '=', $kode)->get();

    //     if ($query == '[]') {
    //         return response()->json([
    //             'code'    => 404,
    //             'status'  => 'not found',
    //             'message' => 'Data kosong'
    //         ], 404);
    //     }

    //     try {
    //         return response()->json([
    //             'code'   => 200,
    //             'status' => 'success',
    //             'data'   => $query
    //         ], 200);
    //     } catch (Exception $e) {
    //         return response()->json([
    //             "code"    => 501,
    //             "status"  => "error",
    //             "message" => $e
    //         ], 501);
    //     }
    // }

    public function show($id, Request $req){
        // $user_id = $req->auth->user_id;
        $kode_kantor = $req->auth->kd_cabang;
        $query = TransSo::where('id', $id)->where('kode_kantor', $kode_kantor)->get();

        if ($query == '[]') {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        $prov_ktp = Provinsi::where('id', $query[0]->debt['id_prov_ktp'])->first();
        $kab_ktp  = Kabupaten::where('id', $query[0]->debt['id_kab_ktp'])->first();
        $kec_ktp  = Kecamatan::where('id', $query[0]->debt['id_kec_ktp'])->first();
        $kel_ktp  = Kelurahan::where('id', $query[0]->debt['id_kel_ktp'])->first();

        $prov_dom = Provinsi::where('id', $query[0]->debt['id_prov_domisili'])->first();
        $kab_dom  = Kabupaten::where('id', $query[0]->debt['id_kab_domisili'])->first();
        $kec_dom  = Kecamatan::where('id', $query[0]->debt['id_kec_domisili'])->first();
        $kel_dom  = Kelurahan::where('id', $query[0]->debt['id_kel_domisili'])->first();

        $penjamin = Penjamin::where('id_calon_debitur', $query[0]->id_calon_debt)->get();

        foreach ($query as $key => $val) {

            if ($val->status_das == 0) {
                $status_das = 'waiting';
            }elseif($val->status_das == 1){
                $status_das = 'complete';
            }else{
                $status_das = 'not complete';
            }

            if ($val->status_hm == 0) {
                $status_hm = 'waiting';
            }elseif ($val->status_hm == 1) {
                $status_hm = 'complete';
            }else{
                $status_hm = 'not complete';
            }

            $data[$key] = [
                'id'             => $val->id,
                'nomor_so'       => $val->nomor_so,
                'kode_kantor'    => $val->kode_kantor,
                'asal_data'      => $val->asaldata['nama'],
                'nama_marketing' => $val->nama_marketing,
                'nama_so'        => $val->nama_so,
                'plafon'         => (int) $val->faspin->plafon,
                'tenor'          => (int) $val->faspin->tenor,
                'fasilitas_pinjaman'  => [
                    'jenis_pinjaman'  => $val->faspin->jenis_pinjaman,
                    'tujuan_pinjaman' => $val->faspin->tujuan_pinjaman
                ],
                'data_debitur' => [
                    'nama_lengkap'          => $val->debt['nama_lengkap'],
                    'gelar_keagamaan'       => $val->debt['gelar_keagamaan'],
                    'gelar_pendidikan'      => $val->debt['gelar_pendidikan'],
                    'jenis_kelamin'         => $val->debt['jenis_kelamin'],
                    'status_nikah'          => $val->debt['status_nikah'],
                    'ibu_kandung'           => $val->debt['ibu_kandung'],
                    'no_ktp'                => $val->debt['no_ktp'],
                    'no_ktp_kk'             => $val->debt[''],
                    'no_kk'                 => $val->debt['no_ktp_kk'],
                    'no_npwp'               => $val->debt['no_npwp'],
                    'tempat_lahir'          => $val->debt['tempat_lahir'],
                    'tgl_lahir'             => $val->debt['tgl_lahir'],
                    'agama'                 => $val->debt['agama'],
                    'alamat_ktp'            => $val->debt['alamat_ktp'],
                    'rt_ktp'                => $val->debt['rt_ktp'],
                    'rw_ktp'                => $val->debt['rw_ktp'],
                    'provinsi_ktp'          => $prov_ktp->nama,
                    'kabupaten_ktp'         => $kab_ktp->nama,
                    'kecamatan_ktp'         => $kec_ktp->nama,
                    'kelurahan_ktp'         => $kel_ktp->nama,
                    'alamat_domisili'       => $val->debt['alamat_domisili'],
                    'rt_domisili'           => $val->debt['rt_domisili'],
                    'rw_domisili'           => $val->debt['rw_domisili'],
                    'provinsi_domisili'     => $prov_dom->nama,
                    'kabupaten_domisili'    => $kab_dom->nama,
                    'kecamatan_domisili'    => $kec_dom->nama,
                    'kelurahan_domisili'    => $kel_dom->nama,
                    'pendidikan_terakhir'   => $val->debt['pendidikan_terakhir'],
                    'jumlah_tanggungan'     => $val->debt['jumlah_tanggungan'],
                    'no_telp'               => $val->debt['no_telp'],
                    'no_hp'                 => $val->debt['no_hp'],
                    'alamat_surat'          => $val->debt['alamat_surat'],
                    'lamp_ktp'              => $val->debt['lamp_ktp'],
                    'lamp_kk'               => $val->debt['lamp_kk'],
                    'lamp_sertifikat'       => $val->debt['lamp_sertifikat'],
                    'lamp_sttp_pbb'         => $val->debt['lamp_sttp_pbb'],
                    'lamp_imb'              => $val->debt['lamp_imb']
                ],
                'data_pasangan' => [
                    'nama_lengkap'     => $val->pas['nama_lengkap'],
                    'nama_ibu_kandung' => $val->pas['nama_ibu_kandung'],
                    'jenis_kelamin'    => $val->pas['jenis_kelamin'],
                    'no_ktp'           => $val->pas['no_ktp'],
                    'no_ktp_kk'        => $val->pas['no_ktp_kk'],
                    'no_npwp'          => $val->pas['no_npwp'],
                    'tempat_lahir'     => $val->pas['tempat_lahir'],
                    'tgl_lahir'        => $val->pas['tgl_lahir'],
                    'alamat_ktp'       => $val->pas['alamat_ktp'],
                    'no_telp'          => $val->pas['no_telp'],
                    'lamp_ktp'         => $val->pas['lamp_ktp'],
                    'lamp_buku_nikah'  => $val->pas['lamp_buku_nikah']
                ],
                'data_penjamin' => $penjamin,
                'das_status'    => $status_das,
                'das_note'      => $val->catatan_das,
                'hm_status'     => $status_das,
                'hm_note'       => $val->catatan_hm
            ];
        }

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => $data[0]
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function update($id, Request $req){
        $check = TransSo::where('id', $id)->first();

        if (!$check) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Tidak Ada!!'
            ], 404);
        }

        $data = array(
            'catatan_hm' => $req->input('catatan_hm'),
            'status_hm'  => $req->input('status_hm')
        );

        if($data['catatan_hm'] == null){
            return response()->json([
                "code"    => 422,
                "status"  => "bad request",
                "message" => "Catatan harus diinput!!"
            ], 422);
        }

        if ($data['status_hm'] == 1) {
            $msg = 'berhasil menyetujui data';
        }else if ($data['status_hm'] == 0) {
            $msg = 'berhasil menolak data';
        }

        TransSo::where('id', $id)->update($data);

        try {
            return response()->json([
                'code'    => 200,
                'status'  => 'success',
                'message' => $msg
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }
}
