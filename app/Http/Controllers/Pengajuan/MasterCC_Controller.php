<?php

namespace App\Http\Controllers\Pengajuan;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Requests\Debt\DebtPenjaminRequest;
use App\Http\Requests\Debt\DebtPasanganRequest;
use App\Http\Controllers\Controller as Helper;
use App\Http\Requests\Debt\FasPinRequest;
use App\Http\Requests\Debt\UsahaRequest;
use App\Http\Requests\Debt\DebtRequest;
use App\Http\Requests\Debt\AguTaReq;
use App\Models\CC\FasilitasPinjaman;
use App\Models\CC\AgunanTanah;
use App\Models\Bisnis\TransSo;
use Illuminate\Http\Request;
use App\Models\CC\Pasangan;
use App\Models\CC\Penjamin;
use App\Models\CC\Debitur;
use App\Models\CC\Usaha;
use App\Http\Requests;
use App\Models\User;
use Carbon\Carbon;
use Validator;
use Image;
use DB;

class MasterCC_Controller extends BaseController
{
    public function store(Request $req, FasPinRequest $reqFasPin, DebtRequest $reqDebt, DebtPasanganRequest $reqPas, DebtPenjaminRequest $reqPen, UsahaRequest $reqUs, AguTaReq $reqAta) {
    // public function store(Request $req, Request $reqFasPin, Request $reqDebt, Request $reqPas, Request $reqPen, Request $reqUs, Request $reqAta) {

        $user_id = $req->auth->user_id;

        $user = User::where('user_id', $user_id)->first();

        $kode_kantor = $user->kd_cabang;
        $so_name     = $user->nama;

        $countTSO = TransSo::count();

        if (!$countTSO) {
            $no = 1;
        }else{
            $no = $countTSO + 1;
        }

        //Data Transaksi SO
        $now   = Carbon::now();
        $year  = $now->year;
        $month = $now->month;

        $nomor_so = $kode_kantor.'-SO-'.$month.'-'.$year.'-'.$no; //  ID-Cabang - AO / CA / SO - Bulan - Tahun - NO. Urut
        $dataTr = array(
            'nomor_so'       => $nomor_so,
            'user_id'        => $user_id,
            'kode_kantor'    => $kode_kantor,
            'nama_so'        => $so_name,
            'id_asal_data'   => $req->input('id_asal_data'),
            'nama_marketing' => $req->input('nama_marketing'),
            'plafon'         => $req->input('plafon'),
            'tenor'          => $req->input('tenor')
        );

        // Data Fasilitas Pinjaman
        $dataFasPin = array(
            'jenis_pinjaman'  => $reqFasPin->input('jenis_pinjaman'),
            'tujuan_pinjaman' => $reqFasPin->input('tujuan_pinjaman'),
            'plafon'          => $reqFasPin->input('plafon_pinjaman'),
            'tenor'           => $reqFasPin->input('tenor_pinjaman')
        );

        // Data Calon Debitur
        $dataDebitur = array(
            'nama_lengkap'          => $reqDebt->input('nama_lengkap'),
            'gelar_keagamaan'       => $reqDebt->input('gelar_keagamaan'),
            'gelar_pendidikan'      => $reqDebt->input('gelar_pendidikan'),
            'jenis_kelamin'         => $reqDebt->input('jenis_kelamin'),
            'status_nikah'          => $reqDebt->input('status_nikah'),
            'ibu_kandung'           => $reqDebt->input('ibu_kandung'),
            'no_ktp'                => $reqDebt->input('no_ktp'),
            'no_ktp_kk'             => $reqDebt->input('no_ktp_kk'),
            'no_kk'                 => $reqDebt->input('no_kk'),
            'no_npwp'               => $reqDebt->input('no_npwp'),
            'tempat_lahir'          => $reqDebt->input('tempat_lahir'),
            'tgl_lahir'             => Carbon::parse($reqDebt->input('tgl_lahir'))->format('Y-m-d'),
            'agama'                 => $reqDebt->input('agama'),
            'alamat_ktp'            => $reqDebt->input('alamat_ktp'),
            'rt_ktp'                => $reqDebt->input('rt_ktp'),
            'rw_ktp'                => $reqDebt->input('rw_ktp'),
            'id_provinsi_ktp'       => $reqDebt->input('id_provinsi_ktp'),
            'id_kabupaten_ktp'      => $reqDebt->input('id_kabupaten_ktp'),
            'id_kecamatan_ktp'      => $reqDebt->input('id_kecamatan_ktp'),
            'id_kelurahan_ktp'      => $reqDebt->input('id_kelurahan_ktp'),
            'alamat_domisili'       => $reqDebt->input('alamat_domisili'),
            'rt_domisili'           => $reqDebt->input('rt_domisili'),
            'rw_domisili'           => $reqDebt->input('rw_domisili'),
            'id_provinsi_domisili'  => $reqDebt->input('id_provinsi_domisili'),
            'id_kabupaten_domisili' => $reqDebt->input('id_kabupaten_domisili'),
            'id_kecamatan_domisili' => $reqDebt->input('id_kecamatan_domisili'),
            'id_kelurahan_domisili' => $reqDebt->input('id_kelurahan_domisili'),
            'pendidikan_terakhir'   => $reqDebt->input('pendidikan_terakhir'),
            'jumlah_tanggungan'     => $reqDebt->input('jumlah_tanggungan'),
            'no_telp'               => $reqDebt->input('no_telp'),
            'no_hp'                 => $reqDebt->input('no_hp'),
            'alamat_surat'          => $reqDebt->input('alamat_surat'),
            'lamp_ktp'              => empty($reqDebt->file('lamp_ktp')) ? null : Helper::img64enc($reqDebt->file('lamp_ktp')),
            'lamp_kk'               => empty($reqDebt->file('lamp_kk')) ? null : Helper::img64enc($reqDebt->file('lamp_kk'))
        );

        // Data Pasangan Calon Debitur
        $dataPasangan = array(
            'nama_lengkap'     => $reqPas->input('nama_lengkap_pas'),
            'nama_ibu_kandung' => $reqPas->input('nama_ibu_kandung_pas'),
            'jenis_kelamin'    => $reqPas->input('jenis_kelamin_pas'),
            'no_ktp'           => $reqPas->input('no_ktp_pas'),
            'no_ktp_kk'        => $reqPas->input('no_ktp_kk_pas'),
            'no_npwp'          => $reqPas->input('no_npwp_pas'),
            'tempat_lahir'     => $reqPas->input('tempat_lahir_pas'),
            'tgl_lahir'        => Carbon::parse($reqPas->input('tgl_lahir_pas'))->format('Y-m-d'),
            'alamat_ktp'       => $reqPas->input('alamat_ktp_pas'),
            'no_telp'          => $reqPas->input('no_telp_pas'),
            'lamp_ktp'         => empty($reqPas->file('lamp_ktp_pas')) ? null : Helper::img64enc($reqPas->file('lamp_ktp_pas')),
            'lamp_buku_nikah'  => empty($reqPas->file('lamp_buku_nikah_pas')) ? null : Helper::img64enc($reqPas->file('lamp_buku_nikah_pas'))
        );

        // Data Usaha Calon Debitur
        $dataUsaha = array(
            'lamp_tempat_usaha' => empty($reqUs->file('lamp_usaha')) ? null : Helper::img64enc($reqUs->file('lamp_usaha'))
        );
//
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

            // if ($dataDebitur['pekerjaan'] == 'usaha' || $dataDebitur['pekerjaan'] == 'USAHA') {
                // $newUsaha = array('id_calon_debitur' => $id_debt, 'lamp_tempat_usaha' => $lamp_tempat_usaha);

                $newUsa   = array_merge($arrIdDebt, $dataUsaha);
                $usaha    = Usaha::create($newUsa);
                $id_usaha = $usaha->id;
            // }else{
                // $id_usaha = null;
            // }

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

                // $a = 1; $b = 1; $c = 1; $d = 1;
                // if($files = $reqPen->file('lamp_ktp_pen')){
                //     foreach($files as $file){
                //         $name = 'ktp.'.$file->getClientOriginalExtension();
                //         $path = base_path('uploads/debiturs/'.$dataDebitur['no_ktp'].'/penjamin'.$a);
                //         $file->move($path,$name);
                //         $imgKTP[]='uploads/debiturs/'.$dataDebitur['no_ktp'].'/penjamin'.$a.'/'.$name;
                //         $a++;
                //     }
                // }

                for ($i = 0; $i < count($reqPen->nama_ktp_pen); $i++) {

                    $DP[] = [
                        'id_calon_debitur' => $id_debt,
                        'nama_ktp'         => empty($reqPen->nama_ktp_pen[$i]) ? null : $reqPen->nama_ktp_pen[$i],
                        'nama_ibu_kandung' => empty($reqPen->nama_ibu_kandung_pen[$i]) ? null : $reqPen->nama_ibu_kandung_pen[$i],
                        'no_ktp'           => empty($reqPen->no_ktp_pen[$i]) ? null : $reqPen->no_ktp_pen[$i],
                        'no_npwp'          => empty($reqPen->no_npwp_pen[$i]) ? null : $reqPen->no_npwp_pen[$i],
                        'tempat_lahir'     => empty($reqPen->tempat_lahir_pen[$i]) null : $reqPen->tempat_lahir_pen[$i],
                        'tgl_lahir'        => empty($reqPen->tgl_lahir_pen[$i]) ? null : Carbon::parse($reqPen->tgl_lahir_pen[$i])->format('Y-m-d'),
                        'jenis_kelamin'    => empty($reqPen->jenis_kelamin_pen[$i]) ? null : $reqPen->jenis_kelamin_pen[$i],
                        'alamat_ktp'       => empty($reqPen->alamat_ktp_pen[$i]) ? null : $reqPen->alamat_ktp_pen[$i],
                        'no_telp'          => empty($reqPen->no_telp_pen[$i]) ? null : $reqPen->no_telp_pen[$i],
                        'hubungan_debitur' => empty($reqPen->hubungan_debitur_pen[$i]) ? null : $reqPen->hubungan_debitur_pen[$i],
                        'lamp_ktp'         => empty($reqPen->file('lamp_ktp_pen')[$i]) ? null : Helper::img64enc($reqPen->file('lamp_ktp_pen')[$i]),
                        'lamp_ktp_pasangan'=> empty($reqPen->file('lamp_ktp_pasangan_pen')[$i]) ? null : Helper::img64enc($reqPen->file('lamp_ktp_pasangan_pen')[$i]),
                        'lamp_kk'          => empty($reqPen->file('lamp_kk_pen')[$i]) ? null : Helper::img64enc($reqPen->file('lamp_kk_pen')[$i]),
                        'lamp_buku_nikah'  => empty($reqPen->file('lamp_buku_nikah_pen')[$i]) ? null : Helper::img64enc($reqPen->file('lamp_buku_nikah_pen')[$i]),
                        'created_at'       => Carbon::now()->toDateTimeString()
                    ];
                }

                for ($i = 0; $i < count($reqAta->file('lamp_sertifikat')); $i++) {

                    $dataAguTa[] = [
                        'id_calon_debitur'     => $id_debt,
                        'lamp_sertifikat'      => empty($reqAta->file('lamp_sertifikat')[$i]) ? null : Helper::img64enc($reqAta->file('lamp_sertifikat')[$i]),
                        'lamp_pbb'             => empty($reqAta->file('lamp_pbb')[$i]) ? null : Helper::img64enc($reqAta->file('lamp_pbb')[$i]),
                        'lamp_agunan_depan'    => empty($reqAta->file('lamp_agunan_depan')[$i]) ? null : Helper::img64enc($reqAta->file('lamp_agunan_depan')[$i]),
                        'lamp_agunan_kanan'    => empty($reqAta->file('lamp_agunan_kanan')[$i]) ? null : Helper::img64enc($reqAta->file('lamp_agunan_kanan')[$i][$i][$i]),
                        'lamp_agunan_kiri'     => empty($reqAta->file('lamp_agunan_kiri')[$i]) ? null : Helper::img64enc($reqAta->file('lamp_agunan_kiri')[$i][$i]),
                        'lamp_agunan_belakang' => empty($reqAta->file('lamp_agunan_belakang')[$i]) ? null : Helper::img64enc($reqAta->file('lamp_agunan_belakang')[$i]),
                        'lamp_agunan_dalam'    => empty($reqAta->file('lamp_agunan_dalam')[$i]) ? null : Helper::img64enc($reqAta->file('lamp_agunan_dalam')[$i]),
                        'lamp_imb'             => empty($reqAta->file('lamp_imb')[$i]) ? null : Helper::img64enc($reqAta->file('lamp_imb')[$i])
                    ];
                }


                $penjamin = Penjamin::insert($DP);
                // $id_penjamin = DB::connection('web')->getPdo()->lastInsertId();
            }

            $newAguta = array_merge($arrIdDebt, $dataAguTa);
            $aguta = AgunanTanah::create($newAguta);
            $id_aguta = $aguta->id;

            $pu = Penjamin::select('id')->where('id_calon_debitur', $id_debt)->get();
            $te = array();
            $i  = 0;
            foreach ($pu as $val) {
                $te['id'][$i] = $val->id;
                $i++;
            };
            $id_penjamins = implode(",", $te['id']);

            $at = AgunanTanah::select('id')->where('id_calon_debitur', $id_debt)->get();
            $ab = array();
            $j  = 0;
            foreach ($at as $val) {
                $ab['id'][$i] = $val->id;
                $i++;
            };
            $id_at = implode(",", $ab['id']);

            // dd($id_penjamins);

            $arrTr = array(
                'id_fasilitas_pinjaman' => $id_faspin,
                'id_calon_debt'         => $id_debt,
                'id_pasangan'           => $id_pasangan,
                'id_penjamin'           => $id_penjamins,
                'id_agunan_tanah'       => $id_at,
                'id_usaha'              => $id_usaha
            );

            $mergeTr  = array_merge($arrTr, $dataTr);
            TransSo::create($mergeTr);

            DB::connection('web')->commit();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message'=> 'Data berhasil dibuat'
            ], 200);
            //all good
        } catch (\Exception $e) {
            DB::connection('web')->rollback();

            //something went wrong
            return response()->json([
                'code'    => 501,
                'status'  => 'error',
                'message' => $e
            ], 501);
        }
    }
}
