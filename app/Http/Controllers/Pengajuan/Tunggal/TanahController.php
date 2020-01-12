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
            'id'          => $check->id,
            'tipe_lokasi' => $check->tipe_lokasi,
            'alamat' => [
                'alamat_singkat' => $check->alamat,
                'rt' => $check->rt,
                'rw' => $check->rw,
                'kelurahan' => [
                    'id'    => $check->id_kelurahan,
                    'nama'  => $check->kel['nama']
                ],
                'kecamatan' => [
                    'id'    => $check->id_kecamatan,
                    'nama'  => $check->kec['nama']
                ],
                'kabupaten' => [
                    'id'    => $check->id_kabupaten,
                    'nama'  => $check->kab['nama'],
                ],
                'provinsi' => [
                    'id'    => $check->id_provinsi,
                    'nama'  => $check->prov['nama']
                ],
                'kode_pos' => $check->kel['kode_pos']
            ],
            'luas_tanah'    => $check->luas_tanah,
            'luas_bangunan' => $check->luas_bangunan,
            'nama_pemilik_sertifikat' => $check->nama_pemilik_sertifikat,
            'jenis_sertifikat'        => $check->jenis_sertifikat,
            'no_sertifikat'           => $check->no_sertifikat,
            'tgl_ukur_sertifikat'     => $check->tgl_ukur_sertifikat,
            'tgl_berlaku_shgb'        => $check->tgl_berlaku_shgb,
            'no_imb' => $check->no_imb,
            'njop'   => $check->njop,
            'nop'    => $check->nop,
            'lampiran' => [
                'lamp_agunan_depan' => $check->lamp_agunan_depan,
                'lamp_agunan_kanan' => $check->lamp_agunan_kanan,
                'lamp_agunan_kiri' => $check->lamp_agunan_kiri,
                'lamp_agunan_belakang' => $check->lamp_agunan_belakang,
                'lamp_agunan_dalam' => $check->lamp_agunan_dalam,
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

        if (!empty($check->lamp_agunan_depan)) {
            $lamp_path = $check->lamp_agunan_depan;
        }elseif (!empty($check->lamp_agunan_kanan)) {
            $lamp_path = $check->lamp_agunan_kanan;
        }elseif (!empty($check->lamp_agunan_kiri)) {
            $lamp_path = $check->lamp_agunan_kiri;
        }elseif (!empty($check->lamp_agunan_belakang)) {
            $lamp_path = $check->lamp_agunan_belakang;
        }elseif (!empty($check->lamp_agunan_dalam)) {
            $lamp_path = $check->lamp_agunan_dalam;
        }else{
            $lamp_path = 'public/lamp_trans.2-AO-1-2020-13/agunan_tanah/agunan_depan1.png';
        }

        $ktp_debt = $so->debt['no_ktp'];

        $arrPath = explode("/", $lamp_path, 4);

        $path = $arrPath[0].'/'.$ktp_debt.'/'.$arrPath[2];

        $no = substr($arrPath[3], 12, 1);

        if($file = $req->file('lamp_agunan_depan')){

            $name = 'agunan_depan'.$no.'.'.$file->getClientOriginalExtension();

            if(!empty($check->lamp_agunan_depan))
            {
                File::delete($check->lamp_agunan_depan);
            }

            $file->move($path,$name);

            $agunanDepan = $path.'/'.$name;
        }else{
            $agunanDepan = $check->lamp_agunan_depan;
        }

        if($file = $req->file('lamp_agunan_kanan')){

            $name = 'agunan_kanan'.$no.'.'.$file->getClientOriginalExtension();

            if(!empty($check->lamp_agunan_kanan))
            {
                File::delete($check->lamp_agunan_kanan);
            }

            $file->move($path,$name);

            $agunanKanan = $path.'/'.$name;
        }else{
            $agunanKanan = $check->lamp_agunan_kanan;
        }

        if($file = $req->file('lamp_agunan_kiri')){

            $name = 'agunan_kiri'.$no.'.'.$file->getClientOriginalExtension();

            if(!empty($check->lamp_agunan_kiri))
            {
                File::delete($check->lamp_agunan_kiri);
            }

            $file->move($path,$name);

            $agunanKiri = $path.'/'.$name;
        }else{
            $agunanKiri = $check->lamp_agunan_kiri;
        }


        if($file = $req->file('lamp_agunan_belakang')){

            $name = 'agunan_belakang'.$no.'.'.$file->getClientOriginalExtension();

            if(!empty($check->lamp_agunan_belakang))
            {
                File::delete($check->lamp_agunan_belakang);
            }

            $file->move($path,$name);

            $agunanBelakang = $path.'/'.$name;
        }else{
            $agunanBelakang = $check->lamp_agunan_belakang;
        }

        if($file = $req->file('lamp_agunan_dalam')){

            $name = 'agunan_dalam'.$no.'.'.$file->getClientOriginalExtension();

            if(!empty($check->lamp_agunan_dalam))
            {
                File::delete($check->lamp_agunan_dalam);
            }

            $file->move($path,$name);

            $agunanDalam = $path.'/'.$name;
        }else{
            $agunanDalam = $check->lamp_agunan_dalam;
        }

        // Tambahan Agunan Tanah
        if ($file = $req->file('lamp_imb')) {

            $name = 'lamp_imb'.$no.'.'.$file->getClientOriginalExtension();

            if(!empty($check->lamp_imb))
            {
                File::delete($check->lamp_imb);
            }

            $file->move($path,$name);

            $lamp_imb = $path.'/'.$name;
        }else{
            $lamp_imb = $check->lamp_imb;
        }

        if ($file = $req->file('lamp_pbb')) {

            $name = 'lamp_pbb'.$no.'.'.$file->getClientOriginalExtension();

            if(!empty($check->lamp_pbb))
            {
                File::delete($check->lamp_pbb);
            }

            $file->move($path,$name);

            $lamp_pbb = $path.'/'.$name;
        }else {
            $lamp_pbb = $check->lamp_pbb;
        }

        if ($file = $req->file('lamp_sertifikat')) {

            $name = 'lamp_sertifikat'.$no.'.'.$file->getClientOriginalExtension();

            if(!empty($check->lamp_sertifikat))
            {
                File::delete($check->lamp_sertifikat);
            }

            $file->move($path,$name);

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
            'lamp_agunan_depan'       => empty($agunanDepan) ? $check->lamp_agunan_depan : $agunanDepan,
            'lamp_agunan_kanan'       => empty($agunanKanan) ? $check->lamp_agunan_kanan : $agunanKanan,
            'lamp_agunan_kiri'        => empty($agunanKiri) ? $check->lamp_agunan_kiri : $agunanKiri,
            'lamp_agunan_belakang'    => empty($agunanBelakang) ? $check->lamp_agunan_belakang : $agunanBelakang,
            'lamp_agunan_dalam'       => empty($agunanDalam) ? $check->lamp_agunan_dalam : $agunanDalam,
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
                'message'=> 'Update Agunan Tanah Berhasil'
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
