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

            $idPenj = $val->id_penjamin;

            $ex_penj = explode (",",$idPenj);

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

    public function update($id) {
        $Trans = TransSo::where('id', $id)->first();

        if ($Trans == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        $debitur = Debitur::where('id', $Trans->id_calon_debt)->first();
        $pasangan = Pasangan::where('id', $Trans->id_pasangan)->first();
        $usaha = Usaha::where('id', $Trans->id_usaha)->first();

        // dd($usaha->lamp_tempat_usaha);

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
            'nama_tempat_usaha' => $req->input('nama_usaha'),
            'jenis_usaha'       => $req->input('jenis_usaha'),
            'alamat' => $req->input('alamat_usaha'),
            'id_provinsi' => $req->input('id_provinsi'),
            'id_kabupaten' => $req->input('id_kabupaten'),
            'id_kecamatan' => $req->input('id_kecamatan'),
            'id_kelurahan' => $req->input('id_kelurahan'),
            'rt' => $req->input('rt'),
            'rw' => $req->input('rw'),
            'lama_usaha' => $req->input('lama_usaha'),
            'telp_tempat_usaha' => $req->input('telp_tempat_usaha'),
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
            'nama_tempat_usaha' => $req->input('nama_usaha'),
            'jenis_usaha'       => $req->input('jenis_usaha'),
            'alamat'            => $req->input('alamat_usaha'),
            'id_provinsi'       => $req->input('id_provinsi'),
            'id_kabupaten'      => $req->input('id_kabupaten'),
            'id_kecamatan'      => $req->input('id_kecamatan'),
            'id_kelurahan'      => $req->input('id_kelurahan'),
            'rt'                => $req->input('rt'),
            'rw'                => $req->input('rw'),
            'lama_usaha'        => $req->input('lama_usaha'),
            'telp_tempat_usaha' => $req->input('telp_tempat_usaha'),
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
                        'nama_ktp'         => $reqPen->nama_ktp_pen[$i],
                        'nama_ibu_kandung' => $reqPen->nama_ibu_kandung_pen[$i],
                        'no_ktp'           => $reqPen->no_ktp_pen[$i],
                        'no_npwp'          => $reqPen->no_npwp_pen[$i],
                        'tempat_lahir'     => $reqPen->tempat_lahir_pen[$i],
                        'tgl_lahir'        => Carbon::parse($reqPen->tgl_lahir_pen[$i])->format('Y-m-d'),
                        'jenis_kelamin'    => $reqPen->jenis_kelamin_pen[$i],
                        'alamat_ktp'       => $reqPen->alamat_ktp_pen[$i],
                        'no_telp'          => $reqPen->no_telp_pen[$i],
                        'hubungan_debitur' => $reqPen->hubungan_debitur_pen[$i],
                        'lamp_ktp'         => empty($reqPen->file('lamp_ktp_pen')[$i]) ? null : Helper::img64enc($reqPen->file('lamp_ktp_pen')[$i]),
                        'lamp_ktp_pasangan'=> empty($reqPen->file('lamp_ktp_pasangan_pen')[$i]) ? null : Helper::img64enc($reqPen->file('lamp_ktp_pasangan_pen')[$i]),
                        'lamp_kk'          => empty($reqPen->file('lamp_kk_pen')[$i]) ? null : Helper::img64enc($reqPen->file('lamp_kk_pen')[$i]),
                        'lamp_buku_nikah'  => empty($reqPen->file('lamp_buku_nikah_pen')[$i]) ? null : Helper::img64enc($reqPen->file('lamp_buku_nikah_pen')[$i]),
                        'created_at'       => Carbon::now()->toDateTimeString()
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
            $i = 0;
            foreach ($pu as $val) {
                $te['id'][$i] = $val->id;
                $i++;
            };

            $id_penjamins = implode(",", $te['id']);

            // dd($id_penjamins);

            $arrTr = array(
                'id_fasilitas_pinjaman' => $id_faspin,
                'id_calon_debt'         => $id_debt,
                'id_pasangan'           => $id_pasangan,
                'id_penjamin'           => $id_penjamins,
                'id_agunan_tanah'       => $id_aguta,
                'id_usaha'              => $id_usaha
            );

            // $mergeTr  = array_merge($arrTr, $dataTr);
            // TransSo::create($mergeTr);

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

     // "id": 5,
     //        "nomor_so": "00-SO-11-2019-2",
     //        "user_id": 1130,
     //        "kode_kantor": 0,
     //        "id_asal_data": 1,
     //        "nama_marketing": "Marketing Name",
     //        "nama_so": "APRELA AGIF SOFYAN",
     //        "id_fasilitas_pinjaman": 47,
     //        "id_calon_debt": 47,
     //        "id_pasangan": 47,
     //        "id_penjamin": 17,
     //        "id_agunan_tanah": 19,
     //        "id_agunan_kendaraan": null,
     //        "id_periksa_agunan_tanah": null,
     //        "id_periksa_agunan_kendaraan": null,
     //        "id_usaha": 39,
     //        "recomendasi_ao": null,
     //        "catatan_hasil_cek": null,
     //        "plafon": 100000,
     //        "tenor": 10,
     //        "flg_aktif": null,
     //        "created_at": "2019-11-20 13:56:10",
     //        "updated_at": "2019-11-20 13:56:10"
}
