<?php

namespace App\Http\Controllers\Transaksi;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use App\Models\Pengajuan\AO\PemeriksaanAgunTan;
use App\Models\Pengajuan\AO\PemeriksaanAgunKen;
use App\Http\Requests\Transaksi\BlankRequest;
use App\Models\Pengajuan\AO\AgunanKendaraan;
use App\Models\Pengajuan\AO\PendapatanUsaha;
use App\Models\Pengajuan\AO\RekomendasiAO;
use App\Models\Pengajuan\AO\AgunanTanah;
use App\Models\Pengajuan\AO\KapBulanan;
use App\Models\Pengajuan\AO\ValidModel;
use App\Models\Pengajuan\AO\VerifModel;
use App\Models\Pengajuan\SO\Penjamin;
use App\Models\Pengajuan\SO\Pasangan;
use App\Models\Pengajuan\SO\Debitur;
use Illuminate\Support\Facades\File;
use App\Models\AreaKantor\Cabang;
use App\Models\Transaksi\TransSO;
use App\Models\Transaksi\TransAO;
use App\Models\AreaKantor\JPIC;
use App\Models\AreaKantor\PIC;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\User;
use Carbon\Carbon;
use DB;

class MasterAO_Controller extends BaseController
{
    public function index(Request $req){
        $user_id  = $req->auth->user_id;

        $pic = PIC::where('user_id', $user_id)->first();

        if ($pic == null) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "User_ID anda adalah '".$user_id."' dengan username '".$req->auth->user."' . Namun anda belum terdaftar sebagai PIC(AO). Harap daftarkan diri sebagai PIC(AO) pada form PIC atau hubungi bagian IT"
            ], 404);
        }

        $id_area   = $pic->id_area;
        $id_cabang = $pic->id_cabang;
        $scope     = $pic->jpic['cakupan'];

        $query_dir = TransSO::with('pic', 'cabang', 'asaldata', 'debt', 'pas', 'faspin', 'ao', 'ca')->orderBy('created_at', 'desc');
        $method = 'get';

        $query = Helper::checkDir($user_id, $scope, $query_dir, $id_area, $id_cabang, $method);


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

            if ($val->ao['status_ao'] == 1) {
                $status_ao = 'recommend';
            }elseif ($val->ao['status_ao'] == 2) {
                $status_ao = 'not recommend';
            }else{
                $status_ao = 'waiting';
            }

            $data[$key] = [
                'id'             => $val->id          == null ? null : (int) $val->id,
                'id_trans_ao'    => $val->id_trans_ao == null ? null : (int) $val->id_trans_ao,
                'nomor_so'       => $val->nomor_so,
                // 'nomor_ao'       => $val->ao['nomor_ao'],
                'pic'            => $val->pic['nama'],
                'area'           => $val->area['nama'],
                'cabang'         => $val->cabang['nama'],
                'asal_data'      => $val->asaldata['nama'],
                'nama_marketing' => $val->nama_marketing,
                'nama_debitur'   => $val->debt['nama_lengkap'],
                'plafon'         => (int) $val->faspin['plafon'],
                'tenor'          => (int) $val->faspin['tenor'],
                'das'            => [
                    'status'  => $status_das,
                    'catatan' => $val->catatan_das
                ],
                'hm'            => [
                    'status'  => $status_hm,
                    'catatan' => $val->catatan_hm
                ],
                'ao'            => [
                    'status'  => $status_ao,
                    'catatan' => $val->ao['catatan_ao']
                ]
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
        $user_id = $req->auth->user_id;
        $pic     = PIC::where('user_id', $user_id)->first();

        if ($pic == null) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "User_ID anda adalah '".$user_id."' dengan username '".$req->auth->user."' . Namun anda belum terdaftar sebagai PIC(AO). Harap daftarkan diri sebagai PIC(AO) pada form PIC atau hubungi bagian IT"
            ], 404);
        }

        $id_area   = $pic->id_area;
        $id_cabang = $pic->id_cabang;
        $scope     = $pic->jpic['cakupan'];

        $query_dir = TransSO::with('pic', 'cabang', 'asaldata', 'debt', 'pas', 'faspin', 'ao', 'ca')->where('id', $id);
        $method = 'first';

        $val = Helper::checkDir($user_id, $scope, $query_dir, $id_area, $id_cabang, $method);


        if (!$val) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        $id_penj = explode (",",$val->id_penjamin);

        $penjamin = Penjamin::whereIn('id', $id_penj)->get();

        if ($penjamin != '[]') {
            foreach ($penjamin as $key => $value) {
                $pen[$key] = [
                    "id"        => $val->id == null ? null : (int) $value->id,
                    "nama_ktp"  => $value->nama_ktp,
                ];
            }
        }else{
            $pen = null;
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

        if ($val->ao['status_ao'] == 1) {
            $status_ao = 'recommend';
        }elseif ($val->ao['status_ao'] == 2) {
            $status_ao = 'not recommend';
        }else{
            $status_ao = 'waiting';
        }

        $data[] = [
            'id'          => $val->id          == null ? null : (int) $val->id,
            'id_trans_ao' => $val->id_trans_ao == null ? null : (int) $val->id_trans_ao,
            'nomor_so'    => $val->nomor_so,
            // 'nomor_ao'    => $val->ao['nomor_ao'],
            'nama_so'     => $val->nama_so,
            'id_pic'      => $val->id_pic == null ? null : (int) $val->id_pic,
            'nama_pic'    => $val->pic['nama'],
            'area'   => [
                'id'      => $val->id_area == null ? null : (int) $val->id_area,
                'nama'    => $val->area['nama']
            ],
            'id_cabang'   => $val->id_cabang == null ? null : (int) $val->id_cabang,
            'nama_cabang' => $val->cabang['nama'],
            'asaldata'  => [
                'id'   => $val->asaldata['id'] == null ? null : (int) $val->asaldata['id'],
                'nama' => $val->asaldata['nama']
            ],
            'nama_marketing' => $val->nama_marketing,
            'fasilitas_pinjaman'  => [
                'id'              => $val->id_fasilitas_pinjaman == null ? null : (int) $val->id_fasilitas_pinjaman
            ],
            'data_debitur' => [
                'id'             => $val->id_calon_debitur == null ? null : (int) $val->id_calon_debitur,
                'nama_lengkap'   => $val->debt['nama_lengkap'],
            ],
            'data_pasangan' => [
                'id'             => $val->id_pasangan == null ? null : (int) $val->id_pasangan,
                'nama_lengkap'   => $val->pas['nama_lengkap'],
            ],
            'data_penjamin' => $pen,
            'das'=> [
                'status'  => $status_das,
                'catatan' => $val->catatan_das
            ],
            'hm' => [
                'status'  => $status_hm,
                'catatan' => $val->catatan_hm
            ],
            'ao' => [
                'status'  => $status_ao,
                'catatan' => $val->ao['catatan_ao']
            ],
            'lampiran'  => [
                "ideb"      => explode(";", $val->lamp_ideb),
                "pefindo"   => explode(";", $val->lamp_pefindo)
            ]
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

        $countTAO = TransAO::latest('id','nomor_ao')->first();

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
        $nomor_ao = $PIC->id_cabang.'-'.$JPIC->nama_jenis.'-'.$month.'-'.$year.'-'.$lastNumb;

        $check = TransSO::where('id',$id)->first();

        if (!$check) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        $lamp_dir = 'public/'.$check->debt['no_ktp'];

        // Form Persetujuan Ideb
        if($file = $req->file('form_persetujuan_ideb')){
            $path = $lamp_dir.'/ideb';
            $name = 'form_persetujuan_ideb.' . $file->getClientOriginalName();
            $file->move($path,$name);

            $form_persetujuan_ideb = $path.'/'.$name;
        }else{
            $form_persetujuan_ideb = $check->form_persetujuan_ideb;
        }


        $id_penj = explode (",",$check->id_penjamin);

        $TransAO = array(
            'nomor_ao'              => $nomor_ao,
            'id_trans_so'           => $id,
            'user_id'               => $user_id,
            'id_pic'                => $PIC->id,
            'id_area'               => $PIC->id_area,
            'id_cabang'             => $PIC->id_cabang,
            'catatan_ao'            => $req->input('catatan_ao'),
            'status_ao'             => empty($req->input('status_ao')) ? 1 : $req->input('status_ao'),
            'form_persetujuan_ideb' => $form_persetujuan_ideb
        );

        $recom_AO = array(
            'produk'                => $req->input('produk'),
            'plafon_kredit'         => empty($req->input('plafon_kredit')) ? $check->faspin['plafon'] : $req->input('plafon_kredit'),
            'jangka_waktu'          => $req->input('jangka_waktu'),
            'suku_bunga'            => $req->input('suku_bunga'),
            'pembayaran_bunga'      => $req->input('pembayaran_bunga'),
            'akad_kredit'           => $req->input('akad_kredit'),
            'ikatan_agunan'         => $req->input('ikatan_agunan'),
            'analisa_ao'            => $req->input('analisa_ao'),
            'biaya_provisi'         => $req->input('biaya_provisi'),
            'biaya_administrasi'    => $req->input('biaya_administrasi'),
            'biaya_credit_checking' => $req->input('biaya_credit_checking'),
            'biaya_tabungan'        => $req->input('biaya_tabungan')
        );

        $dataVerifikasi = array(
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
            'val_data_debt'       => $req->input('val_data_debt'),
            'val_lingkungan_debt' => $req->input('val_lingkungan_debt'),
            'val_domisili_debt'   => $req->input('val_domisili_debt'),
            'val_pekerjaan_debt'  => $req->input('val_pekerjaan_debt'),
            'val_data_pasangan'   => $req->input('val_data_pasangan'),
            'val_data_penjamin'   => $req->input('val_data_penjamin'),
            'val_agunan'          => $req->input('val_agunan'),
            'catatan'             => $req->input('catatan_validasi')
        );

        if($files = $req->file('agunan_bag_depan')){
            foreach($files as $file){
                $path = $lamp_dir.'/agunan_tanah';
                $name = 'agunan_bag_depan.' . $file->getClientOriginalName();
                $file->move($path,$name);

                $agunan_bag_depan[] = $path.'/'.$name;
            }
        }

        if($files = $req->file('agunan_bag_jalan')){
            foreach($files as $file){
                $path = $lamp_dir.'/agunan_tanah';
                $name = 'agunan_bag_jalan.' . $file->getClientOriginalName();
                $file->move($path,$name);

                $agunan_bag_jalan[] = $path.'/'.$name;
            }
        }

        if($files = $req->file('agunan_bag_ruangtamu')){
            foreach($files as $file){
                $path = $lamp_dir.'/agunan_tanah';
                $name = 'agunan_bag_ruangtamu.' . $file->getClientOriginalName();
                $file->move($path,$name);

                $agunan_bag_ruangtamu[] = $path.'/'.$name;
            }
        }

        if($files = $req->file('agunan_bag_kamarmandi')){
            foreach($files as $file){
                $path = $lamp_dir.'/agunan_tanah';
                $name = 'agunan_bag_kamarmandi.' . $file->getClientOriginalName();
                $file->move($path,$name);

                $agunan_bag_kamarmandi[] = $path.'/'.$name;
            }
        }

        if($files = $req->file('agunan_bag_dapur')){
            foreach($files as $file){
                $path = $lamp_dir.'/agunan_tanah';
                $name = 'agunan_bag_dapur.' . $file->getClientOriginalName();
                $file->move($path,$name);

                $agunan_bag_dapur[] = $path.'/'.$name;
            }
        }

        if ($files = $req->file('lamp_agunan_depan_ken')) {
            foreach($files as $file){
                $path = $lamp_dir.'/agunan_kendaraan';
                $name = 'agunan_depan.' . $file->getClientOriginalName();
                $file->move($path,$name);

                $agunanDepanKen[] = $path.'/'.$name;
            }
        }


        if ($files = $req->file('lamp_agunan_kanan_ken')) {
            foreach($files as $file){
                $path = $lamp_dir.'/agunan_kendaraan';
                $name = 'agunan_kanan.' . $file->getClientOriginalName();
                $file->move($path,$name);

                $agunanKananKen[] = $path.'/'.$name;
            }
        }


        if ($files = $req->file('lamp_agunan_kiri_ken')) {
            foreach($files as $file){
                $path = $lamp_dir.'/agunan_kendaraan';
                $name = 'agunan_kiri.' . $file->getClientOriginalName();
                $file->move($path,$name);

                $agunanKiriKen[] = $path.'/'.$name;
            }
        }


        if ($files = $req->file('lamp_agunan_belakang_ken')) {
            foreach($files as $file){
                $path = $lamp_dir.'/agunan_kendaraan';
                $name = 'agunan_belakang.' . $file->getClientOriginalName();
                $file->move($path,$name);
                $a++;

                $agunanBelakangKen[] = $path.'/'.$name;
            }
        }

        if ($files = $req->file('lamp_agunan_dalam_ken')) {
            foreach($files as $file){
                $path = $lamp_dir.'/agunan_kendaraan';
                $name = 'agunan_dalam.' . $file->getClientOriginalName();
                $file->move($path,$name);

                $agunanDalamKen[] = $path.'/'.$name;
            }
        }

        // Tambahan Agunan Tanah
        if ($files = $req->file('lamp_imb')) {
            foreach($files as $file){
                $path = $lamp_dir.'/agunan_tanah';
                $name = 'lamp_imb.' . $file->getClientOriginalName();
                $file->move($path,$name);

                $lamp_imb[] = $path.'/'.$name;
            }
        }

        if ($files = $req->file('lamp_pbb')) {
            foreach($files as $file){
                $path = $lamp_dir.'/agunan_tanah';
                $name = 'lamp_pbb.' . $file->getClientOriginalName();
                $file->move($path,$name);
                $lamp_pbb[] = $path.'/'.$name;
            }
        }

        if ($files = $req->file('lamp_sertifikat')) {
            foreach($files as $file){
                $path = $lamp_dir.'/agunan_tanah';
                $name = 'lamp_sertifikat.' . $file->getClientOriginalName();
                $file->move($path,$name);

                $lamp_sertifikat[] = $path.'/'.$name;
            }
        }


        if (!empty($req->input('tipe_lokasi_agunan'))) {
            for ($i = 0; $i < count($req->input('tipe_lokasi_agunan')); $i++){
                $daAguTa[] = [
                    'tipe_lokasi'             => empty($req->tipe_lokasi_agunan[$i]) ? null : strtoupper($req->tipe_lokasi_agunan[$i]),
                    'alamat'                  => empty($req->alamat_agunan[$i]) ? null : $req->alamat_agunan[$i],
                    'id_provinsi'             => empty($req->id_prov_agunan[$i]) ? null : $req->id_prov_agunan[$i],
                    'id_kabupaten'            => empty($req->id_kab_agunan[$i]) ? null : $req->id_kab_agunan[$i],
                    'id_kecamatan'            => empty($req->id_kec_agunan[$i]) ? null : $req->id_kec_agunan[$i],
                    'id_kelurahan'            => empty($req->id_kel_agunan[$i]) ? null : $req->id_kel_agunan[$i],
                    'rt'                      => empty($req->rt_agunan[$i]) ? null : $req->rt_agunan[$i],
                    'rw'                      => empty($req->rw_agunan[$i]) ? null : $req->rw_agunan[$i],
                    'luas_tanah'              => empty($req->luas_tanah[$i]) ? null : $req->luas_tanah[$i],
                    'luas_bangunan'           => empty($req->luas_bangunan[$i]) ? null : $req->luas_bangunan[$i],
                    'nama_pemilik_sertifikat' => empty($req->nama_pemilik_sertifikat[$i]) ? null : $req->nama_pemilik_sertifikat[$i],
                    'jenis_sertifikat'        => empty($req->jenis_sertifikat[$i]) ? null : strtoupper($req->jenis_sertifikat[$i]),
                    'no_sertifikat'           => empty($req->no_sertifikat[$i]) ? null : $req->no_sertifikat[$i],
                    'tgl_ukur_sertifikat'     => empty($req->tgl_ukur_sertifikat[$i]) ? null : $req->tgl_ukur_sertifikat[$i],
                    'tgl_berlaku_shgb'        => empty($req->tgl_berlaku_shgb[$i]) ? null : Carbon::parse($req->tgl_berlaku_shgb[$i])->format('Y-m-d'),
                    'no_imb'                  => empty($req->no_imb[$i]) ? null : $req->no_imb[$i],
                    'njop'                    => empty($req->njop[$i]) ? null : $req->njop[$i],
                    'nop'                     => empty($req->nop[$i]) ? null : $req->nop[$i],
                    // 'lam_imb'                 => empty($req->file('lam_imb')[$i]) ? null : Helper::img64enc($req->file('lam_imb')[$i]),
                    'agunan_bag_depan'       => empty($agunan_bag_depan[$i]) ? null : $agunan_bag_depan[$i],
                    'agunan_bag_jalan'       => empty($agunan_bag_jalan[$i]) ? null : $agunan_bag_jalan[$i],
                    'agunan_bag_ruangtamu'   => empty($agunan_bag_ruangtamu[$i]) ? null : $agunan_bag_ruangtamu[$i],
                    'agunan_bag_kamarmandi'  => empty($agunan_bag_kamarmandi[$i]) ? null : $agunan_bag_kamarmandi[$i],
                    'agunan_bag_dapur'       => empty($agunan_bag_dapur[$i]) ? null : $agunan_bag_dapur[$i],

                    'lamp_imb'                => empty($lamp_imb[$i]) ? null : $lamp_imb[$i],
                    'lamp_pbb'                => empty($lamp_pbb[$i]) ? null : $lamp_pbb[$i],
                    'lamp_sertifikat'         => empty($lamp_sertifikat[$i]) ? null : $lamp_sertifikat[$i]
                ];

                $pemAguTa[] = [
                    'nama_penghuni'     => empty($req->nama_penghuni_agunan[$i]) ? null : $req->nama_penghuni_agunan[$i],
                    'status_penghuni'   => empty($req->status_penghuni_agunan[$i]) ? null : strtoupper($req->status_penghuni_agunan[$i]),
                    'bentuk_bangunan'   => empty($req->bentuk_bangunan_agunan[$i]) ? null : $req->bentuk_bangunan_agunan[$i],
                    'kondisi_bangunan'  => empty($req->kondisi_bangunan_agunan[$i]) ? null : $req->kondisi_bangunan_agunan[$i],
                    'fasilitas'         => empty($req->fasilitas_agunan[$i]) ? null : $req->fasilitas_agunan[$i],
                    'listrik'           => empty($req->listrik_agunan[$i]) ? null : $req->listrik_agunan[$i],
                    'nilai_taksasi_agunan'   => empty($req->nilai_taksasi_agunan[$i]) ? null : $req->nilai_taksasi_agunan[$i],
                    'nilai_taksasi_bangunan' => empty($req->nilai_taksasi_bangunan[$i]) ? null : $req->nilai_taksasi_bangunan[$i],
                    'tgl_taksasi'       => empty($req->tgl_taksasi_agunan[$i]) ? null : Carbon::parse($req->tgl_taksasi_agunan[$i])->format('Y-m-d'),
                    'nilai_likuidasi'   => empty($req->nilai_likuidasi_agunan[$i]) ? null : $req->nilai_likuidasi_agunan[$i],
                    'nilai_agunan_independen'       => empty($req->nilai_agunan_independen[$i]) ? null : $req->nilai_agunan_independen[$i],
                    'perusahaan_penilai_independen' => empty($req->perusahaan_penilai_independen[$i]) ? null : $req->perusahaan_penilai_independen[$i]
                ];
            }

        }

        if (!empty($req->input('no_bpkb_ken'))) {
            for ($i = 0; $i < count($req->input('no_bpkb_ken')); $i++) {
                $daAguKe[] = [
                    'no_bpkb'               => empty($req->no_bpkb_ken[$i]) ? null : $req->no_bpkb_ken[$i],
                    'nama_pemilik'          => empty($req->nama_pemilik_ken[$i]) ? null : $req->nama_pemilik_ken[$i],
                    'alamat_pemilik'        => empty($req->alamat_pemilik_ken[$i]) ? null : $req->alamat_pemilik_ken[$i],
                    'merk'                  => empty($req->merk_ken[$i]) ? null : $req->merk_ken[$i],
                    'jenis'                 => empty($req->jenis_ken[$i]) ? null : $req->jenis_ken[$i],
                    'no_rangka'             => empty($req->no_rangka_ken[$i]) ? null : $req->no_rangka_ken[$i],
                    'no_mesin'              => empty($req->no_mesin_ken[$i]) ? null : $req->no_mesin_ken[$i],
                    'warna'                 => empty($req->warna_ken[$i]) ? null : $req->warna_ken[$i],
                    'tahun'                 => empty($req->tahun_ken[$i]) ? null : $req->tahun_ken[$i],
                    'no_polisi'             => empty($req->no_polisi_ken[$i]) ? null : strtoupper($req->no_polisi_ken[$i]),
                    'no_stnk'               => empty($req->no_stnk_ken[$i]) ? null : $req->no_stnk_ken[$i],
                    'tgl_kadaluarsa_pajak'  => empty($req->tgl_exp_pajak_ken[$i]) ? null : Carbon::parse($req->tgl_exp_pajak_ken[$i])->format('Y-m-d'),
                    'tgl_kadaluarsa_stnk'   => empty($req->tgl_exp_stnk_ken[$i]) ? null : Carbon::parse($req->tgl_exp_stnk_ken[$i])->format('Y-m-d'),
                    'no_faktur'             => empty($req->no_faktur_ken[$i]) ? null : $req->no_faktur_ken[$i],
                    'lamp_agunan_depan'     => empty($agunanDepanKen[$i]) ? null : $agunanDepanKen[$i],
                    'lamp_agunan_kanan'     => empty($agunanKananKen[$i]) ? null : $agunanKananKen[$i],
                    'lamp_agunan_kiri'      => empty($agunanKiriKen[$i]) ? null : $agunanKiriKen[$i],
                    'lamp_agunan_belakang'  => empty($agunanBelakangKen[$i]) ? null : $agunanBelakangKen[$i],
                    'lamp_agunan_dalam'     => empty($agunanDalamKen[$i]) ? null : $agunanDalamKen[$i]
                ];

                $pemAguKe[] = [
                    'nama_pengguna'         => empty($req->nama_pengguna_ken[$i]) ? null : $req->nama_pengguna_ken[$i],
                    'status_pengguna'       => empty($req->status_pengguna_ken[$i]) ? null : strtoupper($req->status_pengguna_ken[$i]),
                    'jml_roda_kendaraan'    => empty($req->jml_roda_ken[$i]) ? null : $req->jml_roda_ken[$i],
                    'kondisi_kendaraan'     => empty($req->kondisi_ken[$i]) ? null : $req->kondisi_ken[$i],
                    'keberadaan_kendaraan'  => empty($req->keberadaan_ken[$i]) ? null : $req->keberadaan_ken[$i],
                    'body'                  => empty($req->body_ken[$i]) ? null : $req->body_ken[$i],
                    'interior'              => empty($req->interior_ken[$i]) ? null : $req->interior_ken[$i],
                    'km'                    => empty($req->km_ken[$i]) ? null : $req->km_ken[$i],
                    'modifikasi'            => empty($req->modifikasi_ken[$i]) ? null : $req->modifikasi_ken[$i],
                    'aksesoris'             => empty($req->aksesoris_ken[$i]) ? null : $req->aksesoris_ken[$i]
                ];
            }
        }

        // Start Kapasitas Bulanan
        $inputKapBul = array(
            'pemasukan_cadebt'      => empty($req->input('pemasukan_debitur')) ? null : (int) $req->input('pemasukan_debitur'),
            'pemasukan_pasangan'    => empty($req->input('pemasukan_pasangan')) ? null : (int) $req->input('pemasukan_pasangan'),
            'pemasukan_penjamin'    => empty($req->input('pemasukan_penjamin')) ? null : (int) $req->input('pemasukan_penjamin'),
            'biaya_rumah_tangga'    => empty($req->input('biaya_rumah_tangga')) ? null : (int) $req->input('biaya_rumah_tangga'),
            'biaya_transport'       => empty($req->input('biaya_transport')) ? null : (int) $req->input('biaya_transport'),
            'biaya_pendidikan'      => empty($req->input('biaya_pendidikan')) ? null : (int) $req->input('biaya_pendidikan'),
            'biaya_telp_listr_air'  => empty($req->input('biaya_telp_listr_air')) ? null : (int) $req->input('biaya_telp_listr_air'),
            'angsuran'              => empty($req->input('angsuran')) ? null : $req->input('angsuran'),
            'biaya_lain'            => empty($req->input('biaya_lain')) ? null : (int) $req->input('biaya_lain'),
        );

        $total_KapBul = array(
            'total_pemasukan'       => $ttl1 = array_sum(array_slice($inputKapBul, 0, 2)),
            'total_pengeluaran'     => $ttl2 = array_sum(array_slice($inputKapBul, 2)),
            'penghasilan_bersih'    => $ttl1 - $ttl2
        );

        $kapBul = array_merge($inputKapBul, $total_KapBul);
        // End Kapasitas Bulanan


        if (!empty($req->input('pemasukan_tunai'))) {
            // $dataKeUsaha = array(
            $inputKeUsaha = array(
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
                'biaya_lain_lain'      => empty($req->input('biaya_lain_lain')) ? null : (int) $req->input('biaya_lain_lain')
            );

            $total_KeUsaha = array(
                'total_pemasukan'      => $ttl1 = array_sum(array_slice($inputKeUsaha, 0, 2)),
                'total_pengeluaran'    => $ttl2 = array_sum(array_slice($inputKeUsaha, 2)),
                'laba_usaha'           => $ttl1 - $ttl2
            );

            $dataKeUsaha = array_merge($inputKeUsaha, $total_KeUsaha);
        }

        // Lampiran Untuk Debitur
        if ($files = $req->file('lamp_buku_tabungan')) {
            foreach($files as $file){
                $path = $lamp_dir.'/lamp_buku_tabungan';
                $name = 'lamp_buku_tabungan.' . $file->getClientOriginalName();
                $file->move($path,$name);

                $buku_tabungan[] = $path.'/'.$name;

            }

            $lamp_buku_tabungan = implode(";",$buku_tabungan);

        }else{
            $lamp_buku_tabungan = $check->debt['lamp_buku_tabungan'];
        }

        if($file = $req->file('lamp_skk')){
            $path = $lamp_dir.'/debitur';
            $name = 'lamp_skk.'.$file->getClientOriginalName();

            $file->move($path,$name);

            $lamp_skk = $path.'/'.$name;
        }else{
            $lamp_skk = $check->debt['lamp_skk'];
        }

        if($files = $req->file('lamp_sku')){
            foreach ($files as $file) {
                $path = $lamp_dir.'/debitur';
                $name = 'lamp_sku.'.$file->getClientOriginalName();

                $file->move($path,$name);

                $sku[] = $path.'/'.$name;

            }

            $lamp_sku = implode(";",$sku);

        }else{
            $lamp_sku = $check->debt['lamp_sku'];
        }

        if($file = $req->file('lamp_slip_gaji')){
            // foreach ($files as $file) {
                $path = $lamp_dir.'/debitur';

                $extention = $file->getClientOriginalExtension();

                // $exIdeb = $file->getClientMimeType();

                if ($extention != 'jpg' && $extention != 'jpeg' && $extention != 'png' && $extention != 'pdf') {
                    return response()->json([
                        "code"    => 422,
                        "status"  => "not valid request",
                        "message" => "file harus berupa format berikut: jpg, jpeg, png, pdf"
                    ], 422);
                }

                $name = 'lamp_slip_gaji.'.$file->getClientOriginalName();

                $file->move($path,$name);

                $slip_gaji[] = $path.'/'.$name;

                $lamp_slip_gaji = implode(";",$slip_gaji);
            // }
        }else{
            $lamp_slip_gaji = $check->debt['lamp_slip_gaji'];
        }


        if($files = $req->file('foto_pembukuan_usaha')){
            foreach ($files as $file) {
                $path = $lamp_dir.'/debitur';
                $name = 'foto_pembukuan_usaha.'.$file->getClientOriginalName();

                $file->move($path,$name);

                $pembukuan_usaha[] = $path.'/'.$name;

            }

            $foto_pembukuan_usaha = implode(";",$pembukuan_usaha);

        }else{
            $foto_pembukuan_usaha = $check->debt['foto_pembukuan_usaha'];
        }

        if($files = $req->file('lamp_foto_usaha')){
            foreach ($files as $file) {
                $path = $lamp_dir.'/debitur';
                $name = 'lamp_foto_usaha.'.$file->getClientOriginalName();

                $file->move($path,$name);

                $foto_usaha[] = $path.'/'.$name;
            }

            $lamp_foto_usaha = implode(";",$foto_usaha);

        }else{
            $lamp_foto_usaha = $check->debt['lamp_foto_usaha'];
        }

        $cadebt = array(
            'lamp_buku_tabungan'    => $lamp_buku_tabungan,
            'lamp_skk'              => $lamp_skk,
            'lamp_sku'              => $lamp_sku,
            'lamp_slip_gaji'        => $lamp_slip_gaji,
            'foto_pembukuan_usaha'  => $foto_pembukuan_usaha,
            'lamp_foto_usaha'       => $lamp_foto_usaha
        );


        $check_ao = TransAO::where('id_trans_so', $id)->first();

        DB::connection('web')->beginTransaction();
        // try{

            if ($check_ao == null) {

                if (!empty($daAguTa)) {
                    for ($i = 0; $i < count($daAguTa); $i++) {

                        $tanah = AgunanTanah::create($daAguTa[$i]);

                        $id_tanah['id'][$i] = $tanah->id;
                    }

                    for ($i = 0; $i < count($pemAguTa); $i++) {
                        $pemAguTa_N[$i] = array_merge(array('id_agunan_tanah' => $id_tanah['id'][$i]), $pemAguTa[$i]);

                        $pemTanah = PemeriksaanAgunTan::create($pemAguTa_N[$i]);

                        $id_pem_tan['id'][$i] = $pemTanah->id;
                    }

                    $tanID   = implode(",", $id_tanah['id']);
                    $p_tanID = implode(",", $id_pem_tan['id']);
                }else{
                    $tanID   = null;
                    $p_tanID = null;
                }


                if (!empty($daAguKe)) {
                    for ($i = 0; $i < count($daAguKe); $i++) {
                        $kendaraan = AgunanKendaraan::create($daAguKe[$i]);

                        $id_kendaraan['id'][$i] = $kendaraan->id;
                    }

                    for ($i = 0; $i < count($pemAguKe); $i++) {
                        $pemAguKe_N[$i] = array_merge(array('id_agunan_kendaraan' => $id_kendaraan['id'][$i]), $pemAguKe[$i]);

                        $pemKendaraan = PemeriksaanAgunKen::create($pemAguKe_N[$i]);

                        $id_pem_ken['id'][$i] = $pemKendaraan->id;
                    }

                    $kenID   = implode(",", $id_kendaraan['id']);
                    $p_kenID = implode(",", $id_pem_ken['id']);
                }else{
                    $kenID   = null;
                    $p_kenID = null;
                }

                $valid = ValidModel::create($dataValidasi);
                $id_valid = $valid->id;

                $verif = VerifModel::create($dataVerifikasi);
                $id_verif = $verif->id;


                $kap = KapBulanan::create($kapBul);
                $id_kapbul = $kap->id;

                if (!empty($dataKeUsaha)) {
                    $keuangan = PendapatanUsaha::create($dataKeUsaha);
                    $id_usaha = $keuangan->id;
                }else{
                    $id_usaha = null;
                }

                if (!empty($recom_AO)) {
                    $recom = RekomendasiAO::create($recom_AO);
                    $id_recom = $recom->id;
                }else{
                    $id_recom = null;
                }

                $dataAO = array(
                    'id_validasi'          => $id_valid,
                    'id_verifikasi'        => $id_verif,
                    'id_agunan_tanah'      => $tanID,
                    'id_agunan_kendaraan'  => $kenID,
                    'id_periksa_agunan_tanah'     => $p_tanID,
                    'id_periksa_agunan_kendaraan' => $p_kenID,
                    'id_kapasitas_bulanan' => $id_kapbul,
                    'id_pendapatan_usaha'  => $id_usaha,
                    'id_recom_ao'          => $id_recom
                );

                $arrAO = array_merge($TransAO, $dataAO);

                $new_TransAO = TransAO::create($arrAO);

                TransSO::where('id', $id)->update(['id_trans_ao' => $new_TransAO->id]);

                Debitur::where('id', $check->id_calon_debitur)->update($cadebt);
            }else{
                if (!empty($daAguTa)) {

                    if (!empty($check_ao->id_agunan_tanah)) {
                        $id_aguta = explode(",", $check_ao->id_agunan_tanah);

                        AgunanTanah::whereIn('id', $id_aguta)->delete();
                    }


                    for ($i = 0; $i < count($daAguTa); $i++) {
                        // $x[] = $id_aguta[$i];
                        $tanah = AgunanTanah::create($daAguTa[$i]);

                        $id_tanah['id'][$i] = $tanah->id;
                    }


                    if (!empty($check_ao->id_periksa_agunan_tanah)) {
                        $id_pe_aguta = explode(",", $check_ao->id_periksa_agunan_tanah);

                        PemeriksaanAgunTan::whereIn('id', $id_pe_aguta)->delete();
                    }

                    for ($i = 0; $i < count($pemAguTa); $i++) {

                        $pemAguTa_N[$i] = array_merge(array('id_agunan_tanah' => $id_tanah['id'][$i]), $pemAguTa[$i]);

                        $pemTanah = PemeriksaanAgunTan::create($pemAguTa_N[$i]);

                        $id_pem_tan['id'][$i] = $pemTanah->id;
                    }


                    $tanID   = implode(",", $id_tanah['id']);
                    $p_tanID = implode(",", $id_pem_tan['id']);
                }else{
                    $tanID   = $check_ao->id_agunan_tanah;
                    $p_tanID = $check_ao->id_periksa_agunan_tanah;
                }


                if (!empty($daAguKe)) {

                    if (!empty($check_ao->id_agunan_kendaraan)) {
                        $id_aguke = explode(",", $check_ao->id_agunan_kendaraan);

                        AgunanKendaraan::whereIn('id', $id_aguke)->delete();
                    }

                    for ($i = 0; $i < count($daAguKe); $i++) {

                        $kendaraan = AgunanKendaraan::create($daAguKe[$i]);

                        $id_kendaraan['id'][$i] = $kendaraan->id;
                    }


                    if (!empty($check_ao->id_periksa_agunan_kendaraan)) {
                        $id_pe_aguke = explode(",", $check_ao->id_periksa_agunan_kendaraan);

                        PemeriksaanAgunKen::where('id', $id_pe_aguke)->delete();
                    }

                    for ($i = 0; $i < count($pemAguKe); $i++) {

                        $pemAguKe_N[$i] = array_merge(array('id_agunan_kendaraan' => $id_kendaraan['id'][$i]), $pemAguKe[$i]);

                        $pemKendaraan = PemeriksaanAgunKen::create($pemAguKe_N[$i]);

                        $id_pem_ken['id'][$i] = $pemKendaraan->id;
                    }


                    $kenID   = implode(",", $id_kendaraan['id']);
                    $p_kenID = implode(",", $id_pem_ken['id']);
                }else{
                    $kenID   = $check_ao->id_agunan_kendaraan;
                    $p_kenID = $check_ao->id_periksa_agunan_kendaraan;
                }

                if (!empty($check_ao->id_validasi)) {
                    $valid = ValidModel::where('id', $check_ao->id_validasi)->update($dataValidasi);
                    $id_valid = $check_ao->id_validasi;
                }else{
                    $valid = ValidModel::create($dataValidasi);
                    $id_valid = $valid->id;
                }

                if (!empty($check_ao->id_verifikasi)) {
                    $verif = VerifModel::where('id', $check_ao->id_verifikasi)->update($dataVerifikasi);
                    $id_verif = $check_ao->id_verifikasi;
                }else{
                    $verif = VerifModel::create($dataVerifikasi);
                    $id_verif = $verif->id;
                }

                if (!empty($check_ao->id_kapasitas_bulanan)) {
                    $kap = KapBulanan::where('id', $check_ao->id_kapasitas_bulanan)->update($kapBul);
                    $id_kapbul = $check_ao->id_kapasitas_bulanan;
                }else{
                    $kap = KapBulanan::create($kapBul);
                    $id_kapbul = $kap->id;
                }

                if (!empty($check_ao->id_pendapatan_usaha)) {
                    if (!empty($dataKeUsaha)) {
                        $keuangan = PendapatanUsaha::where('id', $check_ao->id_pendapatan_usaha)->update($dataKeUsaha);
                        $id_usaha = $check_ao->id_pendapatan_usaha;
                    }else{
                        $id_usaha = $check_ao->id_pendapatan_usaha;
                    }
                }else{
                    if (!empty($dataKeUsaha)){
                        $keuangan = PendapatanUsaha::create($dataKeUsaha);
                        $id_usaha = $keuangan->id;
                    }else{
                        $id_usaha = null;
                    }
                }

                if (!empty($check_ao->id_recom_ao)){
                    if (!empty($recom_AO)) {
                        $recom = RekomendasiAO::where('id', $check_ao->id_recom_ao)->update($recom_AO);
                        $id_recom = $check_ao->id_recom_ao;
                    }else{
                        $id_recom = $check_ao->id_recom_ao;
                    }
                }else{
                    if (!empty($recom_AO)) {
                        $recom = RekomendasiAO::create($recom_AO);
                        $id_recom = $recom->id;
                    }else{
                        $id_recom = null;
                    }
                }

                $dataAO = array(
                    'id_validasi'          => $id_valid,
                    'id_verifikasi'        => $id_verif,
                    'id_agunan_tanah'      => $tanID,
                    'id_agunan_kendaraan'  => $kenID,
                    'id_periksa_agunan_tanah'     => $p_tanID,
                    'id_periksa_agunan_kendaraan' => $p_kenID,
                    'id_kapasitas_bulanan' => $id_kapbul,
                    'id_pendapatan_usaha'  => $id_usaha,
                    'id_recom_ao'          => $id_recom
                );

                $arrAO = array_merge($TransAO, $dataAO);

                $new_TransAO = TransAO::where('id', $check_ao->id)->update($arrAO);

                TransSO::where('id', $id)->update(['id_trans_ao' => $check_ao->id]);

                Debitur::where('id', $check->id_calon_debitur)->update($cadebt);
            }

            DB::connection('web')->commit();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message'=> 'Data untuk AO berhasil dikirim'
                // 'message'=> $msg
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

    public function search($search, Request $req){
        $user_id  = $req->auth->user_id;

        $pic = PIC::where('user_id', $user_id)->first();

        if ($pic == null) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "User_ID anda adalah '".$user_id."' dengan username '".$req->auth->user."' . Namun anda belum terdaftar sebagai PIC(AO). Harap daftarkan diri sebagai PIC(AO) pada form PIC atau hubungi bagian IT"
            ], 404);
        }

        $id_area   = $pic->id_area;
        $id_cabang = $pic->id_cabang;
        $scope     = $pic->jpic['cakupan'];

        $query_dir = TransSO::with('pic', 'cabang', 'asaldata', 'debt', 'pas', 'faspin', 'ao', 'ca')
                ->where('nomor_so', 'like', '%'.$search.'%')->orderBy('created_at', 'desc');
        $method = 'get';

        $query = Helper::checkDir($user_id, $scope, $query_dir, $id_area, $id_cabang, $method);


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

            if ($val->ao['status_ao'] == 1) {
                $status_ao = 'recommend';
            }elseif ($val->ao['status_ao'] == 2) {
                $status_ao = 'not recommend';
            }else{
                $status_ao = 'waiting';
            }

            $data[$key] = [
                'id'             => $val->id          == null ? null : (int) $val->id,
                'id_trans_ao'    => $val->id_trans_ao == null ? null : (int) $val->id_trans_ao,
                'nomor_so'       => $val->nomor_so,
                // 'nomor_ao'       => $val->ao['nomor_ao'],
                'pic'            => $val->pic['nama'],
                'area'           => $val->area['nama'],
                'cabang'         => $val->cabang['nama'],
                'asal_data'      => $val->asaldata['nama'],
                'nama_marketing' => $val->nama_marketing,
                'nama_debitur'   => $val->debt['nama_lengkap'],
                'plafon'         => (int) $val->faspin['plafon'],
                'tenor'          => (int) $val->faspin['tenor'],
                'das'            => [
                    'status'  => $status_das,
                    'catatan' => $val->catatan_das
                ],
                'hm'            => [
                    'status'  => $status_hm,
                    'catatan' => $val->catatan_hm
                ],
                'ao'            => [
                    'status'  => $status_ao,
                    'catatan' => $val->ao['catatan_ao']
                ]
                // 'das_status'     => $status_das,
                // 'das_note'       => $val->catatan_das,
                // 'hm_status'      => $status_das,
                // 'hm_note'        => $val->catatan_hm,
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
