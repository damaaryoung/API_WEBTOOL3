<?php

namespace App\Http\Controllers\Pengajuan\Tunggal;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;

// Form Request
use App\Http\Requests\Pengajuan\A_TanahRequest;

// Models
use App\Models\Pengajuan\AO\AgunanTanah;
use App\Models\Transaksi\TransSO;
use App\Models\Transaksi\TransAO;
use App\Models\User;

use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use App\Http\Requests;
use Carbon\Carbon;
use Image;
use Illuminate\Support\Facades\DB;

class TanahController extends BaseController
{
    public function store($id_trans, A_TanahRequest $req)
    {
        $check_ao = TransAO::where('id_trans_so', $id_trans)->first();

        // $ch_agu = AgunanTanah::where('id', $check_ao->id_agunan_tanah)->first();
        // dd($ch_agu);
        if ($check_ao === null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Transaksi dengan id "' . $id_trans . '" tidak ada'
            ], 404);
        }

        // $path = 'public/' . $check_ao->debt['no_ktp'] . '/agunan_tanah';

        // /** Lampiran Agunan Tanah */
        // if ($file = $req->file('agunan_bag_depan')) {
        //     $name = 'bag_depan';
        //     $check = '';

        //     $agunan_bag_depan = Helper::uploadImg($check, $file, $path, $name);
        // } else {
        //     $agunan_bag_depan = null;
        // }

        // if ($file = $req->file('agunan_bag_jalan')) {
        //     $name  = 'bag_jalan';
        //     $check = '';

        //     $agunan_bag_jalan = Helper::uploadImg($check, $file, $path, $name);
        // } else {
        //     $agunan_bag_jalan = null;
        // }

        // if ($file = $req->file('agunan_bag_ruangtamu')) {
        //     $name = 'bag_ruangtamu';
        //     $check = '';

        //     $agunan_bag_ruangtamu = Helper::uploadImg($check, $file, $path, $name);
        // } else {
        //     $agunan_bag_ruangtamu = null;
        // }


        // if ($file = $req->file('agunan_bag_kamarmandi')) {
        //     $name = 'bag_kamarmandi';
        //     $check = '';

        //     $agunan_bag_kamarmandi = Helper::uploadImg($check, $file, $path, $name);
        // } else {
        //     $agunan_bag_kamarmandi = null;
        // }

        // if ($file = $req->file('agunan_bag_dapur')) {
        //     $name = 'bag_dapur';
        //     $check = '';

        //     $agunan_bag_dapur = Helper::uploadImg($check, $file, $path, $name);
        // } else {
        //     $agunan_bag_dapur = null;
        // }

        // if ($file = $req->file('lamp_sertifikat')) {
        //     $name = 'lamp_sertifikat';
        //     $check = '';

        //     $lamp_sertifikat = Helper::uploadImg($check, $file, $path, $name);
        // } else {
        //     $lamp_sertifikat = null;
        // }

        // // Tambahan Agunan Tanah
        // if ($file = $req->file('lamp_imb')) {
        //     $name = 'lamp_imb';
        //     $check = '';

        //     $lamp_imb = Helper::uploadImg($check, $file, $path, $name);
        // } else {
        //     $lamp_imb = null;
        // }

        // if ($file = $req->file('lamp_pbb')) {
        //     $name = 'lamp_pbb';
        //     $check = '';

        //     $lamp_pbb = Helper::uploadImg($check, $file, $path, $name);
        // } else {
        //     $lamp_pbb = null;
        // }
        /** */

        $data = array(
            'tipe_lokasi'             => $req->input('tipe_lokasi_agunan'),

            'alamat'                  => $req->input('alamat_agunan'),

            'id_provinsi'             => $req->input('id_prov_agunan'),

            'id_kabupaten'            => $req->input('id_kab_agunan'),

            'id_kecamatan'            => $req->input('id_kec_agunan'),

            'id_kelurahan'            => $req->input('id_kel_agunan'),

            'rt'                      => $req->input('rt_agunan'),

            'rw'                      => $req->input('rw_agunan'),

            'luas_tanah'              => $req->input('luas_tanah'),

            'luas_bangunan'           => $req->input('luas_bangunan'),

            'nama_pemilik_sertifikat' => $req->input('nama_pemilik_sertifikat'),

            'jenis_sertifikat'        => $req->input('jenis_sertifikat'),

            'no_sertifikat'           => $req->input('no_sertifikat'),

            'tgl_ukur_sertifikat'     => $req->input('tgl_ukur_sertifikat'),

            'tgl_berlaku_shgb'        => empty($req->input('tgl_berlaku_shgb')) ? null : Carbon::parse($req->input('tgl_berlaku_shgb'))->format('Y-m-d'),

            'no_imb'                  => $req->input('no_imb'),
            'njop'                    => $req->input('njop'),
            'nop'                     => $req->input('nop')

        );
        $arr = array();
        foreach ($data as $key => $val) {
            $arr[$key] = $val;
        }
        //  dd($val);
        DB::connection('web')->beginTransaction();

        try {

            if ($val === null) {
                return response()->json([
                    'code'  => 401,
                    'message'   => 'bad request',
                    'data'  => 'data agunan harus di input'
                ]);
            }

            $query = AgunanTanah::create($data);

            TransAO::where('id_trans_so', $id_trans)->update(['id_agunan_tanah' => $query->id]);

            DB::connection('web')->commit();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message' => 'Update Agunan Tanah Berhasil',
                'data'   => $query
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

    public function show($id)
    {
        $check = AgunanTanah::with('prov', 'kab', 'kec', 'kel')
            ->where('id', $id)->first();

        if ($check == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Agunan Tanah Kosong'
            ], 404);
        }

        $data = array(
            'id'          => $check->id == null ? null : (int) $check->id,
            'tipe_lokasi' => $check->tipe_lokasi,
            'alamat' => [
                'alamat_singkat' => $check->alamat,
                'rt' => $check->rt == null ? null : (int) $check->rt,
                'rw' => $check->rw == null ? null : (int) $check->rw,
                'kelurahan' => [
                    'id'    => $check->id_kelurahan == null ? null : (int) $check->id_kelurahan,
                    'nama'  => $check->kel['nama']
                ],
                'kecamatan' => [
                    'id'    => $check->id_kecamatan == null ? null : (int) $check->id_kecamatan,
                    'nama'  => $check->kec['nama']
                ],
                'kabupaten' => [
                    'id'    => $check->id_kabupaten == null ? null : (int) $check->id_kabupaten,
                    'nama'  => $check->kab['nama'],
                ],
                'provinsi' => [
                    'id'    => $check->id_provinsi == null ? null : (int) $check->id_provinsi,
                    'nama'  => $check->prov['nama']
                ],
                'kode_pos' => $check->kel['kode_pos'] == null ? null : (int) $check->kel['kode_pos']
            ],
            'luas_tanah'    => (int) $check->luas_tanah,
            'luas_bangunan' => (int) $check->luas_bangunan,
            'nama_pemilik_sertifikat' => $check->nama_pemilik_sertifikat,
            'jenis_sertifikat'        => $check->jenis_sertifikat,
            'no_sertifikat'           => $check->no_sertifikat,
            'tgl_ukur_sertifikat'     => $check->tgl_ukur_sertifikat,
            'tgl_berlaku_shgb'        => $check->tgl_berlaku_shgb,
            'no_imb'                  => $check->no_imb,
            'njop'                    => $check->njop,
            'nop'                     => $check->nop,
            'lampiran' => [
                'agunan_bag_depan'      => $check->agunan_bag_depan,
                'agunan_bag_jalan'      => $check->agunan_bag_jalan,
                'agunan_bag_ruangtamu'  => $check->agunan_bag_ruangtamu,
                'agunan_bag_kamarmandi' => $check->agunan_bag_kamarmandi,
                'agunan_bag_dapur'      => $check->agunan_bag_dapur,
                'lamp_sertifikat' => $check->lamp_sertifikat,
                'lamp_imb' => $check->lamp_imb,
                'lamp_pbb' => $check->lamp_pbb
            ]

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

    public function update($id, A_TanahRequest $req)
    {
        $check_tan = AgunanTanah::where('id', $id)->first();

        if ($check_tan == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data AgunanTanah Kosong'
            ], 404);
        }

        $ao = TransAO::where('id_agunan_tanah', $id)->first();

        if ($ao == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Transaksi AO Kosong'
            ], 404);
        }

        $so = TransSO::where('id_trans_ao', $ao->id)->first();

        if ($so == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Transaksi SO Kosong'
            ], 404);
        }

        /** Check Lampiran */
        $check_agunan_bag_depan     = $check_tan->agunan_bag_depan;
        $check_agunan_bag_jalan     = $check_tan->agunan_bag_jalan;
        $check_agunan_bag_ruangtamu = $check_tan->agunan_bag_ruangtamu;
        $check_agunan_bag_kamarmandi = $check_tan->agunan_bag_kamarmandi;
        $check_agunan_bag_dapur     = $check_tan->agunan_bag_dapur;
        $check_lamp_sertifikat      = $check_tan->lamp_sertifikat;
        $check_lamp_imb             = $check_tan->lamp_imb;
        $check_lamp_pbb             = $check_tan->lamp_pbb;
        /** */

        $path = 'public/' . $so->debt['no_ktp'] . '/agunan_tanah';

        if ($file = $req->file('agunan_bag_depan')) {
            $name = 'bag_depan.';
            $check = $check_agunan_bag_depan;

            $agunan_bag_depan = Helper::uploadImg($check, $file, $path, $name);
        } else {
            $agunan_bag_depan = $check_agunan_bag_depan;
        }

        if ($file = $req->file('agunan_bag_jalan')) {
            $check = $check_agunan_bag_jalan;
            $name  = 'bag_jalan.';

            $agunan_bag_jalan = Helper::uploadImg($check, $file, $path, $name);
        } else {
            $agunan_bag_jalan = $check_agunan_bag_jalan;
        }

        if ($file = $req->file('agunan_bag_ruangtamu')) {
            $check = $check_agunan_bag_ruangtamu;
            $name = 'bag_ruangtamu.';

            $agunan_bag_ruangtamu = Helper::uploadImg($check, $file, $path, $name);
        } else {
            $agunan_bag_ruangtamu = $check_agunan_bag_ruangtamu;
        }


        if ($file = $req->file('agunan_bag_kamarmandi')) {
            $check = $check_agunan_bag_kamarmandi;
            $name = 'bag_kamarmandi.';

            $agunan_bag_kamarmandi = Helper::uploadImg($check, $file, $path, $name);
        } else {
            $agunan_bag_kamarmandi = $check_agunan_bag_kamarmandi;
        }

        if ($file = $req->file('agunan_bag_dapur')) {
            $check = $check_agunan_bag_dapur;
            $name = 'bag_dapur.';

            $agunan_bag_dapur = Helper::uploadImg($check, $file, $path, $name);
        } else {
            $agunan_bag_dapur = $check_agunan_bag_dapur;
        }

        if ($file = $req->file('lamp_sertifikat')) {
            $check = $check_lamp_sertifikat;
            $name = 'lamp_sertifikat.';

            $lamp_sertifikat = Helper::uploadImg($check, $file, $path, $name);
        } else {
            $lamp_sertifikat = $check_lamp_sertifikat;
        }

        // Tambahan Agunan Tanah
        if ($file = $req->file('lamp_imb')) {
            $check = $check_lamp_imb;
            $name = 'lamp_imb.';

            $lamp_imb = Helper::uploadImg($check, $file, $path, $name);
        } else {
            $lamp_imb = $check_lamp_imb;
        }

        if ($file = $req->file('lamp_pbb')) {
            $check = $check_lamp_pbb;
            $name = 'lamp_pbb.';

            $lamp_pbb = Helper::uploadImg($check, $file, $path, $name);
        } else {
            $lamp_pbb = $check_lamp_pbb;
        }

        // AgunanTanah
        $dataAgunanTanah = array(
            'tipe_lokasi'             => empty($req->input('tipe_lokasi_agunan'))
                ? $check_tan->tipe_lokasi : strtoupper($req->input('tipe_lokasi_agunan')),

            'alamat'                  => empty($req->input('alamat_agunan'))
                ? $check_tan->alamat : $req->input('alamat_agunan'),

            'id_provinsi'             => empty($req->input('id_prov_agunan'))
                ? $check_tan->id_provinsi : $req->input('id_prov_agunan'),

            'id_kabupaten'            => empty($req->input('id_kab_agunan'))
                ? $check_tan->id_kabupaten : $req->input('id_kab_agunan'),

            'id_kecamatan'            => empty($req->input('id_kec_agunan'))
                ? $check_tan->id_kecamatan : $req->input('id_kec_agunan'),

            'id_kelurahan'            => empty($req->input('id_kel_agunan'))
                ? $check_tan->id_kelurahan : $req->input('id_kel_agunan'),

            'rt'                      => empty($req->input('rt_agunan'))
                ? $check_tan->rt : $req->input('rt_agunan'),

            'rw'                      => empty($req->input('rw_agunan'))
                ? $check_tan->rw : $req->input('rw_agunan'),

            'luas_tanah'              => empty($req->input('luas_tanah'))
                ? $check_tan->luas_tanah : $req->input('luas_tanah'),

            'luas_bangunan'           => empty($req->input('luas_bangunan'))
                ? $check_tan->luas_bangunan : $req->input('luas_bangunan'),

            'nama_pemilik_sertifikat' => empty($req->input('nama_pemilik_sertifikat'))
                ? $check_tan->nama_pemilik_sertifikat : $req->input('nama_pemilik_sertifikat'),

            'jenis_sertifikat'        => empty($req->input('jenis_sertifikat'))
                ? $check_tan->jenis_sertifikat : strtoupper($req->input('jenis_sertifikat')),

            'no_sertifikat'           => empty($req->input('no_sertifikat'))
                ? $check_tan->no_sertifikat : $req->input('no_sertifikat'),

            'tgl_ukur_sertifikat'     => empty($req->input('tgl_ukur_sertifikat'))
                ? $check_tan->tgl_ukur_sertifikat : $req->input('tgl_ukur_sertifikat'),

            'tgl_berlaku_shgb'        => empty($req->input('tgl_berlaku_shgb'))
                ? $check_tan->tgl_berlaku_shgb : Carbon::parse($req->input('tgl_berlaku_shgb'))->format('Y-m-d'),

            'no_imb'                  => empty($req->input('no_imb'))   ? $check_tan->no_imb : $req->input('no_imb'),
            'njop'                    => empty($req->input('njop'))     ? $check_tan->njop : $req->input('njop'),
            'nop'                     => empty($req->input('nop'))      ? $check_tan->nop : $req->input('nop'),
            'agunan_bag_depan'        => empty($agunan_bag_depan)       ? $check_tan->agunan_bag_depan : $agunan_bag_depan,
            'agunan_bag_jalan'        => empty($agunan_bag_jalan)       ? $check_tan->agunan_bag_jalan : $agunan_bag_jalan,
            'agunan_bag_ruangtamu'    => empty($agunan_bag_ruangtamu)   ? $check_tan->agunan_bag_ruangtamu : $agunan_bag_ruangtamu,
            'agunan_bag_kamarmandi'   => empty($agunan_bag_kamarmandi)  ? $check_tan->agunan_bag_kamarmandi : $agunan_bag_kamarmandi,
            'agunan_bag_dapur'        => empty($agunan_bag_dapur)       ? $check_tan->agunan_bag_dapur : $agunan_bag_dapur,
            'lamp_imb'                => empty($lamp_imb)               ? $check_tan->lamp_imb : $lamp_imb,
            'lamp_pbb'                => empty($lamp_pbb)               ? $check_tan->lamp_pbb : $lamp_pbb,
            'lamp_sertifikat'         => empty($lamp_sertifikat)        ? $check_tan->lamp_sertifikat : $lamp_sertifikat,
        );

        DB::connection('web')->beginTransaction();

        try {

            AgunanTanah::where('id', $id)->update($dataAgunanTanah);

            DB::connection('web')->commit();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message' => 'Update Agunan Tanah Berhasil',
                'data'   => $dataAgunanTanah
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
