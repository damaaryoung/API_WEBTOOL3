<?php

namespace App\Http\Controllers\Pengajuan;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use App\Http\Requests\Transaksi\BlankRequest;
use App\Models\Pengajuan\SO\Penjamin;
use App\Models\Transaksi\TransSO;
use App\Models\Wilayah\Kabupaten;
use App\Models\Wilayah\Kecamatan;
use App\Models\Wilayah\Kelurahan;
use App\Models\Wilayah\Provinsi;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\User;
use Carbon\Carbon;

class HMController extends BaseController
{
    public function index(Request $req){
        $query = TransSO::with('asaldata','debt', 'pic')->get();

        if ($query == '[]') {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        $data = array();
        foreach ($query as $key => $val) {

            if ($val->status_das == 1) {
                $status_das = 'complete';
            }elseif($val->status_das == 2){
                $status_das = 'not complete';
            }else{
                $status_das = 'waiting';
            }

            if ($val->status_hm == 1) {
                $status_hm = 'complete';
            }elseif ($val->status_hm == 2) {
                $status_hm = 'not complete';
            }else{
                $status_hm = 'waiting';
            }

            $data[$key] = [
                'id'             => $val->id,
                'nomor_so'       => $val->nomor_so,
                'nama_so'        => $val->nama_so,
                'pic'            => $val->pic['nama'],
                'cabang'         => $val->cabang['nama'],
                'asal_data'      => $val->asaldata['nama'],
                'nama_marketing' => $val->nama_marketing,
                'nama_debitur'   => $val->debt['nama_lengkap'],
                'plafon'         => $val->faspin['plafon'],
                'tenor'          => $val->faspin['tenor'],
                'das_status'     => $status_das,
                'das_note'       => $val->catatan_das,
                'hm_status'      => $status_hm,
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

    public function show($id, Request $req){
        $val = TransSO::with('asaldata','debt', 'pic')->where('id', $id)->first();
        if (!$val) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        $id_penj = explode (",",$val->id_penjamin);

        $pen = Penjamin::whereIn('id', $id_penj)->get();

        if ($pen != '[]') {
            foreach ($pen as $key => $value) {
                $penjamin[$key] = [
                    "id"               => $value->id,
                    "nama_ktp"         => $value->nama_ktp,
                    "nama_ibu_kandung" => $value->nama_ibu_kandung,
                    "no_ktp"           => $value->no_ktp,
                    "no_npwp"          => $value->no_npwp,
                    "tempat_lahir"     => $value->tempat_lahir,
                    "tgl_lahir"        => Carbon::parse($value->tgl_lahir)->format('d-m-Y'),
                    "jenis_kelamin"    => $value->jenis_kelamin,
                    "alamat_ktp"       => $value->alamat_ktp,
                    "no_telp"          => $value->no_telp,
                    "hubungan_debitur" => $value->hubungan_debitur,
                    "lampiran" => [
                        "lamp_ktp"          => $value->lamp_ktp,
                        "lamp_ktp_pasangan" => $value->lamp_ktp_pasangan,
                        "lamp_kk"           => $value->lamp_kk,
                        "lamp_buku_nikah"   => $value->lamp_buku_nikah
                    ]
                ];
            }
        }else{
            $penjamin = null;
        }


        if ($val->status_das == 1) {
            $status_das = 'complete';
        }elseif($val->status_das == 2){
            $status_das = 'not complete';
        }else{
            $status_das = 'waiting';
        }

        if ($val->status_hm == 1) {
            $status_hm = 'complete';
        }elseif ($val->status_hm == 2) {
            $status_hm = 'not complete';
        }else{
            $status_hm = 'waiting';
        }

        $data = [
            'id'             => $val->id,
            'nomor_so'       => $val->nomor_so,
            'nama_so'        => $val->nama_so,
            'id_cabang'      => $val->pic['id_mk_cabang'],
            'nama_cabang'    => $val->pic['cabang']['nama'],
            'asal_data'      => $val->asaldata['nama'],
            'nama_marketing' => $val->nama_marketing,
            'plafon'         => (int) $val->faspin['plafon'],
            'tenor'          => (int) $val->faspin['tenor'],
            'fasilitas_pinjaman'  => [
                'id'              => $val->id_fasilitas_pinjaman,
                'jenis_pinjaman'  => $val->faspin['jenis_pinjaman'],
                'tujuan_pinjaman' => $val->faspin['tujuan_pinjaman']
            ],
            'data_debitur' => [
                'id'                    => $val->id_calon_debt,
                'nama_lengkap'          => $val->debt['nama_lengkap'],
                'gelar_keagamaan'       => $val->debt['gelar_keagamaan'],
                'gelar_pendidikan'      => $val->debt['gelar_pendidikan'],
                'jenis_kelamin'         => $val->debt['jenis_kelamin'],
                'status_nikah'          => $val->debt['status_nikah'],
                'ibu_kandung'           => $val->debt['ibu_kandung'],
                'no_ktp'                => $val->debt['no_ktp'],
                'no_ktp_kk'             => $val->debt['no_ktp_kk'],
                'no_kk'                 => $val->debt['no_kk'],
                'no_npwp'               => $val->debt['no_npwp'],
                'tempat_lahir'          => $val->debt['tempat_lahir'],
                'tgl_lahir'             => Carbon::parse($val->debt['tgl_lahir'])->format('d-m-Y'),
                'agama'                 => $val->debt['agama'],
                'alamat_ktp'            => $val->debt['alamat_ktp'],
                'rt_ktp'                => $val->debt['rt_ktp'],
                'rw_ktp'                => $val->debt['rw_ktp'],
                'id_provinsi_ktp'       => $val->debt['id_prov_ktp'],
                'provinsi_ktp'          => $val->debt['prov_ktp']['nama'],
                'id_kabupaten_ktp'      => $val->debt['id_kab_ktp'],
                'kabupaten_ktp'         => $val->debt['kab_ktp']['nama'],
                'id_kecamatan_ktp'      => $val->debt['id_kec_ktp'],
                'kecamatan_ktp'         => $val->debt['kec_ktp']['nama'],
                'id_kelurahan_ktp'      => $val->debt['id_kel_ktp'],
                'kelurahan_ktp'         => $val->debt['kel_ktp']['nama'],
                'kode_pos_ktp'          => $val->debt['kel_ktp']['kode_pos'],
                'alamat_domisili'       => $val->debt['alamat_domisili'],
                'rt_domisili'           => $val->debt['rt_domisili'],
                'rw_domisili'           => $val->debt['rw_domisili'],
                'provinsi_domisili'     => $val->debt['prov_dom']['nama'],
                'kabupaten_domisili'    => $val->debt['kab_dom']['nama'],
                'kecamatan_domisili'    => $val->debt['kec_dom']['nama'],
                'kelurahan_domisili'    => $val->debt['kel_dom']['nama'],
                'kode_pos_domisili'     => $val->debt['kel_dom']['kode_pos'],
                'pendidikan_terakhir'   => $val->debt['pendidikan_terakhir'],
                'jumlah_tanggungan'     => $val->debt['jumlah_tanggungan'],
                'no_telp'               => $val->debt['no_telp'],
                'no_hp'                 => $val->debt['no_hp'],
                'alamat_surat'          => $val->debt['alamat_surat'],
                'lamp_ktp'              => $val->debt['lamp_ktp'],
                'lamp_kk'               => $val->debt['lamp_kk'],
                'lamp_sertifikat'       => $val->debt['lamp_sertifikat'],
                'lamp_sttp_pbb'         => $val->debt['lamp_sttp_pbb'],
                'lamp_imb'              => $val->debt['lamp_imb'],
                'lamp_buku_tabungan'    => $val->debt['lamp_buku_tabungan']
            ],
            'data_pasangan' => [
                'nama_lengkap'     => $val->pas['nama_lengkap'],
                'nama_ibu_kandung' => $val->pas['nama_ibu_kandung'],
                'jenis_kelamin'    => $val->pas['jenis_kelamin'],
                'no_ktp'           => $val->pas['no_ktp'],
                'no_ktp_kk'        => $val->pas['no_ktp_kk'],
                'no_npwp'          => $val->pas['no_npwp'],
                'tempat_lahir'     => $val->pas['tempat_lahir'],
                'tgl_lahir'        => Carbon::parse($val->pas['tgl_lahir'])->format('d-m-Y'),
                'alamat_ktp'       => $val->pas['alamat_ktp'],
                'no_telp'          => $val->pas['no_telp'],
                'lamp_ktp'         => $val->pas['lamp_ktp'],
                'lamp_buku_nikah'  => $val->pas['lamp_buku_nikah']
            ],
            'data_penjamin' => $penjamin,
            'das_status'    => $status_das,
            'das_note'      => $val->catatan_das,
            'hm_status'     => $status_hm,
            'hm_note'       => $val->catatan_hm,
            'lampiran'  => [
                'ideb'    => explode(";", $val->lamp_ideb),
                'pefindo' => explode(";", $val->lamp_pefindo)
            ]
        ];

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

    public function update($id, Request $req){
        $check = TransSO::where('id', $id)->first();

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
                "message" => "catatan harus diinput!!"
            ], 422);
        }

        if($data['status_hm'] == null){
            return response()->json([
                "code"    => 422,
                "status"  => "bad request",
                "message" => "status harus diinput!!"
            ], 422);
        }

        if (!preg_match("/^([1-2]{1})$/", $req->input('status_hm'))) {
            response()->json([
                "code"    => 422,
                "status"  => "not valid request",
                "message" => "status_das harus berupa angka 1 digit, range: 1-2"
            ], 422);
        }

        if ($data['status_hm'] == 1) {
            $msg = 'berhasil menyetujui data';
        }else if ($data['status_hm'] == 2) {
            $msg = 'berhasil menolak data';
        }else{
            $msg = 'waiting proccess';
        }

        TransSO::where('id', $id)->update($data);

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

    public function search($search, Request $req){
        $query = TransSO::with('asaldata','debt', 'pic')
                ->where('nomor_so', 'like', '%'.$search.'%')
                ->get();

        if ($query == '[]') {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        $data = array();
        foreach ($query as $key => $val) {

            if ($val->status_das == 1) {
                $status_das = 'complete';
            }elseif($val->status_das == 2){
                $status_das = 'not complete';
            }else{
                $status_das = 'waiting';
            }

            if ($val->status_hm == 1) {
                $status_hm = 'complete';
            }elseif ($val->status_hm == 2) {
                $status_hm = 'not complete';
            }else{
                $status_hm = 'waiting';
            }

            $data[$key] = [
                'id'             => $val->id,
                'nomor_so'       => $val->nomor_so,
                'nama_so'        => $val->nama_so,
                'pic'            => $val->pic['nama'],
                'cabang'         => $val->cabang['nama'],
                'asal_data'      => $val->asaldata['nama'],
                'nama_marketing' => $val->nama_marketing,
                'nama_debitur'   => $val->debt['nama_lengkap'],
                'plafon'         => $val->faspin['plafon'],
                'tenor'          => $val->faspin['tenor'],
                'das_status'     => $status_das,
                'das_note'       => $val->catatan_das,
                'hm_status'      => $status_hm,
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
}
