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
use DB;

class TanahController extends BaseController
{

    public function show($id){
        $check = AgunanTanah::with('prov', 'kab','kec','kel')
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
        } catch (Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function update($id, A_TanahRequest $req){
        $check = AgunanTanah::where('id', $id)->first();

        if ($check == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data AgunanTanah Kosong'
            ], 404);
        }

        $ao = TransAO::where('id_agunan_tanah', 'like', '%'.$id.'%')->first();

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

        if (!empty($check->agunan_bag_depan)) {
            $lamp_path = $check->agunan_bag_depan;
        }elseif (!empty($check->agunan_bag_jalan)) {
            $lamp_path = $check->agunan_bag_jalan;
        }elseif (!empty($check->agunan_bag_ruangtamu)) {
            $lamp_path = $check->agunan_bag_ruangtamu;
        }elseif (!empty($check->agunan_bag_kamarmandi)) {
            $lamp_path = $check->agunan_bag_kamarmandi;
        }elseif (!empty($check->agunan_bag_dapur)) {
            $lamp_path = $check->agunan_bag_dapur;
        }else{
            $lamp_path = 'public/lamp_trans.2-AO-1-2020-13/agunan_tanah/agunan_bag_depan.1.png';
        }

        $ktp_debt = $so->debt['no_ktp'];

        $arrPath = explode("/", $lamp_path, 4);

        $path = $arrPath[0].'/'.$ktp_debt.'/'.$arrPath[2];

        if($file = $req->file('agunan_bag_depan')){

            $name = 'bag_depan.' . $file->getClientOriginalName();

            $img = Image::make($file)->resize(320, 240);
            
            if(!File::isDirectory($path)){
                File::makeDirectory($path, 0777, true, true);
            }
            
            if(!empty($check->agunan_bag_depan))
            {
                File::delete($check->agunan_bag_depan);
            }
                
            $img->save($path.'/'.$name);

            $agunan_bag_depan = $path.'/'.$name;
        }else{
            $agunan_bag_depan = $check->agunan_bag_depan;
        }

        if($file = $req->file('agunan_bag_jalan')){

            $name = 'bag_jalan.' . $file->getClientOriginalName();

            $img = Image::make($file)->resize(320, 240);
            
            if(!File::isDirectory($path)){
                File::makeDirectory($path, 0777, true, true);
            }
            
            if(!empty($check->agunan_bag_jalan))
            {
                File::delete($check->agunan_bag_jalan);
            }
                
            $img->save($path.'/'.$name);

            $agunan_bag_jalan = $path.'/'.$name;
        }else{
            $agunan_bag_jalan = $check->agunan_bag_jalan;
        }

        if($file = $req->file('agunan_bag_ruangtamu')){

            $name = 'bag_ruangtamu.' . $file->getClientOriginalName();

            $img = Image::make($file)->resize(320, 240);
            
            if(!File::isDirectory($path)){
                File::makeDirectory($path, 0777, true, true);
            }
            
            if(!empty($check->agunan_bag_ruangtamu))
            {
                File::delete($check->agunan_bag_ruangtamu);
            }
                
            $img->save($path.'/'.$name);

            $agunan_bag_ruangtamu = $path.'/'.$name;
        }else{
            $agunan_bag_ruangtamu = $check->agunan_bag_ruangtamu;
        }


        if($file = $req->file('agunan_bag_kamarmandi')){

            $name = 'bag_kamarmandi.' . $file->getClientOriginalName();

            $img = Image::make($file)->resize(320, 240);
            
            if(!File::isDirectory($path)){
                File::makeDirectory($path, 0777, true, true);
            }
            
            if(!empty($check->agunan_bag_kamarmandi))
            {
                File::delete($check->agunan_bag_kamarmandi);
            }
                
            $img->save($path.'/'.$name);

            $agunan_bag_kamarmandi = $path.'/'.$name;
        }else{
            $agunan_bag_kamarmandi = $check->agunan_bag_kamarmandi;
        }

        if($file = $req->file('agunan_bag_dapur')){

            $name = 'bag_dapur.' . $file->getClientOriginalName();

            $img = Image::make($file)->resize(320, 240);
            
            if(!File::isDirectory($path)){
                File::makeDirectory($path, 0777, true, true);
            }
            
            if(!empty($check->agunan_bag_dapur))
            {
                File::delete($check->agunan_bag_dapur);
            }
                
            $img->save($path.'/'.$name);

            $agunan_bag_dapur = $path.'/'.$name;
        }else{
            $agunan_bag_dapur = $check->agunan_bag_dapur;
        }

        // Tambahan Agunan Tanah
        if ($file = $req->file('lamp_imb')) {

            $name = 'lamp_imb.' . $file->getClientOriginalName();

            $img = Image::make($file)->resize(320, 240);
            
            if(!File::isDirectory($path)){
                File::makeDirectory($path, 0777, true, true);
            }
            
            if(!empty($check->lamp_imb))
            {
                File::delete($check->lamp_imb);
            }
                
            $img->save($path.'/'.$name);

            $lamp_imb = $path.'/'.$name;
        }else{
            $lamp_imb = $check->lamp_imb;
        }

        if ($file = $req->file('lamp_pbb')) {

            $name = 'lamp_pbb.' . $file->getClientOriginalName();

            $img = Image::make($file)->resize(320, 240);
            
            if(!File::isDirectory($path)){
                File::makeDirectory($path, 0777, true, true);
            }
            
            if(!empty($check->lamp_pbb))
            {
                File::delete($check->lamp_pbb);
            }
            
            $img->save($path.'/'.$name);

            $lamp_pbb = $path.'/'.$name;
        }else {
            $lamp_pbb = $check->lamp_pbb;
        }

        if ($file = $req->file('lamp_sertifikat')) {

            $name = 'lamp_sertifikat.' . $file->getClientOriginalName();

            $img = Image::make($file)->resize(320, 240);
            
            if(!File::isDirectory($path)){
                File::makeDirectory($path, 0777, true, true);
            }
            
            if(!empty($check->lamp_sertifikat))
            {
                File::delete($check->lamp_sertifikat);
            }
                
            $img->save($path.'/'.$name);

            $lamp_sertifikat = $path.'/'.$name;
        }else {
            $lamp_sertifikat = $check->lamp_sertifikat;
        }

        // AgunanTanah
        $dataAgunanTanah = array(
            'tipe_lokasi'             => empty($req->input('tipe_lokasi_agunan')) ? $check->tipe_lokasi : strtoupper($req->input('tipe_lokasi_agunan')),
            'alamat'                  => empty($req->input('alamat_agunan')) ? $check->alamat : $req->input('alamat_agunan'),
            'id_provinsi'             => empty($req->input('id_prov_agunan')) ? $check->id_provinsi : $req->input('id_prov_agunan'),
            'id_kabupaten'            => empty($req->input('id_kab_agunan')) ? $check->id_kabupaten : $req->input('id_kab_agunan'),
            'id_kecamatan'            => empty($req->input('id_kec_agunan')) ? $check->id_kecamatan : $req->input('id_kec_agunan'),
            'id_kelurahan'            => empty($req->input('id_kel_agunan')) ? $check->id_kelurahan : $req->input('id_kel_agunan'),
            'rt'                      => empty($req->input('rt_agunan')) ? $check->rt : $req->input('rt_agunan'),
            'rw'                      => empty($req->input('rw_agunan')) ? $check->rw : $req->input('rw_agunan'),
            'luas_tanah'              => empty($req->input('luas_tanah')) ? $check->luas_tanah : $req->input('luas_tanah'),
            'luas_bangunan'           => empty($req->input('luas_bangunan')) ? $check->luas_bangunan : $req->input('luas_bangunan'),
            'nama_pemilik_sertifikat' => empty($req->input('nama_pemilik_sertifikat')) ? $check->nama_pemilik_sertifikat : $req->input('nama_pemilik_sertifikat'),
            'jenis_sertifikat'        => empty($req->input('jenis_sertifikat')) ? $check->jenis_sertifikat : strtoupper($req->input('jenis_sertifikat')),
            'no_sertifikat'           => empty($req->input('no_sertifikat')) ? $check->no_sertifikat : $req->input('no_sertifikat'),
            'tgl_ukur_sertifikat'     => empty($req->input('tgl_ukur_sertifikat')) ? $check->tgl_ukur_sertifikat : $req->input('tgl_ukur_sertifikat'),
            'tgl_berlaku_shgb'        => empty($req->input('tgl_berlaku_shgb')) ? $check->tgl_berlaku_shgb : Carbon::parse($req->input('tgl_berlaku_shgb'))->format('Y-m-d'),
            'no_imb'                  => empty($req->input('no_imb')) ? $check->no_imb : $req->input('no_imb'),
            'njop'                    => empty($req->input('njop')) ? $check->njop : $req->input('njop'),
            'nop'                     => empty($req->input('nop')) ? $check->nop : $req->input('nop'),
            'agunan_bag_depan'        => empty($agunan_bag_depan) ? $check->agunan_bag_depan : $agunan_bag_depan,
            'agunan_bag_jalan'        => empty($agunan_bag_jalan) ? $check->agunan_bag_jalan : $agunan_bag_jalan,
            'agunan_bag_ruangtamu'    => empty($agunan_bag_ruangtamu) ? $check->agunan_bag_ruangtamu : $agunan_bag_ruangtamu,
            'agunan_bag_kamarmandi'   => empty($agunan_bag_kamarmandi) ? $check->agunan_bag_kamarmandi : $agunan_bag_kamarmandi,
            'agunan_bag_dapur'        => empty($agunan_bag_dapur) ? $check->agunan_bag_dapur : $agunan_bag_dapur,
            'lamp_imb'                => empty($lamp_imb) ? $check->lamp_imb : $lamp_imb,
            'lamp_pbb'                => empty($lamp_pbb) ? $check->lamp_pbb : $lamp_pbb,
            'lamp_sertifikat'         => empty($lamp_sertifikat) ? $check->lamp_sertifikat : $lamp_sertifikat,
        );

        DB::connection('web')->beginTransaction();

        try {

            AgunanTanah::where('id', $id)->update($dataAgunanTanah);

            DB::connection('web')->commit();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message'=> 'Update Agunan Tanah Berhasil',
                'data'   => $dataAgunanTanah
            ], 200);
        } catch (Exception $e) {

            $err = DB::connection('web')->rollback();

            return response()->json([
                'code'    => 501,
                'status'  => 'error',
                'message' => $err
            ], 501);
        }
    }
}
