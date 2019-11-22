<?php

namespace App\Http\Controllers\Pengajuan;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Requests\Debt\DebtPenjaminRequest;
use App\Http\Requests\Debt\DebtPasanganRequest;
use App\Http\Controllers\Controller as Helper;
use App\Http\Requests\Debt\FasPinRequest;
use App\Http\Requests\Debt\UsahaRequest;
use App\Http\Requests\Debt\UsahaPasReq;
use App\Http\Requests\Debt\UsahaPenReq;
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
use App\Models\CC\UsahaPenj;
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
                'data_penjamin' => $penjamin
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

    public function update($id, Request $req, FasPinRequest $reqFasPin, DebtRequest $reqDebt, DebtPasanganRequest $reqPas, DebtPenjaminRequest $reqPen, UsahaRequest $reqUs, UsahaPasReq $reqUsPas, UsahaPenReq $reqUsPen, AguTaReq $reqAta) {

        $Trans = TransSo::where('id', $id)->first();

        if ($Trans == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        $debitur  = Debitur::select('lamp_buku_tabungan')->where('id', $Trans->id_calon_debt)->first();
        $pasangan = Pasangan::select('lamp_ktp', 'lamp_buku_nikah')->where('id', $Trans->id_pasangan)->first();
        $usaha    = Usaha::select('lamp_tempat_usaha')->where('id', $Trans->id_usaha)->first();
        $penjamin = Penjamin::select('pekerjaan', 'posisi_pekerjaan')->where('id_calon_debitur', $Trans->id_calon_debt)->get();
        $aTa      = AgunanTanah::where('id_calon_debitur', $Trans->id_calon_debt)->get();

        $idPenj   = $Trans->id_penjamin;
        $arIdPenj = explode (",",$idPenj);

        $idAta = $Trans->id_agunan_tanah;
        $arIdAT= explode(",",$idAta);

        // Data Calon Debitur
        $dataDebitur = array(
            // 'tinggi_badan'       => $reqDebt->input('tinggi_badan'),
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
            'tgl_mulai_usaha'   => $reqUs->input('tgl_mulai_usaha'),
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
            'pekerjaan'        => $reqPas->input('pekerjaan_pas'),
            'posisi_pekerjaan' => $reqPas->input('posisi_pekerjaan_pas'),
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
            'tgl_mulai_usaha'   => $reqUsPas->input('tgl_mulai_usaha_pas'),
            'telp_tempat_usaha' => $reqUsPas->input('no_telp_usaha_pas')
        );

        DB::connection('web')->beginTransaction();
        // try {

            for ($i = 0; $i < count($reqPen->pekerjaan_pen); $i++) {

                $DaPenj[] = [
                    'pekerjaan'        => empty($reqPen->pekerjaan_pen[$i]) ? $penjamin[$i]->pekerjaan : $reqPen->pekerjaan_pen[$i],
                    'posisi_pekerjaan' => empty($reqPen->posisi_pekerjaan_pen[$i]) ? $penjamin[$i]->posisi_pekerjaan : $reqPen->posisi_pekerjaan_pen[$i],
                    'updated_at'       => Carbon::now()->toDateTimeString()
                ];

                // Penjamin::where('id', $arIdPenj)->update($DaPenj[$i]);
            }

            for ($i = 0; $i < count($reqUsPen->nama_usaha_pen); $i++){
                $DaUsPen[] = [
                    'id_calon_debitur'  => $Trans->id_calon_debt,
                    'id_penjamin'       => $arIdPenj[$i],
                    'nama_tempat_usaha' => empty($reqUsPen->nama_usaha_pen[$i]) ? null : $reqUsPen->nama_usaha_pen[$i],
                    'jenis_usaha'       => empty($reqUsPen->jenis_usaha_pen[$i]) ? null : $reqUsPen->jenis_usaha_pen[$i],
                    'alamat'            => empty($reqUsPen->alamat_usaha_pen[$i]) ? null : $reqUsPen->alamat_usaha_pen[$i],
                    'id_provinsi'       => empty($reqUsPen->id_prov_usaha_pen[$i]) ? null : $reqUsPen->id_prov_usaha_pen[$i],
                    'id_kabupaten'      => empty($reqUsPen->id_kab_usaha_pen[$i]) ? null : $reqUsPen->id_kab_usaha_pen[$i],
                    'id_kecamatan'      => empty($reqUsPen->id_kec_usaha_pen[$i]) ? null : $reqUsPen->id_kec_usaha_pen[$i],
                    'id_kelurahan'      => empty($reqUsPen->id_kel_usaha_pen[$i]) ? null : $reqUsPen->id_kel_usaha_pen[$i],
                    'rt'                => empty($reqUsPen->rt_usaha_pen[$i]) ? null : $reqUsPen->rt_usaha_pen[$i],
                    'rw'                => empty($reqUsPen->rw_usaha_pen[$i]) ? null : $reqUsPen->rw_usaha_pen[$i],
                    'tgl_mulai_usaha'   => empty($reqUsPen->tgl_mulai_usaha_pen[$i]) ? null : $reqUsPen->tgl_mulai_usaha_pen[$i],
                    'telp_tempat_usaha' => empty($reqUsPen->no_telp_usaha_pen[$i]) ? null : $reqUsPen->no_telp_usaha_pen[$i],
                    'created_at'        => Carbon::now()->toDateTimeString()
                ];

                // $Us = UsahaPenj::create($DaUsPen[$i]);
            }

            for ($i = 0; $i < count($reqAta->tipe_lokasi_agunan); $i++){
                $DaAguTa[] = [
                    'id_calon_debitur'        => $Trans->id_calon_debt,
                    'tipe_lokasi'             => empty($reqAta->tipe_lokasi_agunan[$i]) ? $aTa->tipe_lokasi[$i] : $reqAta->tipe_lokasi_agunan[$i],
                    'alamat'                  => empty($reqAta->alamat_agunan[$i]) ? $aTa->alamat_agunan[$i] : $reqAta->alamat_agunan[$i],
                    'id_povinsi'              => empty($reqAta->id_prov_agunan[$i]) ? null : $reqAta->id_prov_agunan[$i],
                    // 'id_kabupaten'            => empty($reqAta->id_kab_agunan[$i]) ? $aTa->id_kab_agunan[$i] : $reqAta->id_kab_agunan[$i],
                    // 'id_kecamatan'            => empty($reqAta->id_kec_agunan[$i]) ? $aTa->id_kec_agunan[$i] : $reqAta->id_kec_agunan[$i],
                    // 'id_kelurahan'            => empty($reqAta->id_kel_agunan[$i]) ? $aTa->id_kel_agunan[$i] : $reqAta->id_kel_agunan[$i],
                    // 'rt'                      => empty($reqAta->rt_agunan[$i]) ? $aTa->rt_agunan[$i] : $reqAta->rt_agunan[$i],
                    // 'rw'                      => empty($reqAta->rw_agunan[$i]) ? $aTa->rw_agunan[$i] : $reqAta->rw_agunan[$i],
                    // 'luas_tanah'              => empty($reqAta->luas_tanah[$i]) ? $aTa->luas_tanah[$i] : $reqAta->luas_tanah[$i],
                    // 'luas_bangunan'           => empty($reqAta->luas_bangunan[$i]) ? $aTa->luas_bangunan[$i] : $reqAta->luas_bangunan[$i],
                    // 'nama_pemilik_sertifikat' => empty($reqAta->nama_pemilik_sertifikat[$i]) ? $aTa->nama_pemilik_sertifikat[$i] : $reqAta->nama_pemilik_sertifikat[$i],
                    // 'jenis_sertifikat'        => empty($reqAta->jenis_sertifikat[$i]) ? $aTa->jenis_sertifikat[$i] : $reqAta->jenis_sertifikat[$i],
                    // 'no_sertifikat'           => empty($reqAta->no_sertifikat[$i]) ? $aTa->no_sertifikat[$i] : $reqAta->no_sertifikat[$i],
                    // 'tgl_ukur_sertifikat'     => empty($reqAta->tgl_ukur_sertifikat[$i]) ? $aTa->tgl_ukur_sertifikat[$i] : $reqAta->tgl_ukur_sertifikat[$i],
                    // 'tgl_berlaku_shgb'        => empty($reqAta->tgl_berlaku_shgb[$i]) ? $aTa->tgl_berlaku_shgb[$i] : $reqAta->tgl_berlaku_shgb[$i],
                    // 'no_imb'                  => empty($reqAta->no_imb[$i]) ? $aTa->no_imb[$i] : $reqAta->no_imb[$i],
                    // 'njop'                    => empty($reqAta->njop[$i]) ? $aTa->njop[$i] : $reqAta->njop[$i],
                    // 'nop'                     => empty($reqAta->nop[$i]) ? $aTa->nop[$i] : $reqAta->nop[$i],
                    // 'lam_imb'                 => empty($reqAta->file('lam_imb')[$i]) ? $aTa->lamp_imb[$i] : Helper::img64enc($reqAta->file('lam_imb')[$i]),
                    // 'lamp_agunan_depan'       => empty($reqAta->file('lamp_agunan_depan')[$i]) ? $aTa->lamp_agunan_depan[$i] : Helper::img64enc($reqAta->file('lamp_agunan_depan')[$i]),
                    // 'lamp_agunan_kanan'       => empty($reqAta->file('lamp_agunan_kanan')[$i]) ? $aTa->lamp_agunan_kanan[$i] : Helper::img64enc($reqAta->file('lamp_agunan_kanan')[$i]),
                    // 'lamp_agunan_kiri'        => empty($reqAta->file('lamp_agunan_kiri')[$i]) ? $aTa->lamp_agunan_kiri[$i] : Helper::img64enc($reqAta->file('lamp_agunan_kiri')[$i]),
                    // 'lamp_agunan_belakang'    => empty($reqAta->file('lamp_agunan_belakang')[$i]) ? $aTa->lamp_agunan_belakang[$i] : Helper::img64enc($reqAta->file('lamp_agunan_belakang')[$i]),
                    // 'lamp_agunan_dalam'       => empty($reqAta->file('lamp_agunan_dalam')[$i]) ? $aTa->lamp_agunan_dalam[$i] : Helper::img64enc($reqAta->file('lamp_agunan_dalam')[$i]),
                    // 'lamp_sertifikat'         => empty($reqAta->file('lamp_sertifikat')[$i]) ? $aTa->lamp_sertifikat[$i] : Helper::img64enc($reqAta->file('lamp_sertifikat')[$i]),
                    // 'lamp_imb'                => empty($reqAta->file('lamp_imb')[$i]) ? $aTa->lamp_imb[$i] : Helper::img64enc($reqAta->file('lamp_imb')[$i]),
                    // 'lamp_pbb'                => empty($reqAta->file('lamp_pbb')[$i]) ? $aTa->lamp_pbb[$i] : Helper::img64enc($reqAta->file('lamp_pbb')[$i])
                ];
            }

            dd($DaAguTa);



            // Debitur::where('id', $id_calon_debt)->update($dataDebitur);
            // Usaha::where('id', $id_usaha)->update($dataUsaha);
            // Pasangan::where('id', $id_pasangan)->update($dataPasangan);
            // UsahaPass::create($dataUsahaPas);
            // $newPen = Penjamin::where('id', $arP)->updateOrCreate($DaPenj);

            // AgunanTanah::where('id', $id_agunan_tanah)->update();

            DB::connection('web')->commit();

            // return response()->json([
            //     'code'   => 200,
            //     'status' => 'success',
            //     'message'=> 'Data berhasil dibuat'
            // ], 200);
            //all good
        // } catch (\Exception $e) {
        //     DB::connection('web')->rollback();

        //     //something went wrong
        //     return response()->json([
        //         'code'    => 501,
        //         'status'  => 'error',
        //         'message' => $e
        //     ], 501);
        // }
    }
}
