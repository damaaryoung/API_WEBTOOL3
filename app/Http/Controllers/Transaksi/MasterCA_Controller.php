<?php

namespace App\Http\Controllers\Transaksi;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Models\Pengajuan\CA\RekomendasiPinjaman;

use App\Models\Pengajuan\AO\AgunanTanah;
use App\Models\Pengajuan\AO\AgunanKendaraan;
use App\Models\Pengajuan\AO\PemeriksaanAgunTan;
use App\Models\Pengajuan\AO\PemeriksaanAgunKen;
use App\Models\Pengajuan\SO\FasilitasPinjaman;
use App\Models\Pengajuan\SO\Debitur;
use App\Models\Pengajuan\CA\AsuransiJaminan;
use App\Models\Pengajuan\CA\AsuransiJaminanKen;
use App\Models\Pengajuan\AO\PendapatanUsaha;
use App\Models\Pengajuan\SO\Penjamin;
use App\Http\Controllers\Controller as Helper;
use App\Http\Requests\Transaksi\BlankRequest;
use App\Models\Pengajuan\CA\RingkasanAnalisa;
use App\Models\Pengajuan\CA\RekomendasiCA;
use App\Models\Pengajuan\CA\AsuransiJiwa;
use App\Models\Pengajuan\AO\KapBulanan;
use App\Models\Pengajuan\CA\MutasiBank;
use App\Models\Pengajuan\CA\TabDebt;
use App\Models\Pengajuan\CA\InfoACC;
use App\Models\Transaksi\TransCA;
use App\Models\Transaksi\TransAO;
use App\Models\Transaksi\TransSO;
use App\Models\AreaKantor\JPIC;
use App\Models\AreaKantor\PIC;
use Illuminate\Http\Request;
use App\Models\Pengajuan\SO\Anak;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use App\Models\master_nilai;
use App\Models\master_transaksi;
use App\Models\TrackingOrderCa;
// use Image;
//use DB;
use Illuminate\Support\Facades\DB;

class MasterCA_Controller extends BaseController
{
    public function index(Request $req)
    {
        $pic = $req->pic; // From PIC middleware

        $arr = array();
        $i = 0;
        foreach ($pic as $val) {
            $arr[] = $val['id_area'];
            $i++;
        }

        $arrr = array();
        foreach ($pic as $val) {
            $arrr[] = $val['id_cabang'];
            $i++;
        }
        $arrrr = array();
        foreach ($pic as $val) {
            $arrrr[] = $val['jpic']['cakupan'];
            $i++;
        }
        //  dd($arr);
        $id_area   = $arr;
        $id_cabang = $arrr;
        // dd($id_cabang);
        $scope     = $arrrr;

        $query_dir = TransAO::with('so', 'pic', 'cabang')->where('status_ao', 1)->orderBy('created_at', 'desc');

        $query = Helper::checkDir($scope, $query_dir, $id_area, $id_cabang);

        if (empty($query)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data di AO masih kosong'
            ], 404);
        }

        $data = array();
        foreach ($query as $key => $val) {
            //   dd($val->so);
            if ($val->status_ao == 1) {
                $status_ao = 'recommend';
            } elseif ($val->status_ao == 2) {
                $status_ao = 'not recommend';
            } else {
                $status_ao = 'waiting';
            }

            if ($val->so['ca']['status_ca'] == 1) {
                $status_ca = 'recommend';
            } elseif ($val->so['ca']['status_ca'] == 2) {
                $status_ca = 'not recommend';
            } else {
                $status_ca = 'waiting';
            }

            if ($val->so['caa']['status_caa'] == 1) {
                $status_caa = 'recommend';
            } elseif ($val->so['caa']['status_caa'] == 2) {
                $status_caa = 'not recommend';
            } else {
                $status_caa = 'waiting';
            }

            $data[$key] = [
                'id_trans_so'    => $val->id_trans_so == null ? null : (int) $val->id_trans_so,
                'nomor_so'       => $val->so['nomor_so'],
                'norev_so'       => $val->so['norev_so'],
                'nama_so'        => $val->so['nama_so'],
                "ao" => [
                    'status_ao'     => $status_ao,
                    'catatan_ao'    => $val->catatan_ao,
					'status_return' => $val->status_return,
					'note_return' => $val->note_return,
					'tgl_pending' => $val->tgl_pending
                ],
                "ca" => [
                    'status_ca'     => $status_ca,
                    'catatan_ca'    => $val->so['ca']['catatan_ca']
                ],
                "caa" => [
                    'status_caa'     => $status_caa,
                    'catatan_caa'    => $val->so['caa']['catatan_caa']
                ],
                'pic'            => $val->pic['nama'],
                'area'           => $val->area['nama'],
                'cabang'         => $val->cabang['nama'],
                'asal_data'      => $val->so['asaldata']['nama'],
                'nama_marketing' => $val->so['nama_marketing'],
                'nama_debitur'   => $val->so['debt']['nama_lengkap'],
                'plafon'         => $val->so['faspin']['plafon'],
                'tenor'          => $val->so['faspin']['tenor'],
                'tgl_transaksi'  => Carbon::parse($val->created_at)->format('d-m-Y H:m:s')
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

        $arr = array();
        $i = 0;
        foreach ($pic as $val) {
            $arr[] = $val['id_area'];
            $i++;
        }

        $arrr = array();
        foreach ($pic as $val) {
            $arrr[] = $val['id_cabang'];
            $i++;
        }
        $arrrr = array();
        foreach ($pic as $val) {
            $arrrr[] = $val['jpic']['cakupan'];
            $i++;
        }
        //  dd($arr);
        $id_area   = $arr;
        $id_cabang = $arrr;
        // dd($id_cabang);
        $scope     = $arrrr;

        $query_dir = TransAO::with('so', 'pic', 'cabang')->where('status_ao', 1)->orderBy('created_at', 'desc');

        $query = Helper::checkDir($scope, $query_dir, $id_area, $id_cabang);

        if (empty($query)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data di AO masih kosong'
            ], 404);
        }

        $data = array();
        foreach ($query as $key => $val) {

            if ($val->status_ao == 1) {
                $status_ao = 'recommend';
            } elseif ($val->status_ao == 2) {
                $status_ao = 'not recommend';
            } else {
                $status_ao = 'waiting';
            }

            if ($val->so['ca']['status_ca'] == 1) {
                $status_ca = 'recommend';
            } elseif ($val->so['ca']['status_ca'] == 2) {
                $status_ca = 'not recommend';
            } else {
                $status_ca = 'waiting';
            }

            $rev =  TransCA::select('revisi')->where('id_trans_so', $val->id_trans_so)->get();
            //merubah nilai null menjadi empty string
            $arr = array();
            foreach ($rev as $key => $value) {
                $arr['revisi'] = $value->revisi;
            }

            $data[] = [
                'id_trans_so'    => $val->id_trans_so == null ? null : (int) $val->id_trans_so,
                'nomor_so'       => $val->so['nomor_so'],
                'notes_so'       => $val->so['notes_so'],
                'no_rev'        => $rev,
                'nomor_ao'       => $val->nomor_ao,
                "ao" => [
                    'status_ao'     => $status_ao,
                    'catatan_ao'    => $val->catatan_ao,
					'assign_to'    => $val->assign_to
                ],
                "ca" => [
                    'status_ca'     => $status_ca,
                    'catatan_ca'    => $val->so['ca']['catatan_ca']
                ],
                'pic'            => $val->pic['nama'],
                'area'           => $val->area['nama'],
                'cabang'         => $val->cabang['nama'],
                'asal_data'      => $val->so['asaldata']['nama'],
                'nama_marketing' => $val->so['nama_marketing'],
                'nama_debitur'   => $val->so['debt']['nama_lengkap'],
                'email'   => $val->so['debt']['email'],
                'plafon'         => $val->so['faspin']['plafon'],
                'tenor'          => $val->so['faspin']['tenor'],
                'tgl_transaksi' => Carbon::parse($val->created_at)->format('d-m-Y H:m:s')
            ];
        }

        $res = array_filter($data, function ($item) use ($ao_ca, $status) {
            if (stripos($item[$ao_ca]["status_{$ao_ca}"], $status) !== false) {
                return true;
            }
            return false;
        });

        array_walk_recursive($res, function (&$item, $key) {
            $item = null === $item ? '-' : $item;
        });

        //        echo json_encode($res);

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
	
	public function indexWaitPic($ao_ca, $status, Request $req)
    {
        $user_id = $req->auth->user_id;
        $pic = $req->pic; // From PIC middleware

        $arr = array();
        $i = 0;
        foreach ($pic as $val) {
            $arr[] = $val['id_area'];
            $i++;
        }

        $arrr = array();
        foreach ($pic as $val) {
            $arrr[] = $val['id_cabang'];
            $i++;
        }
        $arrrr = array();
        foreach ($pic as $val) {
            $arrrr[] = $val['jpic']['cakupan'];
            $i++;
        }
        //  dd($arr);
        $id_area   = $arr;
        $id_cabang = $arrr;
        // dd($id_cabang);
        $scope     = $arrrr;

        $query_dir = TransAO::with('so', 'pic', 'cabang')->where('status_ao', 1)->where('assign_to', $user_id)->orderBy('created_at', 'desc');

        $query = Helper::checkDir($scope, $query_dir, $id_area, $id_cabang);

        if (empty($query)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data di AO masih kosong'
            ], 404);
        }

        $data = array();
        foreach ($query as $key => $val) {

            if ($val->status_ao == 1) {
                $status_ao = 'recommend';
            } elseif ($val->status_ao == 2) {
                $status_ao = 'not recommend';
            } else {
                $status_ao = 'waiting';
            }

            if ($val->so['ca']['status_ca'] == 1) {
                $status_ca = 'recommend';
            } elseif ($val->so['ca']['status_ca'] == 2) {
                $status_ca = 'not recommend';
            } else {
                $status_ca = 'waiting';
            }

            $rev =  TransCA::select('revisi')->where('id_trans_so', $val->id_trans_so)->get();
            //merubah nilai null menjadi empty string
            $arr = array();
            foreach ($rev as $key => $value) {
                $arr['revisi'] = $value->revisi;
            }

            $data[] = [
                'id_trans_so'    => $val->id_trans_so == null ? null : (int) $val->id_trans_so,
                'nomor_so'       => $val->so['nomor_so'],
                'notes_so'       => $val->so['notes_so'],
                'no_rev'        => $rev,
                'nomor_ao'       => $val->nomor_ao,
                "ao" => [
                    'status_ao'     => $status_ao,
                    'catatan_ao'    => $val->catatan_ao,
                    'assign_to'    => $val->assign_to
                ],
                "ca" => [
                    'status_ca'     => $status_ca,
                    'catatan_ca'    => $val->so['ca']['catatan_ca']
                ],
                'pic'            => $val->pic['nama'],
                'area'           => $val->area['nama'],
                'cabang'         => $val->cabang['nama'],
                'asal_data'      => $val->so['asaldata']['nama'],
                'nama_marketing' => $val->so['nama_marketing'],
                'nama_debitur'   => $val->so['debt']['nama_lengkap'],
                'email'   => $val->so['debt']['email'],
                'plafon'         => $val->so['faspin']['plafon'],
                'tenor'          => $val->so['faspin']['tenor'],
                'tgl_transaksi' => Carbon::parse($val->created_at)->format('d-m-Y H:m:s')
            ];
        }

        $res = array_filter($data, function ($item) use ($ao_ca, $status) {
            if (stripos($item[$ao_ca]["status_{$ao_ca}"], $status) !== false) {
                return true;
            }
            return false;
        });

        array_walk_recursive($res, function (&$item, $key) {
            $item = null === $item ? '-' : $item;
        });

        //        echo json_encode($res);

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
	
	public function indexWaitPicCA($ao_ca, $status, Request $req)
    {
        $user_id = $req->auth->user_id;
        $pic = $req->pic; // From PIC middleware

        $arr = array();
        $i = 0;
        foreach ($pic as $val) {
            $arr[] = $val['id_area'];
            $i++;
        }

        $arrr = array();
        foreach ($pic as $val) {
            $arrr[] = $val['id_cabang'];
            $i++;
        }
        $arrrr = array();
        foreach ($pic as $val) {
            $arrrr[] = $val['jpic']['cakupan'];
            $i++;
        }
        //  dd($arr);
        $id_area   = $arr;
        $id_cabang = $arrr;
        // dd($id_cabang);
        $scope     = $arrrr;

        $query_dir = TransAO::with('so', 'pic', 'cabang')->where('status_ao', 1)->orderBy('created_at', 'desc');

        $query = Helper::checkDir($scope, $query_dir, $id_area, $id_cabang);

        if (empty($query)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data di AO masih kosong'
            ], 404);
        }

        $data = array();
        foreach ($query as $key => $val) {

            if ($val->status_ao == 1) {
                $status_ao = 'recommend';
            } elseif ($val->status_ao == 2) {
                $status_ao = 'not recommend';
            } else {
                $status_ao = 'waiting';
            }

            if ($val->so['ca']['status_ca'] == 1) {
                $status_ca = 'recommend';
            } elseif ($val->so['ca']['status_ca'] == 2) {
                $status_ca = 'not recommend';
            } else {
                $status_ca = 'waiting';
            }

            $rev =  TransCA::select('revisi')->where('id_trans_so', $val->id_trans_so)->get();
            //merubah nilai null menjadi empty string
            $arr = array();
            foreach ($rev as $key => $value) {
                $arr['revisi'] = $value->revisi;
            }

            $data[] = [
                'id_trans_so'    => $val->id_trans_so == null ? null : (int) $val->id_trans_so,
                'nomor_so'       => $val->so['nomor_so'],
                'notes_so'       => $val->so['notes_so'],
                'no_rev'        => $rev,
                'nomor_ao'       => $val->nomor_ao,
                "ao" => [
                    'status_ao'     => $status_ao,
                    'catatan_ao'    => $val->catatan_ao,
                    'assign_to'    => $val->assign_to
                ],
                "ca" => [
                    'status_ca'     => $status_ca,
                    'catatan_ca'    => $val->so['ca']['catatan_ca']
                ],
                'pic'            => $val->pic['nama'],
                'area'           => $val->area['nama'],
                'cabang'         => $val->cabang['nama'],
                'asal_data'      => $val->so['asaldata']['nama'],
                'nama_marketing' => $val->so['nama_marketing'],
                'nama_debitur'   => $val->so['debt']['nama_lengkap'],
                'email'   => $val->so['debt']['email'],
                'plafon'         => $val->so['faspin']['plafon'],
                'tenor'          => $val->so['faspin']['tenor'],
                'tgl_transaksi' => Carbon::parse($val->created_at)->format('d-m-Y H:m:s')
            ];
        }

        $res = array_filter($data, function ($item) use ($ao_ca, $status) {
            if (stripos($item[$ao_ca]["status_{$ao_ca}"], $status) !== false) {
                return true;
            }
            return false;
        });

        array_walk_recursive($res, function (&$item, $key) {
            $item = null === $item ? '-' : $item;
        });

        //        echo json_encode($res);

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

        $arr = array();
        $i = 0;
        foreach ($pic as $val) {
            $arr[] = $val['id_area'];
            $i++;
        }

        $arrr = array();
        foreach ($pic as $val) {
            $arrr[] = $val['id_cabang'];
            $i++;
        }
        $arrrr = array();
        foreach ($pic as $val) {
            $arrrr[] = $val['jpic']['cakupan'];
            $i++;
        }
        //  dd($arr);
        $id_area   = $arr;
        $id_cabang = $arrr;
        // dd($id_cabang);
        $scope     = $arrrr;

        $check_so = TransSO::where('id', $id)->where('status_das', 1)->where('status_hm', 1)->first();

        if (empty($check_so)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id ' . $id . ' belum ada di SO atau belum komplit saat pemeriksaaan DAS dan HM'
            ], 404);
        }

        $query_dir = TransAO::with('so', 'pic', 'cabang')->where('id_trans_so', $id);

        $vals = Helper::checkDir($scope, $query_dir, $id_area, $id_cabang);
        $val = $vals->first();
        // dd($val->so['debt']);
        $data_faspin = FasilitasPinjaman::select('id', 'jenis_pinjaman', 'tujuan_pinjaman', 'plafon', 'tenor', 'segmentasi_bpr')->where('id', $val->so['id_fasilitas_pinjaman'])->first();
        //  dd($data_faspin);
        if (empty($val)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id ' . $id . ' belum sampai ke AO'
            ], 404);
        }

        $idPen = Penjamin::whereIn('id',  explode(",", $val->so['id_penjamin']))->get();

        $penjamin = array();
        foreach ($idPen as $pen) {
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
                'pemasukan_penjamin' => $pen->pemasukan_penjamin,

                "pekerjaan" => [
                    "nama_pekerjaan"        => $pen->pekerjaan,
                    "posisi_pekerjaan"      => $pen->posisi_pekerjaan,
                    "nama_tempat_kerja"     => $pen->nama_tempat_kerja,
                    "jenis_pekerjaan"       => $pen->jenis_pekerjaan,
                    "tgl_mulai_kerja"       => Carbon::parse($pen->tgl_mulai_kerja)->format('Y-m-d'),
                    "lama_kerja"       => $pen->lama_kerja,
                    "no_telp_tempat_kerja"  => $pen->no_telp_tempat_kerja,
                    'alamat' => [
                        'alamat_singkat' => $pen->alamat_tempat_kerja,
                        'rt'             => $pen->rt_tempat_kerja,
                        'rw'             => $pen->rw_tempat_kerja,
                        'kelurahan' => [
                            'id'    => $pen->id_kel_tempat_kerja,
                            'nama'  => $pen->kel_kerja['nama']
                        ],
                        'kecamatan' => [
                            'id'    => $pen->id_kec_tempat_kerja,
                            'nama'  => $pen->kec_kerja['nama']
                        ],
                        'kabupaten' => [
                            'id'    => $pen->id_kab_tempat_kerja,
                            'nama'  => $pen->kab_kerja['nama'],
                        ],
                        'provinsi'  => [
                            'id'    => $pen->id_prov_tempat_kerja,
                            'nama'  => $pen->prov_kerja['nama'],
                        ],
                        'kode_pos'  => $pen->kel_kerja['kode_pos'] == null ? null : (int) $pen->kel_kerja['kode_pos']
                    ]
                ],

                'lampiran' => [
                    'lamp_ktp' => $pen->lamp_ktp,
                    'lamp_ktp_pasangan' => $pen->lamp_ktp_pasangan,
                    'lamp_kk' => $pen->lamp_kk,
                    'lamp_buku_nikah' => $pen->lamp_buku_nikah,
                    'foto_selfie_penjamin' => $pen->foto_selfie_penjamin,
                    'lampiran_npwp' => $pen->lampiran_npwp
                ]
            ];
        }

        $idTan = AgunanTanah::whereIn('id', explode(",", $val->id_agunan_tanah))->get();

        $idKen = AgunanKendaraan::whereIn('id', explode(",", $val->id_agunan_kendaraan))->get();

        $idPeTan = PemeriksaanAgunTan::whereIn('id', explode(",", $val->id_periksa_agunan_tanah))->get();

        $idPeKen = PemeriksaanAgunKen::whereIn('id', explode(",", $val->id_periksa_agunan_kendaraan))->get();

        if ($val->status_ao == 1) {
            $status_ao = 'recommend';
        } elseif ($val->status_ao == 2) {
            $status_ao = 'not recommend';
        } else {
            $status_ao = 'waiting';
        }

        if ($val->so['ca']['status_ca'] == 1) {
            $status_ca = 'recommend';
        } elseif ($val->so['ca']['status_ca'] == 2) {
            $status_ca = 'not recommend';
        } else {
            $status_ca = 'waiting';
        }

        $trans_debitur =  TransSo::where('id', $id)->first();
        $value = Debitur::with('prov_ktp', 'kab_ktp', 'kec_ktp', 'kel_ktp', 'prov_dom', 'kab_dom', 'kec_dom', 'kel_dom', 'prov_kerja', 'kab_kerja', 'kec_kerja', 'kel_kerja')
            ->where('id', $trans_debitur->id_calon_debitur)->first();

        if (empty($val)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Debitur Kosong'
            ], 404);
        }

        $nama_anak = explode(",", $value->nama_anak);
        $tgl_anak  = explode(",", $value->tgl_lahir_anak);

        for ($i = 0; $i < count($nama_anak); $i++) {
            $anak[] = array(
                'nama'      => $nama_anak[$i],
                'tgl_lahir' => empty($tgl_anak[$i]) ? null : Carbon::parse($tgl_anak[$i])->format("d-m-Y")
            );
        }

        //  dd($val);
        $data = array(
            'id_trans_so'    => $val->id_trans_so == null ? null : (int) $val->id_trans_so,
            'nomor_so'       => $val->so['nomor_so'],
            'norev_so'       => $val->so['norev_so'],
            'nama_so'        => $val->so['nama_so'],
            'nomor_ao'       => $val->nomor_ao,
            'status_ao'      => $status_ao,
            'status_ca'      => $status_ca,
		'record_ca' => $val->so['ca']['record_ca'] == null ? null : explode(";", $val->so['ca']['record_ca']),
            'nama_marketing' => $val->so['nama_marketing'],
            'notes_so' => $val->so['notes_so'],
			'flg_cancel_debitur' => $val->so['flg_cancel_debitur'],
            'pic'  => [
                'id'         => $val->id_pic == null ? null : (int) $val->id_pic,
                'nama'       => $val->pic['nama'],
            ],
            'area'   => [
                'id'      => $val->id_area == null ? null : (int) $val->id_area,
                'nama'    => $val->area['nama']
            ],
            'cabang' => [
                'id'      => $val->id_cabang == null ? null : (int) $val->id_cabang,
                'nama'    => $val->cabang['nama'],
            ],

            'asaldata'  => [
                'id'   => $val->so['id_asal_data'] == null ? null : $val->so['id_asal_data'],
                'nama' => $val->so['asaldata']['nama']
            ],
            'fasilitas_pinjaman'  =>
            $val->so['id_fasilitas_pinjaman'] == null ? null : $data_faspin,

            'data_debitur' => [
                'id'                    => $val->so['id_calon_debitur'],
                'nama_lengkap'          => $val->so['debt']['nama_lengkap'],
                'gelar_keagamaan'       => $val->so['debt']['gelar_keagamaan'],
                'gelar_pendidikan'      => $val->so['debt']['gelar_pendidikan'],
                'jenis_kelamin'         => $val->so['debt']['jenis_kelamin'],
                'status_nikah'          => $val->so['debt']['status_nikah'],
                'ibu_kandung'           => $val->so['debt']['ibu_kandung'],
                'tinggi_badan'          => $val->so['debt']['tinggi_badan'],
                'berat_badan'           => $val->so['debt']['berat_badan'],
                'no_ktp'                => $val->so['debt']['no_ktp'],
                'no_ktp_kk'             => $val->so['debt']['no_ktp_kk'],
                'no_kk'                 => $val->so['debt']['no_kk'],
                'no_npwp'               => $val->so['debt']['no_npwp'],
                'tempat_lahir'          => $val->so['debt']['tempat_lahir'],
                'tgl_lahir'             => Carbon::parse($val->so['debt']['tgl_lahir'])->format('Y-m-d'),
                'umur'          => $val->so['debt']['umur'],
                'agama'                 => $val->so['debt']['agama'],
                'anak'             => Anak::select('nama_anak AS nama', 'tgl_lahir_anak AS tgl_lahir')->where('nasabah_id', $id)->get(),
                'alamat_ktp' => [
                    'alamat_singkat' => $val->so['debt']['alamat_ktp'],
                    'rt'     => $val->so['debt']['rt_ktp'] == null ? null : (int) $val->so['debt']['rt_ktp'],
                    'rw'     => $val->so['debt']['rw_ktp'] == null ? null : (int) $val->so['debt']['rw_ktp'],
                    'kelurahan' => [
                        'id'    => $val->so['debt']['id_kel_ktp'] == null ? null : (int) $val->so['debt']['id_kel_ktp'],
                        'nama'  => $val->so['debt']['kel_ktp']['nama']
                    ],
                    'kecamatan' => [
                        'id'    => $val->so['debt']['id_kec_ktp'] == null ? null : (int) $val->so['debt']['id_kec_ktp'],
                        'nama'  => $val->so['debt']['kec_ktp']['nama']
                    ],
                    'kabupaten' => [
                        'id'    => $val->so['debt']['id_kab_ktp'] == null ? null : (int) $val->so['debt']['id_kab_ktp'],
                        'nama'  => $val->so['debt']['kab_ktp']['nama'],
                    ],
                    'provinsi'  => [
                        'id'   => $val->so['debt']['id_prov_ktp'] == null ? null : (int) $val->so['debt']['id_prov_ktp'],
                        'nama' => $val->so['debt']['prov_ktp']['nama'],
                    ],
                    'kode_pos' => $val->so['debt']['kel_ktp']['kode_pos'] == null ? null : (int) $val->so['debt']['kel_ktp']['kode_pos']
                ],
                'alamat_domisili' => [
                    'alamat_singkat' => $val->so['debt']['alamat_domisili'],
                    'rt'             => $val->so['debt']['rt_domisili'] == null ? null : (int) $val->so['debt']['rt_domisili'],
                    'rw'             => $val->so['debt']['rw_domisili'] == null ? null : (int) $val->so['debt']['rw_domisili'],
                    'kelurahan' => [
                        'id'    => $val->so['debt']['id_kel_domisili'] == null ? null : (int) $val->so['debt']['id_kel_domisili'],
                        'nama'  => $val->so['debt']['kel_dom']['nama']
                    ],
                    'kecamatan' => [
                        'id'    => $val->so['debt']['id_kec_domisili'] == null ? null : (int) $val->so['debt']['id_kec_domisili'],
                        'nama'  => $val->so['debt']['kec_dom']['nama']
                    ],
                    'kabupaten' => [
                        'id'    => $val->so['debt']['id_kab_domisili'] == null ? null : (int) $val->so['debt']['id_kab_domisili'],
                        'nama'  => $val->so['debt']['kab_dom']['nama'],
                    ],
                    'provinsi'  => [
                        'id'   => $val->so['debt']['id_prov_domisili'] == null ? null : (int) $val->so['debt']['id_prov_domisili'],
                        'nama' => $val->so['debt']['prov_dom']['nama'],
                    ],
                    'kode_pos' => $val->so['debt']['kel_dom']['kode_pos'] == null ? null : (int) $val->so['debt']['kel_dom']['kode_pos']
                ],
                "pekerjaan" => [
                    "nama_pekerjaan"        => $val->so['debt']['pekerjaan'],
                    "posisi_pekerjaan"      => $val->so['debt']['posisi_pekerjaan'],
                    "nama_tempat_kerja"     => $val->so['debt']['nama_tempat_kerja'],
                    "jenis_pekerjaan"       => $val->so['debt']['jenis_pekerjaan'],
                    "tgl_mulai_kerja"       => Carbon::parse($val->so['debt']['tgl_mulai_kerja'])->format('Y-m-d'),
                    "lama_kerja"       => $val->so['debt']['lama_kerja'],
                    "no_telp_tempat_kerja"  => $val->so['debt']['no_telp_tempat_kerja'],
                    'alamat' => [
                        'alamat_singkat' => $val->so['debt']['alamat_tempat_kerja'],
                        'rt'             => $val->so['debt']['rt_tempat_kerja'] == null ? null : (int) $val->so['debt']['rt_tempat_kerja'],
                        'rw'             => $val->so['debt']['rw_tempat_kerja'] == null ? null : (int) $val->so['debt']['rw_tempat_kerja'],
                        'kelurahan' => [
                            'id'    => $val->so['debt']['id_kel_tempat_kerja'] == null ? null : (int) $val->so['debt']['id_kel_tempat_kerja'],
                            'nama'  => $val->so['debt']['kel_kerja']['nama']
                        ],
                        'kecamatan' => [
                            'id'    => $val->so['debt']['id_kec_tempat_kerja'] == null ? null : (int) $val->so['debt']['id_kec_tempat_kerja'],
                            'nama'  => $val->so['debt']['kec_kerja']['nama']
                        ],
                        'kabupaten' => [
                            'id'    => $val->so['debt']['id_kab_tempat_kerja'] == null ? null : (int) $val->so['debt']['id_kab_tempat_kerja'],
                            'nama'  => $val->so['debt']['kab_kerja']['nama'],
                        ],
                        'provinsi'  => [
                            'id'    => $val->so['debt']['id_prov_tempat_kerja'] == null ? null : (int) $val->so['debt']['id_prov_tempat_kerja'],
                            'nama'  => $val->so['debt']['prov_kerja']['nama'],
                        ],
                        'kode_pos'  => $val->so['debt']['kel_kerja']['kode_pos'] == null ? null : (int) $val->so['debt']['kel_kerja']['kode_pos']
                    ]
                ],
                'pendidikan_terakhir'   => $val->so['debt']['pendidikan_terakhir'],
                'jumlah_tanggungan'     => $val->so['debt']['jumlah_tanggungan'],
                'no_telp'               => $val->so['debt']['no_telp'],
                'no_hp'                 => $val->so['debt']['no_hp'],
                'alamat_surat'          => $val->so['debt']['alamat_surat'],
                'email'          => $val->so['debt']['email'],
'waktu_menghubungi'          => $val->so['debt']['waktu_menghubungi'],
                'lampiran' => [
                    'lamp_ktp'              => $val->so['debt']['lamp_ktp'],
                    'lamp_kk'               => $val->so['debt']['lamp_kk'],
                    'lamp_buku_tabungan'    => $val->so['debt']['lamp_buku_tabungan'],
                    'lamp_sertifikat'       => $val->so['debt']['lamp_sertifikat'],
                    'lamp_sttp_pbb'         => $val->so['debt']['lamp_sttp_pbb'],
                    'lamp_imb'              => $val->so['debt']['lamp_imb'],
                    'lamp_surat_cerai'      => $val->so['debt']['lamp_surat_cerai'],
                    'lamp_skk'              => $val->so['debt']['lamp_skk'],
                    'lamp_sku'              => $val->so['debt']['lamp_sku'],
                    'lamp_slip_gaji'        => $val->so['debt']['lamp_slip_gaji'],
                    'lamp_foto_usaha'       => $val->so['debt']['lamp_foto_usaha'],
                    'lamp_tempat_tinggal'   => $val->so['debt']['lamp_tempat_tinggal'],
                    'foto_agunan_rumah'     => $val->so['debt']['foto_agunan_rumah'],
                    'foto_pembukuan_usaha'  => $val->so['debt']['foto_pembukuan_usaha'],
                    'foto_cadeb'  => $val->so['debt']['foto_cadeb'],
                    'lamp_npwp'  => $val->so['debt']['lamp_npwp']
                ]
            ],

            'data_pasangan' => [
                'id'                    => $val->so['id_pasangan'],
                'nama_lengkap'          => $val->so['pas']['nama_lengkap'],
                'nama_ibu_kandung'      => $val->so['pas']['nama_ibu_kandung'],
                'gelar_keagamaan'       => $val->so['pas']['gelar_keagamaan'],
                'gelar_pendidikan'      => $val->so['pas']['gelar_pendidikan'],
                'jenis_kelamin'         => $val->so['pas']['jenis_kelamin'],
                'no_ktp'                => $val->so['pas']['no_ktp'],
                'no_ktp_kk'             => $val->so['pas']['no_ktp_kk'],
                'no_npwp'               => $val->so['pas']['no_npwp'],
                'tempat_lahir'          => $val->so['pas']['tempat_lahir'],
                'tgl_lahir'             => Carbon::parse($val->so['pas']['tgl_lahir'])->format('d-m-Y'),
                'alamat_ktp'            => $val->so['pas']['alamat_ktp'],
                'no_telp'               => $val->so['pas']['no_telp'],


                "pekerjaan" => [
                    "nama_pekerjaan"        => $val->so['pas']['pekerjaan'],
                    "posisi_pekerjaan"      => $val->so['pas']['posisi_pekerjaan'],
                    "nama_tempat_kerja"     => $val->so['pas']['nama_tempat_kerja'],
                    "jenis_pekerjaan"       => $val->so['pas']['jenis_pekerjaan'],
                    "tgl_mulai_kerja"       => Carbon::parse($val->so['pas']['tgl_mulai_kerja'])->format('d-m-Y'),
                    "no_telp_tempat_kerja"  => $val->so['pas']['no_telp_tempat_kerja'],
                    'alamat' => [
                        'alamat_singkat' => $val->so['pas']['alamat_tempat_kerja'],
                        'rt'             => $val->so['pas']['rt_tempat_kerja'] == null ? null : (int) $val->so['pas']['rt_tempat_kerja'],
                        'rw'             => $val->so['pas']['rw_tempat_kerja'] == null ? null : (int) $val->so['pas']['rw_tempat_kerja'],
                        'kelurahan' => [
                            'id'    => $val->so['pas']['id_kel_tempat_kerja'] == null ? null : (int) $val->so['pas']['id_kel_tempat_kerja'],
                            'nama'  => $val->so['pas']['kel_kerja']['nama']
                        ],
                        'kecamatan' => [
                            'id'    => $val->so['pas']['id_kec_tempat_kerja'] == null ? null : (int) $val->so['pas']['id_kec_tempat_kerja'],
                            'nama'  => $val->so['pas']['kec_kerja']['nama']
                        ],
                        'kabupaten' => [
                            'id'    => $val->so['pas']['id_kab_tempat_kerja'] == null ? null : (int) $val->so['pas']['id_kab_tempat_kerja'],
                            'nama'  => $val->so['pas']['kab_kerja']['nama'],
                        ],
                        'provinsi'  => [
                            'id'    => $val->so['pas']['id_prov_tempat_kerja'] == null ? null : (int) $val->so['pas']['id_prov_tempat_kerja'],
                            'nama'  => $val->so['pas']['prov_kerja']['nama'],
                        ],
                        'kode_pos'  => $val->so['pas']['kel_kerja']['kode_pos'] == null ? null : (int) $val->so['pas']['kel_kerja']['kode_pos']
                    ]
                ],
                'lampiran' => [
                    'lamp_ktp'        => $val->so['pas']['lamp_ktp'],
                    'lamp_buku_nikah' => $val->so['pas']['lamp_buku_nikah'],
                    'foto_pasangan'               => $val->so['pas']['foto_pasangan'],
                    'lampiran_npwp'               => $val->so['pas']['lampiran_npwp'],
                ]
            ],

            'data_penjamin' => $penjamin,

            'data_agunan' => [
                'agunan_tanah'     => $idTan,
                'agunan_kendaraan' => $idKen
            ],
            'pemeriksaan' => [
                'agunan_tanah' => $idPeTan,
                'agunan_kendaraan' => $idPeKen
            ],
            'kapasitas_bulanan' => $val->kapbul,
            'pendapatan_usaha'  => $val->usaha,
            'rekomendasi_ao'    => $val->recom_ao,
            'verifikasi'        => $val->verif,
            'validasi'          => $val->valid,
            'lampiran_ao'       => [
                'lamp_ideb'             => empty($val->so['lamp_ideb']) ? null : explode(";", $val->so['lamp_ideb']),
                'lamp_pefindo'          => empty($val->so['lamp_pefindo']) ? null : explode(";", $val->so['lamp_pefindo']),
                'form_persetujuan_ideb' => $val->form_persetujuan_ideb
            ],
            'tgl_transaksi'     => $val->created_at,
			'status_return' => $val->status_return,
			'note_return' => $val->note_return,
			'tgl_pending' => $val->tgl_pending,
			'verifikasi_hm' => $val->verifikasi_hm
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

    public function updateccResult($id, Request $request, BlankRequest $req)
    {
        $pic     = $request->pic; // From PIC middleware
        $user_id = $request->auth->user_id;
        $trans = TransCA::where('id_trans_so', $id)->first();

        if (empty($trans)) {
            return response()->json([
                "code" => 404,
                "message" => "Data Transaksi Kosong"
            ]);
        }
        $data = array("cc_result" => empty($request->input('cc_result')) ? 0 : $request->input('cc_result'));

        $cc = null;
        if ($data['cc_result'] == 1) {
            $cc = "02001";
        } elseif ($data['cc_result'] == 2) {
            $cc = "02002";
        } elseif ($data['cc_result'] == 3) {
            $cc = "02003";
        } elseif ($data['cc_result'] == 4) {
            $cc = "02004";
        } elseif ($data['cc_result'] == 5) {
            $cc = "02005";
        }


        #1
        $scor_cc = DB::connection('simar')->table('master_creditscoring')->select('id_parameter AS parameter', 'id_detail_params AS detail', 'point', 'bobot')->where('id_detail_params', $cc)->first();
        $merge = array($scor_cc);
        //dd($scor_cc);

        $arr_s = array();
        foreach ($merge as $key => $val) {
            if (!empty($val)) {
                $arr_s[$key]['id_aplikasi'] = $id;
                $arr_s[$key]['parameter'] = $val->parameter;
                $arr_s[$key]['detail'] = $val->detail;
                $arr_s[$key]['point'] = $val->point;
                $arr_s[$key]['bobot'] = $val->bobot;
            }
        }
        
        $scor_params = master_nilai::insert($arr_s);

 $get_trans = DB::connection('web')->table('view_transaksi_cs')->where('id', $id)->first();        
        $call_sp = DB::connection('simar')->select("CALL simar.`sp_hitung_hasil_scoring`(?,?)", array($get_trans->id, Carbon::parse($get_trans->tgl_transaksi)->format('Y-m-d')));
        $update = TransCA::where('id_trans_so', $id)->update($data);

        return response()->json([
            "code" => 200,
            "message" => "Success",
            "data" => $data
        ]);
    }

    public function update($id, Request $request, BlankRequest $req)
    {
        $pic     = $request->pic; // From PIC middleware
        $user_id = $request->auth->user_id;

        $mj = array();
        $i = 0;
        foreach ($pic as $val) {
            $mj[] = $val['id_mj_pic'];
            $i++;
        }
        $id_pic = array();
        $i = 0;
        foreach ($pic as $val) {
            $id_pic[] = $val['id'];
            $i++;
        }
        $arrr = array();
        foreach ($pic as $val) {
            $arrr[] = $val['id_cabang'];
            $i++;
        }
        $area = array();
        $i = 0;
        foreach ($pic as $val) {
            $area[] = $val['id_area'];
            $i++;
        }
        $nama = array();
        $i = 0;
        foreach ($pic as $val) {
            $nama[] = $val['nama'];
            $i++;
        }
        $countCA = TransCA::latest('id', 'nomor_ca')->first();

        if (!$countCA) {
            $lastNumb = 1;
        } else {
            $no = $countCA->nomor_ca;

            $arr = explode("-", $no, 5);

            $lastNumb = str_replace(" [revisi]", "", $arr[4]) + 1;
        }

        //Data Transaksi SO
        $nows  = Carbon::now();
        $year  = $nows->year;
        $month = $nows->month;

        $JPIC   = JPIC::whereIn('id', $mj)->first();

        //  ID-Cabang - AO / CA / SO - Bulan - Tahun - NO. Urut
        $nomor_ca = $arrr[0] . '-' . $JPIC->nama_jenis . '-' . $month . '-' . $year . '-' . $lastNumb;

        $check_so = TransSO::where('id', $id)->where('status_das', 1)->where('status_hm', 1)->first();

        if (!$check_so) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id ' . $id . ' belum ada di SO atau belum komplit saat pemeriksaan DAS dan HM'
            ], 404);
        }

        $check_ao = TransAO::where('id_trans_so', $id)->first();

        if (!$check_ao) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id ' . $id . ' belum sampai ke AO'
            ], 404);
        }

        $check_ca = TransCA::where('id_trans_so', $id)->first();

        if ($check_ca != null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id ' . $id . ' sudah ada di CA'
            ], 404);
        }
        $cab = TransSO::where('id', $id)->first();

 $check_ktp_deb = Debitur::join('trans_so', 'trans_so.id_calon_debitur', 'calon_debitur.id')->where('trans_so.id', $id)->first();

           if ($file = $req->file('record_ca')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur/record_ca';
            $name = Carbon::now() . '-' . 'record_ca' . '-' . $check_ktp_deb->id_cabang;
            //. '-' . Carbon::now();
            $check = 'null';

            $arrayPath = array();

            $exAudio = $file->getClientOriginalExtension();

            if ($exAudio != 'wav' && $exAudio != 'mp3') {
                return response()->json([
                    "code"    => 422,
                    "status"  => "not valid request",
                    "message" => "file record ca harus berupa format wav / mp3"
                ], 422);
            }
            //  dd($name);
            // Check Directory
            if (!File::isDirectory($path)) {
                File::makeDirectory($path, 0777, true, true);
            }

            // Delete File is Exists
            if (!empty($check)) {
                File::delete($check);
            }

            $name = $file->getClientOriginalName();

            // dd($path . '/' . $name);

            // Save Image to Directory
            $file->move($path, $name);
            $arrayPath = $path . '/' . $name;


            $record_ca = $arrayPath;
        } else {
            $record_ca = null;
        }

        $transCA = array(
            'nomor_ca'    => $nomor_ca,
            'user_id'     => $user_id,
            'id_trans_so' => $check_so->id,
            'id_pic'      => $id_pic[0],
            'id_area'     => $cab->id_area,
            'id_cabang'   => $cab->id_cabang,
            'catatan_ca'  => $req->input('catatan_ca'),
            'cc_result' => $req->input('cc_result'),
            'status_ca'   => empty($req->input('status_ca')) ? 1 : $req->input('status_ca'),
'record_ca' => $record_ca
        );

        // Pendapatan Usaha Cadebt
        $dataPendapatanUsaha = array(
            'pemasukan_tunai'      => empty($req->input('pemasukan_tunai'))     ? 0 : $req->input('pemasukan_tunai'),
            'pemasukan_kredit'     => empty($req->input('pemasukan_kredit'))    ? 0 : $req->input('pemasukan_kredit'),
            'biaya_sewa'           => empty($req->input('biaya_sewa'))          ? 0 : $req->input('biaya_sewa'),
            'biaya_gaji_pegawai'   => empty($req->input('biaya_gaji_pegawai'))  ? 0 : $req->input('biaya_gaji_pegawai'),
            'biaya_belanja_brg'    => empty($req->input('biaya_belanja_brg'))   ? 0 : $req->input('biaya_belanja_brg'),
            'biaya_telp_listr_air' => empty($req->input('biaya_telp_listr_air')) ? 0 : $req->input('biaya_telp_listr_air'),
            'biaya_sampah_kemanan' => empty($req->input('biaya_sampah_kemanan')) ? 0 : $req->input('biaya_sampah_kemanan'),
            'biaya_kirim_barang'   => empty($req->input('biaya_kirim_barang'))  ? 0 : $req->input('biaya_kirim_barang'),
            'biaya_hutang_dagang'  => empty($req->input('biaya_hutang_dagang')) ? 0 : $req->input('biaya_hutang_dagang'),
            'biaya_angsuran'       => empty($req->input('biaya_angsuran'))      ? 0 : $req->input('biaya_angsuran'),
            'biaya_lain_lain'      => empty($req->input('biaya_lain_lain'))     ? 0 : $req->input('biaya_lain_lain')
        );

        $totalPendapatan = array(
            'total_pemasukan'    => $ttl1 = array_sum(array_slice($dataPendapatanUsaha, 0, 2)),
            'total_pengeluaran'  => $ttl2 = array_sum(array_slice($dataPendapatanUsaha, 2)),
            'laba_usaha'         => $ttl1 - $ttl2
        );

        $Pendapatan = array_merge($dataPendapatanUsaha, $totalPendapatan, array('ao_ca' => 'CA'));

        // Start Kapasitas Bulanan
        $inputKapBul = array(

            'pemasukan_cadebt'
            => empty($req->input('pemasukan_debitur'))    ? 0 : $req->input('pemasukan_debitur'),

            'pemasukan_pasangan'
            => empty($req->input('pemasukan_pasangan'))   ? 0 : $req->input('pemasukan_pasangan'),

            'pemasukan_penjamin'
            => empty($req->input('pemasukan_penjamin'))   ? 0 : $req->input('pemasukan_penjamin'),

            'biaya_rumah_tangga'
            => empty($req->input('biaya_rumah_tangga'))   ? 0 : $req->input('biaya_rumah_tangga'),

            'biaya_transport'
            => empty($req->input('biaya_transport'))      ? 0 : $req->input('biaya_transport'),

            'biaya_pendidikan'
            => empty($req->input('biaya_pendidikan'))     ? 0 : $req->input('biaya_pendidikan'),

            'telp_listr_air'
            => empty($req->input('telp_listr_air'))       ? 0 : $req->input('telp_listr_air'),

            'angsuran'
            => empty($req->input('angsuran'))             ? 0 : $req->input('angsuran'),

            'biaya_lain'
            => empty($req->input('biaya_lain'))           ? 0 : $req->input('biaya_lain'),
        );

        $total_KapBul = array(
            'total_pemasukan'    => $ttl1 = array_sum(array_slice($inputKapBul, 0, 3)),
            'total_pengeluaran'  => $ttl2 = array_sum(array_slice($inputKapBul, 3)),
            'penghasilan_bersih' => $ttl1 - $ttl2
        );

        // Ceiling Recomendasi Pinjaman
        $rekomPinjaman = array(
            'penyimpangan_struktur'
            => empty($req->input('penyimpangan_struktur'))
                ? 0 : $req->input('penyimpangan_struktur'),

            'penyimpangan_dokumen'
            => empty($req->input('penyimpangan_dokumen'))
                ? 0 : $req->input('penyimpangan_dokumen'),

            'recom_nilai_pinjaman'
            => empty($req->input('recom_nilai_pinjaman'))
                ? 0 : $req->input('recom_nilai_pinjaman'),

            'recom_tenor'
            => empty($req->input('recom_tenor'))
                ? 0 : $req->input('recom_tenor'),

            'recom_angsuran'
            => empty($req->input('recom_angsuran'))
                ? 0 : $req->input('recom_angsuran'),

            'recom_produk_kredit'
            => empty($req->input('recom_produk_kredit'))
                ? 0 : $req->input('recom_produk_kredit'),

            'note_recom'
            => empty($req->input('note_recom'))
                ? null : $req->input('note_recom'),

            'bunga_pinjaman'
            => empty($req->input('bunga_pinjaman'))
                ? 0 : $req->input('bunga_pinjaman'),

            'nama_ca'
            => empty($req->input('nama_ca'))
                ? $nama[0] : $req->input('nama_ca')
        );

        // Rekomendasi Angsuran pada table rrekomendasi_pinjaman
        $plafonCA = $rekomPinjaman['recom_nilai_pinjaman'] == null ? 0 : $rekomPinjaman['recom_nilai_pinjaman'];
        $tenorCA  = $rekomPinjaman['recom_tenor']          == null ? 0 : $rekomPinjaman['recom_tenor'];
        $bunga    = $rekomPinjaman['bunga_pinjaman']       == null ? 0 : ($rekomPinjaman['bunga_pinjaman']);

        $rekomen_pendapatan  = $total_KapBul['total_pemasukan']   == null ? 0 : $total_KapBul['total_pemasukan'];
        $rekomen_pengeluaran = $total_KapBul['total_pengeluaran'] == null ? 0 : $total_KapBul['total_pengeluaran'];
        $rekomen_angsuran    = $inputKapBul['angsuran']           == null ? 0 : $inputKapBul['angsuran'];

        if ($plafonCA == 0 && $tenorCA == 0 && $bunga == 0) {
            $recom_angs = 0;
        } else {
            $recom_angs = Helper::recom_angs($plafonCA, $tenorCA, $bunga);
        }

        $rekomen_pend_bersih = $rekomen_pendapatan - $rekomen_pengeluaran;

        $disposable_income   = $rekomen_pend_bersih - $recom_angs;

        $kapBul = array_merge($inputKapBul, $total_KapBul, array('disposable_income'  => $disposable_income, 'ao_ca' => 'CA'));
        // End Kapasitas Bulanan

        // Check Pemeriksaan
        $id_pe_ta = $check_ao->id_periksa_agunan_tanah;

        if (empty($id_pe_ta)) {
            $PeriksaTanah = null;
        }

        // $id_pe_ke = $check_ao->id_periksa_agunan_kendaraan;

        // if ($id_pe_ke == null) {
        //     $PeriksaKenda = null;
        // }

        $PeriksaTanah = PemeriksaanAgunTan::select('nilai_taksasi_agunan')->whereIn('id', explode(",", $id_pe_ta))->get()->toArray();

        if (empty($PeriksaTanah)) {
            $sumTaksasiTan = 0;
        } else {
            $sumTaksasiTan = array_sum(array_column($PeriksaTanah, 'nilai_taksasi_agunan')); //array_sum($PeriksaTanah);
        }

        // $PeriksaKenda = PemeriksaanAgunTan::select('nilai_taksasi_agunan')->whereIn('id', explode(",", $id_pe_ta))->get()->toArray();

        // if ($PeriksaKenda == []) {
        //     $sumTaksasiKen = 0;
        // }else{
        //     $sumTaksasiKen = array_sum(array_column($PeriksaTanah,'nilai_taksasi_agunan')); //array_sum($PeriksaTanah);
        // }
        // $sumAllTaksasi = $sumTaksasiTan + $sumTaksasiKen; // Semua Nilai Taksasi dari semua agunan
        $sumAllTaksasi = $sumTaksasiTan; // Semua Nilai Taksasi dari semua agunan


        // $recom_ltv   = Helper::recom_ltv($plafonCA, $sumAllTaksasi);
        // $recom_idir  = Helper::recom_idir($recom_angs, $rekomen_pendapatan, $rekomen_pengeluaran);
        // $recom_dsr   = Helper::recom_dsr($recom_angs, $rekomen_pendapatan, $rekomen_angsuran);
        // $recom_hasil = Helper::recom_hasil($recom_dsr, $recom_ltv, $recom_idir);

        // Data Ringkasan Analisa CA
        $dataRingkasan = array(
            'kuantitatif_ttl_pendapatan'    => $req->input('kuantitatif_ttl_pendapatan'),
            'kuantitatif_ttl_pengeluaran'   => $req->input('kuantitatif_ttl_pengeluaran'),
            'kuantitatif_pendapatan_bersih' => $req->input('kuantitatif_pendapatan'),
            'kuantitatif_angsuran'          => $req->input('kuantitatif_angsuran'),
            // 'kuantitatif_ttl_pendapatan'    => $rekomen_pendapatan,
            // 'kuantitatif_ttl_pengeluaran'   => $rekomen_pengeluaran,
            // 'kuantitatif_pendapatan_bersih' => $rekomen_pend_bersih,
            // 'kuantitatif_angsuran'          => $recom_angs,
            'kuantitatif_ltv'               => $req->input('kuantitatif_ltv'),
            'kuantitatif_dsr'               => $req->input('kuantitatif_dsr'),
            'kuantitatif_idir'              => $req->input('kuantitatif_idir'),
            'kuantitatif_hasil'             => $req->input('kuantitatif_hasil'),


            'kualitatif_analisa'
            => empty($req->input('kualitatif_analisa'))
                ? null : $req->input('kualitatif_analisa'),

            'kualitatif_strenght'
            => empty($req->input('kualitatif_strenght'))
                ? null : $req->input('kualitatif_strenght'),

            'kualitatif_weakness'
            => empty($req->input('kualitatif_weakness'))
                ? null : $req->input('kualitatif_weakness'),

            'kualitatif_opportunity'
            => empty($req->input('kualitatif_opportunity'))
                ? null : $req->input('kualitatif_opportunity'),

            'kualitatif_threatness'
            => empty($req->input('kualitatif_threatness'))
                ? null : $req->input('kualitatif_threatness'),
        );

        // Mutasi Bank
        if (!empty($req->input('no_rekening_mutasi'))) {

            for ($i = 0; $i < count($req->input('no_rekening_mutasi')); $i++) {

                $dataMuBa[] = array(
                    'urutan_mutasi'
                    => empty($req->input('urutan_mutasi')[$i])
                        ? null : $req->urutan_mutasi[$i],

                    'nama_bank'
                    => empty($req->input('nama_bank_mutasi')[$i])
                        ? null : $req->nama_bank_mutasi[$i],

                    'no_rekening'
                    => empty($req->input('no_rekening_mutasi')[$i])
                        ? null : $req->no_rekening_mutasi[$i],

                    'nama_pemilik'
                    => empty($req->input('nama_pemilik_mutasi')[$i])
                        ? null : $req->nama_pemilik_mutasi[$i],

                    'periode'
                    => empty($req->input('periode_mutasi')[$i])
                        ? null : implode(";", $req->periode_mutasi[$i]),

                    'frek_debet'
                    => empty($req->input('frek_debet_mutasi')[$i])
                        ? null : implode(";", $req->frek_debet_mutasi[$i]),

                    'nominal_debet'
                    => empty($req->input('nominal_debet_mutasi')[$i])
                        ? null : implode(";", $req->nominal_debet_mutasi[$i]),

                    'frek_kredit'
                    => empty($req->input('frek_kredit_mutasi')[$i])
                        ? null : implode(";", $req->frek_kredit_mutasi[$i]),

                    'nominal_kredit'
                    => empty($req->input('nominal_kredit_mutasi')[$i])
                        ? null : implode(";", $req->nominal_kredit_mutasi[$i]),

                    'saldo'
                    => empty($req->input('saldo_mutasi')[$i])
                        ? null : implode(";", $req->saldo_mutasi[$i])
                );
            }
        }

        if (!empty($req->input('nama_bank_acc'))) {
            for ($i = 0; $i < count($req->input('nama_bank_acc')); $i++) {
                $dataACC[] = array(
                    'nama_bank'       => empty($req->input('nama_bank_acc')[$i])       ? "" : $req->nama_bank_acc[$i],
                    'plafon'          => empty($req->input('plafon_acc')[$i])          ? 0 : $req->plafon_acc[$i],
                    'baki_debet'      => empty($req->input('baki_debet_acc')[$i])      ? 0 : $req->baki_debet_acc[$i],
                    'angsuran'        => empty($req->input('angsuran_acc')[$i])        ? 0 : $req->angsuran_acc[$i],
                    'collectabilitas' => empty($req->input('collectabilitas_acc')[$i]) ? 0 : $req->collectabilitas_acc[$i],
                    'jenis_kredit'    => empty($req->input('jenis_kredit_acc')[$i])    ? "" : $req->jenis_kredit_acc[$i]
                );
            }
        }

        $dataTabUang = array(

            'no_rekening'
            => empty($req->input('no_rekening'))
                ? null : $req->input('no_rekening'),

            'nama_bank'
            => empty($req->input('nama_bank'))
                ? null : $req->input('nama_bank'),

            'tujuan_pembukaan_rek'
            => empty($req->input('tujuan_pembukaan_rek'))
                ? null : $req->input('tujuan_pembukaan_rek'),

            'penghasilan_per_tahun'
            => empty($req->input('penghasilan_per_tahun'))
                ? ($rekomen_pendapatan == 0 ? 0 : $rekomen_pendapatan * 12) : $req->input('penghasilan_per_tahun'),

            'sumber_penghasilan'
            => empty($req->input('sumber_penghasilan'))
                ? null : $req->input('sumber_penghasilan'),

            'pemasukan_per_bulan'
            => empty($req->input('pemasukan_per_bulan'))
                ? null : $req->input('pemasukan_per_bulan'),

            'frek_trans_pemasukan'
            => empty($req->input('frek_trans_pemasukan'))
                ? null : $req->input('frek_trans_pemasukan'),

            'pengeluaran_per_bulan'
            => empty($req->input('pengeluaran_per_bulan'))
                ? null : $req->input('pengeluaran_per_bulan'),

            'frek_trans_pengeluaran'
            => empty($req->input('frek_trans_pengeluaran'))
                ? null : $req->input('frek_trans_pengeluaran'),

            'sumber_dana_setoran'
            => empty($req->input('sumber_dana_setoran'))
                ? null : $req->input('sumber_dana_setoran'),

            'tujuan_pengeluaran_dana'
            => empty($req->input('tujuan_pengeluaran_dana'))
                ? null : $req->input('tujuan_pengeluaran_dana')
        );

        // Rekomendasi CA
        $recomCA = array(
            'produk'                => $req->input('produk'),
            'plafon_kredit'         => $req->input('plafon_kredit'),
            'jangka_waktu'          => $req->input('jangka_waktu'),
            'suku_bunga'            => $req->input('suku_bunga'),
            'pembayaran_bunga'      => $req->input('pembayaran_bunga'),
            'akad_kredit'           => $req->input('akad_kredit'),
            'ikatan_agunan'         => $req->input('ikatan_agunan'),
            'biaya_provisi'         => $req->input('biaya_provisi'),
            'biaya_administrasi'    => $req->input('biaya_administrasi'),
            'biaya_credit_checking' => $req->input('biaya_credit_checking'),
            'biaya_asuransi_jiwa'   => $req->input('biaya_asuransi_jiwa'),
            'biaya_asuransi_jaminan_kebakaran' => $req->input('biaya_asuransi_jaminan_kebakaran'),
            'biaya_asuransi_jaminan_kendaraan' => $req->input('biaya_asuransi_jaminan_kendaraan'),
            'notaris'               => $req->input('notaris'),
            'biaya_tabungan'        => $req->input('biaya_tabungan'),

            'rekom_angsuran'        => $recom_angs,

            'angs_pertama_bunga_berjalan' => $req->input('angs_pertama_bunga_berjalan'),
            'pelunasan_nasabah_ro'        => $req->input('pelunasan_nasabah_ro'),
            'blokir_dana'                 => $req->input('blokir_dana'),
            'pelunasan_tempat_lain'       => $req->input('pelunasan_tempat_lain'),
            'blokir_angs_kredit'          => $req->input('blokir_angs_kredit')
        );

        $asJiwa = array(
            'nama_asuransi'       => $req->input('nama_asuransi_jiwa'),
            'jangka_waktu'        => $req->input('jangka_waktu_as_jiwa'),
            'nilai_pertanggungan' => $req->input('nilai_pertanggungan_as_jiwa'),
            'jatuh_tempo'         => empty($req->input('jatuh_tempo_as_jiwa')) ? null : Carbon::parse($req->input('jatuh_tempo_as_jiwa'))->format('Y-m-d'),
            'berat_badan'         => $req->input('berat_badan_as_jiwa'),
            'tinggi_badan'        => $req->input('tinggi_badan_as_jiwa'),
            'umur_nasabah'        => $req->input('umur_nasabah_as_jiwa')
        );

        $asjaminanKeb = array(
            'nama_asuransi'       => $req->input('nama_asuransi_keb'),
            'jangka_waktu'        => $req->input('jangka_waktu_asuransi_keb'),
            'nilai_pertanggungan' => $req->input('nilai_pertanggungan_keb'),
            'jatuh_tempo'         => Carbon::parse($req->input('jatuh_tempo_keb'))->format('Y-m-d'),
        );

        $asjaminanKen = array(
            'nama_asuransi'       => $req->input('nama_asuransi_ken'),
            'jangka_waktu'        => $req->input('jangka_waktu_asuransi_ken'),
            'nilai_pertanggungan' => $req->input('nilai_pertanggungan_ken'),
            'jatuh_tempo'         => Carbon::parse($req->input('jatuh_tempo_ken'))->format('Y-m-d'),
        );


        // if (!empty($req->input('jangka_waktu_as_jaminan'))) {

        //     $asJaminan = array();
        //     for ($i = 0; $i < count($req->input('jangka_waktu_as_jaminan')); $i++) {

        //         $asJaminan[] = array(
        //             'nama_asuransi'
        //             => empty($req->input('nama_asuransi_jaminan')[$i])
        //                 ? null : $req->nama_asuransi_jaminan[$i],

        //             'jangka_waktu'
        //             => empty($req->input('jangka_waktu_as_jaminan')[$i])
        //                 ? null : $req->jangka_waktu_as_jaminan[$i],

        //             'nilai_pertanggungan'
        //             => empty($req->input('nilai_pertanggungan_as_jaminan')[$i])
        //                 ? null : $req->nilai_pertanggungan_as_jaminan[$i],

        //             'jatuh_tempo'
        //             => empty($req->input('jatuh_tempo_as_jaminan')[$i])
        //                 ? null : Carbon::parse($req->jatuh_tempo_as_jaminan[$i])->format('Y-m-d')
        //         );
        //     }

        //     $jaminanImplode = array(
        //         'nama_asuransi'       => implode(";", array_column($asJaminan, 'nama_asuransi')),
        //         'jangka_waktu'        => implode(";", array_column($asJaminan, 'jangka_waktu')),
        //         'nilai_pertanggungan' => implode(";", array_column($asJaminan, 'nilai_pertanggungan')),
        //         'jatuh_tempo'         => implode(";", array_column($asJaminan, 'jatuh_tempo'))
        //     );
        // } else {
        //     $jaminanImplode = array(
        //         'nama_asuransi'       => null,
        //         'jangka_waktu'        => null,
        //         'nilai_pertanggungan' => null,
        //         'jatuh_tempo'         => null
        //     );
        // }

        $get_trans = DB::connection('web')->table('view_transaksi_cs')->where('id', $id)->first();
        #Mengambil Transaksi yang ada di inputan SEFIN dari SO sampai dengan CA#
        $cs_trans = array(
            "tgl_transaksi" => $get_trans->tgl_transaksi,
            "id_aplikasi" => $get_trans->id,
            "nomor_aplikasi" => $get_trans->nomor_so,
            "nama_debitur" => $get_trans->nama_debitur,
            "id_area" => $get_trans->id_area,
            "id_cabang" => $get_trans->id_cabang,
            "nama_so" => $get_trans->nama_so,
            "nama_ao" => $get_trans->nama_ao
        );

        # Mengambil Data Nilai Credit Scoring yang ada di inputan SEFIN dari SO sampai dengan CA#
        $cs_nilai = array(
            "umur" => $get_trans->umur,
            "tanggungan" => $get_trans->tanggungan,
            "pendidikan_terakhir" => $get_trans->pendidikan_terakhir,
            "lama_kerja" => $get_trans->lama_kerja,
            "ltv" => $get_trans->ltv,
            "dsr" => $get_trans->dsr,
            "idir" => $get_trans->idir,
            "kuantitatif_ttl_pendapatan" => $get_trans->kuantitatif_ttl_pendapatan,
            "tenor" => $get_trans->tenor,
            "sku" => $get_trans->sku,
            "dokumen_usaha" => $get_trans->dokumen_usaha,
            "foto_usaha" => $get_trans->foto_usaha,
            "rekening" => $get_trans->rekening,
            "slip_gaji" => $get_trans->slip_gaji
        );

        // //dd(array($cs_trans,$cs_nilai));
        //   ###############################################################################################
        //   # PENGHITUNGAN #
        //   ###############################################################################################
        #start umur 1
        $umr = $cs_nilai['umur'];

        switch ($umr) {
            case ($umr > 50):
                $umr = "02101";
                break;
            case ($umr > 40 && $umr <= 50):
                $umr = "02102";
                break;
            case ($umr > 21  && $umr <= 26):
                $umr = "02103";
                break;
            case ($umr > 26 && $umr <= 33):
                $umr = "02104";
                break;
            case ($umr > 33 && $umr <= 40):
                $umr = "02105";
                break;

            default:
                $umr = null;
        }
        #end umur
        #####################################################################################################
        #start tanggungan 2
        $tang = $cs_nilai['tanggungan'];

        switch ($tang) {
            case 0:
                $tang = "0041";
                break;
            case 1:
                $tang = "0042";
                break;

            case 2:
                $tang = "0043";
                break;
            case 3:
                $tang = "0044";
                break;
            case ($tang = 4 && $tang > 4):
                $tang = "0045";
                break;
            default:
                $tang = null;
        }
        #end tanggungan
        ####################################################################################################
        #start pendidikan 3

        $sek = $cs_nilai['pendidikan_terakhir'];

        switch ($sek) {
            case ">= S2":
                $sek = "02201";
                break;
            case "D3":
                $sek = "02202";
                break;
            case "SLTA":
                $sek = "02203";
                break;
            case "S1":
                $sek = "02204";
                break;
            case "SLTP/SD/TIDAK SEKOLAH":
                $sek = "02205";
                break;
            default:
                $sek = null;
        }
        #end pendidikan
        #######################################################################################################
        #start lama_kerja 4

        $ker = $cs_nilai['lama_kerja'];

        switch ($ker) {
            case ($ker < 6):
                $ker = "01501";
                break;
            case ($ker >= 6 && $ker < 12):
                $ker = "01502";
                break;

            case ($ker >= 12 && $ker < 24):
                $ker = "01503";
                break;
            case ($ker >= 24 && $ker < 36):
                $ker = "01504";
                break;
            case ($ker > 36):
                $ker = "01505";
                break;
            default:
                $ker = null;
        }
        #end lama_kerja

        #start LTV 5
        $ltv = $dataRingkasan['kuantitatif_ltv'];

        switch ($ltv) {
            case ($ltv > 80):
                $ltv = "02501";
                break;
            case ($ltv <= 50):
                $ltv = "02502";
                break;
            case ($ltv > 70 && $ltv <= 80):
                $ltv = "02503";
                break;
            case ($ltv > 50 && $ltv <= 60):
                $ltv = "02504";
                break;
            case ($ltv > 60 && $ltv <= 70):
                $ltv = "02505";
                break;
            default:
                $ltv = null;
        }

        #end LTV

        #start Rasio Kapasitas 6
        $pendapatan = $dataRingkasan['kuantitatif_ttl_pendapatan'];
        $ras_kapasitas = null;
        switch ($ras_kapasitas) {
            case ($dataRingkasan['kuantitatif_idir'] < 80 && $dataRingkasan['kuantitatif_dsr'] < 30):
                $ras_kapasitas = "01901";
                break;
            case ($dataRingkasan['kuantitatif_idir'] < 80 && $dataRingkasan['kuantitatif_dsr'] > 30):
                $ras_kapasitas = "01902";
                break;
            case ($dataRingkasan['kuantitatif_idir']  > 80 && $dataRingkasan['kuantitatif_dsr']  < 30):
                $ras_kapasitas = "01903";
                break;
            case ($dataRingkasan['kuantitatif_idir']  > 80 && $dataRingkasan['kuantitatif_dsr']  > 30 && $pendapatan > 0):
                $ras_kapasitas = "01904";
                break;
            case ($dataRingkasan['kuantitatif_idir']  > 80 && $dataRingkasan['kuantitatif_dsr']  > 30 && $pendapatan < 0):
                $ras_kapasitas = "01905";
                break;

            default:
                $ras_kapasitas = null;
        }
        #end Rasio Kapasitas

        #start Tenor 7
        $ten = $recomCA['jangka_waktu'];

        switch ($ten) {
            case ($ten < 12):
                $ten = "01401";
                break;
            case ($ten >= 12 && $ten <= 24):
                $ten = "01402";
                break;
            case ($ten >= 25 && $ten <= 48):
                $ten = "01403";
                break;
            case ($ten > 48 && $ten <= 60):
                $ten = "01404";
                break;
            case ($ten > 60):
                $ten = "01405";
                break;
            default:
                $ten = null;
        }
        #end tenor

        #start cc 8 
        $cc = $dataACC;
        $col = array();
        foreach ($dataACC as $value) {
            $col[] = $value['collectabilitas'];
        }

        $arr = array();
        foreach ($dataACC as $value) {
            $arr[] = $value['jenis_kredit'];
        }
        //  dd(in_array('3', $arr));
        switch ($cc) {
            case ($dataACC[0]['jenis_kredit'] == 'No Din'):
                $cc = "0011";
                break;
            case (in_array('KTA', $arr)  && in_array('CC (Credit Card)', $arr) && count($col) > 1):
                $cc = "0012";
                break;
            case (!in_array('2', $col) && !in_array('3', $col) && !in_array('4', $col) && !in_array('5', $col)):
                $cc = "0013";
                break;
            case (in_array('BPKB', $arr) && in_array('Sertifikat', $arr) && count($arr) > 1):
                $cc = "0014";
                break;
            case (in_array('KTA', $arr)  && in_array('CC (Credit Card)', $arr) && in_array('BPKB', $arr) && in_array('Sertifikat', $arr) && count($arr) > 1):
                $cc = "0015";
                break;
            default:
                $cc = "0015";
        }
        #end cc

        #start jumlah_pinjaman_bank_lain 9 
        $pin_bank_lain = $dataACC;

        switch ($pin_bank_lain) {
            case (count($dataACC) == null):
                $pin_bank_lain = "0051";
                break;
            case (count($dataACC) == 1):
                $pin_bank_lain = "0052";
                break;
            case (count($dataACC) == 2):
                $pin_bank_lain = "0053";
                break;
            case (count($dataACC) == 3):
                $pin_bank_lain = "0054";
                break;
            case (count($dataACC) >= 4):
                $pin_bank_lain = "0055";
                break;
            default:
                $pin_bank_lain = null;
        }
        #end jumlah_pinjaman_bank_lain

        #start idir 10 
        $idir = $dataRingkasan['kuantitatif_idir'];

        switch ($idir) {
            case ($idir <= 75):
                $idir = "02301";
                break;
            case ($idir > 75 && $idir <= 80):
                $idir = "02302";
                break;
            case ($idir > 80 && $idir <= 85):
                $idir = "02303";
                break;
            case ($idir > 85 && $idir <= 90):
                $idir = "02304";
                break;
            case ($idir > 90):
                $idir = "02305";
                break;

            default:
                $idir = null;
        }
        #end idir

        #start dsr 11
        $dsr = $dataRingkasan['kuantitatif_dsr'];

        switch ($dsr) {
            case ($dsr < 30):
                $dsr = "02401";
                break;
            case ($dsr >= 30 && $dsr <= 35):
                $dsr = "02402";
                break;
            case ($dsr > 35 && $dsr <= 40):
                $dsr = "02403";
                break;
            case ($dsr > 40 && $dsr <= 50):
                $dsr = "02404";
                break;
            case ($dsr > 50):
                $dsr = "02405";
                break;

            default:
                $dsr = null;
        }

        #end dsr


        #start tipe lokasi 12
        $tipelok = AgunanTanah::select('tipe_lokasi')->where('id_trans_so', $id)->first();

        switch ($tipelok) {
            case ($tipelok->tipe_lokasi == 'Mini Cluster / Perkampungan Pinggir Jalan Raya'):
                $tipelok->tipe_lokasi = "02601";
                break;
            case ($tipelok->tipe_lokasi == 'Perumahan Cluster'):
                $tipelok->tipe_lokasi = "02602";
                break;
            case ($tipelok->tipe_lokasi == 'Perkampungan Akses Jalan Gang'):
                $tipelok->tipe_lokasi = "02603";
                break;
            case ($tipelok->tipe_lokasi == 'Perkampungan Desa Akses Jalan Non Aspal'):
                $tipelok->tipe_lokasi = "02604";
                break;
            case ($tipelok->tipe_lokasi == 'Perkampungan Jalan Desa'):
                $tipelok->tipe_lokasi = "02605";
                break;

            default:
                $tipelok->tipe_lokasi = null;
        }
        #end tipe_lokasi

        #start collateral 13
        $collateral = AgunanTanah::select('collateral')->where('id_trans_so', $id)->first();

        switch ($collateral) {
            case ($collateral->collateral == 'RUMAH'):
                $collateral = "01101";
                break;
            case ($collateral->collateral == 'RUKO'):
                $collateral = "01102";
                break;
            case ($collateral->collateral == 'RUMAH KONTRAKAN'):
                $collateral = "01103";
                break;
            case ($collateral->collateral == 'GEDUNG'):
                $collateral = "01104";
                break;
            case ($collateral->collateral == 'TANAH KOSONG'):
                $collateral = "01105";
                break;

            default:
                $collateral = null;
        }
        #end collateral

        #jenis_serti 14 
        $shmshgb = AgunanTanah::select('jenis_sertifikat')->where('id_trans_so', $id)->first();
        //$jen_sert = null;
        //if($shmshgb === null) {
        //$shmshgb = null;
        //} 
        //else {
        switch ($shmshgb) {
            case ($shmshgb->jenis_sertifikat == 'SHM'):
                $shmshgb = "01701";
                break;
            case ($shmshgb->jenis_sertifikat == 'SHGB AKTIF'):
                $shmshgb = "01702";
                break;
            case ($shmshgb->jenis_sertifikat == 'SHGB Akan Expired < 5 Tahun'):
                $shmshgb = "01703";
                break;
            case ($shmshgb->jenis_sertifikat == 'SHM PTSL'):
                $shmshgb = "01704";
                break;
            case ($shmshgb->jenis_sertifikat == 'LAINNYA'):
                $shmshgb = "01705";
                break;
            default:
                $shmshgb = null;
        }
        // }




        #end jenis_serti

        #start pemilik_jaminan 15
        $pemtn = PemeriksaanAgunTan::select('periksa_agunan_tanah.status_penghuni')->join('trans_ao', 'trans_ao.id_periksa_agunan_tanah', '=', 'periksa_agunan_tanah.id')->where('trans_ao.id_trans_so', $id)->first();

        switch ($pemtn) {
            case ($pemtn = 'Suami/Istri'):
                $pemtn = "02701";
                break;
            case ($pemtn = 'ORANG TUA'):
                $pemtn = "02702";
                break;
            case ($pemtn = 'Take Over a/n Sendiri'):
                $pemtn = "02703";
                break;
            case ($pemtn = 'Take Over a/n Orang Lain'):
                $pemtn = "02704";
                break;
            case ($pemtn = 'Belum Balik Nama/waris'):
                $pemtn = "02705";
                break;

            default:
                $pemtn = null;
        }
        #end Pemilik Jaminan



        #start angsuran_lain 17 
        $ang_bank_lain = $dataACC;

        switch ($ang_bank_lain) {
            case (count($dataACC) == null):
                $ang_bank_lain = "0081";
                break;
            case (count($dataACC) == 1):
                $ang_bank_lain = "0082";
                break;
            case (count($dataACC) == 2):
                $ang_bank_lain = "0083";
                break;
            case (count($dataACC) == 3):
                $ang_bank_lain = "0084";
                break;
            case (count($dataACC) >= 4):
                $ang_bank_lain = "0085";
                break;
            default:
                $ang_bank_lain = null;
        }
        #end jumlah_pinjaman_bank_lain

        #start baki_lain 18 
        $baki_bank_lain = $dataACC;

        switch ($baki_bank_lain) {
            case (count($dataACC) == null):
                $baki_bank_lain = "0071";
                break;
            case (count($dataACC) == 1):
                $baki_bank_lain = "0072";
                break;
            case (count($dataACC) == 2):
                $baki_bank_lain = "0073";
                break;
            case (count($dataACC) == 3):
                $baki_bank_lain = "0074";
                break;
            case (count($dataACC) >= 4):
                $baki_bank_lain = "0075";
                break;
            default:
                $baki_bank_lain = null;
        }
        #end baki_bank_lain

        #start bukti_kap 19
        $bukti_kap = null;

        switch ($bukti_kap) {
            case (!empty($cs_nilai['sku']) && !empty($cs_nilai['dokumen_usaha']) && !empty($cs_nilai['rekening']) || !empty($cs_nilai['foto_usaha'])):
                $bukti_kap = "01801";
                break;
            case (!empty($cs_nilai['sku']) && !empty($cs_nilai['rekening']) && !empty($cs_nilai['foto_usaha'])):
                $bukti_kap = "01802";
                break;
            case (!empty($cs_nilai['sku']) && !empty($cs_nilai['dokumen_usaha']) && !empty($cs_nilai['foto_usaha'])):
                $bukti_kap = "01803";
                break;
            case (!empty($cs_nilai['sku']) && !empty($cs_nilai['foto_usaha'])):
                $bukti_kap = "01804";
                break;
            case (empty($cs_nilai['sku']) && empty($cs_nilai['dokumen_usaha']) && empty($cs_nilai['rekening']) && empty($cs_nilai['foto_usaha'])):
                $bukti_kap = "01805";
                break;
            default:
                $bukti_kap = null;
        }

        #end bukti_kap

        $pendapatan = $dataRingkasan['kuantitatif_ttl_pendapatan'];
        switch ($pendapatan) {
            case ($pendapatan < 3000000):
                $pendapatan = "0061";
                break;
            case ($pendapatan >= 3000000 && $pendapatan < 5000000):
                $pendapatan = "0062";
                break;
            case ($pendapatan >= 5000000 && $pendapatan < 10000000):
                $pendapatan = "0063";
                break;
            case ($pendapatan >= 10000000 && $pendapatan < 15000000):
                $pendapatan = "0064";
                break;
            case ($pendapatan >  15000000):
                $pendapatan = "0065";
                break;

            default:
                $pendapatan = null;
        }

$data = array("cc_result" => empty($request->input('cc_result')) ? 0 : $request->input('cc_result'));

        $cc = null;
        if ($data['cc_result'] == 1) {
            $cc = "02001";
        } elseif ($data['cc_result'] == 2) {
            $cc = "02002";
        } elseif ($data['cc_result'] == 3) {
            $cc = "02003";
        } elseif ($data['cc_result'] == 4) {
            $cc = "02004";
        } elseif ($data['cc_result'] == 5) {
            $cc = "02005";
        }


        #1
        $scor_ccresult = DB::connection('simar')->table('master_creditscoring')->select('id_parameter AS parameter', 'id_detail_params AS detail', 'point', 'bobot')->where('id_detail_params', $cc)->first();


        #1
        $scor_cc = DB::connection('simar')->table('master_creditscoring')->select('id_parameter AS parameter', 'id_detail_params AS detail', 'point', 'bobot')->where('id_detail_params', $cc)->first();
        #2
        $scor_sek = DB::connection('simar')->table('master_creditscoring')->select('id_parameter AS parameter', 'id_detail_params AS detail', 'point', 'bobot')->where('id_detail_params', $sek)->first();
        #3
        $scor_umr = DB::connection('simar')->table('master_creditscoring')->select('id_parameter AS parameter', 'id_detail_params AS detail', 'point', 'bobot')->where('id_detail_params', $umr)->first();
        #4
        $scor_tang = DB::connection('simar')->table('master_creditscoring')->select('id_parameter AS parameter', 'id_detail_params AS detail', 'point', 'bobot')->where('id_detail_params', $tang)->first();
        #5
        $scor_pin_bank_lain = DB::connection('simar')->table('master_creditscoring')->select('id_parameter AS parameter', 'id_detail_params AS detail', 'point', 'bobot')->where('id_detail_params', $pin_bank_lain)->first();
        #6
        $scor_baki = DB::connection('simar')->table('master_creditscoring')->select('id_parameter AS parameter', 'id_detail_params AS detail', 'point', 'bobot')->where('id_detail_params', $baki_bank_lain)->first();

        #7
        $scor_ang_lain = DB::connection('simar')->table('master_creditscoring')->select('id_parameter AS parameter', 'id_detail_params AS detail', 'point', 'bobot')->where('id_detail_params', $ang_bank_lain)->first();
        #8
        $scor_idir = DB::connection('simar')->table('master_creditscoring')->select('id_parameter AS parameter', 'id_detail_params AS detail', 'point', 'bobot')->where('id_detail_params', $idir)->first();
        #9
        $scor_dsr = DB::connection('simar')->table('master_creditscoring')->select('id_parameter AS parameter', 'id_detail_params AS detail', 'point', 'bobot')->where('id_detail_params', $dsr)->first();
        #10
        $scor_coll = DB::connection('simar')->table('master_creditscoring')->select('id_parameter AS parameter', 'id_detail_params AS detail', 'point', 'bobot')->where('id_detail_params', $collateral)->first();
        #11
        $scor_ltv = DB::connection('simar')->table('master_creditscoring')->select('id_parameter AS parameter', 'id_detail_params AS detail', 'point', 'bobot')->where('id_detail_params', $ltv)->first();
        #12
        $scor_pem_jam = DB::connection('simar')->table('master_creditscoring')->select('id_parameter AS parameter', 'id_detail_params AS detail', 'point', 'bobot')->where('id_detail_params', $pemtn)->first();
        #13
        $scor_ten = DB::connection('simar')->table('master_creditscoring')->select('id_parameter AS parameter', 'id_detail_params AS detail', 'point', 'bobot')->where('id_detail_params', $ten)->first();
        #14
        $scor_ker = DB::connection('simar')->table('master_creditscoring')->select('id_parameter AS parameter', 'id_detail_params AS detail', 'point', 'bobot')->where('id_detail_params', $ker)->first();
        #15
        $scor_lokasi = DB::connection('simar')->table('master_creditscoring')->select('id_parameter AS parameter', 'id_detail_params AS detail', 'point', 'bobot')->where('id_detail_params', $tipelok->tipe_lokasi)->first();
        #16
        $scor_jen_sert = DB::connection('simar')->table('master_creditscoring')->select('id_parameter AS parameter', 'id_detail_params AS detail', 'point', 'bobot')->where('id_detail_params', $shmshgb)->first();
        #17
        $scor_kap = DB::connection('simar')->table('master_creditscoring')->select('id_parameter AS parameter', 'id_detail_params AS detail', 'point', 'bobot')->where('id_detail_params', $bukti_kap)->first();
        #18
        $scor_rasio = DB::connection('simar')->table('master_creditscoring')->select('id_parameter AS parameter', 'id_detail_params AS detail', 'point', 'bobot')->where('id_detail_params', $ras_kapasitas)->first();

        #18
        $scor_pendapatan = DB::connection('simar')->table('master_creditscoring')->select('id_parameter AS parameter', 'id_detail_params AS detail', 'point', 'bobot')->where('id_detail_params', $pendapatan)->first();



        $merge_scor = array(
            $scor_cc,
            $scor_sek, $scor_umr, $scor_tang, $scor_pin_bank_lain, $scor_baki, $scor_ang_lain, $scor_idir, $scor_dsr, $scor_coll, $scor_ltv, $scor_pem_jam, $scor_ten, $scor_ker, $scor_lokasi, $scor_jen_sert, $scor_kap, $scor_rasio, $scor_pendapatan,$scor_ccresult
        );

        // // dd($merge_scor);


        $arr_s = array();
        foreach ($merge_scor as $key => $val) {
            if (!empty($val)) {
                $arr_s[$key]['id_aplikasi'] = $id;
                $arr_s[$key]['parameter'] = $val->parameter;
                $arr_s[$key]['detail'] = $val->detail;
                $arr_s[$key]['point'] = $val->point;
                $arr_s[$key]['bobot'] = $val->bobot;
            }
        }

        $ms_trans = master_transaksi::create($cs_trans);
        $scor_params = master_nilai::insert($arr_s);

        $call_sp = DB::connection('simar')->select("CALL simar.`sp_hitung_hasil_scoring`(?,?)", array($get_trans->id, Carbon::parse($get_trans->tgl_transaksi)->format('Y-m-d')));

        try {
            DB::connection('web')->beginTransaction();

            if (!empty($dataMuBa)) {
                for ($i = 0; $i < count($dataMuBa); $i++) {
                    $mutasi = MutasiBank::create($dataMuBa[$i]);

                    $id_mutasi['id'][$i] = $mutasi->id;
                }

                $MutasiID   = implode(",", $id_mutasi['id']);
            } else {
                $MutasiID = null;
            }

            if (!empty($dataTabUang)) {
                $tabungan = TabDebt::create($dataTabUang);

                $idTabungan = $tabungan->id;
            } else {
                $idTabungan = null;
            }

            if (!empty($dataACC)) {
                for ($i = 0; $i < count($dataACC); $i++) {
                    $IACC = InfoACC::create($dataACC[$i]);

                    $arrACC['id'][$i] = $IACC->id;
                }

                $idInfo = implode(",", $arrACC['id']);
            } else {
                $idInfo = null;
            }

            if (!empty($dataRingkasan)) {
                $analisa = RingkasanAnalisa::create($dataRingkasan);
                $idAnalisa = $analisa->id;
            } else {
                $idAnalisa = null;
            }

            if (!empty($rekomPinjaman)) {
                $recomPin = RekomendasiPinjaman::create($rekomPinjaman);
                $idrecomPin = $recomPin->id;
            } else {
                $idrecomPin = null;
            }

            if (!empty($asJiwa)) {
                $jiwa = AsuransiJiwa::create($asJiwa);
                $idJiwa = $jiwa->id;
            } else {
                $idJiwa = null;
            }

            if (!empty($asjaminanKeb)) {
                $jaminan = AsuransiJaminan::create($asjaminanKeb);
                $idJaminanKeb = $jaminan->id;
            } else {
                $idJaminanKeb = null;
            }

            if (!empty($asjaminanKen)) {
                $jaminan = AsuransiJaminanKen::create($asjaminanKen);
                $idJaminanKen = $jaminan->id;
            } else {
                $idJaminanKen = null;
            }


            if (!empty($recomCA)) {
                $reCA = RekomendasiCA::create($recomCA);;
                $idReCA = $reCA->id;
            } else {
                $idReCA = null;
            }

            if (!empty($Pendapatan)) {
                $pend = PendapatanUsaha::create($Pendapatan);
                $idPendUs = $pend->id;
            } else {
                $idPendUs = null;
            }

            if (!empty($kapBul)) {
                $Q_Kapbul = KapBulanan::create($kapBul);
                $idKapBul = $Q_Kapbul->id;
            } else {
                $idKapBul = null;
            }

            $dataID = array(
                'id_mutasi_bank'          => $MutasiID,
                'id_log_tabungan'         => $idTabungan,
                'id_info_analisa_cc'      => $idInfo,
                'id_ringkasan_analisa'    => $idAnalisa,
                'id_recom_ca'             => $idReCA,
                'id_rekomendasi_pinjaman' => $idrecomPin,
                'id_asuransi_jiwa'        => $idJiwa,
                'id_asuransi_jaminan_kebakaran'     => $idJaminanKeb,
                'id_asuransi_jaminan_kendaraan'     => $idJaminanKen,
                'id_kapasitas_bulanan'    => $idKapBul,
                'id_pendapatan_usaha'     => $idPendUs
            );


            $newTransCA = array_merge($transCA, $dataID);

            $CA = TransCA::create($newTransCA);
            TransSO::where('id', $id)->update(['id_trans_ca' => $CA->id]);

            DB::connection('web')->commit();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message' => 'Data untuk CA berhasil dikirim',
                'data'   => $CA
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
        $pic = $req->pic;

        $column = array(
            'id', 'nomor_ao', 'id_trans_so', 'user_id', 'id_pic', 'id_area', 'id_cabang', 'id_validasi', 'id_verifikasi', 'id_agunan_tanah', 'id_agunan_kendaraan', 'id_periksa_agunan_tanah', 'id_periksa_agunan_kendaraan', 'id_kapasitas_bulanan', 'id_pendapatan_usaha', 'id_recom_ao', 'catatan_ao', 'status_ao', 'form_persetujuan_ideb'
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

        $query_dir = TransAO::with('so', 'pic', 'cabang')
            ->where('status_ao', 1)
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


        foreach ($result->get() as $key => $val) {

            if ($val->status_ao == 1) {
                $status_ao = 'recommend';
            } elseif ($val->status_ao == 2) {
                $status_ao = 'not recommend';
            } else {
                $status_ao = 'waiting';
            }

            $data[$key] = [
                'id_trans_so'    => $val->id_trans_so == null ? null : (int) $val->id_trans_so,
                'nomor_so'       => $val->so['nomor_so'],
                'nomor_ao'       => $val->nomor_ao,
                // 'nomor_ca'       => $val->so['ca']['nomor_ca'],
                'pic'            => $val->pic['nama'],
                'area'           => $val->area['nama'],
                'cabang'         => $val->cabang['nama'],
                'asal_data'      => $val->so['asaldata']['nama'],
                'nama_marketing' => $val->so['nama_marketing'],
                'nama_debitur'   => $val->so['debt']['nama_lengkap'],
                'plafon'         => $val->so['faspin']['plafon'],
                'tenor'          => $val->so['faspin']['tenor'],
                'status_ao'      => $status_ao,
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

    public function revisi($id_trans_so, $id_trans_ca, Request $req)
    {
        $user_id = $request->auth->user_id;
        $pic = $req->pic; // From PIC middleware

        $check_so = TransSO::where('id', $id_trans_so)->where('status_das', 1)->where('status_hm', 1)->first();

        if (!$check_so) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id ' . $id_trans_so . ' belum ada di SO atau belum komplit saat pemeriksaaan DAS dan HM'
            ], 404);
        }

        $check_ao = TransAO::where('id_trans_so', $id_trans_so)->where('status_ao', 1)->first();

        if (!$check_ao) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id ' . $id_trans_so . ' belum sampai ke AO'
            ], 404);
        }

        $check_ca = TransCA::where('id_trans_so', $id_trans_so)->where('status_ca', 1)->first();

        if (empty($check_ca)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id ' . $id_trans_so . ' belum sampai ke CA'
            ], 404);
        }

        $transCA = array(
            'nomor_ca'    => $check_ca->nomor_ca . ' [revisi]',
            'user_id'     => $user_id,
            'id_trans_so' => $id_trans_so,
            'id_pic'      => $check_ca->id_pic,
            'id_area'     => $check_ca->id_area,
            'id_cabang'   => $check_ca->id_cabang,
            'catatan_ca'  => $check_ca->catatan_ca,
            'status_ca'   => $check_ca->status_ca,
            'revisi'      => $check_ca->id
        );

        // // Pendapatan Usaha Cadebt
        // $dataPendapatanUsaha = array(
        //     'pemasukan_tunai'      => empty($req->input('pemasukan_tunai'))     ? $check_ca->usaha['pemasukan_tunai'] : $req->input('pemasukan_tunai'),
        //     'pemasukan_kredit'     => empty($req->input('pemasukan_kredit'))    ? $check_ca->usaha['pemasukan_kredit'] : $req->input('pemasukan_kredit'),
        //     'biaya_sewa'           => empty($req->input('biaya_sewa'))          ? $check_ca->usaha['biaya_sewa'] : $req->input('biaya_sewa'),
        //     'biaya_gaji_pegawai'   => empty($req->input('biaya_gaji_pegawai'))  ? $check_ca->usaha['biaya_gaji_pegawai'] : $req->input('biaya_gaji_pegawai'),
        //     'biaya_belanja_brg'    => empty($req->input('biaya_belanja_brg'))   ? $check_ca->usaha['biaya_belanja_brg'] : $req->input('biaya_belanja_brg'),
        //     'biaya_telp_listr_air' => empty($req->input('biaya_telp_listr_air')) ? $check_ca->usaha['biaya_telp_listr_air'] : $req->input('biaya_telp_listr_air'),
        //     'biaya_sampah_kemanan' => empty($req->input('biaya_sampah_kemanan')) ? $check_ca->usaha['biaya_sampah_kemanan'] : $req->input('biaya_sampah_kemanan'),
        //     'biaya_kirim_barang'   => empty($req->input('biaya_kirim_barang'))  ? $check_ca->usaha['biaya_kirim_barang'] : $req->input('biaya_kirim_barang'),
        //     'biaya_hutang_dagang'  => empty($req->input('biaya_hutang_dagang')) ? $check_ca->usaha['biaya_hutang_dagang'] : $req->input('biaya_hutang_dagang'),
        //     'biaya_angsuran'       => empty($req->input('biaya_angsuran'))      ? $check_ca->usaha['biaya_angsuran'] : $req->input('biaya_angsuran'),
        //     'biaya_lain_lain'      => empty($req->input('biaya_lain_lain'))     ? $check_ca->usaha['biaya_lain_lain'] : $req->input('biaya_lain_lain')
        // );

        // $totalPendapatan = array(
        //     'total_pemasukan'    => $ttl1 = array_sum(array_slice($dataPendapatanUsaha, 0, 2)),
        //     'total_pengeluaran'  => $ttl2 = array_sum(array_slice($dataPendapatanUsaha, 2)),
        //     'laba_usaha'         => $ttl1 - $ttl2
        // );

        // $Pendapatan = array_merge($dataPendapatanUsaha, $totalPendapatan, array('ao_ca' => 'CA'));

        // Start Kapasitas Bulanan
        $inputKapBul = array(

            'pemasukan_cadebt'
            => empty($req->input('pemasukan_debitur'))    ? $check_ca->usaha['pemasukan_debitur'] : $req->input('pemasukan_debitur'),

            'pemasukan_pasangan'
            => empty($req->input('pemasukan_pasangan'))   ? $check_ca->usaha['pemasukan_pasangan'] : $req->input('pemasukan_pasangan'),

            'pemasukan_penjamin'
            => empty($req->input('pemasukan_penjamin'))   ? $check_ca->usaha['pemasukan_penjamin'] : $req->input('pemasukan_penjamin'),

            'biaya_rumah_tangga'
            => empty($req->input('biaya_rumah_tangga'))   ? $check_ca->usaha['biaya_rumah_tangga'] : $req->input('biaya_rumah_tangga'),

            'biaya_transport'
            => empty($req->input('biaya_transport'))      ? $check_ca->usaha['biaya_transport'] : $req->input('biaya_transport'),

            'biaya_pendidikan'
            => empty($req->input('biaya_pendidikan'))     ? $check_ca->usaha['biaya_pendidikan'] : $req->input('biaya_pendidikan'),

            'telp_listr_air'
            => empty($req->input('telp_listr_air'))       ? $check_ca->usaha['telp_listr_air'] : $req->input('telp_listr_air'),

            'angsuran'
            => empty($req->input('angsuran'))             ? $check_ca->usaha['angsuran'] : $req->input('angsuran'),

            'biaya_lain'
            => empty($req->input('biaya_lain'))           ? $check_ca->usaha['biaya_lain'] : $req->input('biaya_lain'),
        );

        $total_KapBul = array(
            'total_pemasukan'    => $ttl1 = array_sum(array_slice($inputKapBul, 0, 3)),
            'total_pengeluaran'  => $ttl2 = array_sum(array_slice($inputKapBul, 3)),
            'penghasilan_bersih' => $ttl1 - $ttl2
        );

        // Ceiling Recomendasi Pinjaman
        $rekomPinjaman = array(
            'penyimpangan_struktur'
            => empty($req->input('penyimpangan_struktur'))
                ? $check_ca->recom_pin['penyimpangan_struktur'] : $req->input('penyimpangan_struktur'),

            'penyimpangan_dokumen'
            => empty($req->input('penyimpangan_dokumen'))
                ? $check_ca->recom_pin['penyimpangan_dokumen'] : $req->input('penyimpangan_dokumen'),

            'recom_nilai_pinjaman'
            => empty($req->input('recom_nilai_pinjaman'))
                ? $check_ca->recom_pin['recom_nilai_pinjaman'] : $req->input('recom_nilai_pinjaman'),

            'recom_tenor'
            => empty($req->input('recom_tenor'))
                ? $check_ca->recom_pin['recom_tenor'] : $req->input('recom_tenor'),

            'recom_angsuran'
            => empty($req->input('recom_angsuran'))
                ? $check_ca->recom_pin['recom_angsuran'] : $req->input('recom_angsuran'),

            'recom_produk_kredit'
            => empty($req->input('recom_produk_kredit'))
                ? $check_ca->recom_pin['recom_produk_kredit'] : $req->input('recom_produk_kredit'),

            'note_recom'
            => empty($req->input('note_recom'))
                ? $check_ca->recom_pin['note_recom'] : $req->input('note_recom'),

            'bunga_pinjaman'
            => empty($req->input('bunga_pinjaman'))
                ? $check_ca->recom_pin['bunga_pinjaman'] : $req->input('bunga_pinjaman'),

            'nama_ca'
            => empty($req->input('nama_ca'))
                ? $check_ca->recom_pin['nama'] : $req->input('nama_ca')
        );

        // Rekomendasi Angsuran pada table rekomendasi_pinjaman

        $plafonCA = $rekomPinjaman['recom_nilai_pinjaman'] == null ? 0 : $rekomPinjaman['recom_nilai_pinjaman'];
        $tenorCA  = $rekomPinjaman['recom_tenor']          == null ? 0 : $rekomPinjaman['recom_tenor'];
        $bunga    = $rekomPinjaman['bunga_pinjaman']       == null ? 0 : ($rekomPinjaman['bunga_pinjaman'] / 100);

        $rekomen_pendapatan  = $total_KapBul['total_pemasukan']   == null ? 0 : $total_KapBul['total_pemasukan'];
        $rekomen_pengeluaran = $total_KapBul['total_pengeluaran'] == null ? 0 : $total_KapBul['total_pengeluaran'];
        $rekomen_angsuran    = $inputKapBul['angsuran']           == null ? 0 : $inputKapBul['angsuran'];

        if ($plafonCA == 0 && $tenorCA == 0 && $bunga == 0) {
            $recom_angs = 0;
        } else {
            $recom_angs = Helper::recom_angs($plafonCA, $tenorCA, $bunga);
        }

        $rekomen_pend_bersih = $rekomen_pendapatan - $rekomen_pengeluaran;

        $disposable_income   = $rekomen_pend_bersih - $recom_angs;

        $kapBul = array_merge($inputKapBul, $total_KapBul, array('disposable_income'  => $disposable_income, 'ao_ca' => 'CA'));

        // Check Pemeriksaan
        $id_pe_ta = $check_ao->id_periksa_agunan_tanah;

        if (empty($id_pe_ta)) {
            $PeriksaTanah = null;
        }

        // $id_pe_ke = $check_ao->id_periksa_agunan_kendaraan;

        // if ($id_pe_ke == null) {
        //     $PeriksaKenda = null;
        // }

        $PeriksaTanah = PemeriksaanAgunTan::select('nilai_taksasi_agunan')->whereIn('id', explode(",", $id_pe_ta))->get()->toArray();

        if (empty($PeriksaTanah)) {
            $sumTaksasiTan = 0;
        } else {
            $sumTaksasiTan = array_sum(array_column($PeriksaTanah, 'nilai_taksasi_agunan')); //array_sum($PeriksaTanah);
        }

        // $PeriksaKenda = PemeriksaanAgunTan::select('nilai_taksasi_agunan')->whereIn('id', explode(",", $id_pe_ta))->get()->toArray();

        // if ($PeriksaKenda == []) {
        //     $sumTaksasiKen = 0;
        // }else{
        //     $sumTaksasiKen = array_sum(array_column($PeriksaTanah,'nilai_taksasi_agunan')); //array_sum($PeriksaTanah);
        // }
        // $sumAllTaksasi = $sumTaksasiTan + $sumTaksasiKen; // Semua Nilai Taksasi dari semua agunan
        $sumAllTaksasi = $sumTaksasiTan; // Semua Nilai Taksasi dari semua agunan


        $recom_ltv   = Helper::recom_ltv($plafonCA, $sumAllTaksasi);
        $recom_idir  = Helper::recom_idir($recom_angs, $rekomen_pendapatan, $rekomen_pengeluaran);
        $recom_dsr   = Helper::recom_dsr($recom_angs, $rekomen_pendapatan, $rekomen_angsuran);
        $recom_hasil = Helper::recom_hasil($recom_dsr, $recom_ltv, $recom_idir);

        // Data Ringkasan Analisa CA
        $dataRingkasan = array(
            'kuantitatif_ttl_pendapatan'    => $rekomen_pendapatan,
            'kuantitatif_ttl_pengeluaran'   => $rekomen_pengeluaran,
            'kuantitatif_pendapatan_bersih' => $rekomen_pend_bersih,
            'kuantitatif_angsuran'          => $recom_angs,
            'kuantitatif_ltv'               => $recom_ltv,
            'kuantitatif_dsr'               => $recom_dsr,
            'kuantitatif_idir'              => $recom_idir,
            'kuantitatif_hasil'             => $recom_hasil,


            'kualitatif_analisa'
            => empty($req->input('kualitatif_analisa'))
                ? $check_ca->recom_ca['kualitatif_analisa']
                : $req->input('kualitatif_analisa'),

            'kualitatif_strenght'
            => empty($req->input('kualitatif_strenght'))
                ? $check_ca->recom_ca['kualitatif_strenght']
                : $req->input('kualitatif_strenght'),

            'kualitatif_weakness'
            => empty($req->input('kualitatif_weakness'))
                ? $check_ca->recom_ca['kualitatif_weakness']
                : $req->input('kualitatif_weakness'),

            'kualitatif_opportunity'
            => empty($req->input('kualitatif_opportunity'))
                ? $check_ca->recom_ca['kualitatif_opportunity']
                : $req->input('kualitatif_opportunity'),

            'kualitatif_threatness'
            => empty($req->input('kualitatif_threatness'))
                ? $check_ca->recom_ca['kualitatif_threatness']
                : $req->input('kualitatif_threatness')
        );

        try {
            DB::connection('web')->beginTransaction();

            if (!empty($dataRingkasan)) {
                $analisa = RingkasanAnalisa::create($dataRingkasan);
                $idAnalisa = $analisa->id;
            } else {
                $idAnalisa = null;
            }

            if (!empty($recomCA)) {
                $newRecom = array_merge($recomCA, $recomCaOL);

                $reCA = RekomendasiCA::create($newRecom);
                $idReCA = $reCA->id;
            } else {
                $idReCA = null;
            }

            if (!empty($kapBul)) {
                $Q_Kapbul = KapBulanan::create($kapBul);
                $idKapBul = $Q_Kapbul->id;
            } else {
                $idKapBul = null;
            }

            $dataID = array(
                'id_mutasi_bank'          => $check_ca->id_mutasi_bank,
                'id_log_tabungan'         => $check_ca->id_log_tabungan,
                'id_info_analisa_cc'      => $check_ca->id_info_analisa_cc,
                'id_ringkasan_analisa'    => $idAnalisa,
                'id_recom_ca'             => $idReCA,
                'id_rekomendasi_pinjaman' => $check_ca->id_rekomendasi_pinjaman,
                'id_asuransi_jiwa'        => $check_ca->id_asuransi_jiwa,
                'id_asuransi_jaminan'     => $check_ca->id_asuransi_jaminan,
                'id_kapasitas_bulanan'    => $idKapBul,
                'id_pendapatan_usaha'     => $check_ca->id_pendapatan_usaha
            );

            $newTransCA = array_merge($transCA, $dataID);

            TransCA::create($newTransCA);

            DB::connection('web')->commit();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message' => 'Data untuk Revisi CA berhasil dikirim'
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





    // Sample
    public function operator($id_trans_so, Request $req, Helper $help)
    {

        $check = TransSO::where('id', $id_trans_so)->where('status_das', 1)->where('status_hm', 1)->first();

        if (!$check) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id ' . $id_trans_so . ' belum ada di SO atau saat pemeriksaaan DAS dan HM'
            ], 404);
        }

        $check_ao = TransAO::where('id_trans_so', $id_trans_so)->where('status_ao', 1)->first();

        if (!$check_ao) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id ' . $id_trans_so . ' belum sampai ke AO'
            ], 404);
        }

        // Analisa Kuantitatif dan Kualitatif
        $id_pe_ta = $check_ao->id_periksa_agunan_tanah;

        if (empty($id_pe_ta)) {
            $PeriksaTanah = null;
        }

        // $id_pe_ke = $check_ao->id_periksa_agunan_kendaraan;

        // if (empty($id_pe_ke)) {
        //     $PeriksaKenda = null;
        // }

        $PeriksaTanah = PemeriksaanAgunTan::select('nilai_taksasi_agunan')->whereIn('id', explode(",", $id_pe_ta))->get()->toArray();

        if (empty($PeriksaTanah)) {
            $sumTaksasiTan = 0;
        } else {
            $sumTaksasiTan = array_sum(array_column($PeriksaTanah, 'nilai_taksasi_agunan')); //array_sum($PeriksaTanah);
        }

        // $PeriksaKenda = PemeriksaanAgunKen::select('nilai_taksasi_agunan')->whereIn('id', explode(",", $id_pe_ta))->get()->toArray();

        // if ($PeriksaKenda == null) {
        //     $sumTaksasiKen = 0;
        // }else{
        //     $sumTaksasiKen = array_sum(array_column($PeriksaTanah,'nilai_taksasi_agunan')); //array_sum($PeriksaTanah);
        // }

        // $sumAllTaksasi = $sumTaksasiTan + $sumTaksasiKen; // Semua Nilai Taksasi dari semua agunan
        $sumAllTaksasi = $sumTaksasiTan; // Semua Nilai Taksasi dari semua agunan

        // Start Kapasitas Bulanan
        $inputKapBul = array(

            'pemasukan_debitur'
            => empty($req->input('pemasukan_debitur'))    ? 0 : (int) $req->input('pemasukan_debitur'),

            'pemasukan_pasangan'
            => empty($req->input('pemasukan_pasangan'))   ? 0 : (int) $req->input('pemasukan_pasangan'),

            'pemasukan_penjamin'
            => empty($req->input('pemasukan_penjamin'))   ? 0 : (int) $req->input('pemasukan_penjamin'),

            'biaya_rumah_tangga'
            => empty($req->input('biaya_rumah_tangga'))   ? 0 : (int) $req->input('biaya_rumah_tangga'),

            'biaya_transport'
            => empty($req->input('biaya_transport'))      ? 0 : (int) $req->input('biaya_transport'),

            'biaya_pendidikan'
            => empty($req->input('biaya_pendidikan'))     ? 0 : (int) $req->input('biaya_pendidikan'),

            'telp_listr_air'
            => empty($req->input('telp_listr_air'))       ? 0 : (int) $req->input('telp_listr_air'),

            'angsuran'
            => empty($req->input('angsuran'))             ? 0 : (int) $req->input('angsuran'),

            'biaya_lain'
            => empty($req->input('biaya_lain'))           ? 0 : (int) $req->input('biaya_lain'),
        );

        $total_KapBul = array(
            'total_pemasukan'    => $ttl1 = array_sum(array_slice($inputKapBul, 0, 3)),
            'total_pengeluaran'  => $ttl2 = array_sum(array_slice($inputKapBul, 3)),
            'penghasilan_bersih' => $ttl1 - $ttl2
        );

        // Ceiling Recomendasi Pinjaman
        $rekomPinjaman = array(
            'produk'        => $req->input('produk'),
            'plafon_kredit' => (int) $req->input('plafon_kredit'), //45000000
            'jangka_waktu'  => (int) $req->input('jangka_waktu'), // 48
            'suku_bunga'    => (float) $req->input('suku_bunga') // 1.70
        );

        // Rekomendasi Angsuran pada table rrekomendasi_pinjaman
        $plafonCA = $rekomPinjaman['plafon_kredit'] == null ? 0 : $rekomPinjaman['plafon_kredit'];
        $tenorCA  = $rekomPinjaman['jangka_waktu']  == null ? 0 : $rekomPinjaman['jangka_waktu'];
        $bunga    = $rekomPinjaman['suku_bunga']    == null ? 0 : ($rekomPinjaman['suku_bunga'] / 100);

        $rekomen_pendapatan  = $total_KapBul['total_pemasukan']   == null ? 0 : $total_KapBul['total_pemasukan'];
        $rekomen_pengeluaran = $total_KapBul['total_pengeluaran'] == null ? 0 : $total_KapBul['total_pengeluaran'];
        $rekomen_angsuran    = $inputKapBul['angsuran']           == null ? 0 : $inputKapBul['angsuran'];

        if ($plafonCA == 0 && $tenorCA == 0 && $bunga == 0) {
            $recom_angs = 0;
        } else {
            $recom_angs = Helper::recom_angs($plafonCA, $tenorCA, $bunga);
        }

        $rekomen_pend_bersih = $rekomen_pendapatan - $rekomen_pengeluaran;

        $disposable_income = $rekomen_pend_bersih - $recom_angs;

        $kapBul = array_merge($inputKapBul, $total_KapBul, array('disposable_income'  => $disposable_income, 'ao_ca' => 'CA'));
        // End Kapasitas Bulanan

        // Analisa Kuantitatif dan Kualitatif
        $recom_ltv   = Helper::recom_ltv($plafonCA, $sumAllTaksasi);
        $recom_idir  = Helper::recom_idir($recom_angs, $rekomen_pendapatan, $rekomen_pengeluaran);
        $recom_dsr   = Helper::recom_dsr($recom_angs, $rekomen_pendapatan, $rekomen_angsuran);
        $recom_hasil = Helper::recom_hasil($recom_dsr, $recom_ltv, $recom_idir);

        // Data Ringkasan Analisa CA
        $dataRingkasan = array(
            'kuantitatif_ttl_pendapatan'    => $rekomen_pendapatan,
            'kuantitatif_ttl_pengeluaran'   => $rekomen_pengeluaran,
            'kuantitatif_pendapatan_bersih' => $rekomen_pend_bersih,
            'kuantitatif_angsuran'          => $recom_angs,
            'kuantitatif_ltv'               => $recom_ltv,
            'kuantitatif_dsr'               => $recom_dsr,
            'kuantitatif_idir'              => $recom_idir,
            'kuantitatif_hasil'             => $recom_hasil
        );

        $resultAll = array(
            'taksasi_agunan_tanah' => $sumTaksasiTan,
            // 'rekomendasi_ca'       => $rekomPinjaman,
            'rekomendasi_pinjaman' => $rekomPinjaman,
            'rekom_angsuran'       => $recom_angs,
            'kapasitas_bulanan'    => $kapBul,
            'ringkasan_analisa_ca' => $dataRingkasan,
        );

        DB::connection('web')->beginTransaction();
        try {

            DB::connection('web')->commit();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => $resultAll
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

    public function filter($year, $month, Request $req)
    {
        $pic = $req->pic; // From PIC middleware

        $area = array();
        $i = 0;
        foreach ($pic as $val) {
            $area[] = $val['id_area'];
            $i++;
        }

        $arrr = array();
        foreach ($pic as $val) {
            $arrr[] = $val['id_cabang'];
            $i++;
        }
        $arrrr = array();
        foreach ($pic as $val) {
            $arrrr[] = $val['jpic']['cakupan'];
            $i++;
        }
        //  dd($arr);
        $id_area   = $area;
        $id_cabang = $arrr;
        // dd($id_cabang);
        $scope     = $arrrr;
        if ($month == null) {

            $query_dir = TransAO::with('so', 'pic', 'cabang')->where('status_ao', 1)
                ->whereYear('created_at', '=', $year)
                ->orderBy('created_at', 'desc');
        } else {

            $query_dir = TransAO::with('so', 'pic', 'cabang')->where('status_ao', 1)
                ->whereYear('created_at', '=', $year)
                ->whereMonth('created_at', '=', $month)
                ->orderBy('created_at', 'desc');
        }

        $query = Helper::checkDir($scope, $query_dir, $id_area, $id_cabang);

        if ($query == '[]') {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        $data = array();
        foreach ($query as $key => $val) {

            if ($val->status_ao == 1) {
                $status_ao = 'recommend';
            } elseif ($val->status_ao == 2) {
                $status_ao = 'not recommend';
            } else {
                $status_ao = 'waiting';
            }

            if ($val->so['ca']['status_ca'] == 1) {
                $status_ca = 'recommend';
            } elseif ($val->so['ca']['status_ca'] == 2) {
                $status_ca = 'not recommend';
            } else {
                $status_ca = 'waiting';
            }

            $data[$key] = [
                'id_trans_so'    => $val->id_trans_so       == null ? null : (int) $val->id_trans_so,
                'id_trans_ca'    => $val->so['id_trans_ca'] == null ? null : (int) $val->so['id_trans_ca'],
                'nomor_so'       => $val->so['nomor_so'],
                'nomor_ao'       => $val->nomor_ao,
                // 'nomor_ca'       => $val->so['ca']['nomor_ca'],
                'pic'            => $val->pic['nama'],
                'area'           => $val->area['nama'],
                'cabang'         => $val->cabang['nama'],
                'asal_data'      => $val->so['asaldata']['nama'],
                'nama_marketing' => $val->so['nama_marketing'],
                'nama_debitur'   => $val->so['debt']['nama_lengkap'],
                'plafon'         => $val->so['faspin']['plafon'],
                'tenor'          => $val->so['faspin']['tenor'],
                "ao" => [
                    'status_ao'     => $status_ao,
                    'catatan_ao'    => $val->catatan_ao
                ],
                "ca" => [
                    'status_ca'     => $status_ca,
                    'catatan_ca'    => $val->so['ca']['catatan_ca']
                ],
                'tgl_transaksi'     => $val->created_at
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

    public function full_show($id, Request $req)
    {
        $pic = $req->pic; // From PIC middleware

        $area = array();
        $i = 0;
        foreach ($pic as $val) {
            $area[] = $val['id_area'];
            $i++;
        }

        $arrr = array();
        foreach ($pic as $val) {
            $arrr[] = $val['id_cabang'];
            $i++;
        }
        $arrrr = array();
        foreach ($pic as $val) {
            $arrrr[] = $val['jpic']['cakupan'];
            $i++;
        }
        //  dd($arr);
        $id_area   = $area;
        $id_cabang = $arrr;
        // dd($id_cabang);
        $scope     = $arrrr;

        $check_so = TransSO::where('id', $id)->where('status_das', 1)->where('status_hm', 1)->first();

        if ($check_so == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id ' . $id . ' belum ada di SO atau belum komplit saat pemeriksaaan DAS dan HM'
            ], 404);
        }


        $check_ao = TransAO::with('pic', 'cabang')->where('id_trans_so', $id)->where('status_ao', 1)->first();

        if ($check_ao == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id ' . $id . ' belum sampai ke AO'
            ], 404);
        }

        $check_ca = TransCA::with('so', 'pic', 'cabang')->where('id_trans_so', $id)->where('status_ca', 1)->first();
        //  dd(date("d-m-Y", strtotime($check_ca->as_jiwa)));
        //dd($check_ca->log_tab);
        if ($check_ca == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id ' . $id . ' belum sampai ke CA'
            ], 404);
        }

        if ($check_ca->status_ca == 1) {
            $status_ca = 'recommend';
        } elseif ($check_ca->status_ca == 2) {
            $status_ca = 'not recommend';
        } else {
            $status_ca = 'waiting';
        }

        $mutasi = MutasiBank::whereIn('id', explode(",", $check_ca->id_mutasi_bank))->get()->toArray();

        if ($mutasi != []) {

            foreach ($mutasi as $i => $mut) {
                $doub[$i] = array_slice($mut, 0, 5);
            }

            foreach ($mutasi as $i => $mut) {
                $slice[$i] = array_slice($mut, 5);
                foreach ($slice as $key => $val) {
                    foreach ($val as $row => $col) {
                        $arr[$i][$row] = explode(";", $col);
                    }
                }
            }

            foreach ($arr as $key => $subarr) {
                foreach ($subarr as $subkey => $subvalue) {
                    foreach ($subvalue as $childkey => $childvalue) {
                        $out[$key][$childkey][$subkey] = ($childvalue);
                    }

                    $dataMut[$key] = array_merge($doub[$key], array('table' => $out[$key]));
                }
            }
        } else {
            $dataMut = null;
        }

        $id_pe_ta = $check_ao->id_periksa_agunan_tanah;

        if (empty($id_pe_ta)) {
            $PeriksaTanah = null;
        }

        $id_pe_ke = $check_ao->id_periksa_agunan_kendaraan;

        if ($id_pe_ke == null) {
            $PeriksaKenda = null;
        }

        $PeriksaTanah = PemeriksaanAgunTan::select('nilai_taksasi_agunan')->whereIn('id', explode(",", $id_pe_ta))->get()->toArray();

        // $check_ca->getRelations(); // get all the related models
        // $check_ca->getRelation('author'); // to get only related author model
        //dd($check_ca->log_tab);
        $data[] = [
            'id_trans_so'           => $check_so->id == null ? null : (int) $check_so->id,
            'nomor_so'              => $check_so->nomor_so,
            'kapasitas_bulanan'     => $check_ca->kapbul,
            'pendapatan_usaha'      => $check_ca->usaha,
            'mutasi_bank'           => $dataMut,
            'data_keuangan'         => DB::connection('web')->table('log_tabungan_debt')->where('id', $check_ca->log_tab['id'])->first(),
            'informasi_analisa_cc'  => array(
                'table'         => $iac = InfoACC::whereIn('id', explode(",", $check_ca->id_info_analisa_cc))->get()->toArray(),
                'total_plafon'  => array_sum(array_column($iac, 'plafon')),
                'total_baki_debet' => array_sum(array_column($iac, 'baki_debet')),
                'angsuran'         => array_sum(array_column($iac, 'angsuran')),
                'collectabitas_tertinggi' => max(array_column($iac, 'collectabilitas'))
            ),
            'ringkasan_analisa'     => $check_ca->ringkasan,
            'nilai_taksasi_agunan'  => $PeriksaTanah,
            'rekomendasi_pinjaman'  => $check_ca->recom_pin,
            'rekomendasi_ca'        => $check_ca->recom_ca,
            'asuransi_jiwa'         => $check_ca->as_jiwa,
            'asuransi_jaminan_kebakaran'      => AsuransiJaminan::where('id', $check_ca->id_asuransi_jaminan_kebakaran)->get(),
            'asuransi_jaminan_kendaraan'      => AsuransiJaminanKen::whereIn('id', explode(";", $check_ca->id_asuransi_jaminan_kendaraan))->get(),
            'status_ca'             => $status_ca,
            'cc_result' => $check_ca->cc_result,
            'tgl_transaksi'         => $check_ca->created_at
        ];

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => $data[0]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

 public function updateRecordCA($id, Request $req)
    {
        $pic     = $req->pic; // From PIC middleware
        $user_id = $req->auth->user_id;
        $trans = TransCA::where('id_trans_so', $id)->first();
        $check_ktp_deb = Debitur::join('trans_so', 'trans_so.id_calon_debitur', 'calon_debitur.id')->where('trans_so.id', $id)->first();
        if (empty($trans)) {
            return response()->json([
                "code" => 404,
                "message" => "Data Transaksi Kosong"
            ]);
        }

        if ($file = $req->file('record_ca')) {
            $path = 'public/' . $check_ktp_deb->no_ktp . '/debitur/record_ca';
            $name = Carbon::now() . '-' . 'record_ca' . '-' . $check_ktp_deb->id_cabang;
            //. '-' . Carbon::now();
            $check = $trans->record_ca;

            $arrayPath = array();

            $exAudio = $file->getClientOriginalExtension();

            if ($exAudio != 'wav' && $exAudio != 'mp3') {
                return response()->json([
                    "code"    => 422,
                    "status"  => "not valid request",
                    "message" => "file record ca harus berupa format wav / mp3"
                ], 422);
            }
            //  dd($name);
            // Check Directory
            if (!File::isDirectory($path)) {
                File::makeDirectory($path, 0777, true, true);
            }

            // Delete File is Exists
            if (!empty($check)) {
                File::delete($check);
            }

            $name = $file->getClientOriginalName();

            // dd($path . '/' . $name);

            // Save Image to Directory
            $file->move($path, $name);
            $arrayPath = $path . '/' . $name;


            $record_ca = $arrayPath;
        } else {
            $record_ca = null;
        }
        $data = array("record_ca" => $record_ca);


        $update = TransCA::where('id_trans_so', $id)->update($data);

        return response()->json([
            "code" => 200,
            "message" => "Success",
            "data" => $data
        ]);
    }
	
	 public function indexTrackingCA(Request $req)
    {
		$user_id = $req->auth->user_id;
        $trans = TrackingOrderCa::select('tracking_order_ca.id','tracking_order_ca.user_id','tracking_order_ca.id_trans_so','tracking_order_ca.nomor_so','tracking_order_ca.id_cabang','mk_cabang.nama AS nama_cabang','tracking_order_ca.nama_debitur','tracking_order_ca.plafon','tracking_order_ca.status','tracking_order_ca.keterangan','tracking_order_ca.created_at','tracking_order_ca.updated_at')->join('mk_cabang', 'mk_cabang.id', 'tracking_order_ca.id_cabang')->orderBy('created_at','DESC')->where('user_id',$user_id)->get();

        if ($trans === null) {
            return response()->json([
                "code" => 404,
                "meesage" => "Data Tracking Tidak Ditemukan"
            ]);
        }
        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => $trans
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }
    public function showTrackingCA(Request $req, $id)
    {

        $trans = TrackingOrderCa::where('id_trans_so', $id)->first();

        if ($trans === null) {
            return response()->json([
                "code" => 404,
                "meesage" => "Data Tracking Tidak Ditemukan"
            ]);
        }
        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => $trans
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function storeTrackingCA(Request $req)
    {
        $user_id = $req->auth->user_id;
        $data = array(
            'user_id' => $user_id,
            'id_ca' => $req->input('id_ca'),
            'tgl_activity' => $req->input('tgl_activity'),
            'id_trans_so' => $req->input('id_trans_so'),
			'nomor_so' => $req->input('nomor_so'),
            'nama_debitur' => $req->input('nama_debitur'),
            'plafon' => $req->input('plafon'),
            'id_cabang' => $req->input('id_cabang'),
            'status' => $req->input('status'),
            'keterangan' => $req->input('keterangan'),
            'created_at' => Carbon::now(),
        );

        if ($data === null) {
            return response()->json([
                "code" => 404,
                "meesage" => "Silahkan Masukkan Inputan"
            ]);
        }
        try {
            $trans = TrackingOrderCa::create($data);
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

    public function updateTrackingCA(Request $req, $id)
    {
        $user_id = $req->auth->user_id;

        $trans = TrackingOrderCa::where('id_trans_so', $id)->first();
        $data = array(
            'user_id' => $user_id,
            'id_ca' => empty($req->input('id_ca')) ? $trans->id_ca : $req->input('id_ca'),
            'tgl_activity' => empty($req->input('tgl_activity')) ? $trans->tgl_activity : $req->input('tgl_activity'),
            'id_trans_so' => empty($req->input('id_trans_so')) ? $trans->id_trans_so : $req->input('id_trans_so'),
			'nomor_so' => empty($req->input('nomor_so')) ? $trans->nomor_so : $req->input('nomor_so'),
            'nama_debitur' => empty($req->input('nama_debitur')) ? $trans->nama_debitur : $req->input('nama_debitur'),
            'plafon' => empty($req->input('plafon')) ? $trans->plafon : $req->input('plafon'),
            'id_cabang' => empty($req->input('id_cabang')) ? $trans->id_cabang : $req->input('id_cabang'),
            'status' => empty($req->input('status')) ? $trans->status : $req->input('status'),
            'keterangan' => empty($req->input('keterangan')) ? $trans->keterangan : $req->input('keterangan'),
            'updated_at' => Carbon::now()
        );

        if ($data === null) {
            return response()->json([
                "code" => 404,
                "meesage" => "Silahkan Masukkan Inputan"
            ]);
        }
        try {
            $trans = TrackingOrderCa::where('id_trans_so', $id)->update($data);
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
}
