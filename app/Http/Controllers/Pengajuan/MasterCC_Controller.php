<?php

namespace App\Http\Controllers\Pengajuan;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Requests\Debt\DebtPenjaminRequest;
use App\Http\Requests\Debt\DebtPasanganRequest;
use App\Http\Controllers\Controller as Helper;
use App\Http\Requests\Debt\FasPinRequest;
use App\Http\Requests\Debt\DebtRequest;
use App\Models\CC\FasilitasPinjaman;
use App\Models\AreaKantor\Cabang;
use App\Models\AreaKantor\JPIC;
use App\Models\AreaKantor\PIC;
use App\Models\Bisnis\TransSo;
use App\Models\KeuanganUsaha;
use Illuminate\Http\Request;
use App\Models\CC\Pasangan;
use App\Models\CC\Penjamin;
use App\Models\CC\Debitur;
use App\Http\Requests;
Use App\Models\User;
use Carbon\Carbon;
// use Image;
use DB;

class MasterCC_Controller extends BaseController
{
    public function index(Request $req){
        $user_id = $req->auth->user_id;
        $query = TransSo::with('asaldata','debt')
                ->select('id', 'nomor_so', 'kode_kantor', 'nama_so', 'id_asal_data', 'nama_marketing',
        'id_calon_debt')
                ->where('user_id', $user_id)
                ->get();

        if (!$query) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Data kosong!!"
            ], 404);
        }

        foreach ($query as $key => $val) {
            $res[$key] = [
                'id'            => $val->id,
                'nomor_so'      => $val->nomor_so,
                'kode_kantor'   => $val->kode_kantor,
                'nama_so'       => $val->nama_so,
                'id_asal_data'  => $val->asaldata['nama'],
                'nama_marketing'=> $val->nama_marketing,
                'nama_calon_debt' => $val->debt['nama_lengkap']
            ];
        }

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => $res
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
        $val = TransSo::with('asaldata','debt')
                ->where('id', $id)
                ->where('user_id', $user_id)
                ->first();

        if (!$val) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Data kosong!!"
            ], 404);
        }

        $nama_anak = explode (",",$val->debt['nama_anak']);
        $tgl_anak = explode (",",$val->debt['tgl_lahir_anak']);

        for ($i = 0; $i < count($nama_anak); $i++) {
            $anak[] = [
                'nama'  => $nama_anak[$i],
                'tgl_lahir' => $tgl_anak[$i]
            ];
        }

        $debt = Debitur::with('prov_ktp','kab_ktp', 'kec_ktp','kel_ktp','prov_dom','kab_dom','kec_dom','kel_dom','prov_kerja','kab_kerja','kec_kerja','kel_kerja')
            ->where('id', $val->id_calon_debt)->first();

        // $penjamin = Penjamin::where('id_calon_debitur', $query[0]->id_calon_debt)->get();

        $res = [
            'id'                    => $val->id,
            'nomor_so'              => $val->nomor_so,
            'kode_kantor'           => $val->kode_kantor,
            'nama_so'               => $val->nama_so,
            'id_asal_data'          => $val->id_asal_data,
            'asal_data'             => $val->asaldata['nama'],
            'nama_marketing'        => $val->nama_marketing,
            'id_fasilitas_pinjaman' => $val->id_fasilitas_pinjaman,
            'jenis_pinjaman'        => $val->faspin['jenis_pinjaman'],
            'tujuan_pinjaman'       => $val->faspin['tujuan_pinjaman'],
            'plafon'                => $val->faspin['plafon'],
            'tenor'                 => $val->faspin['tenor'],
            'id_calon_debt'         => $val->id_calon_debt,
            'nama_calon_debt'       => $val->debt['nama_lengkap'],

            // 'debt_all'              => $val->debt,

            'gelar_keagamaan'       => $val->debt['gelar_keagamaan'],
            'gelar_pendidikan'      => $val->debt['gelar_pendidikan'],
            'jenis_kelamin'         => $val->debt['jenis_kelamin'],
            'status_nikah'          => $val->debt['status_nikah'],
            'ibu_kandung'           => $val->debt['ibu_kandung'],
            'no_ktp'                => $val->debt['no_ktp'],
            'no_ktp_kk'             => $val->debt['no_ktp_kk'],
            'no_kk'                 => $val->debt['no_kk'],
            'no_npwp'               => $val->debt['no_npwp'],
            'tempat_lahir'          => $val->debt['tempat_lahir'],
            'tgl_lahir'             => $val->debt['tgl_lahir'],
            'agama'                 => $val->debt['agama'],
            'alamat_ktp'            => $val->debt['alamat_ktp'],
            'rt_ktp'                => $val->debt['rt_ktp'],
            'rw_ktp'                => $val->debt['rw_ktp'],

            'id_prov_ktp'           => $val->debt['id_prov_ktp'],
            'nama_prov_ktp'         => $val->prov['nama'],
            'id_prov_ktp2'          => $debt->id_provinsi_ktp,
            'nama_prov_ktp2'        => $debt->kab_ktp['nama'],

            'id_kab_ktp'            => $val->debt['id_kab_ktp'],
            'id_kec_ktp'            => $val->debt['id_kec_ktp'],
            'id_kel_ktp'            => $val->debt['id_kel_ktp'],

            'alamat_domisili'       => $val->debt['alamat_domisili'],
            'rt_domisili'           => $val->debt['rt_domisili'],
            'rw_domisili'           => $val->debt['rw_domisili'],

            'id_prov_domisili'      => $val->debt['id_prov_domisili'],
            'id_kab_domisili'       => $val->debt['id_kab_domisili'],
            'id_kec_domisili'       => $val->debt['id_kec_domisili'],
            'id_kel_domisili'       => $val->debt['id_kel_domisili'],

            'pendidikan_terakhir'   => $val->debt['pendidikan_terakhir'],
            'jumlah_tanggungan'     => $val->debt['jumlah_tanggungan'],
            'no_telp'               => $val->debt['no_telp'],
            'no_hp'                 => $val->debt['no_hp'],
            'alamat_surat'          => $val->debt['alamat_surat'],

            'anak'                  => $anak,

            'tinggi_badan'          => $val->debt['tinggi_badan'],
            'berat_badan'           => $val->debt['berat_badan'],
            'pekerjaan'             => $val->debt['pekerjaan'],
            'posisi'                => $val->debt['posisi'],
            'nama_tempat_kerja'     => $val->debt['nama_tempat_kerja'],
            'jenis_pekerjaan'       => $val->debt['jenis_pekerjaan'],
            'alamat_tempat_kerja'   => $val->debt['alamat_tempat_kerja'],

            'id_prov_tempat_kerja'  => $val->debt['id_prov_tempat_kerja'],
            'id_kab_tempat_kerja'   => $val->debt['id_kab_tempat_kerja'],
            'id_kec_tempat_kerja'   => $val->debt['id_kec_tempat_kerja'],
            'id_kel_tempat_kerja'   => $val->debt['id_kel_tempat_kerja'],

            'rt_tempat_kerja'       => $val->debt['rt_tempat_kerja'],
            'rw_tempat_kerja'       => $val->debt['rw_tempat_kerja'],
            'tgl_mulai_kerja'       => $val->debt['tgl_mulai_kerja'],
            'no_telp_tempat_kerja'  => $val->debt['no_telp_tempat_kerja'],
            'lamp_surat_cerai'      => $val->debt['lamp_surat_cerai'],
            'lamp_ktp'              => $val->debt['lamp_ktp'],
            'lamp_kk'               => $val->debt['lamp_kk'],
            'lamp_buku_tabungan'    => $val->debt['lamp_buku_tabungan'],
            'lamp_sttp_pbb'         => $val->debt['lamp_sttp_pbb'],
            'lamp_sertifikat'       => $val->debt['lamp_sertifikat'],
            'lamp_imb'              => $val->debt['lamp_imb'],
            'lamp_sku'              => $val->debt['lamp_sku'],
            'lamp_slip_gaji'        => $val->debt['lamp_slip_gaji'],
            'lamp_foto_usaha'       => $val->debt['lamp_foto_usaha'],

            'id_pasangan'           => $val->id_pasangan,
            'id_penjamin'           => $val->id_pasangan,
            'id_agunan_tanah'       => $val->id_agunan_tanah,
            'id_agunan_kendaraan'   => $val->id_agunan_kendaraan,
            'id_periksa_agunan_tanah'=> $val->id_periksa_agunan_tanah,
            'id_periksa_agunan_kendaraan'=>$val->id_periksa_agunan_tanah,
            'id_usaha'              => $val->id_usaha,
            'flg_aktif'             => $val->flg_aktif == 0 ? "true" : "false"
        ];

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => $res
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function store(Request $req, FasPinRequest $reqFasPin, DebtRequest $reqDebt, DebtPasanganRequest $reqPas, DebtPenjaminRequest $reqPen) {

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

        $countTSO = TransSo::latest('id','nomor_so')->first();

        if (!$countTSO) {
            $lastNumb = 1;
        }else{
            $no = $countTSO->nomor_so;

            $arr = explode("-", $no, 5);

            $lastNumb = $arr[4] + 1;

            // $no = $countTSO + 1;
        }
        //Data Transaksi SO
        $now   = Carbon::now();
        $year  = $now->year;
        $month = $now->month;

        $JPIC   = JPIC::where('id', $PIC->id_mj_pic)->first();

        //  ID-Cabang - AO / CA / SO - Bulan - Tahun - NO. Urut
        $nomor_so = $PIC->id_mk_cabang.'-'.$JPIC->nama_jenis.'-'.$month.'-'.$year.'-'.$lastNumb; //  ID-Cabang - AO / CA / SO - Bulan - Tahun - NO. Urut
        $dataTr = array(
            'nomor_so'       => $nomor_so,
            'user_id'        => $user_id,
            'kode_kantor'    => $PIC->id_mk_cabang,
            'nama_so'        => $PIC->nama,
            'id_asal_data'   => $req->input('id_asal_data'),
            'nama_marketing' => $req->input('nama_marketing')
        );

        // Data Fasilitas Pinjaman
        $dataFasPin = array(
            'jenis_pinjaman'  => $reqFasPin->input('jenis_pinjaman'),
            'tujuan_pinjaman' => $reqFasPin->input('tujuan_pinjaman'),
            'plafon'          => $reqFasPin->input('plafon_pinjaman'),
            'tenor'           => $reqFasPin->input('tenor_pinjaman')
        );

        $lamp_dir = 'public/lamp_trans.'.$nomor_so;

        if($file = $reqDebt->file('lamp_ktp')){
            $path = $lamp_dir.'/debitur';
            $name = 'ktp.'.$file->getClientOriginalExtension();
            $file->move($path,$name);

            $ktpDebt = $path.'/'.$name;
        }else{
            $ktpDebt = null;
        }

        if($file = $reqDebt->file('lamp_kk')){
            $path = $lamp_dir.'/debitur';
            $name = 'kk.'.$file->getClientOriginalExtension();
            $file->move($path,$name);

            $kkDebt = $path.'/'.$name;
        }else{
            $kkDebt = null;
        }

        if($file = $reqDebt->file('lamp_sertifikat')){
            $path = $lamp_dir.'/debitur';
            $name = 'sertifikat.'.$file->getClientOriginalExtension();
            $file->move($path,$name);

            $sertifikatDebt = $path.'/'.$name;
        }else{
            $sertifikatDebt = null;
        }

        if($file = $reqDebt->file('lamp_pbb')){
            $path = $lamp_dir.'/debitur';
            $name = 'pbb.'.$file->getClientOriginalExtension();
            $file->move($path,$name);

            $pbbDebt = $path.'/'.$name;
        }else{
            $pbbDebt = null;
        }

        if($file = $reqDebt->file('lamp_imb')){
            $path = $lamp_dir.'/debitur';
            $name = 'imb.'.$file->getClientOriginalExtension();
            $file->move($path,$name);

            $imbDebt = $path.'/'.$name;
        }else{
            $imbDebt = null;
        }

        // Data Calon Debitur
        $dataDebitur = array(
            'nama_lengkap'          => $reqDebt->input('nama_lengkap'),
            'gelar_keagamaan'       => $reqDebt->input('gelar_keagamaan'),
            'gelar_pendidikan'      => $reqDebt->input('gelar_pendidikan'),
            'jenis_kelamin'         => strtoupper($reqDebt->input('jenis_kelamin')),
            'status_nikah'          => strtoupper($reqDebt->input('status_nikah')),
            'ibu_kandung'           => $reqDebt->input('ibu_kandung'),
            'no_ktp'                => $reqDebt->input('no_ktp'),
            'no_ktp_kk'             => $reqDebt->input('no_ktp_kk'),
            'no_kk'                 => $reqDebt->input('no_kk'),
            'no_npwp'               => $reqDebt->input('no_npwp'),
            'tempat_lahir'          => $reqDebt->input('tempat_lahir'),
            'tgl_lahir'             => Carbon::parse($reqDebt->input('tgl_lahir'))->format('Y-m-d'),
            'agama'                 => strtoupper($reqDebt->input('agama')),
            'alamat_ktp'            => $reqDebt->input('alamat_ktp'),
            'rt_ktp'                => $reqDebt->input('rt_ktp'),
            'rw_ktp'                => $reqDebt->input('rw_ktp'),
            'id_prov_ktp'           => $reqDebt->input('id_provinsi_ktp'),
            'id_kab_ktp'            => $reqDebt->input('id_kabupaten_ktp'),
            'id_kec_ktp'            => $reqDebt->input('id_kecamatan_ktp'),
            'id_kel_ktp'            => $reqDebt->input('id_kelurahan_ktp'),
            'alamat_domisili'       => $reqDebt->input('alamat_domisili'),
            'rt_domisili'           => $reqDebt->input('rt_domisili'),
            'rw_domisili'           => $reqDebt->input('rw_domisili'),
            'id_prov_domisili'      => $reqDebt->input('id_provinsi_domisili'),
            'id_kab_domisili'       => $reqDebt->input('id_kabupaten_domisili'),
            'id_kec_domisili'       => $reqDebt->input('id_kecamatan_domisili'),
            'id_kel_domisili'       => $reqDebt->input('id_kelurahan_domisili'),
            'pendidikan_terakhir'   => $reqDebt->input('pendidikan_terakhir'),
            'jumlah_tanggungan'     => $reqDebt->input('jumlah_tanggungan'),
            'no_telp'               => $reqDebt->input('no_telp'),
            'no_hp'                 => $reqDebt->input('no_hp'),
            'alamat_surat'          => $reqDebt->input('alamat_surat'),
            'lamp_ktp'              => $ktpDebt,
            'lamp_kk'               => $kkDebt,
            'lamp_sertifikat'       => $sertifikatDebt,
            'lamp_sttp_pbb'         => $pbbDebt,
            'lamp_imb'              => $imbDebt
        );

        if($file = $reqPas->file('lamp_ktp_pas')){
            $path = $lamp_dir.'/pasangan';
            $name = 'ktp.'.$file->getClientOriginalExtension();
            $file->move($path,$name);

            $ktpPass = $path.'/'.$name;
        }else{
            $ktpPass = null;
        }

        if($file = $reqPas->file('lamp_buku_nikah_pas')){
            $path = $lamp_dir.'/pasangan';
            $name = 'buku_nikah.'.$file->getClientOriginalExtension();
            $file->move($path,$name);

            $bukuNikahPass = $path.'/'.$name;
        }else{
            $bukuNikahPass = null;
        }

        // Data Pasangan Calon Debitur
        $dataPasangan = array(
            'nama_lengkap'     => $reqPas->input('nama_lengkap_pas'),
            'nama_ibu_kandung' => $reqPas->input('nama_ibu_kandung_pas'),
            'jenis_kelamin'    => strtoupper($reqPas->input('jenis_kelamin_pas')),
            'no_ktp'           => $reqPas->input('no_ktp_pas'),
            'no_ktp_kk'        => $reqPas->input('no_ktp_kk_pas'),
            'no_npwp'          => $reqPas->input('no_npwp_pas'),
            'tempat_lahir'     => $reqPas->input('tempat_lahir_pas'),
            'tgl_lahir'        => Carbon::parse($reqPas->input('tgl_lahir_pas'))->format('Y-m-d'),
            'alamat_ktp'       => $reqPas->input('alamat_ktp_pas'),
            'no_telp'          => $reqPas->input('no_telp_pas'),
            'lamp_ktp'         => $ktpPass,
            'lamp_buku_nikah'  => $bukuNikahPass
        );

        DB::connection('web')->beginTransaction();
        try {
            $debt = Debitur::create($dataDebitur);
            $id_debt = $debt->id;

            $arrIdDebt = array('id_calon_debitur' => $id_debt);

            if ($dataFasPin) {
                $newFasPin = array_merge($arrIdDebt, $dataFasPin);
                $FasPin    = FasilitasPinjaman::create($newFasPin);
                $id_faspin = $FasPin->id;
            }else{
                $id_faspin = null;
            }

            if ($dataDebitur['status_nikah'] == 'NIKAH') {
                $newPass     = array_merge($arrIdDebt, $dataPasangan);
                $pasangan    = Pasangan::create($newPass);
                $id_pasangan = $pasangan->id;
            }else{
                $id_pasangan = null;
            }

            if (!$reqPen) {
                $id_penjamin = null;
            }else{

                $a = 1; $b = 1; $c = 1; $d = 1;

                if($files = $reqPen->file('lamp_ktp_pen')){
                    foreach($files as $file){
                        $path = $lamp_dir.'/penjamin';
                        $name = 'ktp_penjamin'.$a.'.'.$file->getClientOriginalExtension();
                        $file->move($path,$name);
                        $a++;

                        $ktpPen[] = $path.'/'.$name;
                    }
                }

                if($files = $reqPen->file('lamp_ktp_pasangan_pen')){
                    foreach($files as $file){
                        $path = $lamp_dir.'/penjamin';
                        $name = 'ktp_pasangan'.$b.'.'.$file->getClientOriginalExtension();
                        $file->move($path,$name);
                        $b++;

                        $ktpPenPAS[] = $path.'/'.$name;
                    }
                }

                if($files = $reqPen->file('lamp_kk_pen')){
                    foreach($files as $file){
                        $path = $lamp_dir.'/penjamin';
                        $name = 'kk_penjamin'.$c.'.'.$file->getClientOriginalExtension();
                        $file->move($path,$name);
                        $c++;

                        $kkPen[] = $path.'/'.$name;
                   }
                }

                if($files = $reqPen->file('lamp_buku_nikah_pen')){
                    foreach($files as $file){
                        $path = $lamp_dir.'/penjamin';
                        $name = 'buku_nikah_penjamin'.$d.'.'.$file->getClientOriginalExtension();
                        $file->move($path,$name);
                        $d++;

                        $bukuNikahPen[] = $path.'/'.$name;
                    }
                }

                $DP = array();

                if (!empty($reqPen->input('nama_ktp_pen'))) {
                    for ($i = 0; $i < count($reqPen->input('nama_ktp_pen')); $i++) {

                        $DP[] = [
                            'id_calon_debitur' => $id_debt,
                            'nama_ktp'         => empty($reqPen->nama_ktp_pen[$i]) ? null[$i] : $reqPen->nama_ktp_pen[$i],
                            'nama_ibu_kandung' => empty($reqPen->nama_ibu_kandung_pen[$i]) ? null[$i] : $reqPen->nama_ibu_kandung_pen[$i],
                            'no_ktp'           => empty($reqPen->no_ktp_pen[$i]) ? null[$i] : $reqPen->no_ktp_pen[$i],
                            'no_npwp'          => empty($reqPen->no_npwp_pen[$i]) ? null[$i] : $reqPen->no_npwp_pen[$i],
                            'tempat_lahir'     => empty($reqPen->tempat_lahir_pen[$i]) ? null[$i] : $reqPen->tempat_lahir_pen[$i],
                            'tgl_lahir'        => empty($reqPen->tgl_lahir_pen[$i]) ? null[$i] : Carbon::parse($reqPen->tgl_lahir_pen[$i])->format('Y-m-d'),
                            'jenis_kelamin'    => empty($reqPen->jenis_kelamin_pen[$i]) ? null[$i] : strtoupper($reqPen->jenis_kelamin_pen[$i]),
                            'alamat_ktp'       => empty($reqPen->alamat_ktp_pen[$i]) ? null[$i] : $reqPen->alamat_ktp_pen[$i],
                            'no_telp'          => empty($reqPen->no_telp_pen[$i]) ? null[$i] : $reqPen->no_telp_pen[$i],
                            'hubungan_debitur' => empty($reqPen->hubungan_debitur_pen[$i]) ? null[$i] : $reqPen->hubungan_debitur_pen[$i],
                            'lamp_ktp'         => empty($ktpPen[$i]) ? null[$i] : $ktpPen[$i],
                            'lamp_ktp_pasangan'=> empty($ktpPenPAS[$i]) ? null[$i] : $ktpPenPAS[$i],
                            'lamp_kk'          => empty($kkPen[$i]) ? null[$i] : $kkPen[$i],
                            'lamp_buku_nikah'  => empty($bukuNikahPen[$i]) ? null[$i] : $bukuNikahPen[$i],
                            'created_at'       => Carbon::now()->toDateTimeString(),
                            'updated_at'       => Carbon::now()->toDateTimeString()
                        ];


                        if ($DP[$i]['lamp_ktp'] == null) {
                            return response()->json([
                                "code"    => 422,
                                "status"  => "not valid request",
                                "message" => "lamp_ktp_pen ada yang belum diisi"
                            ], 422);
                        }

                        if ($DP[$i]['lamp_ktp_pasangan'] == null) {
                            return response()->json([
                                "code"    => 422,
                                "status"  => "not valid request",
                                "message" => "lamp_ktp_pasangan_penjamin ada yang belum diisi"
                            ], 422);
                        }

                        if ($DP[$i]['lamp_kk'] == null) {
                            return response()->json([
                                "code"    => 422,
                                "status"  => "not valid request",
                                "message" => "lamp_kk ada yang belum diisi"
                            ], 422);
                        }

                        if ($DP[$i]['lamp_buku_nikah'] == null) {
                            return response()->json([
                                "code"    => 422,
                                "status"  => "not valid request",
                                "message" => "lamp_buku_nikah ada yang belum diisi"
                            ], 422);
                        }

                        $penjamin = Penjamin::insert($DP);
                    }
                }
            }

            $pu = Penjamin::select('id')->where('id_calon_debitur', $id_debt)->get();

            if ($pu != '[]') {
                $te = array();
                $i  = 0;

                foreach ($pu as $val) {
                    $te['id'][$i] = $val->id;
                    $i++;
                }

                $id_penjamins = implode(",", $te['id']);
            }else{
                $id_penjamins = null;
            }

            $arrTr = array(
                'id_fasilitas_pinjaman' => $id_faspin,
                'id_calon_debt'         => $id_debt,
                'id_pasangan'           => $id_pasangan,
                'id_penjamin'           => $id_penjamins
            );

            $mergeTr  = array_merge($dataTr, $arrTr);
            TransSo::create($mergeTr);

            DB::connection('web')->commit();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message'=> 'Data berhasil dibuat'
            ], 200);
        }catch (\Exception $e) {
            $err = DB::connection('web')->rollback();
            return response()->json([
                'code'    => 501,
                'status'  => 'error',
                'message' => $err
            ], 501);
        }
    }

    // public function mitra(Request $req){
    //     $mitra_user = DB::connection('dpm')->table('mitra_user')
    //         ->select('id', 'fullname', 'kode_mitra', 'jenis_mitra')
    //         ->where('jenis_mitra', 'MB')
    //         ->where('no_hp_verified', '1')
    //         ->where('user_verifikasi', '!=', 0)
    //         ->where('kode_referal', '!=', null[$i])
    //         ->get();

    //     dd($mitra_user);
    // }
}
