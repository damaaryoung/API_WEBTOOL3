<?php

namespace App\Http\Controllers\Pengajuan;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Requests\Debt\DebtPenjaminRequest;
use App\Http\Requests\Debt\DebtPasanganRequest;
use App\Http\Controllers\Controller as Helper;
use App\Http\Requests\Bisnis\MCC\UpdateMCCReq;
use App\Http\Requests\Debt\FasPinRequest;
use App\Http\Requests\Debt\DebtRequest;
use App\Models\CC\FasilitasPinjaman;
use Illuminate\Support\Facades\File;
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
use Image;
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

        $nama_anak = explode (",",$val->nama_anak);
        $tgl_anak  = explode (",",$val->tgl_lahir_anak);

        for ($i = 0; $i < count($nama_anak); $i++) {
            $anak[] = [
                'nama'  => $nama_anak[$i],
                'tgl_lahir' => $tgl_anak[$i]
            ];
        }

        $pen = Penjamin::select('id','nama_ktp','nama_ibu_kandung','no_ktp','no_npwp','tempat_lahir','tgl_lahir','jenis_kelamin','alamat_ktp','no_telp','hubungan_debitur','lamp_ktp','lamp_ktp_pasangan','lamp_kk','lamp_buku_nikah')
            ->where('id_calon_debitur', $val->id_calon_debt)
            ->get();

        foreach ($pen as $key => $value) {
            $penjamin[$key] = [
                "id"               => $value->id,
                "nama"             => $value->nama_ktp,
                "nama_ibu_kandung" => $value->nama_ibu_kandung,
                "no_ktp"           => $value->no_ktp,
                "no_npwp"          => $value->no_npwp,
                "tempat_lahir"     => $value->tempat_lahir,
                "tgl_lahir"        => $value->tgl_lahir,
                "jenis_kelamin"    => $value->jenis_kelamin,
                "alamat_ktp"       => $value->alamat_ktp,
                "no_telp"          => $value->no_telp,
                "hubungan_debitur" => $value->hubungan_debitur,
                "lampiran" => [
                    "lamp_ktp"          => $value->lamp_ktp,
                    "lamp_ktp_pasangan" => $value->lamp_ktp_pasangan,
                    "lamp_kk"           => $value->lamp_kk,
                    "lamp_buku_nikah"   => $value->lamp_buku_nikah
                ]
            ];
        }

        $res = [
            'id'                    => $val->id,
            'nomor_so'              => $val->nomor_so,
            'kode_kantor'           => $val->kode_kantor,
            'nama_so'               => $val->nama_so,
            'asal_data' => [
                'id'   => $val->id_asal_data,
                'nama' => $val->asaldata['nama'],
            ],
            'nama_marketing'    => $val->nama_marketing,
            'fasilitas_pinjaman'  => [
                'id'              => $val->id_fasilitas_pinjaman,
                'jenis_pinjaman'  => $val->faspin['jenis_pinjaman'],
                'tujuan_pinjaman' => $val->faspin['tujuan_pinjaman'],
                'plafon'          => $val->faspin['plafon'],
                'tenor'           => $val->faspin['tenor']
            ],
            'calon_debitur'          => [
                'id'                => $val->id_calon_debt,
                'nama_lengkap'      => $val->debt['nama_lengkap'],
                'gelar_keagamaan'   => $val->debt['gelar_keagamaan'],
                'gelar_pendidikan'  => $val->debt['gelar_pendidikan'],
                'jenis_kelamin'     => $val->debt['jenis_kelamin'],
                'status_nikah'      => $val->debt['status_nikah'],
                'ibu_kandung'       => $val->debt['ibu_kandung'],
                'no_ktp'            => $val->debt['no_ktp'],
                'no_ktp_kk'         => $val->debt['no_ktp_kk'],
                'no_kk'             => $val->debt['no_kk'],
                'no_npwp'           => $val->debt['no_npwp'],
                'tempat_lahir'      => $val->debt['tempat_lahir'],
                'tgl_lahir'         => $val->debt['tgl_lahir'],
                'agama'             => $val->debt['agama'],
                'alamat_ktp' => [
                    'alamat_singkat' => $val->debt['alamat_ktp'],
                    'rt'     => $val->debt['rt_ktp'],
                    'rw'     => $val->debt['rw_ktp'],
                    'kelurahan' => [
                        'id'    => $val->debt['kel_ktp']['id'],
                        'nama'  => $val->debt['kel_ktp']['nama']
                    ],
                    'kecamatan' => [
                        'id'    => $val->debt['kec_ktp']['id'],
                        'nama'  => $val->debt['kec_ktp']['nama']
                    ],
                    'kabupaten' => [
                        'id'    => $val->debt['kab_ktp']['id'],
                        'nama'  => $val->debt['kab_ktp']['nama'],
                    ],
                    'povinsi'  => [
                        'id'   => $val->debt['prov_ktp']['id'],
                        'nama' => $val->debt['prov_ktp']['nama'],
                    ],
                    'kode_pos' => $val->debt['kel_ktp']['kode_pos']
                ],
                'alamat_domisili' => [
                    'alamat_singkat' => $val->debt['alamat_domisili'],
                    'rt'             => $val->debt['rt_domisili'],
                    'rw'             => $val->debt['rw_domisili'],
                    'kelurahan' => [
                        'id'    => $val->debt['kel_dom']['id'],
                        'nama'  => $val->debt['kel_dom']['nama']
                    ],
                    'kecamatan' => [
                        'id'    => $val->debt['kec_dom']['id'],
                        'nama'  => $val->debt['kec_dom']['nama']
                    ],
                    'kabupaten' => [
                        'id'    => $val->debt['kab_dom']['id'],
                        'nama'  => $val->debt['kab_dom']['nama'],
                    ],
                    'povinsi'  => [
                        'id'   => $val->debt['prov_dom']['id'],
                        'nama' => $val->debt['prov_dom']['nama'],
                    ],
                    'kode_pos' => $val->debt['kel_dom']['kode_pos']
                ],


                'pendidikan_terakhir'   => $val->debt['pendidikan_terakhir'],
                'jumlah_tanggungan'     => $val->debt['jumlah_tanggungan'],
                'no_telp'               => $val->debt['no_telp'],
                'no_hp'                 => $val->debt['no_hp'],
                'alamat_surat'          => $val->debt['alamat_surat'],
                'lampiran' => [
                    // 'lamp_surat_cerai'      => $val->debt['lamp_surat_cerai'],
                    'lamp_ktp'              => $val->debt['lamp_ktp'],
                    'lamp_kk'               => $val->debt['lamp_kk'],
                    // 'lamp_buku_tabungan'    => $val->debt['lamp_buku_tabungan'],
                    'lamp_sttp_pbb'         => $val->debt['lamp_sttp_pbb'],
                    'lamp_sertifikat'       => $val->debt['lamp_sertifikat'],
                    'lamp_imb'              => $val->debt['lamp_imb'],
                    // 'lamp_sku'              => $val->debt['lamp_sku'],
                    // 'lamp_slip_gaji'        => $val->debt['lamp_slip_gaji'],
                    'lamp_foto_usaha'       => $val->debt['lamp_foto_usaha']
                ]
            ],

            'pasangan'         => [
                'id'                => $val->id_pasangan,
                'nama'              => $val->pas['nama_lengkap'],
                'nama_ibu_kandung'    => $val->pas['nama_ibu_kandung'],
                // 'gelar_keagamaan'     => $pas['gelar_keagamaan'],
                // 'gelar_pendidikan'    => $pas['gelar_pendidikan'],
                'jenis_kelamin'       => $val->pas['jenis_kelamin'],
                'no_ktp'              => $val->pas['no_ktp'],
                'no_ktp_kk'           => $val->pas['no_ktp_kk'],
                'no_npwp'             => $val->pas['no_npwp'],
                'tempat_lahir'        => $val->pas['tempat_lahir'],
                'tgl_lahir'           => $val->pas['tgl_lahir'],
                'alamat_ktp'          => $val->pas['alamat_ktp'],
                'no_telp'             => $val->pas['no_telp'],
                'lampiran' => [
                    'lamp_ktp'        => $val->pas['lamp_ktp'],
                    'lamp_buku_nikah' => $val->pas['lamp_buku_nikah']
                ]
            ],
            'penjamin'  => $penjamin,
            'flg_aktif' => $val->flg_aktif == 0 ? "true" : "false"
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

    public function update($id, Request $request, UpdateMCCReq $req){
        $user_id     = $request->auth->user_id;
        $username    = $request->auth->user;

        $trans = TransSo::where('id', $id)->first();

        if (!$trans) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Data kosong!!"
            ], 404);
        }

        $PIC = PIC::where('user_id', $user_id)->first();

        if (!$PIC) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "User_ID anda adalah '".$user_id."' dengan username '".$username."' . Namun anda belum terdaftar sebagai PIC. Harap daftarkan diri sebagai PIC pada form PIC atau hubungi bagian IT"
            ], 404);
        }

        $dataTr = array(
            'id_asal_data'   => empty($req->input('id_asal_data')) ? $trans->id_asal_data : $req->input('id_asal_data'),
            'nama_marketing' => empty($req->input('nama_marketing')) ? $trans->nama_marketing : $req->input('nama_marketing')
        );

        // Data Fasilitas Pinjaman
        $dataFasPin = array(
            'jenis_pinjaman'  => empty($req->input('jenis_pinjaman')) ? $trans->faspin['jenis_pinjaman'] : $req->input('jenis_pinjaman'),
            'tujuan_pinjaman' => empty($req->input('tujuan_pinjaman')) ? $trans->faspin['tujuan_pinjaman'] : $req->input('tujuan_pinjaman'),
            'plafon'          => empty($req->input('plafon_pinjaman')) ? $trans->faspin['plafon_pinjaman'] : $req->input('plafon_pinjaman'),
            'tenor'           => empty($req->input('tenor_pinjaman')) ? $trans->faspin['tenor_pinjaman'] : $req->input('tenor_pinjaman')
        );

        $lamp_dir = 'public/lamp_trans.'.$trans->nomor_so;

        if($file = $req->file('lamp_ktp')){
            $path = $lamp_dir.'/debitur';
            $name = 'ktp.'.$file->getClientOriginalExtension();

            if(!empty($trans->debt['lamp_ktp']))
            {
                File::delete($trans->debt['lamp_ktp']);
            }

            $file->move($path,$name);

            $ktpDebt = $path.'/'.$name;
        }else{
            $ktpDebt = null;
        }

        if($file = $req->file('lamp_kk')){
            $path = $lamp_dir.'/debitur';
            $name = 'kk.'.$file->getClientOriginalExtension();

            if(!empty($trans->debt['lamp_kk']))
            {
                File::delete($trans->debt['lamp_kk']);
            }

            $file->move($path,$name);

            $kkDebt = $path.'/'.$name;
        }else{
            $kkDebt = null;
        }

        if($file = $req->file('lamp_sertifikat')){
            $path = $lamp_dir.'/debitur';
            $name = 'sertifikat.'.$file->getClientOriginalExtension();

            if(!empty($trans->debt['lamp_sertifikat']))
            {
                File::delete($trans->debt['lamp_sertifikat']);
            }

            $file->move($path,$name);

            $sertifikatDebt = $path.'/'.$name;
        }else{
            $sertifikatDebt = null;
        }

        if($file = $req->file('lamp_pbb')){
            $path = $lamp_dir.'/debitur';
            $name = 'pbb.'.$file->getClientOriginalExtension();

            if(!empty($trans->debt['lamp_pbb']))
            {
                File::delete($trans->debt['lamp_pbb']);
            }

            $file->move($path,$name);

            $pbbDebt = $path.'/'.$name;
        }else{
            $pbbDebt = null;
        }

        if($file = $req->file('lamp_imb')){
            $path = $lamp_dir.'/debitur';
            $name = 'imb.'.$file->getClientOriginalExtension();

            if(!empty($trans->debt['lamp_imb']))
            {
                File::delete($trans->debt['lamp_imb']);
            }

            $file->move($path,$name);

            $imbDebt = $path.'/'.$name;
        }else{
            $imbDebt = null;
        }

        // Data Calon Debitur
        $dataDebitur = array(
            'nama_lengkap'          => empty($req->input('nama_lengkap')) ? $check->debt['nama_lengkap'] : $req->input('nama_lengkap'),
            'gelar_keagamaan'       => empty($req->input('gelar_keagamaan')) ? $check->debt['gelar_keagamaan'] : $req->input('gelar_keagamaan'),
            'gelar_pendidikan'      => empty($req->input('gelar_pendidikan')) ? $check->debt['gelar_pendidikan'] : $req->input('gelar_pendidikan'),
            'jenis_kelamin'         => empty($req->input('jenis_kelamin')) ? $check->debt['jenis_kelamin'] : strtoupper($req->input('jenis_kelamin')),
            'status_nikah'          => empty($req->input('status_nikah')) ? $check->debt['status_nikah'] : strtoupper($req->input('status_nikah')),
            'ibu_kandung'           => empty($req->input('ibu_kandung')) ? $check->debt['ibu_kandung']: $req->input('ibu_kandung'),
            'no_ktp'                => empty($req->input('no_ktp')) ? $check->debt['no_ktp'] : $req->input('no_ktp'),
            // 'no_ktp_kk'             => empty($req->input('no_ktp_kk')) ? $check->debt['no_ktp_kk'] : $req->input('no_ktp_kk'),
            // 'no_kk'                 => empty($req->input('no_kk')) ? $check->debt['no_kk'] : $req->input('no_kk'),
            // 'no_npwp'               => empty($req->input('no_npwp')) ? $check->debt['no_npwp'] : $req->input('no_npwp'),
            'tempat_lahir'          => empty($req->input('tempat_lahir')) ? $check->debt['tempat_lahir']: $req->input('tempat_lahir'),
            'tgl_lahir'             => empty($req->input('tgl_lahir')) ? $check->debt['tgl_lahir'] : Carbon::parse($req->input('tgl_lahir'))->format('Y-m-d'),
            'agama'                 => empty($req->input('agama')) ? $check->debt['agama'] : strtoupper($req->input('agama')),
            'alamat_ktp'            => empty($req->input('alamat_ktp')) ? $check->debt['alamat_ktp'] : $req->input('alamat_ktp'),
            'rt_ktp'                => empty($req->input('rt_ktp')) ? $check->debt['rt_ktp'] : $req->input('rt_ktp'),
            'rw_ktp'                => empty($req->input('rw_ktp')) ? $check->debt['rw_ktp'] : $req->input('rw_ktp'),
            'id_prov_ktp'           => empty($req->input('id_provinsi_ktp')) ? $check->debt['id_prov_ktp'] : $req->input('id_provinsi_ktp'),
            'id_kab_ktp'            => empty($req->input('id_kabupaten_ktp')) ? $check->debt['id_kab_ktp'] : $req->input('id_kabupaten_ktp'),
            'id_kec_ktp'            => empty($req->input('id_kecamatan_ktp')) ? $check->debt['id_kec_ktp'] : $req->input('id_kecamatan_ktp'),
            'id_kel_ktp'            => empty($req->input('id_kelurahan_ktp')) ? $check->debt['id_kel_ktp'] : $req->input('id_kelurahan_ktp'),
            'alamat_domisili'       => empty($req->input('alamat_domisili')) ? $check->debt['alamat_domisili'] : $req->input('alamat_domisili'),
            'rt_domisili'           => empty($req->input('rt_domisili')) ? $check->debt['rt_domisili'] : $req->input('rt_domisili'),
            'rw_domisili'           => empty($req->input('rw_domisili')) ? $check->debt['rw_domisili'] : $req->input('rw_domisili'),
            'id_prov_domisili'      => empty($req->input('id_provinsi_domisili')) ? $check->debt['id_prov_domisili'] : $req->input('id_provinsi_domisili'),
            'id_kab_domisili'       => empty($req->input('id_kabupaten_domisili')) ? $check->debt['id_kab_domisili'] : $req->input('id_kabupaten_domisili'),
            'id_kec_domisili'       => empty($req->input('id_kecamatan_domisili')) ? $check->debt['id_kec_domisili'] : $req->input('id_kecamatan_domisili'),
            'id_kel_domisili'       => empty($req->input('id_kelurahan_domisili')) ? $check->debt['id_kel_domisili'] : $req->input('id_kelurahan_domisili'),
            'pendidikan_terakhir'   => empty($req->input('pendidikan_terakhir')) ? $check->debt['pendidikan_terakhir'] : $req->input('pendidikan_terakhir'),
            'jumlah_tanggungan'     => empty($req->input('jumlah_tanggungan')) ? $check->debt['jumlah_tanggungan'] : $req->input('jumlah_tanggungan'),
            // 'no_telp'               => empty($req->input('no_telp')) ? $check->debt['no_telp'] : $req->input('no_telp'),
            // 'no_hp'                 => empty($req->input('no_hp')) ? $check->debt['no_hp'] : $req->input('no_hp'),
            'alamat_surat'          => empty($req->input('alamat_surat')) ? $check->debt['alamat_surat'] : $req->input('alamat_surat'),
            'lamp_ktp'              => $ktpDebt
            // 'lamp_kk'               => $kkDebt,
            // 'lamp_sertifikat'       => $sertifikatDebt,
            // 'lamp_sttp_pbb'         => $pbbDebt,
            // 'lamp_imb'              => $imbDebt
        );

        dd($dataDebitur);

        // if($file = $reqPas->file('lamp_ktp_pas')){
        //     $path = $lamp_dir.'/pasangan';
        //     $name = 'ktp.'.$file->getClientOriginalExtension();
        //     $file->move($path,$name);

        //     $ktpPass = $path.'/'.$name;
        // }else{
        //     $ktpPass = null;
        // }

        // if($file = $reqPas->file('lamp_buku_nikah_pas')){
        //     $path = $lamp_dir.'/pasangan';
        //     $name = 'buku_nikah.'.$file->getClientOriginalExtension();
        //     $file->move($path,$name);

        //     $bukuNikahPass = $path.'/'.$name;
        // }else{
        //     $bukuNikahPass = null;
        // }

        // Data Pasangan Calon Debitur
        // $dataPasangan = array(
        //     'nama_lengkap'     => empty($reqPas->input('nama_lengkap_pas')) ? $check->nama_lengkap : $reqPas->input('nama_lengkap_pas'),
        //     'nama_ibu_kandung' => empty($reqPas->input('nama_ibu_kandung_pas')) ? $check->nama_ibu_kandung : $reqPas->input('nama_ibu_kandung_pas'),
        //     'jenis_kelamin'    => strtoupper($reqPas->input('jenis_kelamin_pas')),
        //     'no_ktp'           => empty($req->input('no_ktp_pas')) ? $check->no_ktp : ($req->input('no_ktp_pas') == $check->no_ktp ? $check->no_ktp : $reqPas->input('no_ktp_pas')),
        //     'no_ktp_kk'        => $reqPas->input('no_ktp_kk_pas'),
        //     'no_npwp'          => $reqPas->input('no_npwp_pas'),
        //     'tempat_lahir'     => $reqPas->input('tempat_lahir_pas'),
        //     'tgl_lahir'        => Carbon::parse($reqPas->input('tgl_lahir_pas'))->format('Y-m-d'),
        //     'alamat_ktp'       => $reqPas->input('alamat_ktp_pas'),
        //     'no_telp'          => $reqPas->input('no_telp_pas'),
        //     'lamp_ktp'         => $ktpPass,
        //     'lamp_buku_nikah'  => $bukuNikahPass
        // );
    }
}
