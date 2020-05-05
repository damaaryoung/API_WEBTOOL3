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
use App\Models\Pengajuan\SO\Debitur;
// use Illuminate\Support\Facades\File;
use App\Models\Transaksi\TransSO;
use App\Models\Transaksi\TransAO;
use App\Models\AreaKantor\JPIC;
use App\Models\AreaKantor\PIC;
use App\Models\Pengajuan\SO\FasilitasPinjaman;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Image;
use Illuminate\Support\Facades\DB;

class MasterAO_Controller extends BaseController
{
    public function index(Request $req)
    {
        $pic = $req->pic; // From PIC middleware

        $id_area   = $pic->id_area;
        $id_cabang = $pic->id_cabang;
        $scope     = $pic->jpic['cakupan'];

        $query_dir = TransSO::with('pic', 'cabang', 'asaldata', 'debt', 'pas', 'faspin', 'ao', 'ca')->orderBy('created_at', 'desc')->where('status_das', 1)->where('status_hm', 1);

        $query = Helper::checkDir($scope, $query_dir, $id_area, $id_cabang);

        if (empty($query->get())) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data di SO cabang anda masih kosong'
            ], 404);
        }

        $data = array();
        foreach ($query->get() as $key => $val) {

            if ($val->status_das == 1) {
                $status_das = 'complete';
            } elseif ($val->status_das == 2) {
                $status_das = 'not complete';
            } else {
                $status_das = 'waiting';
            }

            if ($val->status_hm == 1) {
                $status_hm = 'complete';
            } elseif ($val->status_hm == 2) {
                $status_hm = 'not complete';
            } else {
                $status_hm = 'waiting';
            }

            if ($val->ao['status_ao'] == 1) {
                $status_ao = 'recommend';
            } elseif ($val->ao['status_ao'] == 2) {
                $status_ao = 'not recommend';
            } else {
                $status_ao = 'waiting';
            }

            $data[$key] = [
                'id'             => $val->id == null ? null : (int) $val->id,
                'nomor_so'       => $val->nomor_so,
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
                ],
                'pic'            => $val->pic['nama'],
                'area'           => $val->area['nama'],
                'cabang'         => $val->cabang['nama'],
                'asal_data'      => $val->asaldata['nama'],
                'nama_marketing' => $val->nama_marketing,
                'nama_debitur'   => $val->debt['nama_lengkap'],
                'plafon'         => $val->faspin['plafon'],
                'tenor'          => $val->faspin['tenor'],
                'tgl_transaksi'  => $val->created_at
            ];
        }

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'count'  => sizeof($data),
                'data'   => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function indexWait($ao_ca, $status, Request $req)
    {
        $pic = $req->pic; // From PIC middleware

        $id_area   = $pic->id_area;
        $id_cabang = $pic->id_cabang;
        $scope     = $pic->jpic['cakupan'];

        $query_dir = TransSO::with('pic', 'cabang', 'ao', 'ca')
            ->where('status_das', 1)
            ->where('status_hm', 1)
            ->orderBy('created_at', 'desc');

        $query = Helper::checkDir($scope, $query_dir, $id_area, $id_cabang);

        if (empty($query->get())) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data di SO masih kosong'
            ], 404);
        }

        $data = array();
        foreach ($query->get() as $val) {

            if ($val->ao['status_ao'] == 1) {
                $status_ao = 'recommend';
            } elseif ($val->ao['status_ao'] == 2) {
                $status_ao = 'not recommend';
            } else {
                $status_ao = 'waiting';
            }

            if ($val->ca['status_ca'] == 1) {
                $status_ca = 'recommend';
            } elseif ($val->ca['status_ca'] == 2) {
                $status_ca = 'not recommend';
            } else {
                $status_ca = 'waiting';
            }

            $data[] = [
                'id_trans_so'    => $val->id == null ? null : (int) $val->id,
                'nomor_so'       => $val->nomor_so,
                "ao" => [
                    'status_ao'     => $status_ao,
                    'catatan_ao'    => $val->ao['catatan_ao']
                ],
                "ca" => [
                    'status_ca'     => $status_ca,
                    'catatan_ca'    => $val->ca['catatan_ca']
                ],
                'pic'            => $val->pic['nama'],
                'area'           => $val->area['nama'],
                'cabang'         => $val->cabang['nama'],
                'asal_data'      => $val->asaldata['nama'],
                'nama_marketing' => $val->nama_marketing,
                'nama_debitur'   => $val->debt['nama_lengkap'],
                'plafon'         => $val->faspin['plafon'],
                'tenor'          => $val->faspin['tenor'],
                'tgl_transaksi'  => $val->created_at
            ];
        }

        $res = array_filter($data, function ($item) use ($ao_ca, $status) {
            if (stripos($item[$ao_ca]["status_{$ao_ca}"], $status) !== false) {
                return true;
            }
            return false;
        });

        try {
            if ($res == false) {
                return response()->json([
                    'code'   => 404,
                    'status' => 'not found',
                    'count'  => 0,
                    'message' => 'data tidak ditemukan'
                ], 404);
            } else {
                foreach ($res as $val) {
                    $result[] = $val;
                }
                return response()->json([
                    'code'   => 200,
                    'status' => 'success',
                    'count'  => sizeof($result),
                    'data'   => $result
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function show($id, Request $req)
    {
        $pic = $req->pic; // From PIC middleware

        $id_area   = $pic->id_area;
        $id_cabang = $pic->id_cabang;
        $scope     = $pic->jpic['cakupan'];

        $query_dir = TransSO::with('pic', 'cabang', 'asaldata', 'debt', 'pas', 'faspin', 'ao', 'ca')
            ->where('id', $id); //->where('status_das', 1)->where('status_hm', 1);

        $vals = Helper::checkDir($scope, $query_dir, $id_area, $id_cabang);
        $val  = $vals->first();
        //    dd($val);
        if (empty($val)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id ' . $id . ' belum ada di SO cabang dan area anda, Atau data transaksi dari SO belum komplit saat pemeriksaaan DAS dan HM'
            ], 404);
        }

        $penj = Penjamin::whereIn('id', explode(",", $val->id_penjamin))->get();

        $penjamin = array();
        foreach ($penj as $pen) {
            $penjamin[] = [
                'id'                => $pen->id,
                'nama_ktp'          => $pen->nama_ktp,
                'nama_ibu_kandung'  => $pen->nama_ibu_kandung,
                'no_ktp'            => $pen->no_ktp,
                'no_npwp'           => $pen->no_npwp,
                'tempat_lahir'      => $pen->tempat_lahir,
                'tgl_lahir'         => Carbon::parse($pen->tgl_lahir)->format('d-m-Y'),
                'jenis_kelamin'     => $pen->jenis_kelamin,
                'alamat_ktp'        => $pen->alamat_ktp,
                'no_telp'           => $pen->no_telp,
                'hubungan_debitur'  => $pen->hubungan_debitur,

                'lampiran' => [
                    'lamp_ktp' => $pen->lamp_ktp,
                    'lamp_ktp_pasangan' => $pen->lamp_ktp_pasangan,
                    'lamp_kk' => $pen->lamp_kk,
                    'lamp_buku_nikah' => $pen->lamp_buku_nikah
                ]
            ];
        }

        if ($val->status_das == 1) {
            $status_das = 'complete';
        } elseif ($val->status_das == 2) {
            $status_das = 'not complete';
        } else {
            $status_das = 'waiting';
        }

        if ($val->status_hm == 1) {
            $status_hm = 'complete';
        } elseif ($val->status_hm == 2) {
            $status_hm = 'not complete';
        } else {
            $status_hm = 'waiting';
        }

        if ($val->ao['status_ao'] == 1) {
            $status_ao = 'recommend';
        } elseif ($val->ao['status_ao'] == 2) {
            $status_ao = 'not recommend';
        } else {
            $status_ao = 'waiting';
        }

        $faspin = FasilitasPinjaman::where('id', $val->id_fasilitas_pinjaman)->first();
        $data = array(
            'id'          => $val->id == null ? null : (int) $val->id,
            'nomor_so'    => $val->nomor_so,
            'nama_so'     => $val->nama_so,
            'das' => [
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
            ],
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
                $faspin
            ],

            'data_debitur' => [
                'id'                    => $val->id_calon_debitur,
                'nama_lengkap'          => $val->debt['nama_lengkap'],
                'gelar_keagamaan'       => $val->debt['gelar_keagamaan'],
                'gelar_pendidikan'      => $val->debt['gelar_pendidikan'],
                'jenis_kelamin'         => $val->debt['jenis_kelamin'],
                'status_nikah'          => $val->debt['status_nikah'],
                'ibu_kandung'           => $val->debt['ibu_kandung'],
                'tinggi_badan'          => $val->debt['tinggi_badan'],
                'berat_badan'           => $val->debt['berat_badan'],
                'no_ktp'                => $val->debt['no_ktp'],
                'no_ktp_kk'             => $val->debt['no_ktp_kk'],
                'no_kk'                 => $val->debt['no_kk'],
                'no_npwp'               => $val->debt['no_npwp'],
                'tempat_lahir'          => $val->debt['tempat_lahir'],
                'tgl_lahir'             => Carbon::parse($val->debt['tgl_lahir'])->format('d-m-Y'),
                'agama'                 => $val->debt['agama'],
                'alamat_ktp' => [
                    'alamat_singkat' => $val->debt['alamat_ktp'],
                    'rt'     => $val->debt['rt_ktp'] == null ? null : (int) $val->debt['rt_ktp'],
                    'rw'     => $val->debt['rw_ktp'] == null ? null : (int) $val->debt['rw_ktp'],
                    'kelurahan' => [
                        'id'    => $val->debt['id_kel_ktp'] == null ? null : (int) $val->debt['id_kel_ktp'],
                        'nama'  => $val->debt['kel_ktp']['nama']
                    ],
                    'kecamatan' => [
                        'id'    => $val->debt['id_kec_ktp'] == null ? null : (int) $val->debt['id_kec_ktp'],
                        'nama'  => $val->debt['kec_ktp']['nama']
                    ],
                    'kabupaten' => [
                        'id'    => $val->debt['id_kab_ktp'] == null ? null : (int) $val->debt['id_kab_ktp'],
                        'nama'  => $val->debt['kab_ktp']['nama'],
                    ],
                    'provinsi'  => [
                        'id'   => $val->debt['id_prov_ktp'] == null ? null : (int) $val->debt['id_prov_ktp'],
                        'nama' => $val->debt['prov_ktp']['nama'],
                    ],
                    'kode_pos' => $val->debt['kel_ktp']['kode_pos'] == null ? null : (int) $val->debt['kel_ktp']['kode_pos']
                ],
                'alamat_domisili' => [
                    'alamat_singkat' => $val->debt['alamat_domisili'],
                    'rt'             => $val->debt['rt_domisili'] == null ? null : (int) $val->debt['rt_domisili'],
                    'rw'             => $val->debt['rw_domisili'] == null ? null : (int) $val->debt['rw_domisili'],
                    'kelurahan' => [
                        'id'    => $val->debt['id_kel_domisili'] == null ? null : (int) $val->debt['id_kel_domisili'],
                        'nama'  => $val->debt['kel_dom']['nama']
                    ],
                    'kecamatan' => [
                        'id'    => $val->debt['id_kec_domisili'] == null ? null : (int) $val->debt['id_kec_domisili'],
                        'nama'  => $val->debt['kec_dom']['nama']
                    ],
                    'kabupaten' => [
                        'id'    => $val->debt['id_kab_domisili'] == null ? null : (int) $val->debt['id_kab_domisili'],
                        'nama'  => $val->debt['kab_dom']['nama'],
                    ],
                    'provinsi'  => [
                        'id'   => $val->debt['id_prov_domisili'] == null ? null : (int) $val->debt['id_prov_domisili'],
                        'nama' => $val->debt['prov_dom']['nama'],
                    ],
                    'kode_pos' => $val->debt['kel_dom']['kode_pos'] == null ? null : (int) $val->debt['kel_dom']['kode_pos']
                ],
                "pekerjaan" => [
                    "nama_pekerjaan"        => $val->debt['pekerjaan'],
                    "posisi_pekerjaan"      => $val->debt['posisi_pekerjaan'],
                    "nama_tempat_kerja"     => $val->debt['nama_tempat_kerja'],
                    "jenis_pekerjaan"       => $val->debt['jenis_pekerjaan'],
                    "tgl_mulai_kerja"       => Carbon::parse($val->debt['tgl_mulai_kerja'])->format('d-m-Y'), //Carbon::parse($val->tgl_mulai_kerja)->format('d-m-Y'),
                    "no_telp_tempat_kerja"  => $val->debt['no_telp_tempat_kerja'],
                    'alamat' => [
                        'alamat_singkat' => $val->debt['alamat_tempat_kerja'],
                        'rt'             => $val->debt['rt_tempat_kerja'] == null ? null : (int) $val->debt['rt_tempat_kerja'],
                        'rw'             => $val->debt['rw_tempat_kerja'] == null ? null : (int) $val->debt['rw_tempat_kerja'],
                        'kelurahan' => [
                            'id'    => $val->debt['id_kel_tempat_kerja'] == null ? null : (int) $val->debt['id_kel_tempat_kerja'],
                            'nama'  => $val->debt['kel_kerja']['nama']
                        ],
                        'kecamatan' => [
                            'id'    => $val->debt['id_kec_tempat_kerja'] == null ? null : (int) $val->debt['id_kec_tempat_kerja'],
                            'nama'  => $val->debt['kec_kerja']['nama']
                        ],
                        'kabupaten' => [
                            'id'    => $val->debt['id_kab_tempat_kerja'] == null ? null : (int) $val->debt['id_kab_tempat_kerja'],
                            'nama'  => $val->debt['kab_kerja']['nama'],
                        ],
                        'provinsi'  => [
                            'id'    => $val->debt['id_prov_tempat_kerja'] == null ? null : (int) $val->debt['id_prov_tempat_kerja'],
                            'nama'  => $val->debt['prov_kerja']['nama'],
                        ],
                        'kode_pos'  => $val->debt['kel_kerja']['kode_pos'] == null ? null : (int) $val->debt['kel_kerja']['kode_pos']
                    ]
                ],
                'pendidikan_terakhir'   => $val->debt['pendidikan_terakhir'],
                'jumlah_tanggungan'     => $val->debt['jumlah_tanggungan'],
                'no_telp'               => $val->debt['no_telp'],
                'no_hp'                 => $val->debt['no_hp'],
                'alamat_surat'          => $val->debt['alamat_surat'],
                'lampiran' => [
                    'lamp_ktp'              => $val->debt['lamp_ktp'],
                    'lamp_kk'               => $val->debt['lamp_kk'],
                    'lamp_buku_tabungan'    => $val->debt['lamp_buku_tabungan'],
                    'lamp_sertifikat'       => $val->debt['lamp_sertifikat'],
                    'lamp_sttp_pbb'         => $val->debt['lamp_sttp_pbb'],
                    'lamp_imb'              => $val->debt['lamp_imb'],
                    'foto_agunan_rumah'     => $val->debt['foto_agunan_rumah']
                ]
            ],

            // 'data_pasangan' => $val->pas,
            'data_pasangan' => [
                'id'                    => $val->id_pasangan,
                'nama_lengkap'          => $val->pas['nama_lengkap'],
                'nama_ibu_kandung'      => $val->pas['nama_ibu_kandung'],
                'gelar_keagamaan'       => $val->pas['gelar_keagamaan'],
                'gelar_pendidikan'      => $val->pas['gelar_pendidikan'],
                'jenis_kelamin'         => $val->pas['jenis_kelamin'],
                'no_ktp'                => $val->pas['no_ktp'],
                'no_ktp_kk'             => $val->pas['no_ktp_kk'],
                'no_npwp'               => $val->pas['no_npwp'],
                'tempat_lahir'          => $val->pas['tempat_lahir'],
                'tgl_lahir'             => $val->pas['tgl_lahir'],
                'alamat_ktp'            => $val->pas['alamat_ktp'],
                'no_telp'               => $val->pas['no_telp'],

                "pekerjaan" => [
                    "nama_pekerjaan"        => $val->pas['pekerjaan'],
                    "posisi_pekerjaan"      => $val->pas['posisi_pekerjaan'],
                    "nama_tempat_kerja"     => $val->pas['nama_tempat_kerja'],
                    "jenis_pekerjaan"       => $val->pas['jenis_pekerjaan'],
                    "tgl_mulai_kerja"       => $val->pas['tgl_mulai_kerja'],
                    "no_telp_tempat_kerja"  => $val->pas['no_telp_tempat_kerja'],
                    'alamat' => [
                        'alamat_singkat' => $val->pas['alamat_tempat_kerja'],
                        'rt'             => $val->pas['rt_tempat_kerja'] == null ? null : (int) $val->pas['rt_tempat_kerja'],
                        'rw'             => $val->pas['rw_tempat_kerja'] == null ? null : (int) $val->pas['rw_tempat_kerja'],
                        'kelurahan' => [
                            'id'    => $val->pas['id_kel_tempat_kerja'] == null ? null : (int) $val->pas['id_kel_tempat_kerja'],
                            'nama'  => $val->pas['kel_kerja']['nama']
                        ],
                        'kecamatan' => [
                            'id'    => $val->pas['id_kec_tempat_kerja'] == null ? null : (int) $val->pas['id_kec_tempat_kerja'],
                            'nama'  => $val->pas['kec_kerja']['nama']
                        ],
                        'kabupaten' => [
                            'id'    => $val->pas['id_kab_tempat_kerja'] == null ? null : (int) $val->pas['id_kab_tempat_kerja'],
                            'nama'  => $val->pas['kab_kerja']['nama'],
                        ],
                        'provinsi'  => [
                            'id'    => $val->pas['id_prov_tempat_kerja'] == null ? null : (int) $val->pas['id_prov_tempat_kerja'],
                            'nama'  => $val->pas['prov_kerja']['nama'],
                        ],
                        'kode_pos'  => $val->pas['kel_kerja']['kode_pos'] == null ? null : (int) $val->pas['kel_kerja']['kode_pos']
                    ]
                ],
                'lampiran' => [
                    'lamp_ktp'        => $val->pas['lamp_ktp'],
                    'lamp_buku_nikah' => $val->pas['lamp_buku_nikah']
                ],
                'flg_aktif'             => $val->pas['flg_aktif']
            ],

            'data_penjamin' => $penjamin,
            'tgl_transaksi' => $val->created_at,
        );

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function update($id, Request $request, BlankRequest $req)
    {
        $pic = $request->pic; // From PIC middleware
        $user_id = $request->auth->user_id;

        $countTAO = TransAO::latest('id', 'nomor_ao')->first();

        if (!$countTAO) {
            $lastNumb = 1;
        } else {
            $no = $countTAO->nomor_ao;

            $arr = explode("-", $no, 5);

            $lastNumb = $arr[4] + 1;
        }

        //Data Transaksi SO
        $nows  = Carbon::now();
        $year  = $nows->year;
        $month = $nows->month;

        $JPIC   = JPIC::where('id', $pic->id_mj_pic)->first();

        //  ID-Cabang - AO / CA / SO - Bulan - Tahun - NO. Urut
        $nomor_ao = $pic->id_cabang . '-' . $JPIC->nama_jenis . '-' . $month . '-' . $year . '-' . $lastNumb;

        $check_so = TransSO::where('id', $id)->where('status_das', 1)->where('status_hm', 1)->first();

        if (empty($check_so)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id ' . $id . ' belum ada di SO atau belum komplit saat pemeriksaaan DAS dan HM'
            ], 404);
        }

        /** Start Check Lampiran */
        $check_form_persetujuan_ideb = $check_so->ao['form_persetujuan_ideb'];

        // Agunan Tanah
        $check_agunan_bag_depan      = $check_so->ao['tan']['agunan_bag_depan'];
        $check_agunan_bag_jalan      = $check_so->ao['tan']['agunan_bag_jalan'];
        $check_agunan_bag_ruangtamu  = $check_so->ao['tan']['agunan_bag_ruangtamu'];
        $check_agunan_bag_kamarmandi = $check_so->ao['tan']['agunan_bag_kamarmandi'];
        $check_agunan_bag_dapur      = $check_so->ao['tan']['agunan_bag_dapur'];

        $check_lamp_imb_tan          = $check_so->ao['tan']['lamp_imb'];
        $check_lamp_pbb_tan          = $check_so->ao['tan']['lamp_pbb'];
        $check_lamp_sertifikat_tan   = $check_so->ao['tan']['lamp_sertifikat'];

        // Agunan Kendaraan
        $check_lamp_agunan_depan_ken = $check_so->ao['tan']['lamp_agunan_depan_ken'];
        $check_lamp_agunan_kanan_ken = $check_so->ao['tan']['lamp_agunan_kanan_ken'];
        $check_lamp_agunan_kiri_ken  = $check_so->ao['tan']['lamp_agunan_kiri_ken'];
        $check_lamp_agunan_belakang_ken = $check_so->ao['tan']['lamp_agunan_belakang_ken'];
        $check_lamp_agunan_dalam_ken = $check_so->ao['tan']['lamp_agunan_dalam_ken'];

        // Debitur
        $check_lamp_ktp             = $check_so->debt['lamp_ktp'];
        $check_lamp_kk              = $check_so->debt['lamp_kk'];
        $check_lamp_sertifikat      = $check_so->debt['lamp_sertifikat'];
        $check_lamp_sttp_pbb        = $check_so->debt['lamp_sttp_pbb'];
        $check_lamp_imb             = $check_so->debt['lamp_imb'];
        $check_foto_agunan_rumah    = $check_so->debt['foto_agunan_rumah'];
        $check_lamp_buku_tabungan   = $check_so->debt['lamp_buku_tabungan'];
        $check_lamp_skk             = $check_so->debt['lamp_skk'];
        $check_lamp_sku             = $check_so->debt['lamp_sku'];
        $check_lamp_slip_gaji       = $check_so->debt['lamp_slip_gaji'];
        $check_foto_pembukuan_usaha = $check_so->debt['foto_pembukuan_usaha'];
        $check_lamp_foto_usaha      = $check_so->debt['lamp_foto_usaha'];
        $check_lamp_surat_cerai     = $check_so->debt['lamp_surat_cerai'];
        $check_lamp_tempat_tinggal  = $check_so->debt['lamp_tempat_tinggal'];

        // dd($check_agunan_bag_depan, $check_agunan_bag_jalan, $check_agunan_bag_ruangtamu, $check_agunan_bag_kamarmandi, $check_agunan_bag_dapur);

        /** End Check Lampiran */

        $check_ao = TransAO::where('id_trans_so', $id)->first();

        if ($check_ao != null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id ' . $id . ' sudah ada di AO'
            ], 404);
        }

        $lamp_dir = $check_so->debt['no_ktp'];

        // Form Persetujuan Ideb
        if ($file = $req->file('form_persetujuan_ideb')) {
            $path = $lamp_dir . '/ideb';
            $name = 'form_persetujuan_ideb';

            $check_file = $check_form_persetujuan_ideb;

            $form_persetujuan_ideb = Helper::uploadImg($check_file, $file, $path, $name);
        } else {
            $form_persetujuan_ideb = $check_form_persetujuan_ideb;
        }

        $TransAO = array(
            'nomor_ao'              => $nomor_ao,
            'id_trans_so'           => $id,
            'user_id'               => $user_id,
            'id_pic'                => $pic->id,
            'id_area'               => $pic->id_area,
            'id_cabang'             => $pic->id_cabang,
            'catatan_ao'            => $req->input('catatan_ao'),
            'status_ao'             => empty($req->input('status_ao')) ? 1 : $req->input('status_ao'),
            'form_persetujuan_ideb' => $form_persetujuan_ideb
        );

        $recom_AO = array(
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
            'tujuan_pinjaman'        => $req->input('tujuan_pinjaman'),
            'jenis_pinjaman'        => $req->input('jenis_pinjaman')
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
            'ver_pembukuan_usaha_debt' => $req->input('ver_pembukuan_usaha_debt'),
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

        /** Lampiran Agunan Tanah */
        if ($files = $req->file('agunan_bag_depan')) {
            $path = $lamp_dir . '/agunan_tanah';
            $name = 'agunan_bag_depan';

            $check_file = $check_agunan_bag_depan;

            $arrayPath = array();
            foreach ($files as $file) {
                $arrayPath[] = Helper::uploadImg($check_file, $file, $path, $name);
            }

            $agunan_bag_depan = $arrayPath;
        } else {
            $agunan_bag_depan = $check_agunan_bag_depan;
        }

        if ($files = $req->file('agunan_bag_jalan')) {
            $path = $lamp_dir . '/agunan_tanah';
            $name = 'agunan_bag_jalan';

            $check_file = $check_agunan_bag_jalan;

            $arrayPath = array();
            foreach ($files as $file) {
                $arrayPath[] = Helper::uploadImg($check_file, $file, $path, $name);
            }

            $agunan_bag_jalan = $arrayPath;
        } else {
            $agunan_bag_jalan = $check_agunan_bag_jalan;
        }

        if ($files = $req->file('agunan_bag_ruangtamu')) {
            $path = $lamp_dir . '/agunan_tanah';
            $name = 'agunan_bag_ruangtamu';

            $check_file = $check_agunan_bag_ruangtamu;

            $arrayPath = array();
            foreach ($files as $file) {
                $arrayPath[] = Helper::uploadImg($check_file, $file, $path, $name);
            }

            $agunan_bag_ruangtamu = $arrayPath;
        } else {
            $agunan_bag_ruangtamu = $check_agunan_bag_ruangtamu;
        }

        if ($files = $req->file('agunan_bag_kamarmandi')) {
            $path = $lamp_dir . '/agunan_tanah';
            $name = 'agunan_bag_kamarmandi';

            $check_file = $check_agunan_bag_kamarmandi;

            $arrayPath = array();
            foreach ($files as $file) {
                $arrayPath[] = Helper::uploadImg($check_file, $file, $path, $name);
            }

            $agunan_bag_ruangtamu = $arrayPath;
        } else {
            $agunan_bag_kamarmandi = $check_agunan_bag_kamarmandi;
        }

        if ($files = $req->file('agunan_bag_dapur')) {
            $path = $lamp_dir . '/agunan_tanah';
            $name = 'agunan_bag_dapur';

            $check_file = $check_agunan_bag_dapur;

            $arrayPath = array();
            foreach ($files as $file) {
                $arrayPath[] = Helper::uploadImg($check_file, $file, $path, $name);
            }

            $agunan_bag_dapur = $arrayPath;
        } else {
            $agunan_bag_dapur = $check_agunan_bag_dapur;
        }

        /** Lampiran Agunan Kendaraan */
        if ($files = $req->file('lamp_agunan_depan_ken')) {
            $path = $lamp_dir . '/agunan_kendaraan';
            $name = 'agunan_depan';

            $check_file = $check_lamp_agunan_depan_ken;

            $arrayPath = array();
            foreach ($files as $file) {
                $arrayPath[] = Helper::uploadImg($check_file, $file, $path, $name);
            }

            $lamp_agunan_depan_ken = $arrayPath;
        } else {
            $lamp_agunan_depan_ken = $check_lamp_agunan_depan_ken;
        }


        if ($files = $req->file('lamp_agunan_kanan_ken')) {
            $path = $lamp_dir . '/agunan_kendaraan';
            $name = 'agunan_kanan';

            $check_file = $check_lamp_agunan_kanan_ken;

            $arrayPath = array();
            foreach ($files as $file) {
                $arrayPath[] = Helper::uploadImg($check_file, $file, $path, $name);
            }

            $lamp_agunan_kanan_ken = $arrayPath;
        } else {
            $lamp_agunan_kanan_ken = $check_lamp_agunan_kanan_ken;
        }


        if ($files = $req->file('lamp_agunan_kiri_ken')) {
            $path = $lamp_dir . '/agunan_kendaraan';
            $name = 'agunan_kiri';

            $check_file = $check_lamp_agunan_kiri_ken;

            $arrayPath = array();
            foreach ($files as $file) {
                $arrayPath[] = Helper::uploadImg($check_file, $file, $path, $name);
            }

            $lamp_agunan_kiri_ken = $arrayPath;
        } else {
            $lamp_agunan_kiri_ken = $check_lamp_agunan_kiri_ken;
        }


        if ($files = $req->file('lamp_agunan_belakang_ken')) {
            $path = $lamp_dir . '/agunan_kendaraan';
            $name = 'agunan_belakang';

            $check_file = $check_lamp_agunan_belakang_ken;

            $arrayPath = array();
            foreach ($files as $file) {
                $arrayPath[] = Helper::uploadImg($check_file, $file, $path, $name);
            }

            $lamp_agunan_belakang_ken = $arrayPath;
        } else {
            $lamp_agunan_belakang_ken = $check_lamp_agunan_belakang_ken;
        }

        if ($files = $req->file('lamp_agunan_dalam_ken')) {
            $path = $lamp_dir . '/agunan_kendaraan';
            $name = 'agunan_dalam';

            $check_file = $check_lamp_agunan_dalam_ken;

            $arrayPath = array();
            foreach ($files as $file) {
                $arrayPath[] = Helper::uploadImg($check_file, $file, $path, $name);
            }

            $lamp_agunan_dalam_ken = $arrayPath;
        } else {
            $lamp_agunan_dalam_ken = $check_lamp_agunan_dalam_ken;
        }

        // Tambahan Agunan Tanah
        if ($files = $req->file('lamp_imb')) {
            $path = $lamp_dir . '/agunan_tanah';
            $name = 'lamp_imb';

            $check_file = $check_lamp_imb_tan;

            $arrayPath = array();
            foreach ($files as $file) {
                $arrayPath[] = Helper::uploadImg($check_file, $file, $path, $name);
            }

            $lamp_imb_tan = $arrayPath;
        } else {
            $lamp_imb_tan = $check_lamp_imb_tan;
        }

        if ($files = $req->file('lamp_pbb')) {
            $path = $lamp_dir . '/agunan_tanah';
            $name = 'lamp_pbb';

            $check_file = $check_lamp_pbb_tan;

            $arrayPath = array();
            foreach ($files as $file) {
                $arrayPath[] = Helper::uploadImg($check_file, $file, $path, $name);
            }

            $lamp_pbb_tan = $arrayPath;
        } else {
            $lamp_pbb_tan = $check_lamp_pbb_tan;
        }

        if ($files = $req->file('lamp_sertifikat')) {
            $path = $lamp_dir . '/agunan_tanah';
            $name = 'lamp_sertifikat';

            $check_file = $check_lamp_sertifikat_tan;

            $arrayPath = array();
            foreach ($files as $file) {
                $arrayPath[] = Helper::uploadImg($check_file, $file, $path, $name);
            }

            $lamp_sertifikat_tan = $arrayPath;
        } else {
            $lamp_sertifikat_tan = $check_lamp_sertifikat_tan;
        }

        if (!empty($req->input('nama_penghuni_agunan'))) {
            for ($i = 0; $i < count($req->input('nama_penghuni_agunan')); $i++) {
                $pemAguTa[] = [
                    'nama_penghuni'
                    => empty($req->nama_penghuni_agunan[$i])
                        ? null : $req->nama_penghuni_agunan[$i],

                    'status_penghuni'
                    => empty($req->status_penghuni_agunan[$i])
                        ? null : strtoupper($req->status_penghuni_agunan[$i]),

                    'bentuk_bangunan'
                    => empty($req->bentuk_bangunan_agunan[$i])
                        ? null : $req->bentuk_bangunan_agunan[$i],

                    'kondisi_bangunan'
                    => empty($req->kondisi_bangunan_agunan[$i])
                        ? null : $req->kondisi_bangunan_agunan[$i],

                    'fasilitas'
                    => empty($req->fasilitas_agunan[$i])
                        ? null : $req->fasilitas_agunan[$i],

                    'listrik'
                    => empty($req->listrik_agunan[$i])
                        ? null : $req->listrik_agunan[$i],

                    'nilai_taksasi_agunan'
                    => empty($req->nilai_taksasi_agunan[$i])
                        ? null : $req->nilai_taksasi_agunan[$i],

                    'nilai_taksasi_bangunan'
                    => empty($req->nilai_taksasi_bangunan[$i])
                        ? null : $req->nilai_taksasi_bangunan[$i],

                    'tgl_taksasi'
                    => empty($req->tgl_taksasi_agunan[$i])
                        ? null : Carbon::parse($req->tgl_taksasi_agunan[$i])->format('Y-m-d'),

                    'nilai_likuidasi'
                    => empty($req->nilai_likuidasi_agunan[$i])
                        ? null : $req->nilai_likuidasi_agunan[$i],

                    'nilai_agunan_independen'
                    => empty($req->nilai_agunan_independen[$i])
                        ? 0 : $req->nilai_agunan_independen[$i],

                    'perusahaan_penilai_independen'
                    => empty($req->perusahaan_penilai_independen[$i])
                        ? null : $req->perusahaan_penilai_independen[$i]
                ];
            }
        }

        // dd($pemAguTa);


        if (!empty($req->input('tipe_lokasi_agunan'))) {

            for ($i = 0; $i < count($req->input('tipe_lokasi_agunan')); $i++) {

                $daAguTa[] = [
                    'tipe_lokasi'
                    => empty($req->tipe_lokasi_agunan[$i])
                        ? null : strtoupper($req->tipe_lokasi_agunan[$i]),

                    'alamat'
                    => empty($req->alamat_agunan[$i])
                        ? null : $req->alamat_agunan[$i],

                    'id_provinsi'
                    => empty($req->id_prov_agunan[$i])
                        ? null : $req->id_prov_agunan[$i],

                    'id_kabupaten'
                    => empty($req->id_kab_agunan[$i])
                        ? null : $req->id_kab_agunan[$i],

                    'id_kecamatan'
                    => empty($req->id_kec_agunan[$i])
                        ? null : $req->id_kec_agunan[$i],

                    'id_kelurahan'
                    => empty($req->id_kel_agunan[$i])
                        ? null : $req->id_kel_agunan[$i],

                    'rt'
                    => empty($req->rt_agunan[$i])
                        ? null : $req->rt_agunan[$i],

                    'rw'
                    => empty($req->rw_agunan[$i])
                        ? null : $req->rw_agunan[$i],

                    'luas_tanah'
                    => empty($req->luas_tanah[$i])
                        ? null : $req->luas_tanah[$i],

                    'luas_bangunan'
                    => empty($req->luas_bangunan[$i])
                        ? null : $req->luas_bangunan[$i],

                    'nama_pemilik_sertifikat'
                    => empty($req->nama_pemilik_sertifikat[$i])
                        ? null : $req->nama_pemilik_sertifikat[$i],

                    'jenis_sertifikat'
                    => empty($req->jenis_sertifikat[$i])
                        ? null : strtoupper($req->jenis_sertifikat[$i]),

                    'no_sertifikat'
                    => empty($req->no_sertifikat[$i])
                        ? null : $req->no_sertifikat[$i],

                    'tgl_ukur_sertifikat'
                    => empty($req->tgl_ukur_sertifikat[$i])
                        ? null : $req->tgl_ukur_sertifikat[$i],

                    'tgl_berlaku_shgb'
                    => empty($req->tgl_berlaku_shgb[$i])
                        ? null : Carbon::parse($req->tgl_berlaku_shgb[$i])->format('Y-m-d'),

                    'no_imb'
                    => empty($req->no_imb[$i])
                        ? null : $req->no_imb[$i],

                    'njop'
                    => empty($req->njop[$i])
                        ? null : $req->njop[$i],

                    'nop'
                    => empty($req->nop[$i])
                        ? null : $req->nop[$i],
                    // 'lam_imb'                 => empty($req->file('lam_imb')[$i]) ? null : Helper::img64enc($req->file('lam_imb')[$i]),
                    'agunan_bag_depan'
                    => empty($agunan_bag_depan[$i])
                        ? null : $agunan_bag_depan[$i],

                    'agunan_bag_jalan'
                    => empty($agunan_bag_jalan[$i])
                        ? null : $agunan_bag_jalan[$i],

                    'agunan_bag_ruangtamu'
                    => empty($agunan_bag_ruangtamu[$i])
                        ? null : $agunan_bag_ruangtamu[$i],

                    'agunan_bag_kamarmandi'
                    => empty($agunan_bag_kamarmandi[$i])
                        ? null : $agunan_bag_kamarmandi[$i],

                    'agunan_bag_dapur'
                    => empty($agunan_bag_dapur[$i])
                        ? null : $agunan_bag_dapur[$i],

                    'lamp_imb'
                    => empty($lamp_imb_tan[$i])
                        ? null : $lamp_imb_tan[$i],

                    'lamp_pbb'
                    => empty($lamp_pbb_tan[$i])
                        ? null : $lamp_pbb_tan[$i],

                    'lamp_sertifikat'
                    => empty($lamp_sertifikat_tan[$i])
                        ? null : $lamp_sertifikat_tan[$i]
                ];
            }
        }


        if (!empty($req->input('no_bpkb_ken'))) {

            for ($i = 0; $i < count($req->input('no_bpkb_ken')); $i++) {

                $daAguKe[] = [
                    'no_bpkb'
                    => empty($req->no_bpkb_ken[$i])
                        ? null : $req->no_bpkb_ken[$i],

                    'nama_pemilik'
                    => empty($req->nama_pemilik_ken[$i])
                        ? null : $req->nama_pemilik_ken[$i],

                    'alamat_pemilik'
                    => empty($req->alamat_pemilik_ken[$i])
                        ? null : $req->alamat_pemilik_ken[$i],

                    'merk'
                    => empty($req->merk_ken[$i])
                        ? null : $req->merk_ken[$i],

                    'jenis'
                    => empty($req->jenis_ken[$i])
                        ? null : $req->jenis_ken[$i],

                    'no_rangka'
                    => empty($req->no_rangka_ken[$i])
                        ? null : $req->no_rangka_ken[$i],

                    'no_mesin'
                    => empty($req->no_mesin_ken[$i])
                        ? null : $req->no_mesin_ken[$i],

                    'warna'
                    => empty($req->warna_ken[$i])
                        ? null : $req->warna_ken[$i],

                    'tahun'
                    => empty($req->tahun_ken[$i])
                        ? null : $req->tahun_ken[$i],

                    'no_polisi'
                    => empty($req->no_polisi_ken[$i])
                        ? null : strtoupper($req->no_polisi_ken[$i]),

                    'no_stnk'
                    => empty($req->no_stnk_ken[$i])
                        ? null : $req->no_stnk_ken[$i],

                    'tgl_kadaluarsa_pajak'
                    => empty($req->tgl_exp_pajak_ken[$i])
                        ? null : Carbon::parse($req->tgl_exp_pajak_ken[$i])->format('Y-m-d'),

                    'tgl_kadaluarsa_stnk'
                    => empty($req->tgl_exp_stnk_ken[$i])
                        ? null : Carbon::parse($req->tgl_exp_stnk_ken[$i])->format('Y-m-d'),

                    'no_faktur'
                    => empty($req->no_faktur_ken[$i])
                        ? null : $req->no_faktur_ken[$i],

                    'lamp_agunan_depan'
                    => empty($lamp_agunan_depan_ken[$i])
                        ? null : $lamp_agunan_depan_ken[$i],

                    'lamp_agunan_kanan'
                    => empty($lamp_agunan_kanan_ken[$i])
                        ? null : $lamp_agunan_kanan_ken[$i],

                    'lamp_agunan_kiri'
                    => empty($lamp_agunan_kiri_ken[$i])
                        ? null : $lamp_agunan_kiri_ken[$i],

                    'lamp_agunan_belakang'
                    => empty($lamp_agunan_belakang_ken[$i])
                        ? null : $lamp_agunan_belakang_ken[$i],

                    'lamp_agunan_dalam'
                    => empty($lamp_agunan_dalam_ken[$i])
                        ? null : $lamp_agunan_dalam_ken[$i]
                ];

                $pemAguKe[] = [
                    'nama_pengguna'
                    => empty($req->nama_pengguna_ken[$i])
                        ? null : $req->nama_pengguna_ken[$i],

                    'status_pengguna'
                    => empty($req->status_pengguna_ken[$i])
                        ? null : strtoupper($req->status_pengguna_ken[$i]),

                    'jml_roda_kendaraan'
                    => empty($req->jml_roda_ken[$i])
                        ? null : $req->jml_roda_ken[$i],

                    'kondisi_kendaraan'
                    => empty($req->kondisi_ken[$i])
                        ? null : $req->kondisi_ken[$i],

                    'keberadaan_kendaraan'
                    => empty($req->keberadaan_ken[$i])
                        ? null : $req->keberadaan_ken[$i],

                    'body'
                    => empty($req->body_ken[$i])
                        ? null : $req->body_ken[$i],

                    'interior'
                    => empty($req->interior_ken[$i])
                        ? null : $req->interior_ken[$i],

                    'km'
                    => empty($req->km_ken[$i])
                        ? null : $req->km_ken[$i],

                    'modifikasi'
                    => empty($req->modifikasi_ken[$i])
                        ? null : $req->modifikasi_ken[$i],

                    'aksesoris'
                    => empty($req->aksesoris_ken[$i])
                        ? null : $req->aksesoris_ken[$i]
                ];
            }
        }

        // Start Kapasitas Bulanan
        $inputKapBul = array(

            'pemasukan_cadebt'      => $req->input('pemasukan_debitur'),
            'pemasukan_pasangan'    => $req->input('pemasukan_pasangan'),
            'pemasukan_penjamin'    => $req->input('pemasukan_penjamin'),
            'biaya_rumah_tangga'    => $req->input('biaya_rumah_tangga'),
            'biaya_transport'       => $req->input('biaya_transport'),
            'biaya_pendidikan'      => $req->input('biaya_pendidikan'),
            'telp_listr_air'        => $req->input('telp_listr_air'), // jangan lupa hampir sama dengan pendapatan usaha
            'angsuran'              => $req->input('angsuran'),
            'biaya_lain'            => $req->input('biaya_lain')
        );

        $total_KapBul = array(
            'total_pemasukan'    => $ttl1 = array_sum(array_slice($inputKapBul, 0, 3)),
            'total_pengeluaran'  => $ttl2 = array_sum(array_slice($inputKapBul, 3)),
            'penghasilan_bersih' => $ttl1 - $ttl2
        );

        $kapBul = array_merge($inputKapBul, $total_KapBul);
        // End Kapasitas Bulanan


        //   if (!empty($req->input('pemasukan_tunai'))) {
        // $dataKeUsaha = array(
        $inputKeUsaha = array(
            'pemasukan_tunai'
            => empty($req->input('pemasukan_tunai')) ? 0
                :  $req->input('pemasukan_tunai'),

            'pemasukan_kredit'
            => empty($req->input('pemasukan_kredit')) ? 0
                :  $req->input('pemasukan_kredit'),

            'biaya_sewa'
            => empty($req->input('biaya_sewa')) ? 0
                :  $req->input('biaya_sewa'),

            'biaya_gaji_pegawai'
            => empty($req->input('biaya_gaji_pegawai')) ? 0
                :  $req->input('biaya_gaji_pegawai'),

            'biaya_belanja_brg'
            => empty($req->input('biaya_belanja_brg')) ? 0
                :  $req->input('biaya_belanja_brg'),

            'biaya_telp_listr_air'
            => empty($req->input('biaya_telp_listr_air')) ? 0
                :  $req->input('biaya_telp_listr_air'),

            'biaya_sampah_kemanan'
            => empty($req->input('biaya_sampah_kemanan')) ? 0
                :  $req->input('biaya_sampah_kemanan'),

            'biaya_kirim_barang'
            => empty($req->input('biaya_kirim_barang')) ? 0
                :  $req->input('biaya_kirim_barang'),

            'biaya_hutang_dagang'
            => empty($req->input('biaya_hutang_dagang')) ? 0
                :  $req->input('biaya_hutang_dagang'),

            'biaya_angsuran'
            => empty($req->input('biaya_angsuran')) ? 0
                :  $req->input('biaya_angsuran'),

            'biaya_lain_lain'
            => empty($req->input('biaya_lain_lain')) ? 0
                :  $req->input('biaya_lain_lain')
        );

        $total_KeUsaha = array(
            'total_pemasukan'      => $ttl1 = array_sum(array_slice($inputKeUsaha, 0, 2)),
            'total_pengeluaran'    => $ttl2 = array_sum(array_slice($inputKeUsaha, 2)),
            'laba_usaha'           => $ttl1 - $ttl2
        );

        $dataKeUsaha = array_merge($inputKeUsaha, $total_KeUsaha);
        //  }

        // Lampiran Debitur
        if ($file = $req->file('lamp_ktp')) {
            $path = $lamp_dir . '/debitur';
            $name = 'ktp';

            $check_file = $check_lamp_ktp;

            $lamp_ktp = Helper::uploadImg($check_file, $file, $path, $name);
        } else {
            $lamp_ktp = $check_lamp_ktp;
        }

        if ($file = $req->file('lamp_kk')) {
            $path = $lamp_dir . '/debitur';
            $name = 'kk';

            $check_file = $check_lamp_kk;

            $lamp_kk = Helper::uploadImg($check_file, $file, $path, $name);
        } else {
            $lamp_kk = $check_lamp_kk;
        }

        if ($file = $req->file('lamp_sertifikat')) {
            $path = $lamp_dir . '/debitur';
            $name = 'sertifikat';

            $check_file = $check_lamp_sertifikat;

            $lamp_sertifikat = Helper::uploadImg($check_file, $file, $path, $name);
        } else {
            $lamp_sertifikat = $check_lamp_sertifikat;
        }

        if ($file = $req->file('lamp_pbb')) {
            $path = $lamp_dir . '/debitur';
            $name = 'pbb';

            $check_file = $check_lamp_sttp_pbb;

            $lamp_sttp_pbb = Helper::uploadImg($check_file, $file, $path, $name);
        } else {
            $lamp_sttp_pbb = $check_lamp_sttp_pbb;
        }

        if ($file = $req->file('lamp_imb')) {
            $path = $lamp_dir . '/debitur';
            $name = 'imb';

            $check_file = $check_lamp_imb;

            $lamp_imb = Helper::uploadImg($check_file, $file, $path, $name);
        } else {
            $lamp_imb = $check_lamp_imb;
        }

        if ($file = $req->file('foto_agunan_rumah')) {
            $path = $lamp_dir . '/debitur';
            $name = 'foto_agunan_rumah';

            $check_file = $check_foto_agunan_rumah;

            $foto_agunan_rumah = Helper::uploadImg($check_file, $file, $path, $name);
        } else {
            $foto_agunan_rumah = $check_foto_agunan_rumah;
        }

        if ($files = $req->file('lamp_buku_tabungan')) {
            $path = $lamp_dir . '/lamp_buku_tabungan';
            $name = 'lamp_buku_tabungan';

            $check_file = $check_lamp_buku_tabungan;

            $arrayPath = array();
            foreach ($files as $file) {
                $arrayPath[] = Helper::uploadImg($check_file, $file, $path, $name);
            }

            $lamp_buku_tabungan = implode(";", $arrayPath);
        } else {
            $lamp_buku_tabungan = $check_lamp_buku_tabungan;
        }

        if ($file = $req->file('lamp_skk')) {
            $path = $lamp_dir . '/debitur';
            $name = 'lamp_skk';

            $check_file = $check_lamp_skk;

            $lamp_skk = Helper::uploadImg($check_file, $file, $path, $name);
        } else {
            $lamp_skk = $check_lamp_skk;
        }

        if ($files = $req->file('lamp_sku')) {
            $path = $lamp_dir . '/debitur';
            $name = 'lamp_sku';

            $check_file = $check_lamp_sku;

            $arrayPath = array();
            foreach ($files as $file) {
                $arrayPath[] = Helper::uploadImg($check_file, $file, $path, $name);
            }

            $lamp_sku = implode(";", $arrayPath);
        } else {
            $lamp_sku = $check_lamp_sku;
        }

        if ($file = $req->file('lamp_slip_gaji')) {
            $path = $lamp_dir . '/debitur';
            $name = 'lamp_slip_gaji'; //->getClientOriginalExtension();

            $check_file = $check_lamp_slip_gaji;

            $lamp_slip_gaji = Helper::uploadImg($check_file, $file, $path, $name);
        } else {
            $lamp_slip_gaji = $check_lamp_slip_gaji;
        }


        if ($files = $req->file('foto_pembukuan_usaha')) {
            $path = $lamp_dir . '/debitur';
            $name = 'foto_pembukuan_usaha';

            $check_file = $check_foto_pembukuan_usaha;
            $arrayPath = array();
            foreach ($files as $file) {
                $arrayPath[] = Helper::uploadImg($check_file, $file, $path, $name);
            }

            $foto_pembukuan_usaha = implode(";", $arrayPath);
        } else {
            $foto_pembukuan_usaha = $check_foto_pembukuan_usaha;
        }

        if ($files = $req->file('lamp_foto_usaha')) {
            $path = $lamp_dir . '/debitur';
            $name = 'lamp_foto_usaha';

            $check_file = $check_lamp_foto_usaha;
            $arrayPath = array();
            foreach ($files as $file) {
                $arrayPath[] = Helper::uploadImg($check_file, $file, $path, $name);
            }

            $lamp_foto_usaha = implode(";", $arrayPath);
        } else {
            $lamp_foto_usaha = $check_lamp_foto_usaha;
        }

        if ($file = $req->file('lamp_surat_cerai')) {
            $path = $lamp_dir . '/debitur';
            $name = 'lamp_surat_cerai';

            $check_file = $check_lamp_surat_cerai;

            $lamp_surat_cerai = Helper::uploadImg($check_file, $file, $path, $name);
        } else {
            $lamp_surat_cerai = $check_lamp_surat_cerai;
        }

        if ($file = $req->file('lamp_tempat_tinggal')) {
            $path = $lamp_dir . '/debitur';
            $name = 'lamp_tempat_tinggal';

            $check_file = $check_lamp_tempat_tinggal;

            $lamp_tempat_tinggal = Helper::uploadImg($check_file, $file, $path, $name);
        } else {
            $lamp_tempat_tinggal = $check_lamp_tempat_tinggal;
        }

        $cadebt = array(
            'lamp_ktp'              => $lamp_ktp,
            'lamp_kk'               => $lamp_kk,
            'lamp_sertifikat'       => $lamp_sertifikat,
            'lamp_sttp_pbb'         => $lamp_sttp_pbb,
            'lamp_imb'              => $lamp_imb,
            'lamp_buku_tabungan'    => $lamp_buku_tabungan,
            'lamp_skk'              => $lamp_skk,
            'lamp_sku'              => $lamp_sku,
            'lamp_slip_gaji'        => $lamp_slip_gaji,
            'foto_pembukuan_usaha'  => $foto_pembukuan_usaha,
            'lamp_foto_usaha'       => $lamp_foto_usaha,
            'foto_agunan_rumah'     => $foto_agunan_rumah,
            'lamp_surat_cerai'      => $lamp_surat_cerai,
            'lamp_tempat_tinggal'   => $lamp_tempat_tinggal
        );

        DB::connection('web')->beginTransaction();
        try {

            if (!empty($pemAguTa)) {
                $arrayPemTan = array();
                for ($i = 0; $i < count($pemAguTa); $i++) {
                    // $pemAguTa_N[$i] = array_merge(array('id_agunan_tanah' => $id_tanah['id'][$i]), $pemAguTa[$i]);

                    $pemTanah = PemeriksaanAgunTan::create($pemAguTa[$i]);

                    $id_pem_tan['id'][$i] = $pemTanah->id;

                    $arrayPemTan[] = $pemTanah;
                }

                $p_tanID = implode(",", $id_pem_tan['id']);
            } else {
                $arrayPemTan = null;
                $p_tanID = null;
            }

            if (!empty($daAguTa)) {
                $arrayTan = array();
                for ($i = 0; $i < count($daAguTa); $i++) {

                    $tanah = AgunanTanah::create($daAguTa[$i]);

                    $id_tanah['id'][$i] = $tanah->id;

                    $arrayTan[] = $tanah;
                }

                $tanID   = implode(",", $id_tanah['id']);
            } else {
                $arrayTan = null;
                $tanID   = null;
            }

            if (!empty($daAguKe)) {
                $arrayKen = array();
                $arrayPemKen = array();

                for ($i = 0; $i < count($daAguKe); $i++) {
                    $kendaraan = AgunanKendaraan::create($daAguKe[$i]);

                    $id_kendaraan['id'][$i] = $kendaraan->id;
                    $arrayKen[] = $kendaraan;
                }

                for ($i = 0; $i < count($pemAguKe); $i++) {
                    $pemAguKe_N[$i] = array_merge(array('id_agunan_kendaraan' => $id_kendaraan['id'][$i]), $pemAguKe[$i]);

                    $pemKendaraan = PemeriksaanAgunKen::create($pemAguKe_N[$i]);

                    $id_pem_ken['id'][$i] = $pemKendaraan->id;
                    $arrayPemKen[] = $pemKendaraan;
                }

                $kenID   = implode(",", $id_kendaraan['id']);
                $p_kenID = implode(",", $id_pem_ken['id']);
            } else {
                $arrayKen    = null;
                $arrayPemKen = null;
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
            } else {
                $keuangan = null;
                $id_usaha = null;
            }

            if (!empty($recom_AO)) {
                $recom = RekomendasiAO::create($recom_AO);
                $id_recom = $recom->id;
            } else {
                $recom = null;
                $id_recom = null;
            }

            $dataAO = array(
                'id_validasi'                 => $id_valid,
                'id_verifikasi'               => $id_verif,
                'id_agunan_tanah'             => $tanID,
                'id_agunan_kendaraan'         => $kenID,
                'id_periksa_agunan_tanah'     => $p_tanID,
                'id_periksa_agunan_kendaraan' => $p_kenID,
                'id_kapasitas_bulanan'        => $id_kapbul,
                'id_pendapatan_usaha'         => $id_usaha,
                'id_recom_ao'                 => $id_recom
            );

            $arrAO = array_merge($TransAO, $dataAO);

            $new_TransAO = TransAO::create($arrAO);

            TransSO::where('id', $id)->update(['id_trans_ao' => $new_TransAO->id]);

            Debitur::where('id', $check_so->id_calon_debitur)->update($cadebt);

            DB::connection('web')->commit();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message' => 'Data untuk AO berhasil dikirim',
                'data'   => [
                    'trans_ao'                      => $new_TransAO,
                    'agunan_tanah'                  => $arrayTan,
                    'pemeriksaaan_agunan_tanah'     => $arrayPemTan,
                    'agunan_kendaraan'              => $arrayKen,
                    'pemeriksaaan_agunan_kendaraan' => $arrayPemKen,
                    'validasi'                      => $valid,
                    'vierifikasi'                   => $verif,
                    'kapasitas_bulanan'             => $kap,
                    'pendapatan_usaha'              => $keuangan,
                    'rekomendasi_so'                => $recom
                ]
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

    public function search($param, $key, $value, $status, $orderVal, $orderBy, $limit, Request $req)
    {
        $pic = $req->pic; // From PIC middleware

        $column = array(
            'id', 'nomor_so', 'user_id', 'id_pic', 'id_area', 'id_cabang', 'id_asal_data', 'nama_marketing', 'nama_so', 'id_fasilitas_pinjaman', 'id_calon_debitur', 'id_pasangan', 'id_penjamin', 'id_trans_ao', 'id_trans_ca', 'id_trans_caa', 'catatan_das', 'catatan_hm', 'status_das', 'status_hm', 'lamp_ideb', 'lamp_pefindo'
        );

        if ($param != 'filter' && $param != 'search') {
            return response()->json([
                'code'    => 412,
                'status'  => 'not valid',
                'message' => 'gunakan parameter yang valid diantara berikut: filter, search'
            ], 412);
        }

        if (in_array($key, $column) == false) {
            return response()->json([
                'code'    => 412,
                'status'  => 'not valid',
                'message' => 'gunakan key yang valid diantara berikut: ' . implode(",", $column)
            ], 412);
        }

        if (in_array($orderBy, $column) == false) {
            return response()->json([
                'code'    => 412,
                'status'  => 'not valid',
                'message' => 'gunakan order by yang valid diantara berikut: ' . implode(",", $column)
            ], 412);
        }

        if ($param == 'search') {
            $operator   = "like";
            $func_value = "%{$value}%";
        } else {
            $operator   = "=";
            $func_value = "{$value}";
        }

        $id_area   = $pic->id_area;
        $id_cabang = $pic->id_cabang;
        $scope     = $pic->jpic['cakupan'];

        $query_dir = TransSO::with('pic', 'cabang', 'asaldata', 'debt', 'pas', 'faspin', 'ao', 'ca')
            ->where('status_hm', 1)
            ->where('status_das', 1)
            ->where('flg_aktif', $status)
            ->orderBy($orderBy, $orderVal);

        $query = Helper::checkDir($scope, $query_dir, $id_area, $id_cabang);

        if ($value == 'default') {
            $res = $query;
        } else {
            $res = $query->where($key, $operator, $func_value);
        }

        if ($limit == 'default') {
            $result = $res;
        } else {
            $result = $res->limit($limit);
        }

        if ($result->get() == '[]') {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $data = array();
        foreach ($result->get() as $key => $val) {

            if ($val->status_das == 1) {
                $status_das = 'complete';
            } elseif ($val->status_das == 2) {
                $status_das = 'not complete';
            } else {
                $status_das = 'waiting';
            }

            if ($val->status_hm == 1) {
                $status_hm = 'complete';
            } elseif ($val->status_hm == 2) {
                $status_hm = 'not complete';
            } else {
                $status_hm = 'waiting';
            }

            if ($val->ao['status_ao'] == 1) {
                $status_ao = 'recommend';
            } elseif ($val->ao['status_ao'] == 2) {
                $status_ao = 'not recommend';
            } else {
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
                ],
                'tgl_transaksi' => $val->created_at
            ];
        }

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'count'  => sizeof($data),
                'data'   => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function filter($year, $month = null, Request $req)
    {
        $pic = $req->pic; // From PIC middleware

        $id_area   = $pic->id_area;
        $id_cabang = $pic->id_cabang;
        $scope     = $pic->jpic['cakupan'];

        if ($month == null) {

            $query_dir = TransSO::with('pic', 'cabang', 'asaldata', 'debt', 'pas', 'faspin', 'ao', 'ca')
                ->where('status_das', 1)->where('status_hm', 1)
                ->whereYear('created_at', '=', $year)
                ->orderBy('created_at', 'desc');
        } else {

            $query_dir = TransSO::with('pic', 'cabang', 'asaldata', 'debt', 'pas', 'faspin', 'ao', 'ca')
                ->where('status_das', 1)->where('status_hm', 1)
                ->whereYear('created_at', '=', $year)
                ->whereMonth('created_at', '=', $month)
                ->orderBy('created_at', 'desc');
        }

        $query = Helper::checkDir($scope, $query_dir, $id_area, $id_cabang);


        if ($query->get() == '[]') {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        $data = array();
        foreach ($query->get() as $key => $val) {

            if ($val->status_das == 1) {
                $status_das = 'complete';
            } elseif ($val->status_das == 2) {
                $status_das = 'not complete';
            } else {
                $status_das = 'waiting';
            }

            if ($val->status_hm == 1) {
                $status_hm = 'complete';
            } elseif ($val->status_hm == 2) {
                $status_hm = 'not complete';
            } else {
                $status_hm = 'waiting';
            }

            if ($val->ao['status_ao'] == 1) {
                $status_ao = 'recommend';
            } elseif ($val->ao['status_ao'] == 2) {
                $status_ao = 'not recommend';
            } else {
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
                ],
                'tgl_transaksi' => $val->created_at
            ];
        }

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'count'  => sizeof($data),
                'data'   => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }
}
