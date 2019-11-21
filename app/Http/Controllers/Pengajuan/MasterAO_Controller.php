<?php

namespace App\Http\Controllers\Pengajuan;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Requests\Debt\DebtPenjaminRequest;
use App\Http\Requests\Debt\DebtPasanganRequest;
use App\Http\Controllers\Controller as Helper;
use App\Http\Requests\Debt\FasPinRequest;
use App\Http\Requests\Debt\UsahaRequest;
use App\Http\Requests\Debt\UsahaPasReq;
use App\Http\Requests\Debt\DebtRequest;
use App\Http\Requests\Debt\AguTaReq;
use App\Models\CC\FasilitasPinjaman;
use App\Models\Wilayah\Provinsi;
use App\Models\Wilayah\Kabupaten;
use App\Models\Wilayah\Kecamatan;
use App\Models\Wilayah\Kelurahan;
use App\Models\CC\AgunanTanah;
use App\Models\Bisnis\TransSo;
use Illuminate\Http\Request;
use App\Models\CC\UsahaPass;
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

class MasterAO_Controller extends BaseController
{
    public function index(){
        $query = TransSo::get();

        if ($query == '[]') {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        foreach ($query as $key => $val) {

            $data[$key] = [
                'id'             => $val->id,
                'nomor_so'       => $val->nomor_so,
                'kode_kantor'    => $val->kode_kantor,
                'asal_data'      => $val->asaldata->nama,
                'nama_marketing' => $val->nama_marketing,
                'nama_so'        => $val->nama_so,
                'nama_debitur'   => $val->debt['nama_lengkap'],
                'plafon'         => $val->plafon,
                'tenor'          => $val->tenor
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

    public function show($id){
        $query = TransSo::where('id', $id)->get();

        if ($query == '[]') {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        foreach ($query as $key => $val) {

            // $idPenj = $val->id_penjamin;

            // $ex_penj = explode (",",$idPenj);

            $prov_ktp = Provinsi::where('id', $val->debt['id_provinsi_ktp'])->first();
            $kab_ktp  = Kabupaten::where('id', $val->debt['id_kabupaten_ktp'])->first();
            $kec_ktp  = Kecamatan::where('id', $val->debt['id_kecamatan_ktp'])->first();
            $kel_ktp  = Kelurahan::where('id', $val->debt['id_kelurahan_ktp'])->first();

            $prov_dom = Provinsi::where('id', $val->debt['id_provinsi_domisili'])->first();
            $kab_dom  = Kabupaten::where('id', $val->debt['id_kabupaten_domisili'])->first();
            $kec_dom  = Kecamatan::where('id', $val->debt['id_kecamatan_domisili'])->first();
            $kel_dom  = Kelurahan::where('id', $val->debt['id_kelurahan_domisili'])->first();

            $penjamin = Penjamin::where('id_calon_debitur', $val->id_calon_debt)->get();

            foreach ($penjamin as $keys => $penj) {
                $NP[$keys] = [
                    'id'                => $penj->id,
                    'nama_ktp'          => $penj->nama_ktp,
                    'nama_ibu_kandung'  => $penj->nama_ibu_kandung,
                    'no_ktp'            => $penj->no_ktp,
                    'no_npwp'           => $penj->no_npwp,
                    'tempat_lahir'      => $penj->tempat_lahir,
                    'tgl_lahir'         => $penj->tgl_lahir,
                    'jenis_kelamin'     => $penj->jenis_kelamin,
                    'alamat_ktp'        => $penj->alamat_ktp,
                    'no_telp'           => $penj->no_telp,
                    'hubungan_debitur'  => $penj->hubungan_debitr,
                    'lamp_ktp'          => $penj->lamp_ktp,
                    'lamp_ktp_pasangan' => $penj->lamp_ktp_pasangan,
                    'lamp_buku_nikah'   => $penj->lamp_buku_nikah
                ];
            }

            $data[$key] = [
                'id'             => $val->id,
                'nomor_so'       => $val->nomor_so,
                'kode_kantor'    => $val->kode_kantor,
                'asal_data'      => $val->asaldata->nama,
                'nama_marketing' => $val->nama_marketing,
                'nama_so'        => $val->nama_so,
                'plafon'         => $val->plafon,
                'tenor'          => $val->tenor,
                'fasilitas_pinjaman'  => [
                    'jenis_pinjaman'  => $val->faspin->jenis_pinjaman,
                    'tujuan_pinjaman' => $val->faspin->tujuan_pinjaman,
                    'plafon'          => (int) $val->faspin->plafon,
                    'tenor'           => (int) $val->faspin->tenor
                ],
                'data_debitur' => [
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
                     'tgl_lahir'             => $val->debt['tgl_lahir'],
                     'agama'                 => $val->debt['agama'],
                     'alamat_ktp'            => $val->debt['alamat_ktp'],
                     'rt_ktp'                => $val->debt['rt_ktp'],
                     'rw_ktp'                => $val->debt['rw_ktp'],
                     'provinsi_ktp'          => $prov_ktp->nama,
                     'kabupaten_ktp'         => $kab_ktp->nama,
                     'kecamatan_ktp'         => $kec_ktp->nama,
                     'kelurahan_ktp'         => $kel_ktp->nama,
                     'alamat_domisili'       => $val->debt['alamat_domisili'],
                     'rt_domisili'           => $val->debt['rt_domisili'],
                     'rw_domisili'           => $val->debt['rw_domisili'],
                     'provinsi_domisili'     => $prov_dom->nama,
                     'kabupaten_domisili'    => $kab_dom->nama,
                     'kecamatan_domisili'    => $kec_dom->nama,
                     'kelurahan_domisili'    => $kel_dom->nama,
                     'pendidikan_terakhir'   => $val->debt['pendidikan_terakhir'],
                     'jumlah_tanggungan'     => $val->debt['jumlah_tanggungan'],
                     'no_telp'               => $val->debt['no_telp'],
                     'no_hp'                 => $val->debt['no_hp'],
                     'alamat_surat'          => $val->debt['alamat_surat'],
                     'lamp_ktp'              => $val->debt['lamp_ktp'],
                     'lamp_kk'               => $val->debt['lamp_kk']
                ],
                'data_pasangan' => [
                    'nama_lengkap'     => $val->pas['nama_lengkap'],
                    'nama_ibu_kandung' => $val->pas['nama_ibu_kandung'],
                    'jenis_kelamin'    => $val->pas['jenis_kelamin'],
                    'no_ktp'           => $val->pas['no_ktp'],
                    'tempat_lahir'     => $val->pas['tempat_lahir'],
                    'tgl_lahir'        => $val->pas['tgl_lahir'],
                    'alamat_ktp'       => $val->pas['alamat_ktp'],
                    'no_telp'          => $val->pas['no_telp'],
                ],
                'data_penjamin' => [$NP]
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

    public function update($id, Request $req, FasPinRequest $reqFasPin, DebtRequest $reqDebt, DebtPasanganRequest $reqPas, DebtPenjaminRequest $reqPen, UsahaRequest $reqUs, UsahaPasReq $reqUsPas, AguTaReq $reqAta) {
        $Trans = TransSo::where('id', $id)->first();

        if ($Trans == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        $debitur  = Debitur::where('id', $Trans->id_calon_debt)->first();
        $pasangan = Pasangan::where('id', $Trans->id_pasangan)->first();
        $usaha    = Usaha::where('id', $Trans->id_usaha)->first();
        $penjamin = Penjamin::where('id_calon_debitur', $Trans->id_calon_debt)->get();

        dd($penjamin);

        // Data Calon Debitur
        $dataDebitur = array(
            'tinggi_badan'       => $reqDebt->input('tinggi_badan'),
            'berat_badan'        => $reqDebt->input('berat_badan'),
            'nama_anak1'         => $reqDebt->input('nama_anak1'),
            'tgl_lahir_anak1'    => $reqDebt->input('tgl_lahir_anak1'),
            'nama_anak2'         => $reqDebt->input('nama_anak2'),
            'tgl_lahir_anak2'    => $reqDebt->input('tgl_lahir_anak2'),
            'alamat_surat'       => $reqDebt->input('alamat_surat'),
            'pekerjaan'          => $reqDebt->input('pekerjaan'),
            'posisi_pekerjaan'   => $reqDebt->input('posisi_pekerjaan'),
            'lamp_buku_tabungan' => empty($reqDebt->file('lamp_buku_tabungan')) ? $debitur->lamp_buku_tabungan : Helper::img64enc($reqDebt->file('lamp_buku_tabungan'))
        );

        // Data Usaha Calon Debitur
        $dataUsaha = array(
            'nama_tempat_usaha' => $reqUs->input('nama_usaha'),
            'jenis_usaha'       => $reqUs->input('jenis_usaha'),
            'alamat'            => $reqUs->input('alamat_usaha'),
            'id_provinsi'       => $reqUs->input('id_prov_usaha'),
            'id_kabupaten'      => $reqUs->input('id_kab_usaha'),
            'id_kecamatan'      => $reqUs->input('id_kec_usaha'),
            'id_kelurahan'      => $reqUs->input('id_kel_usaha'),
            'rt'                => $reqUs->input('rt_usaha'),
            'rw'                => $reqUs->input('rw_usaha'),
            'lama_usaha'        => $reqUs->input('lama_usaha'),
            'telp_tempat_usaha' => $reqUs->input('no_tlp_usaha'),
            'lamp_tempat_usaha' => empty($reqUs->file('lamp_tempat_usaha')) ? $usaha->lamp_tempat_usaha : Helper::img64enc($reqUs->file('lamp_tempat_usaha'))
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
            'pekerjaan'        => $reqPas->input('pekerjaan'),
            'posisi_pekerjaan' => $reqPas->input('posisi_pekerjaan'),
            'no_telp'          => $reqPas->input('no_telp_pas'),
            'lamp_ktp'         => empty($reqPas->file('lamp_ktp_pas')) ? $pasangan->lamp_ktp : Helper::img64enc($reqPas->file('lamp_ktp_pas')),
            'lamp_buku_nikah'  => empty($reqPas->file('lamp_buku_nikah_pas')) ? $pasangan->lamp_buku_nikah : Helper::img64enc($reqPas->file('lamp_buku_nikah_pas'))
        );

        $dataUsahaPas = array(
            'id_calon_debitur'  => $Trans->id_calon_debt,
            'id_pasangan'       => $Trans->id_pasangan,
            'nama_tempat_usaha' => $reqUsPas->input('nama_usaha_pas'),
            'jenis_usaha'       => $reqUsPas->input('jenis_usaha_pas'),
            'alamat'            => $reqUsPas->input('alamat_usaha_pas'),
            'id_provinsi'       => $reqUsPas->input('id_prov_usaha_pas'),
            'id_kabupaten'      => $reqUsPas->input('id_kab_usaha_pas'),
            'id_kecamatan'      => $reqUsPas->input('id_kec_usaha_pas'),
            'id_kelurahan'      => $reqUsPas->input('id_kel_usaha_pas'),
            'rt'                => $reqUsPas->input('rt_usaha_pas'),
            'rw'                => $reqUsPas->input('rw_usaha_pas'),
            'lama_usaha'        => $reqUsPas->input('lama_usaha_pas'),
            'telp_tempat_usaha' => $reqUsPas->input('no_telp_usaha_pas')
        );

        $dataAguTa = array(
            'lamp_sertifikat'      => empty($reqAta->file('lamp_sertifikat')) ? null : Helper::img64enc($reqAta->file('lamp_sertifikat')),
            'lamp_pbb'             => empty($reqAta->file('lamp_pbb')) ? null : Helper::img64enc($reqAta->file('lamp_pbb')),
            'lamp_agunan_depan'    => empty($reqAta->file('lamp_agunan_depan')) ? null : Helper::img64enc($reqAta->file('lamp_agunan_depan')),
            'lamp_agunan_kanan'    => empty($reqAta->file('lamp_agunan_kanan')) ? null : Helper::img64enc($reqAta->file('lamp_agunan_kanan')),
            'lamp_agunan_kiri'     => empty($reqAta->file('lamp_agunan_kiri')) ? null : Helper::img64enc($reqAta->file('lamp_agunan_kiri')),
            'lamp_agunan_belakang' => empty($reqAta->file('lamp_agunan_belakang')) ? null : Helper::img64enc($reqAta->file('lamp_agunan_belakang')),
            'lamp_agunan_dalam'    => empty($reqAta->file('lamp_agunan_dalam')) ? null : Helper::img64enc($reqAta->file('lamp_agunan_dalam')),
            'lamp_imb'             => empty($reqAta->file('lamp_imb')) ? null : Helper::img64enc($reqAta->file('lamp_imb'))
        );

        DB::connection('web')->beginTransaction();
        try {

            if (!$reqPen) {
                $id_penjamin = null;
            }else{

                for ($i = 0; $i < count($reqPen->pekerjaan); $i++) {

                    $DP[] = [
                        'id_calon_debitur' => $Trans->id_calon_debt,
                        'pekerjaan'        => $reqPen->pekerjaan_pen[$i],
                        'posisi_pekerjaan' => $reqPen->posisi_pekerjaan[$i],
                        'updated_at'       => Carbon::now()->toDateTimeString()
                    ];

                    $DUP[] = [
                        'id_calon_debitur'  => $Trans->id_calon_debt,
                        'id_penjamin'       => $penjamin->id,
                        'nama_tempat_usaha' => $reqUs->nama_usaha_pen[$i],
                        'jenis_usaha'       => $reqUs->jenis_usaha_pen[$i],
                        'alamat'            => $reqUs->alamat_usaha_pen[$i],
                        'id_provinsi'       => $reqUs->id_prov_usaha_pen[$i],
                        'id_kabupaten'      => $reqUs->id_kab_usaha_pen[$i],
                        'id_kecamatan'      => $reqUs->id_kec_usaha_pen[$i],
                        'id_kelurahan'      => $reqUs->id_kel_usaha_pen[$i],
                        'rt'                => $reqUs->rt_usaha_pen[$i],
                        'rw'                => $reqUs->rw_usaha_pen[$i],
                        'lama_usaha'        => $reqUs->lama_usaha_pen[$i],
                        'telp_tempat_usaha' => $reqUs->no_telp_usaha_pen[$i]
                    ];
                }


                $penjamin = Penjamin::insert($DP);
                // $id_penjamin = DB::connection('web')->getPdo()->lastInsertId();
            }

            $debitur = Debitur::where('id', $id_calon_debt)->update();
            $debitur = Pasangan::where('id', $id_pasangan)->update();
            $debitur = Penjamin::where('id', $id_penjamin)->update();
            $debitur = Usaha::where('id', $id_usaha)->update();
            $debitur = AgunanTanah::where('id', $id_agunan_tanah)->update();

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
