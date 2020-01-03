<?php

namespace App\Http\Controllers\Pengajuan;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use App\Models\AreaKantor\Cabang;
use App\Models\Wilayah\Kabupaten;
use App\Models\Wilayah\Kecamatan;
use App\Models\Wilayah\Kelurahan;
use App\Models\Wilayah\Provinsi;
use App\Models\AreaKantor\JPIC;
use App\Models\AreaKantor\PIC;
use App\Models\Bisnis\TransAO;
use App\Models\Bisnis\TransSo;
use Illuminate\Http\Request;
use App\Models\CC\Pasangan;
use App\Models\CC\Penjamin;
use App\Models\CC\Debitur;
use App\Http\Requests;
use App\Models\User;
use Carbon\Carbon;
use DB;

class MasterCA_Controller extends BaseController
{
    public function index(Request $req){
        $kode_kantor = $req->auth->kd_cabang;
        $query = TransAO::where('kode_kantor', $kode_kantor)->where('status_ao', 1)->get();

        if ($query == '[]') {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        foreach ($query as $key => $val) {

            $data[$key] = [
                'id_trans_so'    => $val->id_trans_so,
                'nomor_so'       => $val->so['nomor_so'],
                'nomor_ao'       => $val->nomor_ao,
                'kode_kantor'    => $val->so['kode_kantor'],
                'asal_data'      => $val->so['asaldata']['nama'],
                'nama_marketing' => $val->so['nama_marketing'],
                'nama_so'        => $val->so['nama_so'],
                'nama_debitur'   => $val->so['debt']['nama_lengkap'],
                'plafon'         => $val->so['faspin']['plafon'],
                'tenor'          => $val->so['faspin']['tenor']
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
        // $kode_kantor = $req->auth->kd_cabang;
        $user_id = $req->auth->user_id;
        $pic     = PIC::where('user_id', $user_id)->first();
        $id_cabang = $pic->id_mk_cabang;

        $query = TransAO::where('id_trans_so', $id)->where('kode_kantor', $kode_kantor)->where('status_ao', 1)->get();

        if ($query == '[]') {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        foreach ($query as $key => $val) {

            $prov_ktp = Provinsi::where('id', $val->debt['id_prov_ktp'])->first();
            $kab_ktp  = Kabupaten::where('id', $val->debt['id_kab_ktp'])->first();
            $kec_ktp  = Kecamatan::where('id', $val->debt['id_kec_ktp'])->first();
            $kel_ktp  = Kelurahan::where('id', $val->debt['id_kel_ktp'])->first();

            $prov_dom = Provinsi::where('id', $val->debt['id_prov_domisili'])->first();
            $kab_dom  = Kabupaten::where('id', $val->debt['id_kab_domisili'])->first();
            $kec_dom  = Kecamatan::where('id', $val->debt['id_kec_domisili'])->first();
            $kel_dom  = Kelurahan::where('id', $val->debt['id_kel_domisili'])->first();

            $penjamin = Penjamin::where('id_calon_debitur', $val->id_calon_debt)->get();

            $data[$key] = [
                'nomor_so'       => $val->so['nomor_so'],
                'nomor_ao'       => $val->nomor_ao,
                'kode_kantor'    => $val->so['kode_kantor'],
                'asal_data'      => $val->so['asaldata']['nama'],
                'nama_marketing' => $val->so['nama_marketing'],
                'nama_so'        => $val->so['nama_so'],
                'nama_debitur'   => $val->so['debt']['nama_lengkap'],
                'plafon'         => $val->so['faspin']['plafon'],
                'tenor'          => $val->so['faspin']['tenor'],
                'fasilitas_pinjaman'  => [
                    'jenis_pinjaman'  => $val->faspin['jenis_pinjaman'],
                    'tujuan_pinjaman' => $val->faspin['tujuan_pinjaman']
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
                    'no_kk'                 => $val->debt['no_ktp'],
                    'no_npwp'               => $val->debt['no_npwp'],
                    'tempat_lahir'          => $val->debt['tempat_lahir'],
                    'tgl_lahir'             => Carbon::parse($val->debt['tgl_lahir'])->format('d-m-Y'),
                    'agama'                 => $val->debt['agama'],
                    'alamat_ktp'            => $val->debt['alamat_ktp'],
                    'rt_ktp'                => $val->debt['rt_ktp'],
                    'rw_ktp'                => $val->debt['rw_ktp'],
                    'provinsi_ktp'          => $prov_ktp['nama'],
                    'kabupaten_ktp'         => $kab_ktp['nama'],
                    'kecamatan_ktp'         => $kec_ktp['nama'],
                    'kelurahan_ktp'         => $kel_ktp['nama'],
                    'alamat_domisili'       => $val->debt['alamat_domisili'],
                    'rt_domisili'           => $val->debt['rt_domisili'],
                    'rw_domisili'           => $val->debt['rw_domisili'],
                    'provinsi_domisili'     => $prov_dom['nama'],
                    'kabupaten_domisili'    => $kab_dom['nama'],
                    'kecamatan_domisili'    => $kec_dom['nama'],
                    'kelurahan_domisili'    => $kel_dom['nama'],
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
                    'lamp_buku_nikah'  => $val->pas['lamp_buku_nikah']
                ],
                'data_penjamin' => $penjamin,
                'status_hm'     => $val->status_hm,
                'catatan_hm'    => $val->catatan_hm
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

    public function update($id, Request $req, FasPinRequest $reqFasPin, DebtRequest $reqDebt, DebtPasanganRequest $reqPas, DebtPenjaminRequest $reqPen, UsahaRequest $reqUs, AguTaReq $reqAta, AguKenReq $reqAk, PemAgTaReq $reqPAT, PemAgKeReq $reqPAK, KapBulananReq $reqkapBul, TrAoReq $reqAo) {

        $Trans = TransSo::where('id', $id)->first();

        if ($Trans == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        $user_id     = $req->auth->user_id;
        $username    = $req->auth->user;

        $PIC = PIC::where('user_id', $user_id)->first();

        if ($PIC == null) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "User_ID anda adalah '".$user_id."' dengan username '".$username."' . Namun anda belum terdaftar sebagai PIC. Harap daftarkan diri sebagai PIC pada form PIC atau hubungi bagian IT"
            ], 404);
        }

        $countTSO = TransAo::count();

        if (!$countTSO) {
            $no = 1;
        }else{
            $no = $countTSO + 1;
        }

        //Data Transaksi SO
        $nows  = Carbon::now();
        $year  = $nows->year;
        $month = $nows->month;

        $JPIC   = JPIC::where('id', $PIC->id_mj_pic)->first();

        $nomor_so = $PIC->id_mk_cabang.'-'.$JPIC->nama_jenis.'-'.$month.'-'.$year.'-'.$no;


        $debitur  = Debitur::select('lamp_buku_tabungan')->where('id', $Trans->id_calon_debt)->first();
        $pasangan = Pasangan::select('lamp_ktp', 'lamp_buku_nikah')->where('id', $Trans->id_pasangan)->first();
        // $usaha    = Usaha::select('lamp_tempat_usaha')->where('id', $Trans->id_usaha)->first();
        $penjamin = Penjamin::select('pekerjaan', 'posisi_pekerjaan')->where('id_calon_debitur', $Trans->id_calon_debt)->get();
        $aTa      = AgunanTanah::where('id_calon_debitur', $Trans->id_calon_debt)->get();

        $lamp_dir = 'public/lamp_trans.'.$Trans->nomor_so;

        $now      = Carbon::now()->toDateTimeString();

        $idPenj   = $Trans->id_penjamin;
        $arIdPenj = explode (",",$idPenj);

        for ($i = 0; $i < count($reqDebt->nama_anak); $i++){
            $namaAnak[] = empty($reqDebt->nama_anak[$i]) ? null[$i] : $reqDebt->nama_anak[$i];

            $tglLahirAnak[] = empty($reqDebt->tgl_lahir_anak[$i]) ? null[$i] : Carbon::parse($reqDebt->tgl_lahir_anak[$i])->format('Y-m-d');
        }

        $nama_anak    = implode(",", $namaAnak);
        $tgl_lhr_anak = implode(",", $tglLahirAnak);

        if($file = $reqDebt->file('lamp_buku_tabungan')){
            $path = $lamp_dir.'/debitur';
            $name = 'buku_tabungan.'.$file->getClientOriginalExtension();
            $file->move($path,$name);

            $tabungan = $path.'/'.$name;
        }else{
            $tabungan = null;
        }

        if($file = $reqDebt->file('lamp_sku')){
            $path = $lamp_dir.'/debitur';
            $name = 'sku.'.$file->getClientOriginalExtension();
            $file->move($path,$name);

            $sku = $path.'/'.$name;
        }else{
            $sku = null;
        }

        if($file = $reqDebt->file('lamp_slip_gaji')){
            $path = $lamp_dir.'/debitur';
            $name = 'slip_gaji.'.$file->getClientOriginalExtension();
            $file->move($path,$name);

            $slipGaji = $path.'/'.$name;
        }else{
            $slipGaji = null;
        }

        if($file = $reqDebt->file('lamp_foto_usaha')){
            $path = $lamp_dir.'/debitur';
            $name = 'tempat_usaha.'.$file->getClientOriginalExtension();
            $file->move($path,$name);

            $fotoUsaha = $path.'/'.$name;
        }else{
            $fotoUsaha = null;
        }

        // Data Calon Debitur
        $dataDebitur = array(
            'tinggi_badan'          => empty($reqDebt->input('tinggi_badan')) ? $debitur->tinggi_badan : $reqDebt->input('tinggi_badan'),
            'berat_badan'           => empty($reqDebt->input('berat_badan')) ? $debitur->berat_badan : $reqDebt->input('berat_badan'),
            'nama_anak'             => $nama_anak,
            'tgl_lahir_anak'        => Carbon::parse($tgl_lhr_anak)->format('d-m-Y'),
            'alamat_surat'          => empty($reqDebt->input('alamat_surat')) ? $debitur->alamat_surat : $reqDebt->input('alamat_surat'),
            'pekerjaan'             => empty($reqDebt->input('pekerjaan')) ? $debitur->pekerjaan : $reqDebt->input('pekerjaan'),
            'posisi_pekerjaan'      => empty($reqDebt->input('posisi_pekerjaan')) ? $debitur->posisi_pekerjaan : $reqDebt->input('posisi_pekerjaan'),
            'nama_tempat_kerja'     => $reqDebt->input('nama_tempat_kerja'),
            'jenis_pekerjaan'       => $reqDebt->input('jenis_pekerjaan'),
            'alamat_tempat_kerja'   => $reqDebt->input('alamat_tempat_kerja'),
            'id_prov_tempat_kerja'  => $reqDebt->input('id_prov_tempat_kerja'),
            'id_kab_tempat_kerja'   => $reqDebt->input('id_kab_tempat_kerja'),
            'id_kec_tempat_kerja'   => $reqDebt->input('id_kec_tempat_kerja'),
            'id_kel_tempat_kerja'   => $reqDebt->input('id_kel_tempat_kerja'),
            'rt_tempat_kerja'       => $reqDebt->input('rt_tempat_kerja'),
            'rw_tempat_kerja'       => $reqDebt->input('rw_tempat_kerja'),
            'tgl_mulai_kerja'       => Carbon::parse($reqDebt->input('tgl_mulai_kerja'))->format('Y-m-d'),
            'no_telp_tempat_kerja'  => $reqDebt->input('no_telp_tempat_kerja'),
            'lamp_buku_tabungan'    => $tabungan,
            'lamp_sku'              => $sku,
            'lamp_slip_gaji'        => $slipGaji,
            'lamp_foto_usaha'       => $fotoUsaha
        );

        // Data Usaha Calon Debitur
        $dataPasangan = array(
            'nama_tempat_kerja'     => $reqPas->input('nama_tempat_kerja_pas'),
            'jenis_pekerjaan'       => $reqPas->input('jenis_pekerjaan_pas'),
            'alamat_tempat_kerja'   => $reqPas->input('alamat_tempat_kerja_pas'),
            'id_prov_tempat_kerja'  => $reqPas->input('id_prov_tempat_kerja_pas'),
            'id_kab_tempat_kerja'   => $reqPas->input('id_kab_tempat_kerja_pas'),
            'id_kec_tempat_kerja'   => $reqPas->input('id_kec_tempat_kerja_pas'),
            'id_kel_tempat_kerja'   => $reqPas->input('id_kel_tempat_kerja_pas'),
            'rt_tempat_kerja'       => $reqPas->input('rt_tempat_kerja_pas'),
            'rw_tempat_kerja'       => $reqPas->input('rw_tempat_kerja_pas'),
            'tgl_mulai_kerja'       => Carbon::parse($reqPas->input('tgl_mulai_kerja_pas'))->format('Y-m-d'),
            'no_telp_tempat_kerja'  => $reqPas->input('no_telp_tempat_kerja_pas')
        );

        $dataVerifikasi = array(
            'id_trans_so'             => $id,
            'id_calon_debitur'        => $Trans->id_calon_debt,
            'ver_ktp_debt'            => $req->input('ver_ktp_debt'),
            'ver_kk_debt'             => $req->input('ver_kk_debt'),
            'ver_akta_cerai_debt'     => $req->input('ver_akta_cerai_debt'),
            'ver_akta_kematian_debt'  => $req->input('ver_akta_kematian_debt'),
            'ver_rek_tabungan_debt'   => $req->input('ver_rek_tabungan_debt'),
            'ver_sertifikat_debt'     => $req->input('ver_sertifikat_debt'),
            'ver_sttp_pbb_debt'       => $req->input('ver_sttp_pbb_debt'),
            'ver_imb_debt'            => $req->input('ver_imb_debt'),
            'ver_ktp_pasangan'        => $req->input('ver_ktp_pasangan'),
            'ver_akta_nikah_pasangan' => $req->input('ver_akta_nikah_pasangan'),
            'ver_data_penjamin'       => $req->input('ver_data_penjamin'),
            'ver_sku_debt'            => $req->input('ver_sku_debt'),
            'ver_pembukuan_usaha_debt'=> $req->input('ver_pembukuan_usaha_debt'),
            'catatan'                 => $req->input('catatan_verifikasi')
        );

        $dataValidasi = array(
            'id_trans_so'         => $id,
            'id_calon_debitur'    => $Trans->id_calon_debt,
            'val_data_debt'       => $req->input('val_data_debt'),
            'val_lingkungan_debt' => $req->input('val_lingkungan_debt'),
            'val_domisili_debt'   => $req->input('val_domisili_debt'),
            'val_pekerjaan_debt'  => $req->input('val_pekerjaan_debt'),
            'val_data_pasangan'   => $req->input('val_data_pasangan'),
            'val_data_penjamin'   => $req->input('val_data_penjamin'),
            'val_agunan'          => $req->input('val_agunan'),
            'catatan'             => $req->input('catatan_validasi')
        );

        if($files = $reqAta->file('lamp_agunan_depan')){
            $a = 1;
            foreach($files as $file){
                $path = $lamp_dir.'/agunan_tanah';
                $name = 'agunan_depan'.$a.'.'.$file->getClientOriginalExtension();
                $file->move($path,$name);
                $a++;

                $agunanDepan[] = $path.'/'.$name;
            }
        }

        if ($files = $reqAk->file('lamp_agunan_depan_ken')) {
            $a = 1;
            foreach($files as $file){
                $path = $lamp_dir.'/agunan_kendaraan';
                $name = 'agunan_depan'.$a.'.'.$file->getClientOriginalExtension();
                $file->move($path,$name);
                $a++;

                $agunanDepanKen[] = $path.'/'.$name;
            }
        }

        if($files = $reqAta->file('lamp_agunan_kanan')){
            $a = 1;
            foreach($files as $file){
                $path = $lamp_dir.'/agunan_tanah';
                $name = 'agunan_kanan'.$a.'.'.$file->getClientOriginalExtension();
                $file->move($path,$name);
                $a++;

                $agunanKanan[] = $path.'/'.$name;
            }
        }

        if ($files = $reqAk->file('lamp_agunan_kanan_ken')) {
            $a = 1;
            foreach($files as $file){
                $path = $lamp_dir.'/agunan_kendaraan';
                $name = 'agunan_kanan'.$a.'.'.$file->getClientOriginalExtension();
                $file->move($path,$name);
                $a++;

                $agunanKananKen[] = $path.'/'.$name;
            }
        }

        if($files = $reqAta->file('lamp_agunan_kiri')){
            $a = 1;
            foreach($files as $file){
                $path = $lamp_dir.'/agunan_tanah';
                $name = 'agunan_kiri'.$a.'.'.$file->getClientOriginalExtension();
                $file->move($path,$name);
                $a++;

                $agunanKiri[] = $path.'/'.$name;
            }
        }

        if ($files = $reqAk->file('lamp_agunan_kiri_ken')) {
            $a = 1;
            foreach($files as $file){
                $path = $lamp_dir.'/agunan_kendaraan';
                $name = 'agunan_kiri'.$a.'.'.$file->getClientOriginalExtension();
                $file->move($path,$name);
                $a++;

                $agunanKiriKen[] = $path.'/'.$name;
            }
        }


        if($files = $reqAta->file('lamp_agunan_belakang')){
            $a = 1;
            foreach($files as $file){
                $path = $lamp_dir.'/agunan_tanah';
                $name = 'agunan_belakang'.$a.'.'.$file->getClientOriginalExtension();
                $file->move($path,$name);
                $a++;

                $agunanBelakang[] = $path.'/'.$name;
            }
        }

        if ($files = $reqAk->file('lamp_agunan_belakang_ken')) {
            $a = 1;
            foreach($files as $file){
                $path = $lamp_dir.'/agunan_kendaraan';
                $name = 'agunan_belakang'.$a.'.'.$file->getClientOriginalExtension();
                $file->move($path,$name);
                $a++;

                $agunanBelakangKen[] = $path.'/'.$name;
            }
        }


        if($files = $reqAta->file('lamp_agunan_dalam')){
            $a = 1;
            foreach($files as $file){
                $path = $lamp_dir.'/agunan_tanah';
                $name = 'agunan_dalam'.$a.'.'.$file->getClientOriginalExtension();
                $file->move($path,$name);
                $a++;

                $agunanDalam[] = $path.'/'.$name;
            }
        }

        if ($files = $reqAk->file('lamp_agunan_dalam_ken')) {
            $a = 1;
            foreach($files as $file){
                $path = $lamp_dir.'/agunan_kendaraan';
                $name = 'agunan_dalam'.$a.'.'.$file->getClientOriginalExtension();
                $file->move($path,$name);
                $a++;

                $agunanDalamKen[] = $path.'/'.$name;
            }
        }


        if (!empty($reqAta->input('tipe_lokasi_agunan'))) {
            for ($i = 0; $i < count($reqAta->input('tipe_lokasi_agunan')); $i++){
                $daAguTa[] = [
                    'id_calon_debitur'        => $Trans->id_calon_debt,
                    'tipe_lokasi'             => empty($reqAta->tipe_lokasi_agunan[$i]) ? null[$i] : strtoupper($reqAta->tipe_lokasi_agunan[$i]),
                    'alamat'                  => empty($reqAta->alamat_agunan[$i]) ? null[$i] : $reqAta->alamat_agunan[$i],
                    'id_provinsi'              => empty($reqAta->id_prov_agunan[$i]) ? null[$i] : $reqAta->id_prov_agunan[$i],
                    'id_kabupaten'            => empty($reqAta->id_kab_agunan[$i]) ? null[$i] : $reqAta->id_kab_agunan[$i],
                    'id_kecamatan'            => empty($reqAta->id_kec_agunan[$i]) ? null[$i] : $reqAta->id_kec_agunan[$i],
                    'id_kelurahan'            => empty($reqAta->id_kel_agunan[$i]) ? null[$i] : $reqAta->id_kel_agunan[$i],
                    'rt'                      => empty($reqAta->rt_agunan[$i]) ? null[$i] : $reqAta->rt_agunan[$i],
                    'rw'                      => empty($reqAta->rw_agunan[$i]) ? null[$i] : $reqAta->rw_agunan[$i],
                    'luas_tanah'              => empty($reqAta->luas_tanah[$i]) ? null[$i] : $reqAta->luas_tanah[$i],
                    'luas_bangunan'           => empty($reqAta->luas_bangunan[$i]) ? null[$i] : $reqAta->luas_bangunan[$i],
                    'nama_pemilik_sertifikat' => empty($reqAta->nama_pemilik_sertifikat[$i]) ? null[$i] : $reqAta->nama_pemilik_sertifikat[$i],
                    'jenis_sertifikat'        => empty($reqAta->jenis_sertifikat[$i]) ? null[$i] : strtoupper($reqAta->jenis_sertifikat[$i]),
                    'no_sertifikat'           => empty($reqAta->no_sertifikat[$i]) ? null[$i] : $reqAta->no_sertifikat[$i],
                    'tgl_ukur_sertifikat'     => empty($reqAta->tgl_ukur_sertifikat[$i]) ? null[$i] : Carbon::parse($reqAta->tgl_ukur_sertifikat[$i])->format('Y-m-d'),
                    'tgl_berlaku_shgb'        => empty($reqAta->tgl_berlaku_shgb[$i]) ? null[$i] : Carbon::parse($reqAta->tgl_berlaku_shgb[$i])->format('Y-m-d'),
                    'no_imb'                  => empty($reqAta->no_imb[$i]) ? null[$i] : $reqAta->no_imb[$i],
                    'njop'                    => empty($reqAta->njop[$i]) ? null[$i] : $reqAta->njop[$i],
                    'nop'                     => empty($reqAta->nop[$i]) ? null[$i] : $reqAta->nop[$i],
                    // 'lam_imb'                 => empty($reqAta->file('lam_imb')[$i]) ? null : Helper::img64enc($reqAta->file('lam_imb')[$i]),
                    'lamp_agunan_depan'       => empty($agunanDepan[$i]) ? null[$i] : $agunanDepan[$i],
                    'lamp_agunan_kanan'       => empty($agunanKanan[$i]) ? null[$i] : $agunanKanan[$i],
                    'lamp_agunan_kiri'        => empty($agunanKiriKen[$i]) ? null[$i] : $agunanKiriKen[$i],
                    'lamp_agunan_belakang'    => empty($agunanBelakang[$i]) ? null[$i] : $agunanBelakang[$i],
                    'lamp_agunan_dalam'       => empty($agunanDalamKen[$i]) ? null[$i] : $agunanDalamKen[$i],
                    // 'lamp_sertifikat'         => empty($reqAta->file('lamp_sertifikat')[$i]) ? null : Helper::img64enc($reqAta->file('lamp_sertifikat')[$i]),
                    // 'lamp_imb'                => empty($reqAta->file('lamp_imb')[$i]) ? null : Helper::img64enc($reqAta->file('lamp_imb')[$i]),
                    // 'lamp_pbb'                => empty($reqAta->file('lamp_pbb')[$i]) ? null : Helper::img64enc($reqAta->file('lamp_pbb')[$i])
                    'created_at'              => $now,
                    'updated_at'              => $now
                ];
            }
        }

        if (!empty($reqAk->input('no_bpkb_ken'))) {
            for ($i = 0; $i < count($reqAk->input('no_bpkb_ken')); $i++) {
                $daAguKe[] = [
                    'id_calon_debitur'      => $Trans->id_calon_debt,
                    'no_bpkb'               => empty($reqAk->no_bpkb_ken[$i]) ? null[$i] : $reqAk->no_bpkb_ken[$i],
                    'nama_pemilik'          => empty($reqAk->nama_pemilik_ken[$i]) ? null[$i] : $reqAk->nama_pemilik_ken[$i],
                    'alamat_pemilik'        => empty($reqAk->alamat_pemilik_ken[$i]) ? null[$i] : $reqAk->nama_pemilik_ken[$i],
                    'merk'                  => empty($reqAk->merk_ken[$i]) ? null[$i] : $reqAk->merk_ken[$i],
                    'jenis'                 => empty($reqAk->jenis_ken[$i]) ? null[$i] : $reqAk->jenis_ken[$i],
                    'no_rangka'             => empty($reqAk->no_rangka_ken[$i]) ? null[$i] : $reqAk->no_rangka_ken[$i],
                    'no_mesin'              => empty($reqAk->no_mesin_ken[$i]) ? null[$i] : $reqAk->no_mesin_ken[$i],
                    'warna'                 => empty($reqAk->warna_ken[$i]) ? null[$i] : $reqAk->warna_ken[$i],
                    'tahun'                 => empty($reqAk->tahun_ken[$i]) ? null[$i] : $reqAk->tahun_ken[$i],
                    'no_polisi'             => empty($reqAk->no_polisi_ken[$i]) ? null[$i] : strtoupper($reqAk->no_polisi_ken[$i]),
                    'no_stnk'               => empty($reqAk->no_stnk_ken[$i]) ? null[$i] : $reqAk->no_stnk_ken[$i],
                    'tgl_kadaluarsa_pajak'  => empty($reqAk->tgl_exp_pajak_ken[$i]) ? null[$i] : Carbon::parse($reqAk->tgl_exp_pajak_ken[$i])->format('Y-m-d'),
                    'tgl_kadaluarsa_stnk'   => empty($reqAk->tgl_exp_stnk_ken[$i]) ? null[$i] : Carbon::parse($reqAk->tgl_exp_stnk_ken[$i])->format('Y-m-d'),
                    'no_faktur'             => empty($reqAk->no_faktur_ken[$i]) ? null[$i] : $reqAk->no_faktur_ken[$i],
                    'lamp_agunan_depan'     => empty($agunanDepanKen[$i]) ? null[$i] : $agunanDepanKen[$i],
                    'lamp_agunan_kanan'     => empty($agunanKananKen[$i]) ? null[$i] : $agunanKananKen[$i],
                    'lamp_agunan_kiri'      => empty($agunanKiriKen[$i]) ? null[$i] : $agunanKiriKen[$i],
                    'lamp_agunan_belakang'  => empty($agunanBelakangKen[$i]) ? null[$i] : $agunanBelakangKen[$i],
                    'lamp_agunan_dalam'     => empty($agunanDalamKen[$i]) ? null[$i] : $agunanDalamKen[$i],
                    'created_at'            => $now,
                    'updated_at'            => $now
                ];
            }
        }

        $kapBul = array(
            'id_calon_debitur'      => $Trans->id_calon_debt,
            'pemasukan_cadebt'      => empty($reqkapBul->input('pemasukan_debitur')) ? null : (int) $reqkapBul->input('pemasukan_debitur'),
            'pemasukan_pasangan'    => empty($reqkapBul->input('pemasukan_pasangan')) ? null : (int) $reqkapBul->input('pemasukan_pasangan'),
            'pemasukan_penjamin'    => empty($reqkapBul->input('pemasukan_penjamin')) ? null : (int) $reqkapBul->input('pemasukan_penjamin'),
            'biaya_rumah_tangga'    => empty($reqkapBul->input('biaya_rumah_tangga')) ? null : (int) $reqkapBul->input('biaya_rumah_tangga'),
            'biaya_transport'       => empty($reqkapBul->input('biaya_transport')) ? null : (int) $reqkapBul->input('biaya_transport'),
            'biaya_pendidikan'      => empty($reqkapBul->input('biaya_pendidikan')) ? null : (int) $reqkapBul->input('biaya_pendidikan'),
            'biaya_telp_listr_air'  => empty($reqkapBul->input('biaya_telp_listr_air')) ? null : (int) $reqkapBul->input('biaya_telp_listr_air'),
            'biaya_lain'            => empty($reqkapBul->input('biaya_lain')) ? null : (int) $reqkapBul->input('biaya_lain'),

            'total_pemasukan'       => (empty($reqkapBul->input('pemasukan_debitur')) ? 0 : $reqkapBul->input('pemasukan_debitur')) + (empty($reqkapBul->input('pemasuk + an_pasangan')) ? 0 : $reqkapBul->input('pemasukan_pasangan')) + (empty($reqkapBul->input('pemasukan_penjamin')) ? 0 : $reqkapBul->input('pemasukan_penjamin')),
            'total_pengeluaran'     => (empty($reqkapBul->input('biaya_rumah_tangga')) ? 0 : $reqkapBul->input('biaya_rumah_tangga')) + (empty($reqkapBul->input('biaya_transport')) ? 0 : $reqkapBul->input('biaya_transport')) + (empty($reqkapBul->input('biaya_pendidikan')) ? 0 : $reqkapBul->input('biaya_pendidikan')) + (empty($reqkapBul->input('biaya_telp_listr_air')) ? 0 : $reqkapBul->input('biaya_telp_listr_air')) + (empty($reqkapBul->input('biaya_lain')) ? 0 : $reqkapBul->input('biaya_lain')),
            'penghasilan_bersih'    => ((empty($reqkapBul->input('pemasukan_debitur')) ? 0 : $reqkapBul->input('pemasukan_debitur')) + (empty($reqkapBul->input('pemasuk + an_pasangan')) ? 0 : $reqkapBul->input('pemasukan_pasangan')) + (empty($reqkapBul->input('pemasukan_penjamin')) ? 0 : $reqkapBul->input('pemasukan_penjamin'))) - ((empty($reqkapBul->input('biaya_rumah_tangga')) ? 0 : $reqkapBul->input('biaya_rumah_tangga')) + (empty($reqkapBul->input('biaya_transport')) ? 0 : $reqkapBul->input('biaya_transport')) + (empty($reqkapBul->input('biaya_pendidikan')) ? 0 : $reqkapBul->input('biaya_pendidikan')) + (empty($reqkapBul->input('biaya_telp_listr_air')) ? 0 : $reqkapBul->input('biaya_telp_listr_air')) + (empty($reqkapBul->input('biaya_lain')) ? 0 : $reqkapBul->input('biaya_lain')))
        );

        $TransAO = array(
            'nomor_ao'              => $noAO,
            'id_trans_so'           => $id,
            'produk'                => $reqAo->input('produk'),
            'plafon_kredit'         => $reqAo->input('plafon_kredit'),
            'jangka_waktu'          => $reqAo->input('jangka_waktu'),
            'suku_bunga'            => $reqAo->input('suku_bunga'),
            'pembayaran_bunga'      => $reqAo->input('pembayaran_bunga'),
            'akad_kredit'           => $reqAo->input('akad_kredit'),
            'ikatan_agunan'         => $reqAo->input('ikatan_agunan'),
            'analisa_ao'            => $reqAo->input('analisa_ao'),
            'biaya_provisi'         => $reqAo->input('biaya_provisi'),
            'biaya_administrasi'    => $reqAo->input('biaya_administrasi'),
            'biaya_credit_checking' => $reqAo->input('biaya_credit_checking'),
            'biaya_tabungan'        => $reqAo->input('biaya_tabungan')
        );

        $dataKeUsaha = array(
            'id_calon_debitur'     => $Trans->id_calon_debt,
            'pemasukan_tunai'      => empty($req->input('pemasukan_tunai')) ? null : (int) $req->input('pemasukan_tunai'),
            'pemasukan_kredit'     => empty($req->input('pemasukan_kredit')) ? null : (int) $req->input('pemasukan_kredit'),
            'biaya_sewa'           => empty($req->input('biaya_sewa')) ? null : (int) $req->input('biaya_sewa'),
            'biaya_gaji_pegawai'   => empty($req->input('biaya_gaji_pegawai')) ? null : (int) $req->input('biaya_gaji_pegawai'),
            'biaya_belanja_brg'    => empty($req->input('biaya_belanja_brg')) ? null : (int) $req->input('biaya_belanja_brg'),
            'biaya_telp_listr_air' => empty($req->input('biaya_telp_listr_air')) ? null : (int) $req->input('biaya_telp_listr_air'),
            'biaya_sampah_kemanan' => empty($req->input('biaya_sampah_kemanan')) ? null : (int) $req->input('biaya_sampah_kemanan'),
            'biaya_kirim_barang'   => empty($req->input('biaya_kirim_barang')) ? null : (int) $req->input('biaya_kirim_barang'),
            'biaya_hutang_dagang'  => empty($req->input('biaya_hutang_dagang')) ? null : (int) $req->input('biaya_hutang_dagang'),
            'biaya_angsuran'       => empty($req->input('biaya_angsuran')) ? null : (int) $req->input('biaya_angsuran'),
            'biaya_lain_lain'      => empty($req->input('biaya_lain_lain')) ? null : (int) $req->input('biaya_lain_lain'),
            'total_pemasukan'      => (empty($req->input('pemasukan_tunai')) ? 0 : $req->input('pemasukan_tunai')) + (empty($req->input('pemasukan_kredit')) ? 0 : $req->input('pemasukan_kredit')),
            'total_pengeluaran'    => (empty($req->input('biaya_sewa')) ? 0 : $req->input('biaya_sewa')) + (empty($req->input('biaya_gaji_pegawai')) ? 0 : $req->input('biaya_gaji_pegawai')) + (empty($req->input('biaya_belanja_brg')) ? 0 : $req->input('biaya_belanja_brg')) + (empty($req->input('biaya_telp_listr_air')) ? 0 : $req->input('biaya_telp_listr_air')) + (empty($req->input('biaya_sampah_kemanan')) ? 0 : $req->input('biaya_sampah_kemanan')) + (empty($req->input('biaya_kirim_barang')) ? 0 : $req->input('biaya_kirim_barang')) + (empty($req->input('biaya_hutang_dagang')) ? 0 : $req->input('biaya_hutang_dagang')) + (empty($req->input('biaya_angsuran')) ? 0 : $req->input('biaya_angsuran')) + (empty($req->input('biaya_lain_lain')) ? 0 : $req->input('biaya_lain_lain')),
            'laba_usaha'           => ((empty($req->input('pemasukan_tunai')) ? 0 : $req->input('pemasukan_tunai')) + (empty($req->input('pemasukan_kredit')) ? 0 : $req->input('pemasukan_kredit'))) - ((empty($req->input('biaya_sewa')) ? 0 : $req->input('biaya_sewa')) + (empty($req->input('biaya_gaji_pegawai')) ? 0 : $req->input('biaya_gaji_pegawai')) + (empty($req->input('biaya_belanja_brg')) ? 0 : $req->input('biaya_belanja_brg')) + (empty($req->input('biaya_telp_listr_air')) ? 0 : $req->input('biaya_telp_listr_air')) + (empty($req->input('biaya_sampah_kemanan')) ? 0 : $req->input('biaya_sampah_kemanan')) + (empty($req->input('biaya_kirim_barang')) ? 0 : $req->input('biaya_kirim_barang')) + (empty($req->input('biaya_hutang_dagang')) ? 0 : $req->input('biaya_hutang_dagang')) + (empty($req->input('biaya_angsuran')) ? 0 : $req->input('biaya_angsuran')) + (empty($req->input('biaya_lain_lain')) ? 0 : $req->input('biaya_lain_lain')))
        );

        DB::connection('web')->beginTransaction();
        try {

            Debitur::where('id', $Trans->id_calon_debt)->update($dataDebitur);
            Pasangan::where('id', $Trans->id_pasangan)->update($dataPasangan);

            for ($i = 0; $i < count($arIdPenj); $i++){
                $dataPenjamin[] = array(
                    'pekerjaan'             => empty($reqPen->pekerjaan_pen[$i]) ? null[$i] : $reqPen->pekerjaan_pen[$i],
                    'posisi_pekerjaan'      => empty($reqPen->posisi_pekerjaan_pen[$i]) ? null[$i] : $reqPen->posisi_pekerjaan_pen[$i],
                    'nama_tempat_kerja'     => empty($reqPen->nama_tempat_kerja_pen[$i]) ? null[$i] : $reqPen->nama_tempat_kerja_pen[$i],
                    'jenis_pekerjaan'       => empty($reqPen->jenis_pekerjaan_pen[$i]) ? null[$i] : $reqPen->jenis_pekerjaan_pen[$i],
                    'alamat_tempat_kerja'   => empty($reqPen->alamat_tempat_kerja_pen[$i]) ? null[$i] : $reqPen->alamat_tempat_kerja_pen[$i],
                    'id_prov_tempat_kerja'  => empty($reqPen->id_prov_tempat_kerja_pen[$i]) ? null[$i] : $reqPen->id_prov_tempat_kerja_pen[$i],
                    'id_kab_tempat_kerja'   => empty($reqPen->id_kab_tempat_kerja_pen[$i]) ? null[$i] : $reqPen->id_kab_tempat_kerja_pen[$i],
                    'id_kec_tempat_kerja'   => empty($reqPen->id_kec_tempat_kerja_pen[$i]) ? null[$i] : $reqPen->id_kec_tempat_kerja_pen[$i],
                    'id_kel_tempat_kerja'   => empty($reqPen->id_kel_tempat_kerja_pen[$i]) ? null[$i] : $reqPen->id_kel_tempat_kerja_pen[$i],
                    'rt_tempat_kerja'       => empty($reqPen->rt_tempat_kerja_pen[$i]) ? null[$i] : $reqPen->rt_tempat_kerja_pen[$i],
                    'rw_tempat_kerja'       => empty($reqPen->rw_tempat_kerja_pen[$i]) ? null[$i] : $reqPen->rw_tempat_kerja_pen[$i],
                    'tgl_mulai_kerja'       => empty($reqPen->tgl_mulai_kerja_pen[$i]) ? null[$i] : Carbon::parse($reqPen->tgl_mulai_kerja_pen[$i])->format('Y-m-d'),,
                    'no_telp_tempat_kerja'  => empty($reqPen->no_telp_tempat_kerja_pen[$i]) ? null[$i] : $reqPen->no_telp_tempat_kerja_pen[$i]
                );

                $pen = Penjamin::where('id', $arIdPenj[$i])->update($dataPenjamin[$i]);
            }

            VerifModel::updateOrCreate($dataVerifikasi);
            ValidModel::updateOrCreate($dataValidasi);

            if (!empty($reqAta->input('tipe_lokasi_agunan'))){
                AgunanTanah::updateOrCreate($daAguTa);

                for ($i = 0; $i < count($reqAta->input('tipe_lokasi_agunan')); $i++){
                    $pemAguTa[] = [
                        'id_calon_debitur'      => $Trans->id_calon_debt,
                        'id_agunan_tanah'       => $id_AguTa[$i],
                        'nama_penghuni'         => empty($reqPAT->nama_penghuni_agunan[$i]) ? null[$i] : $reqPAT->nama_penghuni_agunan[$i],
                        'status_penghuni'       => empty($reqPAT->status_penghuni_agunan[$i]) ? null[$i] : strtoupper($reqPAT->status_penghuni_agunan[$i]),
                        'bentuk_bangunan'       => empty($reqPAT->bentuk_bangunan_agunan[$i]) ? null[$i] : $reqPAT->bentuk_bangunan_agunan[$i],
                        'kondisi_bangunan'      => empty($reqPAT->kondisi_bangunan_agunan[$i]) ? null[$i] : $reqPAT->kondisi_bangunan_agunan[$i],
                        'fasilitas'             => empty($reqPAT->fasilitas_agunan[$i]) ? null[$i] : $reqPAT->fasilitas_agunan[$i],
                        'listrik'               => empty($reqPAT->listrik_agunan[$i]) ? null[$i] : $reqPAT->listrik_agunan[$i],
                        'nilai_taksasi_agunan'  => empty($reqPAT->nilai_taksasi_agunan[$i]) ? null[$i] : $reqPAT->nilai_taksasi_agunan[$i],
                        'nilai_taksasi_bangunan'=> empty($reqPAT->nilai_taksasi_bangunan[$i]) ? null[$i] : $reqPAT->nilai_taksasi_bangunan[$i],
                        'tgl_taksasi'           => empty($reqPAT->tgl_taksasi_agunan[$i]) ? null[$i] : Carbon::parse($reqPAT->tgl_taksasi_agunan[$i])->format('Y-m-d'),
                        'nilai_likuidasi'       => empty($reqPAT->nilai_likuidasi_agunan[$i]) ? null[$i] : $reqPAT->nilai_likuidasi_agunan[$i],
                        'created_at'            => $now,
                        'updated_at'            => $now
                    ];

                    PemeriksaanAgunTan::updateOrCreate($pemAguTa[$i]);
                }
            }

            $getAguta = AgunanTanah::select('id')->where('id_calon_debitur', $Trans->id_calon_debt)->get();

            $i  = 0;
            if ($getAguta != '[]') {
                $At = array();
                foreach ($getAguta as $val) {
                    $At['id'][$i] = $val->id;
                    $i++;
                }

                $id_AguTa = implode(",", $At['id']);
            }else{
                $id_AguTa = null;
            }

            if (!empty($reqAk->input('no_bpkb_ken'))){
               AgunanKendaraan::updateOrCreate($daAguKe);

                for ($i = 0; $i < count($reqAk->input('no_bpkb_ken')); $i++){
                    $pemAguKe[] = [
                        'id_calon_debitur'      => $Trans->id_calon_debt,
                        'id_agunan_kendaraan'   => $id_AguKe[$i],
                        'nama_pengguna'         => empty($reqPAK->nama_pengguna_ken[$i]) ? null[$i] : $reqPAK->nama_pengguna_ken[$i],
                        'status_pengguna'       => empty($reqPAK->status_pengguna_ken[$i]) ? null[$i] : strtoupper($reqPAK->status_pengguna_ken[$i]),
                        'jml_roda_kendaraan'    => empty($reqPAK->jml_roda_ken[$i]) ? null[$i] : $reqPAK->jml_roda_ken[$i],
                        'kondisi_kendaraan'     => empty($reqPAK->kondisi_ken[$i]) ? null[$i] : $reqPAK->kondisi_ken[$i],
                        'keberadaan_kendaraan'  => empty($reqPAK->keberadaan_ken[$i]) ? null[$i] : $reqPAK->keberadaan_ken[$i],
                        'body'                  => empty($reqPAK->body_ken[$i]) ? null[$i] : $reqPAK->body_ken[$i],
                        'interior'              => empty($reqPAK->interior_ken[$i]) ? null[$i] : $reqPAK->interior_ken[$i],
                        'km'                    => empty($reqPAK->km_ken[$i]) ? null[$i] : $reqPAK->km_ken[$i],
                        'modifikasi'            => empty($reqPAK->modifikasi_ken[$i]) ? null[$i] : $reqPAK->modifikasi_ken[$i],
                        'aksesoris'             => empty($reqPAK->aksesoris_ken[$i]) ? null[$i] : $reqPAK->aksesoris_ken[$i],
                        'created_at'            => $now,
                        'updated_at'            => $now
                    ];

                    PemeriksaanAgunKen::updateOrCreate($pemAguKe[$i]);
                }
            }

            $getAguKe = AgunanKendaraan::select('id')->where('id_calon_debitur', $Trans->id_calon_debt)->get();

            if ($getAguKe != '[]') {
                foreach ($getAguKe as $val) {
                    $Ak['id'][$i] = $val->id;
                    $i++;
                }

                $id_AguKe = implode(",", $Ak['id']);
            }else{
                $id_AguKe = null;
            }

            $getAguTa = PemeriksaanAgunTan::select('id')->where('id_calon_debitur', $Trans->id_calon_debt)->get();

            if ($getAguTa != '[]') {
                foreach ($getAguTa as $val) {
                    $PAT['id'][$i] = $val->id;
                    $i++;
                }

                $id_PAT = implode(",", $PAT['id']);
            }else{
                $id_PAT = null;
            }

            $getAguKe = PemeriksaanAgunKen::select('id')->where('id_calon_debitur', $Trans->id_calon_debt)->get();

            if ($getAguKe != '[]') {
                foreach ($getAguKe as $val) {
                    $PAK['id'][$i] = $val->id;
                    $i++;
                }

                $id_PAK = implode(",", $PAK['id']);
            }else{
                $id_PAK = null;
            }


            $KB = KapBulanan::updateOrCreate($kapBul);
            $KU = KeuanganUsaha::updateOrCreate($dataKeUsaha);
            $RAO = TransAO::updateOrCreate($TransAO);

            TransSo::where('id', $Trans->id)->update([
                'id_agunan_tanah'             => $id_AguTa,
                'id_agunan_kendaraan'         => $id_AguKe,
                'id_periksa_agunan_tanah'     => $id_PAT,
                'id_periksa_agunan_kendaraan' => $id_PAK,
                'id_usaha'                    => $KU->id
            ]);

            DB::connection('web')->commit();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message'=> 'Data berhasil dibuat'
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
