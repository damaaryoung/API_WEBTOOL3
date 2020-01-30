<?php

namespace App\Http\Controllers\Pengajuan;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use Illuminate\Support\Facades\File;
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
use DB;

class DASController extends BaseController
{
    public function index(Request $req){
        $user_id = $req->auth->user_id;
        $query = TransSO::with('pic', 'cabang', 'asaldata','debt', 'faspin')->get();

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
                $status = 'complete';
            }elseif ($val->status_das == 2) {
                $status = 'not complete';
            }else{
                $status = 'waiting';
            }

            $data[$key] = [
                'id'              => $val->id,
                'nomor_so'        => $val->nomor_so,
                'nama_so'         => $val->nama_so,
                'pic'             => $val->pic['nama'],
                'area'            => $val->area['nama'],
                'cabang'          => $val->cabang['nama'],
                'asal_data'       => $val->asaldata['nama'],
                'nama_marketing'  => $val->nama_marketing,
                'nama_debitur'    => $val->debt['nama_lengkap'],
                'plafon'          => $val->faspin['plafon'],
                'tenor'           => $val->faspin['tenor'],
                'status'          => $status,
                'note'            => $val->catatan_das
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
            $status = 'complete';
        }elseif ($val->status_das == 2) {
            $status = 'not complete';
        }else{
            $status = 'waiting';
        }

        $data = [
            'id'             => $val->id,
            'nomor_so'       => $val->nomor_so,
            'nama_so'        => $val->nama_so,
            'area'   => [
                'id'    => $val->id_area,
                'nama'  => $val->area['nama']
            ],
            'id_cabang'      => $val->pic['id_mk_cabang'],
            'nama_cabang'    => $val->pic['cabang']['nama'],
            'asal_data'      => $val->asaldata['nama'],
            'nama_marketing' => $val->nama_marketing,
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
                'no_ktp_kk'             => $val->debt['no_ktp_kk'],
                'no_kk'                 => $val->debt['no_ktp_kk'],
                'no_npwp'               => $val->debt['no_npwp'],
                'tempat_lahir'          => $val->debt['tempat_lahir'],
                'tgl_lahir'             => Carbon::parse($val->debt['tgl_lahir'])->format('d-m-Y'),
                'agama'                 => $val->debt['agama'],
                'alamat_ktp'            => $val->debt['alamat_ktp'],
                'rt_ktp'                => $val->debt['rt_ktp'],
                'rw_ktp'                => $val->debt['rw_ktp'],
                'provinsi_ktp'          => $val->debt['prov_ktp']['id'],
                'nama_provinsi_ktp'     => $val->debt['prov_ktp']['nama'],
                'kabupaten_ktp'         => $val->debt['kab_ktp']['id'],
                'nama_kabupaten_ktp'    => $val->debt['kab_ktp']['nama'],
                'kecamatan_ktp'         => $val->debt['kec_ktp']['id'],
                'nama_kecamatan_ktp'    => $val->debt['kec_ktp']['nama'],
                'kelurahan_ktp'         => $val->debt['kel_ktp']['id'],
                'nama_kelurahan_ktp'    => $val->debt['kel_ktp']['nama'],
                'kode_pos_ktp'          => $val->debt['kel_ktp']['kode_pos'],
                'alamat_domisili'       => $val->debt['alamat_domisili'],
                'rt_domisili'           => $val->debt['rt_domisili'],
                'rw_domisili'           => $val->debt['rw_domisili'],
                'provinsi_domisili'     => $val->debt['prov_dom']['id'],
                'nama_provinsi_domisili'=> $val->debt['prov_dom']['nama'],
                'kabupaten_domisili'    => $val->debt['kab_dom']['id'],
                'nama_kabupaten_domisili'=> $val->debt['kab_dom']['nama'],
                'kecamatan_domisili'    => $val->debt['kec_dom']['id'],
                'nama_kecamatan_domisili'=> $val->debt['kec_dom']['nama'],
                'kelurahan_domisili'    => $val->debt['kel_dom']['id'],
                'nama_kelurahan_domisili' => $val->debt['kel_dom']['nama'],
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
                'tgl_lahir'        => Carbon::parse($val->pas['tgl_lahir'])->format('d-m-Y'),
                'alamat_ktp'       => $val->pas['alamat_ktp'],
                'no_telp'          => $val->pas['no_telp'],
                'lamp_ktp'         => $val->pas['lamp_ktp'],
                'lamp_buku_nikah'  => $val->pas['lamp_buku_nikah']
            ],
            'data_penjamin' => $penjamin,
            'status'        => $status,
            'note'          => $val->catatan_das,
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

        // $validator = \Validator::make(
        //   [
        //       'file'      => $req->file,
        //       'extension' => strtolower($request->file->getClientOriginalExtension()),
        //   ],
        //   [
        //       'file'          => 'required',
        //       'extension'      => 'required|in:doc,csv,xlsx,xls,docx,ppt,odt,ods,odp',
        //   ]
        // );

        $validator = \Validator::make($req->all(),[
            'status_das'=> 'numeric'
        ],$messages = [
            'numeric'=> 'Status harus berupa digit'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "code"    => 422,
                "status"  => "not valid request",
                "message" => $validator->errors()
            ], 422);
        }

        // $exIdeb = $req->file('lamp_ideb')->getClientOriginalExtension();
        // $exPef  = $req->file('lamp_ideb')->getClientOriginalExtension();

        // if ($exIdeb != 'ideb') {
        //     return response()->json([
        //         "code"    => 422,
        //         "status"  => "not valid request",
        //         "message" => "file ideb harus berupa format ideb"
        //     ], 422);
        // }

        $lamp_dir = 'public/lamp_trans.'.$check->nomor_so;

        if($files = $req->file('lamp_ideb')){
            foreach($files as $key => $file){
                $path = $lamp_dir.'/ideb';
                $exIdeb = $file->getClientOriginalExtension();

                if ($exIdeb != 'ideb') {
                    return response()->json([
                        "code"    => 422,
                        "status"  => "not valid request",
                        "message" => "file ideb harus berupa format ideb"
                    ], 422);
                }

                $name = $file->getClientOriginalName(); //'ktp.'.$file->getClientOriginalExtension();

                $file->move($path,$name);

                $ideb[] = $path.'/'.$name;

                $im_ideb = implode(";", $ideb);
            }
        }else{
            $im_ideb = null;
        }

        if($files = $req->file('lamp_pefindo')){
            foreach($files as $file){
                $path = $lamp_dir.'/pefindo';

                $name = $file->getClientOriginalName(); //'ktp.'.$file->getClientOriginalExtension();
                $file->move($path,$name);

                $pefindo[] = $path.'/'.$name;
                $im_pef = implode(";", $pefindo);
            }
        }else{
            $im_pef = null;
        }

        $data = array(
            'catatan_das' => $req->input('catatan_das'),
            'status_das'  => $req->input('status_das'),
            'lamp_ideb'   => empty($im_ideb) ? null : $im_ideb,
            'lamp_pefindo'=> empty($im_pef) ? null : $im_pef
        );

        if ($data['status_das'] == 1) {
            $msg = 'data lengkap';
        }else if($data['status_das'] == 2){
            $msg = 'data perlu ditinjau';
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
        $user_id = $req->auth->user_id;
        $query = TransSO::with('pic', 'cabang', 'asaldata','debt', 'faspin')
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
                $status = 'complete';
            }elseif ($val->status_das == 2) {
                $status = 'not complete';
            }else{
                $status = 'waiting';
            }

            $data[$key] = [
                'id'              => $val->id,
                'nomor_so'        => $val->nomor_so,
                'nama_so'         => $val->nama_so,
                'pic'             => $val->pic['nama'],
                'area'            => $val->area['nama'],
                'cabang'          => $val->cabang['nama'],
                'asal_data'       => $val->asaldata['nama'],
                'nama_marketing'  => $val->nama_marketing,
                'nama_debitur'    => $val->debt['nama_lengkap'],
                'plafon'          => $val->faspin['plafon'],
                'tenor'           => $val->faspin['tenor'],
                'status'          => $status,
                'note'            => $val->catatan_das
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
