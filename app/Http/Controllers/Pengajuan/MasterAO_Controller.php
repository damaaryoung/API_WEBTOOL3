<?php

namespace App\Http\Controllers\Pengajuan;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use App\Http\Requests\Bisnis\BlankRequest;
use Illuminate\Support\Facades\File;
use App\Models\CC\PemeriksaanAgunTan;
use App\Models\CC\PemeriksaanAgunKen;
use App\Models\CC\AgunanKendaraan;
use App\Models\CC\AgunanTanah;
use App\Models\AreaKantor\Cabang;
use App\Models\AreaKantor\JPIC;
use App\Models\AreaKantor\PIC;
use App\Models\Bisnis\TransAO;
use App\Models\Bisnis\TransSo;
use Illuminate\Http\Request;
use App\Models\CC\Penjamin;
use App\Http\Requests;
use App\Models\User;
use Carbon\Carbon;
use DB;

class MasterAO_Controller extends BaseController
{
    public function index(Request $req){
        $kode_kantor = $req->auth->kd_cabang;

        $query = TransSo::where('kode_kantor', $kode_kantor)->where('status_hm', 1)->get();

        if ($query == '[]') {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

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
                'user_id'        => $val->user_id,
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

    public function show($id, Request $req){
        $val = TransSo::where('id', $id)->first();

        if (!$val) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        $id_penj = explode (",",$val->id_penjamin);

        $penjamin = Penjamin::whereIn('id', $id_penj)->get();

        foreach ($penjamin as $key => $value) {
            $pen[$key] = [
                "id"                    => $value->id,
                "nama_ktp"              => $value->nama_ktp,
                "nama_ibu_kandung"      => $value->nama_ibu_kandung,
                "no_ktp"                => $value->no_ktp,
                "no_npwp"               => $value->no_npwp,
                "tempat_lahir"          => $value->tempat_lahir,
                "tgl_lahir"             => Carbon::parse($value->tgl_lahir)->format('d-m-Y'),
                "jenis_kelamin"         => $value->jenis_kelamin,
                "alamat_ktp"            => $value->alamat_ktp,
                "no_telp"               => $value->no_telp,
                "hubungan_debitur"      => $value->hubungan_debitur,
                "pekerjaan"             => $value->pekerjaan,
                "posisi_pekerjaan"      => $value->posisi_pekerjaan,
                "nama_tempat_kerja"     => $value->nama_tempat_kerja,
                "jenis_pekerjaan"       => $value->jenis_pekerjaan,
                "alamat_tempat_kerja"   => $value->alamat_tempat_kerja,
                "id_prov_tempat_kerja"  => $value->id_prov_tempat_kerja,
                "nama_prov_tempat_kerja"=> $value->prov_kerja['nama'],
                "id_kab_tempat_kerja"   => $value->id_kab_tempat_kerja,
                "nama_kab_tempat_kerja" => $value->kab_kerja['nama'],
                "id_kec_tempat_kerja"   => $value->id_kec_tempat_kerja,
                "nama_kec_tempat_kerja" => $value->kec_kerja['nama'],
                "id_kel_tempat_kerja"   => $value->id_kel_tempat_kerja,
                "nama_kel_tempat_kerja" => $value->kel_kerja['nama'],
                "kode_pos_tempat_kerja" => $value->kel_kerja['kode_pos'],
                "rt_tempat_kerja"       => $value->rt_tempat_kerja,
                "rw_tempat_kerja"       => $value->rw_tempat_kerja,
                "tgl_mulai_kerja"       => Carbon::parse($value->tgl_mulai_kerja)->format('d-m-Y'),
                "no_telp_tempat_kerja"  => $value->no_telp_tempat_kerja,
                "lamp_ktp"              => $value->lamp_ktp,
                "lamp_ktp_pasangan"     => $value->lamp_ktp_pasangan,
                "lamp_kk"               => $value->lamp_kk,
                "lamp_buku_nikah"       => $value->lamp_buku_nikah
            ];
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

        $data[] = [
            'id'             => $val->id,
            'nomor_so'       => $val->nomor_so,
            'kode_kantor'    => $val->kode_kantor,
            'asal_data'      => $val->asaldata['nama'],
            'nama_marketing' => $val->nama_marketing,
            'nama_so'        => $val->nama_so,
            'plafon'         => (int) $val->faspin->plafon,
            'tenor'          => (int) $val->faspin->tenor,
            'fasilitas_pinjaman'  => [
                'id'              => $val->id_fasilitas_pinjaman,
                'jenis_pinjaman'  => $val->faspin->jenis_pinjaman,
                'tujuan_pinjaman' => $val->faspin->tujuan_pinjaman
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
                'no_ktp_kk'             => $val->debt[''],
                'no_kk'                 => $val->debt['no_ktp_kk'],
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
                'id_provinsi_domisili'  => $val->debt['id_prov_domisili'],
                'provinsi_domisili'     => $val->debt['prov_dom']['nama'],
                'id_kabupaten_domisili' => $val->debt['id_kab_domisili'],
                'kabupaten_domisili'    => $val->debt['kab_dom']['nama'],
                'id_kecamatan_domisili' => $val->debt['id_kec_domisili'],
                'kecamatan_domisili'    => $val->debt['kec_dom']['nama'],
                'id_kelurahan_domisili' => $val->debt['id_kel_domisili'],
                'kelurahan_domisili'    => $val->debt['kel_dom']['nama'],
                'kode_pos_domisili'     => $val->debt['kel_dom']['kode_pos'],
                'pendidikan_terakhir'   => $val->debt['pendidikan_terakhir'],
                'jumlah_tanggungan'     => $val->debt['jumlah_tanggungan'],
                'no_telp'               => $val->debt['no_telp'],
                'no_hp'                 => $val->debt['no_hp'],
                'alamat_surat'          => $val->debt['alamat_surat'],
                'lamp_ktp'              => $val->debt['lamp_ktp'],
                'lamp_kk'               => $val->debt['lamp_kk'],
                'lamp_buku_tabungan'    => $val->debt['lamp_buku_tabungan'],
                'lamp_sertifikat'       => $val->debt['lamp_sertifikat'],
                'lamp_sttp_pbb'         => $val->debt['lamp_sttp_pbb'],
                'lamp_imb'              => $val->debt['lamp_imb']
            ],
            'data_pasangan' => [
                'id'               => $val->id_pasangan,
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
            'data_penjamin' => $pen,
            'das_status'    => $status_das,
            'das_note'      => $val->catatan_das,
            'hm_status'     => $status_das,
            'hm_note'       => $val->catatan_hm
        ];

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

    public function update($id, Request $request, BlankRequest $req) {

        $user_id  = $request->auth->user_id;
        $username = $request->auth->user;

        $PIC = PIC::where('user_id', $user_id)->first();

        if ($PIC == null) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "User_ID anda adalah '".$user_id."' dengan username '".$username."' . Namun anda belum terdaftar sebagai PIC. Harap daftarkan diri sebagai PIC pada form PIC atau hubungi bagian IT"
            ], 404);
        }

        $countTAO = TransAo::latest('id','nomor_ao')->first();

        if (!$countTAO) {
            $lastNumb = 1;
        }else{
            $no = $countTAO->nomor_ao;

            $arr = explode("-", $no, 5);

            $lastNumb = $arr[4] + 1;
        }

        //Data Transaksi SO
        $nows  = Carbon::now();
        $year  = $nows->year;
        $month = $nows->month;

        $JPIC   = JPIC::where('id', $PIC->id_mj_pic)->first();

        //  ID-Cabang - AO / CA / SO - Bulan - Tahun - NO. Urut
        $nomor_ao = $PIC->id_mk_cabang.'-'.$JPIC->nama_jenis.'-'.$month.'-'.$year.'-'.$lastNumb;

        $check = TransSo::where('id',$id)->first();
        $id_penj = explode (",",$check->id_penjamin);

        if (!$check) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        $TransAO = array(
            'nomor_ao'              => $nomor_ao,
            'id_trans_so'           => $id,
            'user_id'               => $user_id,
            'kode_kantor'           => $PIC->id_mk_cabang,
            'nama_ao'               => $PIC->nama,
            'produk'                => $req->input('produk'),
            'plafon_kredit'         => $req->input('plafon_kredit'),
            'jangka_waktu'          => $req->input('jangka_waktu'),
            'suku_bunga'            => $req->input('suku_bunga'),
            'pembayaran_bunga'      => $req->input('pembayaran_bunga'),
            'akad_kredit'           => $req->input('akad_kredit'),
            'ikatan_agunan'         => $req->input('ikatan_agunan'),
            'analisa_ao'            => $req->input('analisa_ao'),
            'biaya_provisi'         => $req->input('biaya_provisi'),
            'biaya_administrasi'    => $req->input('biaya_administrasi'),
            'biaya_credit_checking' => $req->input('biaya_credit_checking'),
            'biaya_tabungan'        => $req->input('biaya_tabungan'),
            'catatan_ao'            => $req->input('catatan_ao'),
            'status_ao'             => $req->input('status_ao')
        );

        if ($TransAO['status_ao'] == 1) {
            $msg = 'berhasil menyetujui data';
        }elseif ($TransAO['status_ao'] == 2) {
            $msg = 'berhasil menolak data';
        }else{
            $msg = 'waiting proccess';
        }

        $penjamin = Penjamin::whereIn('id', $id_penj)->get();

        $lamp_dir = 'public/lamp_trans.'.$check->nomor_so;

        $now   = Carbon::now()->toDateTimeString();

        // dd(sizeof($req->nama_anak));

        for ($i = 0; $i < count($req->nama_anak); $i++){
            $namaAnak[] = empty($req->nama_anak[$i]) ? null[$i] : $req->nama_anak[$i];

            $tglLahirAnak[] = empty($req->tgl_lahir_anak[$i]) ? null[$i] : Carbon::parse($req->tgl_lahir_anak[$i])->format('Y-m-d');
        }

        $nama_anak    = implode(",", $namaAnak);
        $tgl_lhr_anak = implode(",", $tglLahirAnak);

        if($file = $req->file('lamp_buku_tabungan')){
            $path = $lamp_dir.'/debitur';
            $name = 'buku_tabungan.'.$file->getClientOriginalExtension();

            if(!empty($check->debt['lamp_buku_tabungan']))
            {
                File::delete($check->debt['lamp_buku_tabungan']);
            }

            $file->move($path,$name);

            $tabungan = $path.'/'.$name;
        }else{
            $tabungan = $check->debt['lamp_buku_tabungan'];
        }

        if($file = $req->file('lamp_sku')){
            $path = $lamp_dir.'/debitur';
            $name = 'sku.'.$file->getClientOriginalExtension();

            if(!empty($check->debt['lamp_sku']))
            {
                File::delete($check->debt['lamp_sku']);
            }

            $file->move($path,$name);

            $sku = $path.'/'.$name;
        }else{
            $sku = $check->debr['lamp_sku'];
        }

        if($file = $req->file('lamp_slip_gaji')){
            $path = $lamp_dir.'/debitur';
            $name = 'slip_gaji.'.$file->getClientOriginalExtension();

            if(!empty($check->debt['lamp_slip_gaji']))
            {
                File::delete($check->debt['lamp_slip_gaji']);
            }

            $file->move($path,$name);

            $slipGaji = $path.'/'.$name;
        }else{
            $slipGaji = $check->debt['lamp_slip_gaji'];
        }

        if($file = $req->file('lamp_foto_usaha')){
            $path = $lamp_dir.'/debitur';
            $name = 'tempat_usaha.'.$file->getClientOriginalExtension();

            if(!empty($check->debt['lamp_foto_usaha']))
            {
                File::delete($check->debt['lamp_foto_usaha']);
            }

            $file->move($path,$name);

            $fotoUsaha = $path.'/'.$name;
        }else{
            $fotoUsaha = $check->debt['lamp_foto_usaha'];
        }

        // Data Calon Debitur
        $dataDebitur = array(
            'tinggi_badan'          => empty($req->input('tinggi_badan')) ? $check->debt['tinggi_badan'] : $req->input('tinggi_badan'),
            'berat_badan'           => empty($req->input('berat_badan')) ? $check->debt['berat_badan'] : $req->input('berat_badan'),
            'nama_anak'             => $nama_anak,
            'tgl_lahir_anak'        => $tgl_lhr_anak,
            'alamat_surat'          => empty($req->input('alamat_surat')) ? $check->debt['alamat_surat'] : $req->input('alamat_surat'),
            'pekerjaan'             => empty($req->input('pekerjaan')) ? $check->debt['pekerjaan'] : $req->input('pekerjaan'),
            'posisi_pekerjaan'      => empty($req->input('posisi_pekerjaan')) ? $check->debt['posisi_pekerjaan'] : $req->input('posisi_pekerjaan'),
            'nama_tempat_kerja'     => $req->input('nama_tempat_kerja'),
            'jenis_pekerjaan'       => $req->input('jenis_pekerjaan'),
            'alamat_tempat_kerja'   => $req->input('alamat_tempat_kerja'),
            'id_prov_tempat_kerja'  => $req->input('id_prov_tempat_kerja'),
            'id_kab_tempat_kerja'   => $req->input('id_kab_tempat_kerja'),
            'id_kec_tempat_kerja'   => $req->input('id_kec_tempat_kerja'),
            'id_kel_tempat_kerja'   => $req->input('id_kel_tempat_kerja'),
            'rt_tempat_kerja'       => $req->input('rt_tempat_kerja'),
            'rw_tempat_kerja'       => $req->input('rw_tempat_kerja'),
            'tgl_mulai_kerja'       => Carbon::parse($req->input('tgl_mulai_kerja'))->format('Y-m-d'),
            'no_telp_tempat_kerja'  => $req->input('no_telp_tempat_kerja'),
            'lamp_buku_tabungan'    => $tabungan,
            'lamp_sku'              => $sku,
            'lamp_slip_gaji'        => $slipGaji,
            'lamp_foto_usaha'       => $fotoUsaha
        );

        // Data Usaha Calon Debitur
        $dataPasangan = array(
            'nama_tempat_kerja'     => $req->input('nama_tempat_kerja_pas'),
            'jenis_pekerjaan'       => $req->input('jenis_pekerjaan_pas'),
            'alamat_tempat_kerja'   => $req->input('alamat_tempat_kerja_pas'),
            'id_prov_tempat_kerja'  => $req->input('id_prov_tempat_kerja_pas'),
            'id_kab_tempat_kerja'   => $req->input('id_kab_tempat_kerja_pas'),
            'id_kec_tempat_kerja'   => $req->input('id_kec_tempat_kerja_pas'),
            'id_kel_tempat_kerja'   => $req->input('id_kel_tempat_kerja_pas'),
            'rt_tempat_kerja'       => $req->input('rt_tempat_kerja_pas'),
            'rw_tempat_kerja'       => $req->input('rw_tempat_kerja_pas'),
            'tgl_mulai_kerja'       => Carbon::parse($req->input('tgl_mulai_kerja_pas'))->format('Y-m-d'),
            'no_telp_tempat_kerja'  => $req->input('no_telp_tempat_kerja_pas')
        );

        $dataVerifikasi = array(
            'id_trans_so'             => $id,
            'id_calon_debitur'        => $check->id_calon_debt,
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
            'id_calon_debitur'    => $check->id_calon_debt,
            'val_data_debt'       => $req->input('val_data_debt'),
            'val_lingkungan_debt' => $req->input('val_lingkungan_debt'),
            'val_domisili_debt'   => $req->input('val_domisili_debt'),
            'val_pekerjaan_debt'  => $req->input('val_pekerjaan_debt'),
            'val_data_pasangan'   => $req->input('val_data_pasangan'),
            'val_data_penjamin'   => $req->input('val_data_penjamin'),
            'val_agunan_tanah'    => $req->input('val_agunan_tanah'),
            'val_agunan_kendaraan'=> $req->input('val_agunan_kendaraan'),
            'val_usaha_debt'      => $req->input('val_usaha_debt'),
            'catatan'             => $req->input('catatan_validasi')
        );

        if($files = $req->file('lamp_agunan_depan')){
            $a = 1;
            foreach($files as $file){
                $path = $lamp_dir.'/agunan_tanah';
                $name = 'agunan_depan'.$a.'.'.$file->getClientOriginalExtension();
                $file->move($path,$name);
                $a++;

                $agunanDepan[] = $path.'/'.$name;
            }
        }

        if ($files = $req->file('lamp_agunan_depan_ken')) {
            $a = 1;
            foreach($files as $file){
                $path = $lamp_dir.'/agunan_kendaraan';
                $name = 'agunan_depan'.$a.'.'.$file->getClientOriginalExtension();
                $file->move($path,$name);
                $a++;

                $agunanDepanKen[] = $path.'/'.$name;
            }
        }

        if($files = $req->file('lamp_agunan_kanan')){
            $a = 1;
            foreach($files as $file){
                $path = $lamp_dir.'/agunan_tanah';
                $name = 'agunan_kanan'.$a.'.'.$file->getClientOriginalExtension();
                $file->move($path,$name);
                $a++;

                $agunanKanan[] = $path.'/'.$name;
            }
        }

        if ($files = $req->file('lamp_agunan_kanan_ken')) {
            $a = 1;
            foreach($files as $file){
                $path = $lamp_dir.'/agunan_kendaraan';
                $name = 'agunan_kanan'.$a.'.'.$file->getClientOriginalExtension();
                $file->move($path,$name);
                $a++;

                $agunanKananKen[] = $path.'/'.$name;
            }
        }

        if($files = $req->file('lamp_agunan_kiri')){
            $a = 1;
            foreach($files as $file){
                $path = $lamp_dir.'/agunan_tanah';
                $name = 'agunan_kiri'.$a.'.'.$file->getClientOriginalExtension();
                $file->move($path,$name);
                $a++;

                $agunanKiri[] = $path.'/'.$name;
            }
        }

        if ($files = $req->file('lamp_agunan_kiri_ken')) {
            $a = 1;
            foreach($files as $file){
                $path = $lamp_dir.'/agunan_kendaraan';
                $name = 'agunan_kiri'.$a.'.'.$file->getClientOriginalExtension();
                $file->move($path,$name);
                $a++;

                $agunanKiriKen[] = $path.'/'.$name;
            }
        }


        if($files = $req->file('lamp_agunan_belakang')){
            $a = 1;
            foreach($files as $file){
                $path = $lamp_dir.'/agunan_tanah';
                $name = 'agunan_belakang'.$a.'.'.$file->getClientOriginalExtension();
                $file->move($path,$name);
                $a++;

                $agunanBelakang[] = $path.'/'.$name;
            }
        }

        if ($files = $req->file('lamp_agunan_belakang_ken')) {
            $a = 1;
            foreach($files as $file){
                $path = $lamp_dir.'/agunan_kendaraan';
                $name = 'agunan_belakang'.$a.'.'.$file->getClientOriginalExtension();
                $file->move($path,$name);
                $a++;

                $agunanBelakangKen[] = $path.'/'.$name;
            }
        }


        if($files = $req->file('lamp_agunan_dalam')){
            $a = 1;
            foreach($files as $file){
                $path = $lamp_dir.'/agunan_tanah';
                $name = 'agunan_dalam'.$a.'.'.$file->getClientOriginalExtension();
                $file->move($path,$name);
                $a++;

                $agunanDalam[] = $path.'/'.$name;
            }
        }

        if ($files = $req->file('lamp_agunan_dalam_ken')) {
            $a = 1;
            foreach($files as $file){
                $path = $lamp_dir.'/agunan_kendaraan';
                $name = 'agunan_dalam'.$a.'.'.$file->getClientOriginalExtension();
                $file->move($path,$name);
                $a++;

                $agunanDalamKen[] = $path.'/'.$name;
            }
        }


        if (!empty($req->input('tipe_lokasi_agunan'))) {
            for ($i = 0; $i < count($req->input('tipe_lokasi_agunan')); $i++){
                $daAguTa[] = [
                    'id_calon_debitur'        => $check->id_calon_debt,
                    'tipe_lokasi'             => empty($req->tipe_lokasi_agunan[$i]) ? null[$i] : strtoupper($req->tipe_lokasi_agunan[$i]),
                    'alamat'                  => empty($req->alamat_agunan[$i]) ? null[$i] : $req->alamat_agunan[$i],
                    'id_povinsi'              => empty($req->id_prov_agunan[$i]) ? null[$i] : $req->id_prov_agunan[$i],
                    'id_kabupaten'            => empty($req->id_kab_agunan[$i]) ? null[$i] : $req->id_kab_agunan[$i],
                    'id_kecamatan'            => empty($req->id_kec_agunan[$i]) ? null[$i] : $req->id_kec_agunan[$i],
                    'id_kelurahan'            => empty($req->id_kel_agunan[$i]) ? null[$i] : $req->id_kel_agunan[$i],
                    'rt'                      => empty($req->rt_agunan[$i]) ? null[$i] : $req->rt_agunan[$i],
                    'rw'                      => empty($req->rw_agunan[$i]) ? null[$i] : $req->rw_agunan[$i],
                    'luas_tanah'              => empty($req->luas_tanah[$i]) ? null[$i] : $req->luas_tanah[$i],
                    'luas_bangunan'           => empty($req->luas_bangunan[$i]) ? null[$i] : $req->luas_bangunan[$i],
                    'nama_pemilik_sertifikat' => empty($req->nama_pemilik_sertifikat[$i]) ? null[$i] : $req->nama_pemilik_sertifikat[$i],
                    'jenis_sertifikat'        => empty($req->jenis_sertifikat[$i]) ? null[$i] : strtoupper($req->jenis_sertifikat[$i]),
                    'no_sertifikat'           => empty($req->no_sertifikat[$i]) ? null[$i] : $req->no_sertifikat[$i],
                    'tgl_ukur_sertifikat'     => empty($req->tgl_ukur_sertifikat[$i]) ? null[$i] : Carbon::parse($req->tgl_ukur_sertifikat[$i])->format('Y-m-d'),
                    'tgl_berlaku_shgb'        => empty($req->tgl_berlaku_shgb[$i]) ? null[$i] : Carbon::parse($req->tgl_berlaku_shgb[$i])->format('Y-m-d'),
                    'no_imb'                  => empty($req->no_imb[$i]) ? null[$i] : $req->no_imb[$i],
                    'njop'                    => empty($req->njop[$i]) ? null[$i] : $req->njop[$i],
                    'nop'                     => empty($req->nop[$i]) ? null[$i] : $req->nop[$i],
                    // 'lam_imb'                 => empty($req->file('lam_imb')[$i]) ? null : Helper::img64enc($req->file('lam_imb')[$i]),
                    'lamp_agunan_depan'       => empty($agunanDepan[$i]) ? null[$i] : $agunanDepan[$i],
                    'lamp_agunan_kanan'       => empty($agunanKanan[$i]) ? null[$i] : $agunanKanan[$i],
                    'lamp_agunan_kiri'        => empty($agunanKiri[$i]) ? null[$i] : $agunanKiri[$i],
                    'lamp_agunan_belakang'    => empty($agunanBelakang[$i]) ? null[$i] : $agunanBelakang[$i],
                    'lamp_agunan_dalam'       => empty($agunanDalam[$i]) ? null[$i] : $agunanDalam[$i],
                    'created_at'              => $now,
                    'updated_at'              => $now
                ];

                $pemAguTa[] = [
                    'id_calon_debitur'  => $check->id_calon_debt,
                    // 'id_agunan_tanah'   => $id_AguTa[$i],
                    'nama_penghuni'     => empty($req->nama_penghuni_agunan[$i]) ? null[$i] : $req->nama_penghuni_agunan[$i],
                    'status_penghuni'   => empty($req->status_penghuni_agunan[$i]) ? null[$i] : strtoupper($req->status_penghuni_agunan[$i]),
                    'bentuk_bangunan'   => empty($req->bentuk_bangunan_agunan[$i]) ? null[$i] : $req->bentuk_bangunan_agunan[$i],
                    'kondisi_bangunan'  => empty($req->kondisi_bangunan_agunan[$i]) ? null[$i] : $req->kondisi_bangunan_agunan[$i],
                    'fasilitas'         => empty($req->fasilitas_agunan[$i]) ? null[$i] : $req->fasilitas_agunan[$i],
                    'listrik'           => empty($req->listrik_agunan[$i]) ? null[$i] : $req->listrik_agunan[$i],
                    'nilai_taksasi_agunan'   => empty($req->nilai_taksasi_agunan[$i]) ? null[$i] : $req->nilai_taksasi_agunan[$i],
                    'nilai_taksasi_bangunan' => empty($req->nilai_taksasi_bangunan[$i]) ? null[$i] : $req->nilai_taksasi_bangunan[$i],
                    'tgl_taksasi'       => empty($req->tgl_taksasi_agunan[$i]) ? null[$i] : Carbon::parse($req->tgl_taksasi_agunan[$i])->format('Y-m-d'),
                    'nilai_likuidasi'   => empty($req->nilai_likuidasi_agunan[$i]) ? null[$i] : $req->nilai_likuidasi_agunan[$i],
                    'created_at'        => $now,
                    'updated_at'        => $now
                ];
            }

        }

        if (!empty($req->input('no_bpkb_ken'))) {
            for ($i = 0; $i < count($req->input('no_bpkb_ken')); $i++) {
                $daAguKe[] = [
                    'id_calon_debitur'      => $check->id_calon_debt,
                    'no_bpkb'               => empty($req->no_bpkb_ken[$i]) ? null[$i] : $req->no_bpkb_ken[$i],
                    'nama_pemilik'          => empty($req->nama_pemilik_ken[$i]) ? null[$i] : $req->nama_pemilik_ken[$i],
                    'alamat_pemilik'        => empty($req->alamat_pemilik_ken[$i]) ? null[$i] : $req->nama_pemilik_ken[$i],
                    'merk'                  => empty($req->merk_ken[$i]) ? null[$i] : $req->merk_ken[$i],
                    'jenis'                 => empty($req->jenis_ken[$i]) ? null[$i] : $req->jenis_ken[$i],
                    'no_rangka'             => empty($req->no_rangka_ken[$i]) ? null[$i] : $req->no_rangka_ken[$i],
                    'no_mesin'              => empty($req->no_mesin_ken[$i]) ? null[$i] : $req->no_mesin_ken[$i],
                    'warna'                 => empty($req->warna_ken[$i]) ? null[$i] : $req->warna_ken[$i],
                    'tahun'                 => empty($req->tahun_ken[$i]) ? null[$i] : $req->tahun_ken[$i],
                    'no_polisi'             => empty($req->no_polisi_ken[$i]) ? null[$i] : strtoupper($req->no_polisi_ken[$i]),
                    'no_stnk'               => empty($req->no_stnk_ken[$i]) ? null[$i] : $req->no_stnk_ken[$i],
                    'tgl_kadaluarsa_pajak'  => empty($req->tgl_exp_pajak_ken[$i]) ? null[$i] : Carbon::parse($req->tgl_exp_pajak_ken[$i])->format('Y-m-d'),
                    'tgl_kadaluarsa_stnk'   => empty($req->tgl_exp_stnk_ken[$i]) ? null[$i] : Carbon::parse($req->tgl_exp_stnk_ken[$i])->format('Y-m-d'),
                    'no_faktur'             => empty($req->no_faktur_ken[$i]) ? null[$i] : $req->no_faktur_ken[$i],
                    'lamp_agunan_depan'     => empty($agunanDepanKen[$i]) ? null[$i] : $agunanDepanKen[$i],
                    'lamp_agunan_kanan'     => empty($agunanKananKen[$i]) ? null[$i] : $agunanKananKen[$i],
                    'lamp_agunan_kiri'      => empty($agunanKiriKen[$i]) ? null[$i] : $agunanKiriKen[$i],
                    'lamp_agunan_belakang'  => empty($agunanBelakangKen[$i]) ? null[$i] : $agunanBelakangKen[$i],
                    'lamp_agunan_dalam'     => empty($agunanDalamKen[$i]) ? null[$i] : $agunanDalamKen[$i],
                    'created_at'            => $now,
                    'updated_at'            => $now
                ];

                $pemAguKe[] = [
                    'id_calon_debitur'      => $check->id_calon_debt,
                    // 'id_agunan_kendaraan'   => $id_AguKe[$i],
                    'nama_pengguna'         => empty($req->nama_pengguna_ken[$i]) ? null[$i] : $req->nama_pengguna_ken[$i],
                    'status_pengguna'       => empty($req->status_pengguna_ken[$i]) ? null[$i] : strtoupper($req->status_pengguna_ken[$i]),
                    'jml_roda_kendaraan'    => empty($req->jml_roda_ken[$i]) ? null[$i] : $req->jml_roda_ken[$i],
                    'kondisi_kendaraan'     => empty($req->kondisi_ken[$i]) ? null[$i] : $req->kondisi_ken[$i],
                    'keberadaan_kendaraan'  => empty($req->keberadaan_ken[$i]) ? null[$i] : $req->keberadaan_ken[$i],
                    'body'                  => empty($req->body_ken[$i]) ? null[$i] : $req->body_ken[$i],
                    'interior'              => empty($req->interior_ken[$i]) ? null[$i] : $req->interior_ken[$i],
                    'km'                    => empty($req->km_ken[$i]) ? null[$i] : $req->km_ken[$i],
                    'modifikasi'            => empty($req->modifikasi_ken[$i]) ? null[$i] : $req->modifikasi_ken[$i],
                    'aksesoris'             => empty($req->aksesoris_ken[$i]) ? null[$i] : $req->aksesoris_ken[$i],
                    'created_at'            => $now,
                    'updated_at'            => $now
                ];
            }
        }

        $kapBul = array(
            'id_calon_debitur'      => $check->id_calon_debt,
            'pemasukan_cadebt'      => empty($req->input('pemasukan_debitur')) ? null : (int) $req->input('pemasukan_debitur'),
            'pemasukan_pasangan'    => empty($req->input('pemasukan_pasangan')) ? null : (int) $req->input('pemasukan_pasangan'),
            'pemasukan_penjamin'    => empty($req->input('pemasukan_penjamin')) ? null : (int) $req->input('pemasukan_penjamin'),
            'biaya_rumah_tangga'    => empty($req->input('biaya_rumah_tangga')) ? null : (int) $req->input('biaya_rumah_tangga'),
            'biaya_transport'       => empty($req->input('biaya_transport')) ? null : (int) $req->input('biaya_transport'),
            'biaya_pendidikan'      => empty($req->input('biaya_pendidikan')) ? null : (int) $req->input('biaya_pendidikan'),
            'biaya_telp_listr_air'  => empty($req->input('biaya_telp_listr_air')) ? null : (int) $req->input('biaya_telp_listr_air'),
            'biaya_lain'            => empty($req->input('biaya_lain')) ? null : (int) $req->input('biaya_lain'),

            'total_pemasukan'       => (empty($req->input('pemasukan_debitur')) ? 0 : $req->input('pemasukan_debitur')) + (empty($req->input('pemasuk + an_pasangan')) ? 0 : $req->input('pemasukan_pasangan')) + (empty($req->input('pemasukan_penjamin')) ? 0 : $req->input('pemasukan_penjamin')),
            'total_pengeluaran'     => (empty($req->input('biaya_rumah_tangga')) ? 0 : $req->input('biaya_rumah_tangga')) + (empty($req->input('biaya_transport')) ? 0 : $req->input('biaya_transport')) + (empty($req->input('biaya_pendidikan')) ? 0 : $req->input('biaya_pendidikan')) + (empty($req->input('biaya_telp_listr_air')) ? 0 : $req->input('biaya_telp_listr_air')) + (empty($req->input('biaya_lain')) ? 0 : $req->input('biaya_lain')),
            'penghasilan_bersih'    => ((empty($req->input('pemasukan_debitur')) ? 0 : $req->input('pemasukan_debitur')) + (empty($req->input('pemasuk + an_pasangan')) ? 0 : $req->input('pemasukan_pasangan')) + (empty($req->input('pemasukan_penjamin')) ? 0 : $req->input('pemasukan_penjamin'))) - ((empty($req->input('biaya_rumah_tangga')) ? 0 : $req->input('biaya_rumah_tangga')) + (empty($req->input('biaya_transport')) ? 0 : $req->input('biaya_transport')) + (empty($req->input('biaya_pendidikan')) ? 0 : $req->input('biaya_pendidikan')) + (empty($req->input('biaya_telp_listr_air')) ? 0 : $req->input('biaya_telp_listr_air')) + (empty($req->input('biaya_lain')) ? 0 : $req->input('biaya_lain')))
        );

        $dataKeUsaha = array(
            'id_calon_debitur'     => $check->id_calon_debt,
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
        // try{

            for ($i = 0; $i < count($daAguTa); $i++) {
                $tanah = AgunanTanah::create($daAguTa[$i]);

                $id_tanah['id'][$i] = $tanah->id;
            }


            for ($i = 0; $i < count($daAguKe); $i++) {
                $kendaraan = AgunanKendaraan::create($daAguKe[$i]);

                $id_kendaraan['id'][$i] = $kendaraan->id;
            }

            for ($i = 0; $i < count($pemAguTa); $i++) {
                $pemAguTa_N[$i] = array_merge(array('id_agunan_tanah' => $id_tanah['id'][$i]), $pemAguTa[$i]);

                $pemTanah = PemeriksaanAgunTan::create($pemAguTa_N[$i]);

                $id_pem_tan['id'][$i] = $pemTanah->id;
            }

            for ($i = 0; $i < count($pemAguKe); $i++) {
                $pemAguKe_N[$i] = array_merge(array('id_agunan_kendaraan' => $id_kendaraan['id'][$i]), $pemAguKe[$i]);

                $pemKendaraan = PemeriksaanAgunKen::create($pemAguKe_N[$i]);

                $id_pem_ken['id'][$i] = $pemKendaraan->id;
            }

            $tanID   = implode(",", $id_tanah['id']);
            $kenID   = implode(",", $id_kendaraan['id']);
            $p_tanID = implode(",", $id_pem_tan['id']);
            $p_kenID = implode(",", $id_pem_ken['id']);


            TransSo::where('id', $id)->update([
                'id_agunan_tanah'             => $tanID,
                'id_agunan_kendaraan'         => $kenID,
                'id_periksa_agunan_tanah'     => $p_tanID,
                'id_periksa_agunan_kendaraan' => $p_kenID,
                'id_usaha'                    => $KU->id
            ]);



        //     $getAguTa = PemeriksaanAgunTan::select('id')->where('id_calon_debitur', $Trans->id_calon_debt)->get();

        //     if ($getAguTa != '[]') {
        //         foreach ($getAguTa as $val) {
        //             $PAT['id'][$i] = $val->id;
        //             $i++;
        //         }

        //         $id_PAT = implode(",", $PAT['id']);
        //     }else{
        //         $id_PAT = null;
        //     }

        //     $getAguKe = PemeriksaanAgunKen::select('id')->where('id_calon_debitur', $Trans->id_calon_debt)->get();

        //     if ($getAguKe != '[]') {
        //         foreach ($getAguKe as $val) {
        //             $PAK['id'][$i] = $val->id;
        //             $i++;
        //         }

        //         $id_PAK = implode(",", $PAK['id']);
        //     }else{
        //         $id_PAK = null;
        //     }


        //     $KB = KapBulanan::updateOrCreate($kapBul);
        //     $KU = KeuanganUsaha::updateOrCreate($dataKeUsaha);
        //     $RAO = TransAO::updateOrCreate($TransAO);

        //     $id_tr_ao = array('id_trans_ao' => $RAO->id);

        //     $NewVerif = array_merge($id_tr_ao, $dataVerifikasi);
        //     $NewValid = array_merge($id_tr_ao, $dataValidasi);

        //     VerifModel::updateOrCreate($NewVerif);
        //     ValidModel::updateOrCreate($NewValid);

        //     TransSo::where('id', $Trans->id)->update([
        //         'id_agunan_tanah'             => $id_AguTa,
        //         'id_agunan_kendaraan'         => $id_AguKe,
        //         'id_periksa_agunan_tanah'     => $id_PAT,
        //         'id_periksa_agunan_kendaraan' => $id_PAK,
        //         'id_usaha'                    => $KU->id
        //     ]);

            DB::connection('web')->commit();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message'=> $msg
            ], 200);
        // } catch (\Exception $e) {
        //     $err = DB::connection('web')->rollback();
        //     return response()->json([
        //         'code'    => 501,
        //         'status'  => 'error',
        //         'message' => $err
        //     ], 501);
        // }
    }
}
