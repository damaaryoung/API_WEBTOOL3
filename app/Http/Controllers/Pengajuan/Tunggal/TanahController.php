<?php

namespace App\Http\Controllers\Pengajuan\Tunggal;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;

// Form Request
use App\Http\Requests\Pengajuan\A_TanahRequest;
use App\Http\Requests\Pengajuan\Pe_TanahRequest;

// Models
use App\Models\Pengajuan\AgunanTanah;
use App\Models\Pengajuan\PemeriksaanAgunTan;
use App\Models\Bisnis\TransSo;
use App\Models\User;

use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use App\Http\Requests;
use Carbon\Carbon;
use DB;

class TanahController extends BaseController
{

    public function showAgunan($id){
        $check = AgunanTanah::with('debt', 'prov', 'kab','kec','kel')
            ->where('id', $id)->first();

        if ($check == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data AgunanTanah Kosong'
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

    public function updateAgunan($id, AgunanTanahRequest $req){
        $check = AgunanTanah::where('id', $id)->first();

        if ($check == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data AgunanTanah Kosong'
            ], 404);
        }

        $so = TransSo::where('id_AgunanTanah', 'like', '%'.$check->id.'%')->get();

        if ($so == '[]') {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Transaksi SO Kosong'
            ], 404);
        }

        $lamp_path = $check->lamp_ktp;

        $arrPath = explode("/", $lamp_path, 4);

        $path = $arrPath[0].'/'.$arrPath[1].'/'.$arrPath[2];

        $no = substr($arrPath[3], 12, 1);

        if($file = $req->file('lamp_ktp_pen')){

            $name = 'ktp_AgunanTanah'.$no.'.'.$file->getClientOriginalExtension();

            if(!empty($check->lamp_ktp))
            {
                File::delete($check->lamp_ktp);
            }

            $file->move($path,$name);

            $ktpPen = $path.'/'.$name;
        }else{
            $ktpPen = $check->lamp_ktp;
        }

        if($file = $req->file('lamp_ktp_pasangan_pen')){

            $name = 'ktp_pasangan'.$no.'.'.$file->getClientOriginalExtension();

            if(!empty($check->lamp_ktp_pasangan))
            {
                File::delete($check->lamp_ktp_pasangan);
            }

            $file->move($path,$name);

            $ktpPenPAS = $path.'/'.$name;
        }else{
            $ktpPenPAS = $check->lamp_ktp_pasangan;
        }

        if($file = $req->file('lamp_kk_pen')){

            $name = 'kk_AgunanTanah'.$no.'.'.$file->getClientOriginalExtension();

            if(!empty($check->lamp_kk))
            {
                File::delete($check->lamp_kk);
            }

            $file->move($path,$name);

            $kkPen = $path.'/'.$name;
        }else{
            $kkPen = $check->lamp_kk;
        }

        if($file = $req->file('lamp_buku_nikah_pen')){

            $name = 'buku_nikah_AgunanTanah'.$no.'.'.$file->getClientOriginalExtension();

            if(!empty($check->lamp_buku_nikah))
            {
                File::delete($check->lamp_buku_nikah);
            }

            $file->move($path,$name);

            $bukuNikahPen = $path.'/'.$name;
        }else{
            $bukuNikahPen = $check->lamp_buku_nikah;
        }

        // Data Usaha Calon Debitur
        // AgunanTanah Lama
        $dataAgunanTanah = array(
            // 'id_calon_debitur' => $check->id_calon_debitur,
            'nama_ktp'         => empty($req->input('nama_ktp_pen')) ? $check->nama_ktp : $req->input('nama_ktp_pen'),
            'nama_ibu_kandung' => empty($req->input('nama_ibu_kandung_pen')) ? $check->nama_ibu_kandung : $req->input('nama_ibu_kandung_pen'),
            'no_ktp'           => empty($req->input('no_ktp_pen')) ? $check->no_ktp : $req->input('no_ktp_pen'),
            'no_npwp'          => empty($req->input('no_npwp_pen')) ? $check->no_npwp : $req->input('no_npwp_pen'),
            'tempat_lahir'     => empty($req->input('tempat_lahir_pen')) ? $check->tempat_lahir : $req->input('tempat_lahir_pen'),
            'tgl_lahir'        => empty($req->input('tgl_lahir_pen')) ? $check->tgl_lahir : Carbon::parse($req->input('tgl_lahir_pen'))->format('Y-m-d'),
            'jenis_kelamin'    => empty($req->input('jenis_kelamin_pen')) ? $check->jenis_kelamin : strtoupper($req->input('jenis_kelamin_pen')),
            'alamat_ktp'       => empty($req->input('alamat_ktp_pen')) ? $check->alamat_ktp : $req->input('alamat_ktp_pen'),
            'no_telp'          => empty($req->input('no_telp_pen')) ? $check->no_telp : $req->input('no_telp_pen'),
            'hubungan_debitur' => empty($req->input('hubungan_debitur_pen')) ? $check->hubungan_debitur : $req->input('hubungan_debitur_pen'),

            'pekerjaan'             => empty($req->input('pekerjaan_pen')) ? $check->pekerjaan : $req->input('pekerjaan_pen'),
            'nama_tempat_kerja'     => empty($req->input('nama_tempat_kerja_pen')) ? $check->nama_tempat_kerja : $req->input('nama_tempat_kerja_pen'),
            'posisi_pekerjaan'      => empty($req->input('posisi_pekerjaan_pen')) ? $check->posisi_pekerjaan : $req->input('posisi_pekerjaan_pen'),
            'jenis_pekerjaan'       => empty($req->input('jenis_pekerjaan_pen')) ? $check->jenis_pekerjaan : $req->input('jenis_pekerjaan_pen'),
            'alamat_tempat_kerja'   => empty($req->input('alamat_tempat_kerja_pen')) ? $check->alamat_tempat_kerja : $req->input('alamat_tempat_kerja_pen'),
            'id_prov_tempat_kerja'  => empty($req->input('id_prov_tempat_kerja_pen')) ? $check->id_prov_tempat_kerja : $req->input('id_prov_tempat_kerja_pen'),
            'id_kab_tempat_kerja'   => empty($req->input('id_kab_tempat_kerja_pen')) ? $check->id_kab_tempat_kerja : $req->input('id_kab_tempat_kerja_pen'),
            'id_kec_tempat_kerja'   => empty($req->input('id_kec_tempat_kerja_pen')) ? $check->id_kec_tempat_kerja : $req->input('id_kec_tempat_kerja_pen'),
            'id_kel_tempat_kerja'   => empty($req->input('id_kel_tempat_kerja_pen')) ? $check->id_kel_tempat_kerja : $req->input('id_kel_tempat_kerja_pen'),
            'rt_tempat_kerja'       => empty($req->input('rt_tempat_kerja_pen')) ? $check->rt_tempat_kerja : $req->input('rt_tempat_kerja_pen'),
            'rw_tempat_kerja'       => empty($req->input('rw_tempat_kerja_pen')) ? $check->rw_tempat_kerja : $req->input('rw_tempat_kerja_pen'),
            'tgl_mulai_kerja'       => empty($req->input('tgl_mulai_kerja_pen')) ? $check->tgl_mulai_kerja : $req->input('tgl_mulai_kerja_pen'),
            'no_telp_tempat_kerja'  => empty($req->input('no_telp_tempat_kerja_pen')) ? $check->no_telp_tempat_kerja : $req->input('no_telp_tempat_kerja_pen'),
            'lamp_ktp'         => $ktpPen,
            'lamp_ktp_pasangan'=> $ktpPenPAS,
            'lamp_kk'          => $kkPen,
            'lamp_buku_nikah'  => $bukuNikahPen,
        );

        DB::connection('web')->beginTransaction();

        AgunanTanah::where('id', $id)->update($dataAgunanTanah);

        try {
            DB::connection('web')->commit();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message'=> 'Update AgunanTanah Berhasil'
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
