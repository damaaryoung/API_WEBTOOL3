<?php

namespace App\Http\Controllers\Pengajuan\Tunggal;

use Laravel\Lumen\Routing\Controller as BaseController;
// use App\Http\Controllers\Controller as Helper;

// Form Request
use App\Http\Requests\Pengajuan\DebiturRequest;

// Models
use App\Models\Pengajuan\SO\Debitur;
use App\Models\Transaksi\TransSO;

use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use Image;
use DB;

class DebiturController extends BaseController
{

    public function show($id){
        $val = Debitur::with('prov_ktp','kab_ktp','kec_ktp','kel_ktp','prov_dom','kab_dom','kec_dom','kel_dom','prov_kerja','kab_kerja','kec_kerja','kel_kerja')
            ->where('id', $id)->first();

        if ($val == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Debitur Kosong'
            ], 404);
        }

        $nama_anak = explode(",", $val->nama_anak);
        $tgl_anak = explode(",", $val->tgl_lahir_anak);

        for ($i = 0; $i < count($nama_anak); $i++) {
            $anak[] = array(
                'nama'      => $nama_anak[$i],
                'tgl_lahir' => Carbon::parse($tgl_anak[$i])->format("d-m-Y")
            );
        }

        $data = array(
            'id'                    => $val->id == null ? null : (int) $val->id,
            'nama_lengkap'          => $val->nama_lengkap,
            'gelar_keagamaan'       => $val->gelar_keagamaan,
            'gelar_pendidikan'      => $val->gelar_pendidikan,
            'jenis_kelamin'         => $val->jenis_kelamin,
            'status_nikah'          => $val->status_nikah,
            'ibu_kandung'           => $val->ibu_kandung,
            'tinggi_badan'          => $val->tinggi_badan,
            'berat_badan'           => $val->berat_badan,
            'no_ktp'                => $val->no_ktp,
            'no_ktp_kk'             => $val->no_ktp_kk,
            'no_kk'                 => $val->no_kk,
            'no_npwp'               => $val->no_npwp,
            'tempat_lahir'          => $val->tempat_lahir,
            'tgl_lahir'             => Carbon::parse($val->tgl_lahir)->format('d-m-Y'),
            'agama'                 => $val->agama,
            'anak'                  => $anak,
            'alamat_ktp' => [
                'alamat_singkat' => $val->alamat_ktp,
                'rt'     => $val->rt_ktp == null ? null : (int) $val->rt_ktp,
                'rw'     => $val->rw_ktp == null ? null : (int) $val->rw_ktp,
                'kelurahan' => [
                    'id'    => $val->id_kel_ktp == null ? null : (int) $val->id_kel_ktp,
                    'nama'  => $val->kel_ktp['nama']
                ],
                'kecamatan' => [
                    'id'    => $val->id_kec_ktp == null ? null : (int) $val->id_kec_ktp,
                    'nama'  => $val->kec_ktp['nama']
                ],
                'kabupaten' => [
                    'id'    => $val->id_kab_ktp == null ? null : (int) $val->id_kab_ktp,
                    'nama'  => $val->kab_ktp['nama'],
                ],
                'provinsi'  => [
                    'id'   => $val->id_prov_ktp == null ? null : (int) $val->id_prov_ktp,
                    'nama' => $val->prov_ktp['nama'],
                ],
                'kode_pos' => $val->kel_ktp['kode_pos'] == null ? null : (int) $val->kel_ktp['kode_pos']
            ],
            'alamat_domisili' => [
                'alamat_singkat' => $val->alamat_domisili,
                'rt'             => $val->rt_domisili == null ? null : (int) $val->rt_domisili,
                'rw'             => $val->rw_domisili == null ? null : (int) $val->rw_domisili,
                'kelurahan' => [
                    'id'    => $val->id_kel_tempat_kerja == null ? null : (int) $val->id_kel_tempat_kerja,
                    'nama'  => $val->kel_dom['nama']
                ],
                'kecamatan' => [
                    'id'    => $val->id_kec_domisili == null ? null : (int) $val->id_kec_domisili,
                    'nama'  => $val->kec_dom['nama']
                ],
                'kabupaten' => [
                    'id'    => $val->id_kab_domisili == null ? null : (int) $val->id_kab_domisili,
                    'nama'  => $val->kab_dom['nama'],
                ],
                'provinsi'  => [
                    'id'   => $val->id_prov_domisili == null ? null : (int) $val->id_prov_domisili,
                    'nama' => $val->prov_dom['nama'],
                ],
                'kode_pos' => $val->kel_dom['kode_pos'] == null ? null : (int) $val->kel_dom['kode_pos']
            ],
            "pekerjaan" => [
                "nama_pekerjaan"        => $val->pekerjaan,
                "posisi_pekerjaan"      => $val->posisi_pekerjaan,
                "nama_tempat_kerja"     => $val->nama_tempat_kerja,
                "jenis_pekerjaan"       => $val->jenis_pekerjaan,
                "tgl_mulai_kerja"       => $val->tgl_mulai_kerja, //Carbon::parse($val->tgl_mulai_kerja)->format('d-m-Y'),
                "no_telp_tempat_kerja"  => $val->no_telp_tempat_kerja,
                'alamat' => [
                    'alamat_singkat' => $val->alamat_tempat_kerja,
                    'rt'             => $val->rt_tempat_kerja == null ? null : (int) $val->rt_tempat_kerja,
                    'rw'             => $val->rw_tempat_kerja == null ? null : (int) $val->rw_tempat_kerja,
                    'kelurahan' => [
                        'id'    => $val->id_kel_tempat_kerja == null ? null : (int) $val->id_kel_tempat_kerja,
                        'nama'  => $val->kel_kerja['nama']
                    ],
                    'kecamatan' => [
                        'id'    => $val->id_kec_tempat_kerja == null ? null : (int) $val->id_kec_tempat_kerja,
                        'nama'  => $val->kec_kerja['nama']
                    ],
                    'kabupaten' => [
                        'id'    => $val->id_kab_tempat_kerja == null ? null : (int) $val->id_kab_tempat_kerja,
                        'nama'  => $val->kab_kerja['nama'],
                    ],
                    'provinsi'  => [
                        'id'    => $val->id_prov_tempat_kerja == null ? null : (int) $val->id_prov_tempat_kerja,
                        'nama'  => $val->prov_kerja['nama'],
                    ],
                    'kode_pos'  => $val->kel_kerja['kode_pos'] == null ? null : (int) $val->kel_kerja['kode_pos']
                ]
            ],
            'pendidikan_terakhir'   => $val->pendidikan_terakhir,
            'jumlah_tanggungan'     => $val->jumlah_tanggungan,
            'no_telp'               => $val->no_telp,
            'no_hp'                 => $val->no_hp,
            'alamat_surat'          => $val->alamat_surat,
            'lampiran' => [
                'lamp_ktp'              => $val->lamp_ktp,
                'lamp_kk'               => $val->lamp_kk,
                'lamp_buku_tabungan'    => $val->lamp_buku_tabungan,
                'lamp_sertifikat'       => $val->lamp_sertifikat,
                'lamp_sttp_pbb'         => $val->lamp_sttp_pbb,
                'lamp_imb'              => $val->lamp_imb,
                'foto_agunan_rumah'     => $val->foto_agunan_rumah
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

    public function update($id, DebiturRequest $req){
        $check = Debitur::where('id', $id)->first();

        if ($check == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Debitur Kosong'
            ], 404);
        }

        $so = TransSO::where('id_calon_debitur', $id)->first();

        if ($so == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Transaksi SO Kosong'
            ], 404);
        }

        $lamp_dir = 'public/' . $so->debt['no_ktp'];

        // Lampiran Debitur
        if($req->file('lamp_ktp') != null){
            $file = $req->file('lamp_ktp');

            $path = $lamp_dir.'/debitur';
            $name = 'ktp.'.$file->getClientOriginalName();

            $img = Image::make($file)->resize(320, 240);

            if(!File::isDirectory($path)){
                File::makeDirectory($path, 0777, true, true);
            }
            
            if(!empty($check->lamp_ktp))
            {
                File::delete($check->lamp_ktp);
            }
            
            $img->save($path.'/'.$name);

            $ktpDebt = $path.'/'.$name;
        }else{
            $ktpDebt = $check->lamp_ktp;
        }


        if($req->file('lamp_kk') != null){
            $file = $req->file('lamp_kk');

            $path = $lamp_dir.'/debitur';
            $name = 'kk.'.$file->getClientOriginalName();
            
            $img = Image::make($file)->resize(320, 240);
            
            if(!File::isDirectory($path)){
                File::makeDirectory($path, 0777, true, true);
            }
            
            if(!empty($check->lamp_kk))
            {
                File::delete($check->lamp_kk);
            }
            
            $img->save($path.'/'.$name);

            $kkDebt = $path.'/'.$name;
        }else{
            $kkDebt = $check->lamp_kk;
        }

        if($req->file('lamp_sertifikat') != null){
            $file = $req->file('lamp_sertifikat');

            $path = $lamp_dir.'/debitur';
            $name = 'sertifikat.'.$file->getClientOriginalName();

            
            $img = Image::make($file)->resize(320, 240);
            
            if(!File::isDirectory($path)){
                File::makeDirectory($path, 0777, true, true);
            }
            
            if(!empty($check->lamp_sertifikat))
            {
                File::delete($check->lamp_sertifikat);
            }
            
            $img->save($path.'/'.$name);

            $sertifikatDebt = $path.'/'.$name;
        }else{
            $sertifikatDebt = $check->lamp_sertifikat;
        }

        if($req->file('lamp_pbb') != null){
            $file = $req->file('lamp_pbb');

            $path = $lamp_dir.'/debitur';
            $name = 'pbb.'.$file->getClientOriginalName();

            $img = Image::make($file)->resize(320, 240);
            
            if(!File::isDirectory($path)){
                File::makeDirectory($path, 0777, true, true);
            }            
            
            if(!empty($check->lamp_sttp_pbb))
            {
                File::delete($check->lamp_sttp_pbb);
            }
            
            $img->save($path.'/'.$name);

            $pbbDebt = $path.'/'.$name;
        }else{
            $pbbDebt = $check->lamp_sttp_pbb;
        }

        if($req->file('lamp_imb') != null){
            $file = $req->file('lamp_imb');

            $path = $lamp_dir.'/debitur';
            $name = 'imb.'.$file->getClientOriginalName();
            
            $img = Image::make($file)->resize(320, 240);
            
            if(!File::isDirectory($path)){
                File::makeDirectory($path, 0777, true, true);
            }
            
            if(!empty($check->lamp_imb))
            {
                File::delete($check->lamp_imb);
            }
            
            $img->save($path.'/'.$name);

            $imbDebt = $path.'/'.$name;
        }else{
            $imbDebt = $check->lamp_imb;
        }

        if(empty($req->input('nama_anak'))){
            $nama_anak = $check->nama_anak;
            $tgl_lhr_anak = $check->tgl_lhr_anak;
        }else{
            for ($i = 0; $i < count($req->input('nama_anak')); $i++){
                $namaAnak[] = empty($req->nama_anak[$i]) ? $check->nama_anak[$i] : $req->nama_anak[$i];

                $tglLahirAnak[] = empty($req->tgl_lahir_anak[$i]) ? $check->tgl_lahir_anak[$i] : Carbon::parse($req->tgl_lahir_anak[$i])->format('Y-m-d');
            }

            $nama_anak    = implode(",", $namaAnak);
            $tgl_lhr_anak = implode(",", $tglLahirAnak);
        }

        if($req->file('foto_agunan_rumah') != null){
            $file = $req->file('foto_agunan_rumah');

            $path = $lamp_dir.'/debitur';
            $name = 'foto_agunan_rumah.'.$file->getClientOriginalName();

            $img = Image::make($file)->resize(320, 240);
            
            if(!File::isDirectory($path)){
                File::makeDirectory($path, 0777, true, true);
            }
            
            if(!empty($check->foto_agunan_rumah))
            {
                File::delete($check->foto_agunan_rumah);
            }
            
            $img->save($path.'/'.$name);

            $foto_agunan_rumah = $path.'/'.$name;
        }else{
            $foto_agunan_rumah = null;
        }

        // New UIT 2
        // Lampiran Untuk Debitur
        if ($req->file('lamp_buku_tabungan') != null) {

            $files = $req->file('lamp_buku_tabungan');

            $buku_tabungan = array();
            foreach($files as $file){
                $path = $lamp_dir.'/lamp_buku_tabungan';
                $name = 'lamp_buku_tabungan.' . $file->getClientOriginalName();

                $img = Image::make($file)->resize(320, 240);
                
                if(!File::isDirectory($path)){
                    File::makeDirectory($path, 0777, true, true);
                }
                
                $img->save($path.'/'.$name);

                $buku_tabungan[] = $path.'/'.$name;

            }

            $lamp_buku_tabungan = implode(";",$buku_tabungan);

        }else{
            $lamp_buku_tabungan = $check->lamp_buku_tabungan;
        }

        if($req->file('lamp_skk') != null){
            $file = $req->file('lamp_skk');

            $path = $lamp_dir.'/debitur';
            $name = 'lamp_skk.'.$file->getClientOriginalName();

            $img = Image::make($file)->resize(320, 240);
            
            if(!File::isDirectory($path)){
                File::makeDirectory($path, 0777, true, true);
            }

            if(!empty($check->lamp_skk))
            {
                File::delete($check->lamp_skk);
            }
            
            $img->save($path.'/'.$name);

            $lamp_skk = $path.'/'.$name;
        }else{
            $lamp_skk = $check->lamp_skk;
        }

        if($req->file('lamp_sku') != null){
            $files = $req->file('lamp_sku');

            $sku = array();
            foreach ($files as $file) {
                $path = $lamp_dir.'/debitur';
                $name = 'lamp_sku.'.$file->getClientOriginalName();

                $img = Image::make($file)->resize(320, 240);
                
                if(!File::isDirectory($path)){
                    File::makeDirectory($path, 0777, true, true);
                }

                $img->save($path.'/'.$name);

                $sku[] = $path.'/'.$name;
            }

            $lamp_sku = implode(";",$sku);

        }else{
            $lamp_sku = $check->lamp_sku;
        }

        if($req->file('lamp_slip_gaji') != null){
            $file = $req->file('lamp_slip_gaji');

            $path = $lamp_dir.'/debitur';
            $name = 'lamp_slip_gaji.'.$file->getClientOriginalName(); //->getClientOriginalExtension();

            $img = Image::make($file)->resize(320, 240);
            
            if(!File::isDirectory($path)){
                File::makeDirectory($path, 0777, true, true);
            }

            if(!empty($check->lamp_slip_gaji))
            {
                File::delete($check->lamp_slip_gaji);
            }
            
            $img->save($path.'/'.$name);

            $lamp_slip_gaji = $path.'/'.$name;

        }else{
            $lamp_slip_gaji = $check->lamp_slip_gaji;
        }


        if($req->file('foto_pembukuan_usaha') != null){
            $files = $req->file('foto_pembukuan_usaha');

            $pembukuan_usaha = array();
            foreach ($files as $file) {
                $path = $lamp_dir.'/debitur';
                $name = 'foto_pembukuan_usaha.'.$file->getClientOriginalName();

                $img = Image::make($file)->resize(320, 240);
                
                if(!File::isDirectory($path)){
                    File::makeDirectory($path, 0777, true, true);
                }

                $img->save($path.'/'.$name);

                $pembukuan_usaha[] = $path.'/'.$name;
            }

            $foto_pembukuan_usaha = implode(";",$pembukuan_usaha);

        }else{
            $foto_pembukuan_usaha = $check->foto_pembukuan_usaha;
        }

        if($req->file('lamp_foto_usaha') != null){
            $files = $req->file('lamp_foto_usaha');

            $foto_usaha = array();
            foreach ($files as $file) {
                $path = $lamp_dir.'/debitur';
                $name = 'lamp_foto_usaha.'.$file->getClientOriginalName();

                $img = Image::make($file)->resize(320, 240);
                
                if(!File::isDirectory($path)){
                    File::makeDirectory($path, 0777, true, true);
                }

                $img->save($path.'/'.$name);

                $foto_usaha[] = $path.'/'.$name;
            }

            $lamp_foto_usaha = implode(";",$foto_usaha);

        }else{
            $lamp_foto_usaha = $check->lamp_foto_usaha;
        }

        if ($file = $req->file('lamp_surat_cerai')) {
            $path = $lamp_dir.'/debitur';
            $name = 'lamp_surat_cerai.' . $file->getClientOriginalName();

            $img = Image::make($file)->resize(320, 240);

            if(!File::isDirectory($path)){
                File::makeDirectory($path, 0777, true, true);
            }

            $img->save($path.'/'.$name);

            $lamp_surat_cerai = $path.'/'.$name;
        }else{
            $lamp_surat_cerai = $check->lamp_surat_cerai;
        }


        // Data Debitur
        $dataDebitur = array(
            'nama_lengkap'          => empty($req->input('nama_lengkap')) ? $check->nama_lengkap : $req->input('nama_lengkap'),
            'gelar_keagamaan'       => empty($req->input('gelar_keagamaan')) ? $check->gelar_keagamaan : $req->input('gelar_keagamaan'),
            'gelar_pendidikan'      => empty($req->input('gelar_pendidikan')) ? $check->gelar_pendidikan : $req->input('gelar_pendidikan'),
            'jenis_kelamin'         => empty($req->input('jenis_kelamin')) ? strtoupper($check->jenis_kelamin) : strtoupper($req->input('jenis_kelamin')),
            'status_nikah'          => empty($req->input('status_nikah')) ? strtoupper($check->status_nikah) : $req->input('status_nikah'),
            'ibu_kandung'           => empty($req->input('ibu_kandung')) ? $check->ibu_kandung : $req->input('ibu_kandung'),
            'no_ktp'                => empty($req->input('no_ktp')) ? $check->no_ktp : $req->input('no_ktp'),
            'no_ktp_kk'             => empty($req->input('no_ktp_kk')) ? $check->no_ktp_kk : $req->input('no_ktp_kk'),
            'no_kk'                 => empty($req->input('no_kk')) ? $check->no_kk : $req->input('no_kk'),
            'no_npwp'               => empty($req->input('no_npwp')) ? $check->no_npwp : $req->input('no_npwp'),
            'tempat_lahir'          => empty($req->input('tempat_lahir')) ? $check->tempat_lahir : $req->input('tempat_lahir'),
            'tgl_lahir'             => empty($req->input('tgl_lahir')) ? $check->tgl_lahir : Carbon::parse($req->input('tgl_lahir'))->format('Y-m-d'),
            'agama'                 => empty($req->input('agama')) ? $check->agama : strtoupper($req->input('agama')),
            'alamat_ktp'            => empty($req->input('alamat_ktp')) ? $check->alamat_ktp : $req->input('alamat_ktp'),
            'rt_ktp'                => empty($req->input('rt_ktp')) ? $check->rt_ktp : $req->input('rt_ktp'),
            'rw_ktp'                => empty($req->input('rw_ktp')) ? $check->rw_ktp : $req->input('rw_ktp'),
            'id_prov_ktp'           => empty($req->input('id_prov_ktp')) ? $check->id_prov_ktp : $req->input('id_prov_ktp'),
            'id_kab_ktp'            => empty($req->input('id_kab_ktp')) ? $check->id_kab_ktp : $req->input('id_kab_ktp'),
            'id_kec_ktp'            => empty($req->input('id_kec_ktp')) ? $check->id_kec_ktp : $req->input('id_kec_ktp'),
            'id_kel_ktp'            => empty($req->input('id_kel_ktp')) ? $check->id_kel_ktp : $req->input('id_kel_ktp'),

            'alamat_domisili'       => empty($req->input('alamat_domisili')) ? $check->alamat_domisili : $req->input('alamat_domisili'),
            'rt_domisili'           => empty($req->input('rt_domisili')) ? $check->rt_domisili : $req->input('rt_domisili'),
            'rw_domisili'           => empty($req->input('rw_domisili')) ? $check->rw_domisili : $req->input('rw_domisili'),
            'id_prov_domisili'      => empty($req->input('id_prov_domisili')) ? $check->id_prov_domisili : $req->input('id_prov_domisili'),
            'id_kab_domisili'       => empty($req->input('id_kab_domisili')) ? $check->id_kab_domisili : $req->input('id_kab_domisili'),
            'id_kec_domisili'       => empty($req->input('id_kec_domisili')) ? $check->id_kec_domisili : $req->input('id_kec_domisili'),
            'id_kel_domisili'       => empty($req->input('id_kel_domisili')) ? $check->id_kel_domisili : $req->input('id_kel_domisili'),

            'pendidikan_terakhir'   => empty($req->input('pendidikan_terakhir')) ? $check->pendidikan_terakhir : $req->input('pendidikan_terakhir'),
            'jumlah_tanggungan'     => empty($req->input('jumlah_tanggungan')) ? $check->jumlah_tanggungan : $req->input('jumlah_tanggungan'),
            'no_telp'               => empty($req->input('no_telp')) ? $check->no_telp : $req->input('no_telp'),
            'no_hp'                 => empty($req->input('no_hp')) ? $check->no_hp : $req->input('no_hp'),
            'alamat_surat'          => empty($req->input('alamat_surat')) ? $check->alamat_surat : $req->input('alamat_surat'),

            'tinggi_badan'          => empty($req->input('tinggi_badan')) ? $check->tinggi_badan : $req->input('tinggi_badan'),
            'berat_badan'           => empty($req->input('berat_badan')) ? $check->berat_badan : $req->input('berat_badan'),
            'nama_anak'             => $nama_anak,
            'tgl_lahir_anak'        => $tgl_lhr_anak,
            'pekerjaan'             => empty($req->input('pekerjaan')) ? $check->pekerjaan : $req->input('pekerjaan'),
            'posisi_pekerjaan'      => empty($req->input('posisi_pekerjaan')) ? $check->posisi_pekerjaan : $req->input('posisi_pekerjaan'),
            'nama_tempat_kerja'     => empty($req->input('nama_tempat_kerja')) ? $check->nama_tempat_kerja : $req->input('nama_tempat_kerja'),
            'jenis_pekerjaan'       => empty($req->input('jenis_pekerjaan')) ? $check->jenis_pekerjaan : $req->input('jenis_pekerjaan'),

            'alamat_tempat_kerja'   => empty($req->input('alamat_tempat_kerja')) ? $check->alamat_tempat_kerja : $req->input('alamat_tempat_kerja'),
            'id_prov_tempat_kerja'  => empty($req->input('id_prov_tempat_kerja')) ? $check->id_prov_tempat_kerja : $req->input('id_prov_tempat_kerja'),
            'id_kab_tempat_kerja'   => empty($req->input('id_kab_tempat_kerja')) ? $check->id_kab_tempat_kerja : $req->input('id_kab_tempat_kerja'),
            'id_kec_tempat_kerja'   => empty($req->input('id_kec_tempat_kerja')) ? $check->id_kec_tempat_kerja : $req->input('id_kec_tempat_kerja'),
            'id_kel_tempat_kerja'   => empty($req->input('id_kel_tempat_kerja')) ? $check->id_kel_tempat_kerja : $req->input('id_kel_tempat_kerja'),
            'rt_tempat_kerja'       => empty($req->input('rt_tempat_kerja')) ? $check->rt_tempat_usaha : $req->input('rt_tempat_kerja'),
            'rw_tempat_kerja'       => empty($req->input('rw_tempat_kerja')) ? $check->rw_tempat_usaha : $req->input('rw_tempat_kerja'),
            'tgl_mulai_kerja'       => empty($req->input('tgl_mulai_kerja')) ? $check->tgl_mulai_kerja : Carbon::parse($req->input('tgl_mulai_kerja'))->format('Y-m-d'),
            'no_telp_tempat_kerja'  => empty($req->input('no_telp_tempat_kerja')) ? $check->no_telp_tempat_kerja : $req->input('no_telp_tempat_kerja'),

            'lamp_ktp'              => $ktpDebt,
            'lamp_kk'               => $kkDebt,
            'lamp_sertifikat'       => $sertifikatDebt,
            'lamp_sttp_pbb'         => $pbbDebt,
            'lamp_imb'              => $imbDebt,

            // New UIT 2
            'lamp_buku_tabungan'    => $lamp_buku_tabungan,
            'lamp_skk'              => $lamp_skk,
            'lamp_sku'              => $lamp_sku,
            'lamp_slip_gaji'        => $lamp_slip_gaji,
            'foto_pembukuan_usaha'  => $foto_pembukuan_usaha,
            'lamp_foto_usaha'       => $lamp_foto_usaha,
            'foto_agunan_rumah'     => $foto_agunan_rumah,

            'lamp_surat_cerai'      => $lamp_surat_cerai
        );

        DB::connection('web')->beginTransaction();

        try {

            Debitur::where('id', $id)->update($dataDebitur);

            DB::connection('web')->commit();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message'=> 'Update Debitur Berhasil',
                'data'   => $dataDebitur
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
