<?php

namespace App\Http\Controllers\Pengajuan\Tunggal;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;

// Form Request
use App\Http\Requests\Pengajuan\A_KendaraanRequest;

// Models
use App\Models\Pengajuan\AgunanKendaraan;
use App\Models\Bisnis\TransSo;
use App\Models\User;

use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use App\Http\Requests;
use Carbon\Carbon;
use DB;

class KendaraanController extends BaseController
{

    public function show($id){
        $check = AgunanKendaraan::with('debt')
            ->where('id', $id)->first();

        if ($check == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Agunan Kendaraan Kosong'
            ], 404);
        }

        $data = array(
            'id'            => $check->id,
            'no_bpkb'       => $check->no_bpkb,
            'nama_pemilik'  => $check->nama_pemilik,
            'alamat_pemilik'=> $check->alamat_pemilik,
            'merk'          => $check->merk,
            'jenis'         => $check->jenis,
            'no_rangka'     => $check->no_rangka,
            'no_mesin'      => $check->no_mesin,
            'warna'         => $check->warna,
            'tahun'         => $check->tahun,
            'no_polisi'     => $check->no_polisi,
            'no_stnk'       => $check->no_stnk,
            'tgl_kadaluarsa_pajak'=> $check->tgl_kadaluarsa_pajak,
            'tgl_kadaluarsa_stnk' => $check->tgl_kadaluarsa_stnk,
            'no_faktur'         => $check->no_faktur,
            'lampiran'  => [
                'lamp_agunan_depan' => $check->lamp_agunan_depan,
                'lamp_agunan_kanan' => $check->lamp_agunan_kanan,
                'lamp_agunan_kiri'  => $check->lamp_agunan_kiri,
                'lamp_agunan_belakang' => $check->lamp_agunan_belakang,
                'lamp_agunan_dalam' => $check->lamp_agunan_dalam
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

    public function update($id, A_KendaraanRequest $req){
        $check = AgunanKendaraan::where('id', $id)->first();

        if ($check == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data AgunanKendaraan Kosong'
            ], 404);
        }

        $so = TransSo::where('id_agunan_kendaraan', 'like', '%'.$check->id.'%')->get();

        if ($so == '[]') {
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
            $lamp_path = 'public/lamp_trans.2-AO-1-2020-13/agunan_kendaraan/agunan_depan1.png';
        }

        $arrPath = explode("/", $lamp_path, 4);

        $path = $arrPath[0].'/'.$arrPath[1].'/'.$arrPath[2];

        $no = substr($arrPath[3], 12, 1);

        if($file = $req->file('lamp_agunan_depan_ken')){

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

        if($file = $req->file('lamp_agunan_kanan_ken')){

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

        if($file = $req->file('lamp_agunan_kiri_ken')){

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


        if($file = $req->file('lamp_agunan_belakang_ken')){

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

        if($file = $req->file('lamp_agunan_dalam_ken')){

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

        $dataAgunanKendaraan = array(
            'no_bpkb'               => empty($req->no_bpkb_ken) ? $check->no_bpkb : $req->no_bpkb_ken,
            'nama_pemilik'          => empty($req->nama_pemilik_ken) ? $check->nama_pemilik : $req->nama_pemilik_ken,
            'alamat_pemilik'        => empty($req->alamat_pemilik_ken) ? $check->alamat_pemilik : $req->alamat_pemilik,
            'merk'                  => empty($req->merk_ken) ? $check->merk : $req->merk_ken,
            'jenis'                 => empty($req->jenis_ken) ? $check->jenis : $req->jenis_ken,
            'no_rangka'             => empty($req->no_rangka_ken) ? $check->no_rangka : $req->no_rangka_ken,
            'no_mesin'              => empty($req->no_mesin_ken) ? $check->no_mesin : $req->no_mesin_ken,
            'warna'                 => empty($req->warna_ken) ? $check->warna : $req->warna_ken,
            'tahun'                 => empty($req->tahun_ken) ? $check->tahun : $req->tahun_ken,
            'no_polisi'             => empty($req->no_polisi_ken) ? $check->no_polisi : strtoupper($req->no_polisi_ken),
            'no_stnk'               => empty($req->no_stnk_ken) ? $check->no_stnk : $req->no_stnk_ken,
            'tgl_kadaluarsa_pajak'  => empty($req->tgl_exp_pajak_ken) ? $check->tgl_kadaluarsa_pajak : Carbon::parse($req->tgl_exp_pajak_ken)->format('Y-m-d'),
            'tgl_kadaluarsa_stnk'   => empty($req->tgl_exp_stnk_ken) ? $check->tgl_kadaluarsa_stnk : Carbon::parse($req->tgl_exp_stnk_ken)->format('Y-m-d'),
            'no_faktur'             => empty($req->no_faktur_ken) ? $check->no_faktur : $req->no_faktur_ken,
            'lamp_agunan_depan'     => $agunanDepan,
            'lamp_agunan_kanan'     => $agunanKanan,
            'lamp_agunan_kiri'      => $agunanKiri,
            'lamp_agunan_belakang'  => $agunanBelakang,
            'lamp_agunan_dalam'     => $agunanDalam,
        );

        DB::connection('web')->beginTransaction();

        try {

            AgunanKendaraan::where('id', $id)->update($dataAgunanKendaraan);

            DB::connection('web')->commit();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message'=> 'Update Agunan Kendaraan Berhasil'
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
