<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use App\Http\Requests\Transaksi\BlankRequest;
use App\Models\Pengajuan\CAA\Penyimpangan;
use App\Models\Pengajuan\AO\AgunanKendaraan;
use App\Models\Pengajuan\AO\AgunanTanah;
use App\Models\Pengajuan\AO\PemeriksaanAgunTan;
use App\Models\Pengajuan\AO\PemeriksaanAgunKen;
use App\Models\Pengajuan\SO\Penjamin;
use App\Models\Pengajuan\SO\Debitur;
use App\Models\Pengajuan\CA\InfoACC;
use App\Models\Pengajuan\CA\MutasiBank;
use Illuminate\Support\Facades\File;
use App\Models\Transaksi\Approval;
use App\Models\Transaksi\TransCAA;
use App\Models\Transaksi\TransCA;
use App\Models\Transaksi\TransSO;
use App\Models\Transaksi\TransAO;
use App\Models\AreaKantor\JPIC;
use App\Models\AreaKantor\PIC;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\ActivitySo;
use App\Models\Efilling\Bi_checking;
use App\Models\Efilling\Efilling;
use App\Models\Efilling\Efilling_asset;
use App\Models\Efilling\Efilling_bi;
use App\Models\Efilling\Efilling_ca;
use App\Models\Efilling\Efilling_foto;
use App\Models\Efilling\Efilling_legal;
use App\Models\Efilling\Efilling_spkndk;
use App\Models\Efilling\EfillingJaminan;
use App\Models\Transaksi\LogRekomCA;
use App\Models\Transaksi\LogRingAnalisa;
use App\Models\v2\Target_lending;
use App\Models\master_nilai;
use App\Models\master_transaksi;
//use Image;
use App\Models\Efilling\EfillingNasabah;
use App\Models\Efilling\EfillingPermohonan;
use Intervention\Image\Image;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Ftp as Adapter;
use App\Http\Requests\Transaksi\EfillingwebtoolReq;

// use Intervention\Image\ImageManagerStatic as Image;


use App\Models\MasterActivity\Activity;
use App\Models\MasterActivity\TargetPeriodik;
use App\Models\MasterActivity\TargetApproval;
use App\Models\Transaksi\Lpdk_lampiran;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;


class EfillingWebtoolController extends BaseController
{
    public function updateWebtool(EfillingwebtoolReq $req, Request $request, $no_kontrak)
    {

        $user_id = $request->auth;

        $ftp = Storage::createFtpDriver([
            'host'     => '103.234.254.186',
            'username' => 'bonar',
            'password' => 'Abc123!!**',
            'port'     => '2123', // your ftp port
            // 'timeout'  => '30', // timeout setting 
            'ignorePassiveAddress' => true,
            'root' => 'F:/Apache2.2/htdocs/efiling'
        ]);

        $get_nas = EfillingNasabah::where('no_rekening', $no_kontrak)->first();
        $get_ef = Efilling::where('no_rekening', $no_kontrak)->first();

        $get_kre = EfillingPermohonan::where('no_rekening', $no_kontrak)->first();
        $get_bi = Bi_checking::where('no_rekening', $no_kontrak)->first();
        $get_bi_pengajuan = Efilling_bi::where('no_rekening', $no_kontrak)->first();
        $get_caa = Efilling_ca::where('no_rekening', $no_kontrak)->first();
        $get_foto = Efilling_foto::where('no_rekening', $no_kontrak)->first();
        $get_jaminan = EfillingJaminan::where('no_rekening', $no_kontrak)->first();
        $get_legal = Efilling_legal::where('no_rekening', $no_kontrak)->first();
        $get_asset = Efilling_asset::where('no_rekening', $no_kontrak)->first();
        $get_spkndk = Efilling_spkndk::where('no_rekening', $no_kontrak)->first();

        $subs_thn = substr($get_ef->tgl_realisasi_eng_lama, 0, 4);
        $subs_bln = substr($get_ef->tgl_realisasi_eng_lama, 5, 2);
        $subs_rekcab = substr($get_ef->no_rekening, 0, 12);
        $enc = json_decode($get_nas->npwp);


        #######################################################################################################
        #lampiran Nasabah   
        if ($files = $req->file('lampiran_ktp')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';

            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                //  $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_nas->ktp === null) {
                $get_nas->ktp = array('');
                $enc = array_merge($get_nas->ktp, array($name));
            } else {
                $enc = json_decode($get_nas->ktp);

                array_push($enc, $name);
            }
            $ktp = $enc;
        } else {
            $ktp = $get_nas->ktp;
        }

        if ($files = $req->file('lampiran_npwp')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                // dd(array_push($get_nas->npwp, $name));
                //  dd($path, $file, $name);
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_nas->npwp === null) {
                $get_nas->npwp = array('');
                $enc = array_merge($get_nas->npwp, array($name));
            } else {
                $enc = json_decode($get_nas->npwp);

                array_push($enc, $name);
            }
            $npwp = $enc;
        } else {
            $npwp = $get_nas->npwp;
        }

        if ($files = $req->file('lampiran_kk')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }
            if ($get_nas->kk === null) {
                $get_nas->kk = array('');
                $enc = array_merge($get_nas->kk, array($name));
            } else {
                $enc = json_decode($get_nas->kk);
                array_push($enc, $name);
            }
            $kk = $enc;
        } else {
            $kk = $get_nas->kk;
        }

        if ($files = $req->file('surat_nikah')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_nas->surat_nikah === null) {
                $get_nas->surat_nikah = array('');
                $enc = array_merge($get_nas->surat_nikah, array($name));
            } else {
                $enc = json_decode($get_nas->surat_nikah);

                array_push($enc, $name);
            }
            $surat_nikah = $enc;
        } else {
            $surat_nikah = $get_nas->surat_nikah;
        }

        if ($files = $req->file('surat_lahir')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_nas->surat_lahir === null) {
                $get_nas->surat_lahir = array('');
                $enc = array_merge($get_nas->surat_lahir, array($name));
            } else {
                $enc = json_decode($get_nas->surat_lahir);

                array_push($enc, $name);
            }
            $surat_lahir = $enc;
        } else {
            $surat_lahir = $get_nas->surat_lahir;
        }

        if ($files = $req->file('surat_kematian')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_nas->surat_kematian === null) {
                $get_nas->surat_kematian = array('');
                $enc = array_merge($get_nas->surat_kematian, array($name));
            } else {
                $enc = json_decode($get_nas->surat_kematian);

                array_push($enc, $name);
            }
            $surat_kematian = $enc;
        } else {
            $surat_kematian = $get_nas->surat_kematian;
        }

        if ($files = $req->file('slipgaji')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_nas->slip_gaji === null) {
                $get_nas->slip_gaji = array('');
                $enc = array_merge($get_nas->slip_gaji, array($name));
            } else {
                $enc = json_decode($get_nas->slip_gaji);

                array_push($enc, $name);
            }
            $slip_gaji = $enc;
        } else {
            $slip_gaji = $get_nas->slip_gaji;
        }

        if ($files = $req->file('domisili')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_nas->domisili === null) {
                $get_nas->domisili = array('');
                $enc = array_merge($get_nas->domisili, array($name));
            } else {
                $enc = json_decode($get_nas->domisili);

                array_push($enc, $name);
            }
            $domisili = $enc;
        } else {
            $domisili = $get_nas->domisili;
        }

        if ($files = $req->file('skk')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_nas->sk_kerja === null) {
                $get_nas->sk_kerja = array('');
                $enc = array_merge($get_nas->sk_kerja, array($name));
            } else {
                $enc = json_decode($get_nas->sk_kerja);

                array_push($enc, $name);
            }
            $skk = $enc;
        } else {
            $skk = $get_nas->sk_kerja;
        }

        if ($files = $req->file('sku')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_nas->sk_usaha === null) {
                $get_nas->sk_usaha = array('');
                $enc = array_merge($get_nas->sk_usaha, array($name));
            } else {
                $enc = json_decode($get_nas->sk_usaha);

                array_push($enc, $name);
            }
            $sku = $enc;
        } else {
            $sku = $get_nas->sk_usaha;
        }

        if ($files = $req->file('skd')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }
            if ($get_nas->skd === null) {
                $get_nas->skd = array('');
                $enc = array_merge($get_nas->skd, array($name));
            } else {

                $enc = json_decode($get_nas->skd);

                array_push($enc, $name);
            }
            $skd = $enc;
        } else {
            $skd = $get_nas->skd;
        }

        if ($files = $req->file('take_over')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_nas->take_over === null) {
                $get_nas->take_over = array('');
                $enc = array_merge($get_nas->take_over, array($name));
            } else {
                $enc = json_decode($get_nas->take_over);

                array_push($enc, $name);
            }
            $take_over = $enc;
        } else {
            $take_over = $get_nas->take_over;
        }

        if ($files = $req->file('rek_koran')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }
            if ($get_nas->rek_koran === null) {
                $get_nas->rek_koran = array('');
                $enc = array_merge($get_nas->rek_koran, array($name));
            } else {

                $enc = json_decode($get_nas->rek_koran);

                array_push($enc, $name);
            }
            $rek_koran = $enc;
        } else {
            $rek_koran = $get_nas->rek_koran;
        }

        if ($files = $req->file('tdp')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_nas->tdp === null) {
                $get_nas->tdp = array('');
                $enc = array_merge($get_nas->tdp, array($name));
            } else {
                $enc = json_decode($get_nas->tdp);

                array_push($enc, $name);
            }
            $tdp = $enc;
        } else {
            $tdp = $get_nas->tdp;
        }

        if ($files = $req->file('bon_usaha')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_nas->bon_usaha === null) {
                $get_nas->bon_usaha = array('');
                $enc = array_merge($get_nas->bon_usaha, array($name));
            } else {
                $enc = json_decode($get_nas->bon_usaha);

                array_push($enc, $name);
            }
            $bon_usaha = $enc;
        } else {
            $bon_usaha = $get_nas->bon_usaha;
        }

        ######################################################################################################       
        if ($files = $req->file('surat_cerai')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_nas->surat_cerai === null) {
                $get_nas->surat_cerai = array('');
                $enc = array_merge($get_nas->surat_cerai, array($name));
            } else {
                $enc = json_decode($get_nas->surat_cerai);

                array_push($enc, $name);
            }
            $surat_cerai = $enc;
        } else {
            $surat_cerai = $get_nas->surat_cerai;
        }

        #########################################################################################
        if ($files = $req->file('aplikasi_kredit')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_kre->aplikasi === null) {
                $get_kre->aplikasi = array('');
                $enc = array_merge($get_kre->aplikasi, array($name));
            } else {
                $enc = json_decode($get_kre->aplikasi);

                array_push($enc, $name);
            }
            $aplikasi = $enc;
        } else {
            $aplikasi = $get_kre->aplikasi;
        }

        if ($files = $req->file('denah_lokasi')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_kre->denah_lokasi === null) {
                $get_kre->denah_lokasi = array('');
                $enc = array_merge($get_kre->denah_lokasi, array($name));
            } else {
                $enc = json_decode($get_kre->denah_lokasi);

                array_push($enc, $name);
            }
            $denah_lokasi = $enc;
        } else {
            $denah_lokasi = $get_kre->denah_lokasi;
        }

        if ($files = $req->file('checklist_kelengkapan')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_kre->checklist_kelengkapan === null) {
                $get_kre->checklist_kelengkapan = array('');
                $enc = array_merge($get_kre->checklist_kelengkapan, array($name));
            } else {
                $enc = json_decode($get_kre->checklist_kelengkapan);

                array_push($enc, $name);
            }
            $checklist_kelengkapan = $enc;
        } else {
            $checklist_kelengkapan = $get_kre->checklist_kelengkapan;
        }
        ########################################################################################
        if ($files = $req->file('lampiran_pengajuan_bi')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_bi_pengajuan->pengajuan_bi === null) {
                $get_bi_pengajuan->pengajuan_bi = array('');
                $enc = array_merge($get_bi_pengajuan->pengajuan_bi, array($name));
            } else {
                $enc = json_decode($get_bi_pengajuan->pengajuan_bi);

                array_push($enc, $name);
            }
            $pengajuan_bi = $enc;
        } else {
            $pengajuan_bi = $get_bi_pengajuan->pengajuan_bi;
        }

        if ($files = $req->file('lampiran_persetujuan_bi')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_bi_pengajuan->persetujuan === null) {
                $get_bi_pengajuan->persetujuan = array('');
                $enc = array_merge($get_bi_pengajuan->persetujuan, array($name));
            } else {
                $enc = json_decode($get_bi_pengajuan->persetujuan);

                array_push($enc, $name);
            }
            $persetujuan = $enc;
        } else {
            $persetujuan = $get_bi_pengajuan->persetujuan;
        }

        if ($files = $req->file('lampiran_hasil_bi')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_bi_pengajuan->hasil === null) {
                $get_bi_pengajuan->hasil = array('');
                $enc = array_merge($get_bi_pengajuan->hasil, array($name));
            } else {
                $enc = json_decode($get_bi_pengajuan->hasil);

                array_push($enc, $name);
            }
            $hasil = $enc;
        } else {
            $hasil = $get_bi_pengajuan->hasil;
        }
        ########################################################################################
        if ($files = $req->file('memo_ao')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_caa->memo_ao === null) {
                $get_caa->memo_ao = array('');
                $enc = array_merge($get_caa->memo_ao, array($name));
            } else {
                $enc = json_decode($get_caa->memo_ao);

                array_push($enc, $name);
            }
            $memo_ao = $enc;
        } else {
            $memo_ao = $get_caa->memo_ao;
        }

        if ($files = $req->file('memo_ca')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_caa->memo_ca === null) {
                $get_caa->memo_ca = array('');
                $enc = array_merge($get_caa->memo_ca, array($name));
            } else {
                $enc = json_decode($get_caa->memo_ca);

                array_push($enc, $name);
            }
            $memo_ca = $enc;
        } else {
            $memo_ca = $get_caa->memo_ca;
        }

        if ($files = $req->file('offering_letter')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_caa->offering_letter === null) {
                $get_caa->offering_letter = array('');
                $enc = array_merge($get_caa->offering_letter, array($name));
            } else {
                $enc = json_decode($get_caa->offering_letter);

                array_push($enc, $name);
            }
            $offering_letter = $enc;
        } else {
            $offering_letter = $get_caa->offering_letter;
        }

        if ($files = $req->file('penilaian_jaminan')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_caa->penilaian_jaminan === null) {
                $get_caa->penilaian_jaminan = array('');
                $enc = array_merge($get_caa->penilaian_jaminan, array($name));
            } else {
                $enc = json_decode($get_caa->penilaian_jaminan);

                array_push($enc, $name);
            }
            $penilaian_jaminan = $enc;
        } else {
            $penilaian_jaminan = $get_caa->penilaian_jaminan;
        }

        if ($files = $req->file('cheklist_survey')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_caa->cheklist_survey === null) {
                $get_caa->cheklist_survey = array('');
                $enc = array_merge($get_caa->cheklist_survey, array($name));
            } else {
                $enc = json_decode($get_caa->cheklist_survey);

                array_push($enc, $name);
            }
            $cheklist_survey = $enc;
        } else {
            $cheklist_survey = $get_caa->cheklist_survey;
        }

        if ($files = $req->file('persetujuan_kredit')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_caa->persetujuan_kredit === null) {
                $get_caa->persetujuan_kredit = array('');
                $enc = array_merge($get_caa->persetujuan_kredit, array($name));
            } else {
                $enc = json_decode($get_caa->persetujuan_kredit);

                array_push($enc, $name);
            }
            $persetujuan_kredit = $enc;
        } else {
            $persetujuan_kredit = $get_caa->persetujuan_kredit;
        }
        ########################################################################################
        if ($files = $req->file('ft_jaminan')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_foto->ft_jaminan === null) {
                $get_foto->ft_jaminan = array('');
                $enc = array_merge($get_foto->ft_jaminan, array($name));
            } else {
                $enc = json_decode($get_foto->ft_jaminan);

                array_push($enc, $name);
            }
            $ft_jaminan = $enc;
        } else {
            $ft_jaminan = $get_foto->ft_jaminan;
        }

        if ($files = $req->file('ft_pengikatan')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_foto->ft_pengikatan === null) {
                $get_foto->ft_pengikatan = array('');
                $enc = array_merge($get_foto->ft_pengikatan, array($name));
            } else {
                $enc = json_decode($get_foto->ft_pengikatan);

                array_push($enc, $name);
            }
            $ft_pengikatan = $enc;
        } else {
            $ft_pengikatan = $get_foto->ft_pengikatan;
        }

        if ($files = $req->file('ft_domisili')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_foto->ft_domisili === null) {
                $get_foto->ft_domisili = array('');
                $enc = array_merge($get_foto->ft_domisili, array($name));
            } else {
                $enc = json_decode($get_foto->ft_domisili);

                array_push($enc, $name);
            }
            $ft_domisili = $enc;
        } else {
            $ft_domisili = $get_foto->ft_domisili;
        }

        if ($files = $req->file('ft_usaha')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_foto->ft_usaha === null) {
                $get_foto->ft_usaha = array('');
                $enc = array_merge($get_foto->ft_usaha, array($name));
            } else {
                $enc = json_decode($get_foto->ft_usaha);

                array_push($enc, $name);
            }
            $ft_usaha = $enc;
        } else {
            $ft_usaha = $get_foto->ft_usaha;
        }
        ########################################################################################
        if ($files = $req->file('sertifikat')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_jaminan->sertifikat === null) {
                $get_jaminan->sertifikat = array('');
                $enc = array_merge($get_jaminan->sertifikat, array($name));
            } else {
                $enc = json_decode($get_jaminan->sertifikat);

                array_push($enc, $name);
            }
            $sertifikat = $enc;
        } else {
            $sertifikat = $get_jaminan->sertifikat;
        }

        if ($files = $req->file('skmht')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_jaminan->skmht === null) {
                $get_jaminan->skmht = array('');
                $enc = array_merge($get_jaminan->skmht, array($name));
            } else {
                $enc = json_decode($get_jaminan->skmht);

                array_push($enc, $name);
            }
            $skmht = $enc;
        } else {
            $skmht = $get_jaminan->skmht;
        }

        if ($files = $req->file('apht')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_jaminan->apht === null) {
                $get_jaminan->apht = array('');
                $enc = array_merge($get_jaminan->apht, array($name));
            } else {
                $enc = json_decode($get_jaminan->apht);

                array_push($enc, $name);
            }
            $apht = $enc;
        } else {
            $apht = $get_jaminan->apht;
        }

        if ($files = $req->file('cabut_roya')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_jaminan->cabut_roya === null) {
                $get_jaminan->cabut_roya = array('');
                $enc = array_merge($get_jaminan->cabut_roya, array($name));
            } else {
                $enc = json_decode($get_jaminan->cabut_roya);

                array_push($enc, $name);
            }
            $cabut_roya = $enc;
        } else {
            $cabut_roya = $get_jaminan->cabut_roya;
        }

        if ($files = $req->file('sht')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_jaminan->sht === null) {
                $get_jaminan->sht = array('');
                $enc = array_merge($get_jaminan->sht, array($name));
            } else {
                $enc = json_decode($get_jaminan->sht);

                array_push($enc, $name);
            }
            $sht = $enc;
        } else {
            $sht = $get_jaminan->sht;
        }

        if ($files = $req->file('pbb')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_jaminan->pbb === null) {
                $get_jaminan->pbb = array('');
                $enc = array_merge($get_jaminan->pbb, array($name));
            } else {
                $enc = json_decode($get_jaminan->pbb);

                array_push($enc, $name);
            }
            $pbb = $enc;
        } else {
            $pbb = $get_jaminan->pbb;
        }

        if ($files = $req->file('imb')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_jaminan->imb === null) {
                $get_jaminan->imb = array('');
                $enc = array_merge($get_jaminan->imb, array($name));
            } else {
                $enc = json_decode($get_jaminan->imb);

                array_push($enc, $name);
            }
            $imb = $enc;
        } else {
            $imb = $get_jaminan->imb;
        }

        if ($files = $req->file('ajb')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_jaminan->ajb === null) {
                $get_jaminan->ajb = array('');
                $enc = array_merge($get_jaminan->ajb, array($name));
            } else {
                $enc = json_decode($get_jaminan->ajb);

                array_push($enc, $name);
            }
            $ajb = $enc;
        } else {
            $ajb = $get_jaminan->ajb;
        }

        if ($files = $req->file('bpkb')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_jaminan->bpkb === null) {
                $get_jaminan->bpkb = array('');
                $enc = array_merge($get_jaminan->bpkb, array($name));
            } else {
                $enc = json_decode($get_jaminan->bpkb);

                array_push($enc, $name);
            }
            $bpkb = $enc;
        } else {
            $bpkb = $get_jaminan->bpkb;
        }

        if ($files = $req->file('ahli_waris')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_jaminan->ahli_waris === null) {
                $get_jaminan->ahli_waris = array('');
                $enc = array_merge($get_jaminan->ahli_waris, array($name));
            } else {
                $enc = json_decode($get_jaminan->ahli_waris);

                array_push($enc, $name);
            }
            $ahli_waris = $enc;
        } else {
            $ahli_waris = $get_jaminan->ahli_waris;
        }

        if ($files = $req->file('pengakuan_hutang')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_jaminan->pengakuan_hutang === null) {
                $get_jaminan->pengakuan_hutang = array('');
                $enc = array_merge($get_jaminan->pengakuan_hutang, array($name));
            } else {
                $enc = json_decode($get_jaminan->pengakuan_hutang);

                array_push($enc, $name);
            }
            $pengakuan_hutang = $enc;
        } else {
            $pengakuan_hutang = $get_jaminan->pengakuan_hutang;
        }

        if ($files = $req->file('akta_pengakuan_hak_bersama')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_jaminan->akta_pengakuan_hak_bersama === null) {
                $get_jaminan->akta_pengakuan_hak_bersama = array('');
                $enc = array_merge($get_jaminan->akta_pengakuan_hak_bersama, array($name));
            } else {
                $enc = json_decode($get_jaminan->akta_pengakuan_hak_bersama);

                array_push($enc, $name);
            }
            $akta_pengakuan_hak_bersama = $enc;
        } else {
            $akta_pengakuan_hak_bersama = $get_jaminan->akta_pengakuan_hak_bersama;
        }

        if ($files = $req->file('adendum')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_jaminan->adendum === null) {
                $get_jaminan->adendum = array('');
                $enc = array_merge($get_jaminan->adendum, array($name));
            } else {
                $enc = json_decode($get_jaminan->adendum);

                array_push($enc, $name);
            }
            $adendum = $enc;
        } else {
            $adendum = $get_jaminan->adendum;
        }

        if ($files = $req->file('fidusia')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_jaminan->fidusia === null) {
                $get_jaminan->fidusia = array('');
                $enc = array_merge($get_jaminan->fidusia, array($name));
            } else {
                $enc = json_decode($get_jaminan->fidusia);

                array_push($enc, $name);
            }
            $fidusia = $enc;
        } else {
            $fidusia = $get_jaminan->fidusia;
        }
        ########################################################################################
        if ($files = $req->file('pengajuan_lpdk')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_legal->pengajuan_lpdk === null) {
                $get_legal->pengajuan_lpdk = array('');
                $enc = array_merge($get_legal->pengajuan_lpdk, array($name));
            } else {
                $enc = json_decode($get_legal->pengajuan_lpdk);

                array_push($enc, $name);
            }
            $pengajuan_lpdk = $enc;
        } else {
            $pengajuan_lpdk = $get_legal->pengajuan_lpdk;
        }

        if ($files = $req->file('lpdk')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_legal->lpdk === null) {
                $get_legal->lpdk = array('');
                $enc = array_merge($get_legal->lpdk, array($name));
            } else {
                $enc = json_decode($get_legal->lpdk);

                array_push($enc, $name);
            }
            $lpdk = $enc;
        } else {
            $lpdk = $get_legal->lpdk;
        }

        if ($files = $req->file('cheklist_pengikatan')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_legal->cheklist_pengikatan === null) {
                $get_legal->cheklist_pengikatan = array('');
                $enc = array_merge($get_legal->cheklist_pengikatan, array($name));
            } else {
                $enc = json_decode($get_legal->cheklist_pengikatan);

                array_push($enc, $name);
            }
            $cheklist_pengikatan = $enc;
        } else {
            $cheklist_pengikatan = $get_legal->cheklist_pengikatan;
        }

        if ($files = $req->file('order_pengikatan')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_legal->order_pengikatan === null) {
                $get_legal->order_pengikatan = array('');
                $enc = array_merge($get_legal->order_pengikatan, array($name));
            } else {
                $enc = json_decode($get_legal->order_pengikatan);

                array_push($enc, $name);
            }
            $order_pengikatan = $enc;
        } else {
            $order_pengikatan = $get_legal->order_pengikatan;
        }
        #######################################################################################
        if ($files = $req->file('ra_tanda_terima')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_asset->ra_tanda_terima === null) {
                $get_asset->ra_tanda_terima = array('');
                $enc = array_merge($get_asset->ra_tanda_terima, array($name));
            } else {
                $enc = json_decode($get_asset->ra_tanda_terima);

                array_push($enc, $name);
            }
            $ra_tanda_terima = $enc;
        } else {
            $ra_tanda_terima = $get_asset->ra_tanda_terima;
        }

        if ($files = $req->file('ra_surat_kuasa')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_asset->ra_surat_kuasa === null) {
                $get_asset->ra_surat_kuasa = array('');
                $enc = array_merge($get_asset->ra_surat_kuasa, array($name));
            } else {
                $enc = json_decode($get_asset->ra_surat_kuasa);

                array_push($enc, $name);
            }
            $ra_surat_kuasa = $enc;
        } else {
            $ra_surat_kuasa = $get_asset->ra_surat_kuasa;
        }

        if ($files = $req->file('ra_identitas_pengambilan')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_asset->ra_identitas_pengambilan === null) {
                $get_asset->ra_identitas_pengambilan = array('');
                $enc = array_merge($get_asset->ra_identitas_pengambilan, array($name));
            } else {
                $enc = json_decode($get_asset->ra_identitas_pengambilan);

                array_push($enc, $name);
            }
            $ra_identitas_pengambilan = $enc;
        } else {
            $ra_identitas_pengambilan = $get_asset->ra_identitas_pengambilan;
        }

        if ($files = $req->file('ra_lainnya')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_asset->ra_lainnya === null) {
                $get_asset->ra_lainnya = array('');
                $enc = array_merge($get_asset->ra_lainnya, array($name));
            } else {
                $enc = json_decode($get_asset->ra_lainnya);

                array_push($enc, $name);
            }
            $ra_lainnya = $enc;
        } else {
            $ra_lainnya = $get_asset->ra_lainnya;
        }

        if ($files = $req->file('ra_serah_terima')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_asset->ra_serah_terima === null) {
                $get_asset->ra_serah_terima = array('');
                $enc = array_merge($get_asset->ra_serah_terima, array($name));
            } else {
                $enc = json_decode($get_asset->ra_serah_terima);

                array_push($enc, $name);
            }
            $ra_serah_terima = $enc;
        } else {
            $ra_serah_terima = $get_asset->ra_serah_terima;
        }
        ######################################################################################
        if ($files = $req->file('spk_ndk')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_spkndk->spk_ndk === null) {
                $get_spkndk->spk_ndk = array('');
                $enc = array_merge($get_spkndk->spk_ndk, array($name));
            } else {
                $enc = json_decode($get_spkndk->spk_ndk);

                array_push($enc, $name);
            }
            $spk_ndk = $enc;
        } else {
            $spk_ndk = $get_spkndk->spk_ndk;
        }

        if ($files = $req->file('asuransi')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_spkndk->asuransi === null) {
                $get_spkndk->asuransi = array('');
                $enc = array_merge($get_spkndk->asuransi, array($name));
            } else {
                $enc = json_decode($get_spkndk->asuransi);

                array_push($enc, $name);
            }
            $asuransi = $enc;
        } else {
            $asuransi = $get_spkndk->asuransi;
        }

        if ($files = $req->file('sp_no_imb')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_spkndk->sp_no_imb === null) {
                $get_spkndk->sp_no_imb = array('');
                $enc = array_merge($get_spkndk->sp_no_imb, array($name));
            } else {
                $enc = json_decode($get_spkndk->sp_no_imb);

                array_push($enc, $name);
            }
            $sp_no_imb = $enc;
        } else {
            $sp_no_imb = $get_spkndk->sp_no_imb;
        }

        if ($files = $req->file('jadwal_angsuran')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_spkndk->jadwal_angsuran === null) {
                $get_spkndk->jadwal_angsuran = array('');
                $enc = array_merge($get_spkndk->jadwal_angsuran, array($name));
            } else {
                $enc = json_decode($get_spkndk->jadwal_angsuran);

                array_push($enc, $name);
            }
            $jadwal_angsuran = $enc;
        } else {
            $jadwal_angsuran = $get_spkndk->jadwal_angsuran;
        }

        if ($files = $req->file('personal_guarantee')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_spkndk->personal_guarantee === null) {
                $get_spkndk->personal_guarantee = array('');
                $enc = array_merge($get_spkndk->personal_guarantee, array($name));
            } else {
                $enc = json_decode($get_spkndk->personal_guarantee);

                array_push($enc, $name);
            }
            $personal_guarantee = $enc;
        } else {
            $personal_guarantee = $get_spkndk->personal_guarantee;
        }

        if ($files = $req->file('hold_dana')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_spkndk->hold_dana === null) {
                $get_spkndk->hold_dana = array('');
                $enc = array_merge($get_spkndk->hold_dana, array($name));
            } else {
                $enc = json_decode($get_spkndk->hold_dana);

                array_push($enc, $name);
            }
            $hold_dana = $enc;
        } else {
            $hold_dana = $get_spkndk->hold_dana;
        }

        if ($files = $req->file('surat_transfer')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_spkndk->surat_transfer === null) {
                $get_spkndk->surat_transfer = array('');
                $enc = array_merge($get_spkndk->surat_transfer, array($name));
            } else {
                $enc = json_decode($get_spkndk->surat_transfer);

                array_push($enc, $name);
            }
            $surat_transfer = $enc;
        } else {
            $surat_transfer = $get_spkndk->surat_transfer;
        }

        if ($files = $req->file('keabsahan_data')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_spkndk->keabsahan_data === null) {
                $get_spkndk->keabsahan_data = array('');
                $enc = array_merge($get_spkndk->keabsahan_data, array($name));
            } else {
                $enc = json_decode($get_spkndk->keabsahan_data);

                array_push($enc, $name);
            }
            $keabsahan_data = $enc;
        } else {
            $keabsahan_data = $get_spkndk->keabsahan_data;
        }

        if ($files = $req->file('sp_beda_jt_tempo')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_spkndk->sp_beda_jt_tempo === null) {
                $get_spkndk->sp_beda_jt_tempo = array('');
                $enc = array_merge($get_spkndk->sp_beda_jt_tempo, array($name));
            } else {
                $enc = json_decode($get_spkndk->sp_beda_jt_tempo);

                array_push($enc, $name);
            }
            $sp_beda_jt_tempo = $enc;
        } else {
            $sp_beda_jt_tempo = $get_spkndk->sp_beda_jt_tempo;
        }

        if ($files = $req->file('sp_authentic')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_spkndk->sp_authentic === null) {
                $get_spkndk->sp_authentic = array('');
                $enc = array_merge($get_spkndk->sp_authentic, array($name));
            } else {
                $enc = json_decode($get_spkndk->sp_authentic);

                array_push($enc, $name);
            }
            $sp_authentic = $enc;
        } else {
            $sp_authentic = $get_spkndk->sp_authentic;
        }

        if ($files = $req->file('sp_penyerahan_jaminan')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_spkndk->sp_penyerahan_jaminan === null) {
                $get_spkndk->sp_penyerahan_jaminan = array('');
                $enc = array_merge($get_spkndk->sp_penyerahan_jaminan, array($name));
            } else {
                $enc = json_decode($get_spkndk->sp_penyerahan_jaminan);

                array_push($enc, $name);
            }
            $sp_penyerahan_jaminan = $enc;
        } else {
            $sp_penyerahan_jaminan = $get_spkndk->sp_penyerahan_jaminan;
        }

        if ($files = $req->file('surat_aksep')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_spkndk->surat_aksep === null) {
                $get_spkndk->surat_aksep = array('');
                $enc = array_merge($get_spkndk->surat_aksep, array($name));
            } else {
                $enc = json_decode($get_spkndk->surat_aksep);

                array_push($enc, $name);
            }
            $surat_aksep = $enc;
        } else {
            $surat_aksep = $get_spkndk->surat_aksep;
        }

        if ($files = $req->file('tt_uang')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_spkndk->tt_uang === null) {
                $get_spkndk->tt_uang = array('');
                $enc = array_merge($get_spkndk->tt_uang, array($name));
            } else {
                $enc = json_decode($get_spkndk->tt_uang);

                array_push($enc, $name);
            }
            $tt_uang = $enc;
        } else {
            $tt_uang = $get_spkndk->tt_uang;
        }

        if ($files = $req->file('sp_pendebetan_rekening')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_spkndk->sp_pendebetan_rekening === null) {
                $get_spkndk->sp_pendebetan_rekening = array('');
                $enc = array_merge($get_spkndk->sp_pendebetan_rekening, array($name));
            } else {
                $enc = json_decode($get_spkndk->sp_pendebetan_rekening);

                array_push($enc, $name);
            }
            $sp_pendebetan_rekening = $enc;
        } else {
            $sp_pendebetan_rekening = $get_spkndk->sp_pendebetan_rekening;
        }

        if ($files = $req->file('sp_plang')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_spkndk->sp_plang === null) {
                $get_spkndk->sp_plang = array('');
                $enc = array_merge($get_spkndk->sp_plang, array($name));
            } else {
                $enc = json_decode($get_spkndk->sp_plang);

                array_push($enc, $name);
            }
            $sp_plang = $enc;
        } else {
            $sp_plang = $get_spkndk->sp_plang;
        }

        if ($files = $req->file('hal_penting')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_spkndk->hal_penting === null) {
                $get_spkndk->hal_penting = array('');
                $enc = array_merge($get_spkndk->hal_penting, array($name));
            } else {
                $enc = json_decode($get_spkndk->hal_penting);

                array_push($enc, $name);
            }
            $hal_penting = $enc;
        } else {
            $hal_penting = $get_spkndk->hal_penting;
        }

        if ($files = $req->file('restruktur_bunga_denda')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_spkndk->restruktur_bunga_denda === null) {
                $get_spkndk->restruktur_bunga_denda = array('');
                $enc = array_merge($get_spkndk->restruktur_bunga_denda, array($name));
            } else {
                $enc = json_decode($get_spkndk->restruktur_bunga_denda);

                array_push($enc, $name);
            }
            $restruktur_bunga_denda = $enc;
        } else {
            $restruktur_bunga_denda = $get_spkndk->restruktur_bunga_denda;
        }

        if ($files = $req->file('spajk_spa_fpk')) {
            $path = '/' . $subs_thn . '/' . $subs_bln . '/' . $subs_rekcab . '/' . $no_kontrak . '/';
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $arrayPath[] = Helper::uploadImgWebtool($path, $file, $name);
            }

            if ($get_spkndk->spajk_spa_fpk === null) {
                $get_spkndk->spajk_spa_fpk = array('');
                $enc = array_merge($get_spkndk->spajk_spa_fpk, array($name));
            } else {
                $enc = json_decode($get_spkndk->spajk_spa_fpk);

                array_push($enc, $name);
            }
            $spajk_spa_fpk = $enc;
        } else {
            $spajk_spa_fpk = $get_spkndk->spajk_spa_fpk;
        }


        ######################################################################################


        $nas = array(
            'ktp' => $ktp,
            'npwp' => $npwp,
            'kk' => $kk,
            'domisili' => $domisili,
            'surat_nikah' => $surat_nikah,
            'surat_cerai' => $surat_cerai,
            'surat_lahir' => $surat_lahir,
            'surat_kematian' => $surat_kematian,
            'skd' => $skd,
            'slip_gaji' => $slip_gaji,
            'take_over' => $take_over,
            'sk_kerja' => $skk,
            'sk_usaha' => $sku,
            'rek_koran' => $rek_koran,
            'tdp' => $tdp,
            'bon_usaha' => $bon_usaha,
            'verifikasi' => empty($req->input('verifikasi_nasabah')) ? $get_nas->verifikasi : $req->input('verifikasi_nasabah'),
            'notes' => empty($req->input('notes_nasabah')) ? $get_nas->notes : $req->input('notes_nasabah')
        );

        $permohonan = array(
            //  'no_rekening' => $no_kontrak,
            'aplikasi'   => $aplikasi,
            'denah_lokasi'   => $denah_lokasi,
            'checklist_kelengkapan'   => $checklist_kelengkapan,
            'verifikasi' => empty($req->input('verifikasi_permohonan')) ? $get_kre->verifikasi : $req->input('verifikasi_permohonan'),
            'notes' => empty($req->input('notes_permohonan')) ? $get_kre->notes : $req->input('notes_permohonan')
        );

        $pengajuan_bi = array(
            'pengajuan_bi' => $pengajuan_bi,
            'persetujuan' => $persetujuan,
            'hasil' => $hasil,
            'verifikasi' => empty($req->input('verifikasi_bi')) ? $get_bi_pengajuan->verifikasi : $req->input('verifikasi_bi'),
            'notes' => empty($req->input('notes_bi')) ? $get_bi_pengajuan->notes : $req->input('notes_bi')
        );

        $creditanalist = array(
            'memo_ao' => $memo_ao,
            'memo_ca' => $memo_ca,
            'offering_letter' => $offering_letter,
            'penilaian_jaminan' => $penilaian_jaminan,
            'cheklist_survey' => $cheklist_survey,
            'persetujuan_kredit' => $persetujuan_kredit,
            'verifikasi' => empty($req->input('verifikasi_ca')) ? $get_caa->verifikasi : $req->input('verifikasi_ca'),
            'notes' => empty($req->input('notes_ca')) ? $get_caa->notes : $req->input('notes_ca')
        );

        $foto = array(
            'ft_jaminan' => $ft_jaminan,
            'ft_pengikatan' => $ft_pengikatan,
            'ft_domisili' => $ft_domisili,
            'ft_usaha'  => $ft_usaha,
            'verifikasi' => empty($req->input('verifikasi_foto')) ? $get_foto->verifikasi : $req->input('verifikasi_foto'),
            'notes' => empty($req->input('notes_foto')) ? $get_foto->notes : $req->input('notes_foto')
        );

        $jaminan = array(
            'sertifikat'   => $sertifikat,
            'skmht'   => $skmht,
            'apht'   => $apht,
            'cabut_roya'   => $cabut_roya,
            'sht'   => $sht,
            'pbb'   => $pbb,
            'imb'   => $imb,
            'ajb'   => $ajb,
            'bpkb'   => $bpkb,
            'ahli_waris'   => $ahli_waris,
            'pengakuan_hutang'   => $pengakuan_hutang,
            'akta_pengakuan_hak_bersama'   => $akta_pengakuan_hak_bersama,
            'adendum'   => $adendum,
            'fidusia'   => $fidusia,
            'verifikasi' => empty($req->input('verifikasi_jaminan')) ? $get_jaminan->verifikasi : $req->input('verifikasi_jaminan'),
            'notes' => empty($req->input('notes_jaminan')) ? $get_jaminan->notes : $req->input('notes_jaminan')
        );

        $legal = array(
            'pengajuan_lpdk' => $pengajuan_lpdk,
            'lpdk' => $lpdk,
            'cheklist_pengikatan' => $cheklist_pengikatan,
            'order_pengikatan'  => $order_pengikatan,
            'verifikasi' => empty($req->input('verifikasi_legal')) ? $get_legal->verifikasi : $req->input('verifikasi_legal'),
            'notes' => empty($req->input('notes_legal')) ? $get_legal->notes : $req->input('notes_legal')
        );

        $asset = array(
            'ra_tanda_terima' => $ra_tanda_terima,
            'ra_surat_kuasa' => $ra_surat_kuasa,
            'ra_identitas_pengambilan' => $ra_identitas_pengambilan,
            'ra_lainnya'  => $ra_lainnya,
            'ra_serah_terima'  => $ra_serah_terima,
            'verifikasi' => empty($req->input('verifikasi_asset')) ? $get_asset->verifikasi : $req->input('verifikasi_asset'),
            'notes' => empty($req->input('notes_asset')) ? $get_asset->notes : $req->input('notes_asset')
        );

        $spkndk = array(
            'spk_ndk'   => $spk_ndk,
            'asuransi'   => $asuransi,
            'sp_no_imb'   => $sp_no_imb,
            'jadwal_angsuran'   => $jadwal_angsuran,
            'personal_guarantee'   => $personal_guarantee,
            'hold_dana'   => $hold_dana,
            'surat_transfer'   => $surat_transfer,
            'keabsahan_data'   => $keabsahan_data,
            'sp_beda_jt_tempo'   => $sp_beda_jt_tempo,
            'sp_authentic'   => $sp_authentic,
            'sp_penyerahan_jaminan'   => $sp_penyerahan_jaminan,
            'surat_aksep'   => $surat_aksep,
            'tt_uang'   => $tt_uang,
            'sp_pendebetan_rekening'   => $sp_pendebetan_rekening,
            'sp_plang'   => $sp_plang,
            'hal_penting'   => $hal_penting,
            'restruktur_bunga_denda'   => $restruktur_bunga_denda,
            'spajk_spa_fpk'   => $spajk_spa_fpk,
            'verifikasi' => empty($req->input('verifikasi_spkndk')) ? $get_spkndk->verifikasi : $req->input('verifikasi_spkndk'),
            'notes' => empty($req->input('notes_spkndk')) ? $get_spkndk->notes : $req->input('notes_spkndk')
        );


        $get_empty_verif = array(
            $req->input('verifikasi_nasabah'), $req->input('verifikasi_permohonan'), $req->input('verifikasi_bi'), $req->input('verifikasi_ca'), $req->input('verifikasi_foto'), $req->input('verifikasi_jaminan'), $req->input('verifikasi_legal'), $req->input('verifikasi_asset'), $req->input('verifikasi_spkndk')

        );

        //   dd(!array_filter($get_empty_verif));
        $data_nas =  EfillingNasabah::where('no_rekening', $no_kontrak)->update($nas);
        $data_per =  EfillingPermohonan::where('no_rekening', $no_kontrak)->update($permohonan);
        //   $data_bichecking = Bi_checking::where('no_rekening', $no_kontrak)->update($bichecking);
        $data_pengajuan_bi = Efilling_bi::where('no_rekening', $no_kontrak)->update($pengajuan_bi);
        $data_creditanalist = Efilling_ca::where('no_rekening', $no_kontrak)->update($creditanalist);
        $data_foto = Efilling_foto::where('no_rekening', $no_kontrak)->update($foto);
        $data_jaminan = EfillingJaminan::where('no_rekening', $no_kontrak)->update($jaminan);
        $data_legal = Efilling_legal::where('no_rekening', $no_kontrak)->update($legal);
        $data_asset = Efilling_asset::where('no_rekening', $no_kontrak)->update($asset);
        $data_spkndk = Efilling_spkndk::where('no_rekening', $no_kontrak)->update($spkndk);

        $get_status_verif = DB::connection('centro')->select("SELECT * FROM view_verifikasi_efilling WHERE 2 IN(verif_bi,verif_ca,verif_foto,verif_jaminan,verif_legal,verif_nasabah,verif_permohonan_kredit,verif_ra,verif_spk) AND no_rekening = '$no_kontrak'");

        //    dd($get_status_verif);
        $efilling_updt = array(
            "user_id" => empty($req->input('user_id')) ? $get_ef->user_id : $req->input('user_id'),
            "user_verif" => empty($req->input('user_verif')) ? $get_ef->user_verif : $req->input('user_verif'),
            "tgl_buat" => empty($req->input('tgl_buat')) ? $get_ef->tgl_buat : $req->input('tgl_buat'),
            "tgl_verif" => empty($req->input('tgl_verif')) ? $get_ef->tgl_verif : $req->input('tgl_verif'),
            "tgl_update" => Carbon::now(),
            "status_dokument" => empty($req->input('status_dokument')) ? $get_ef->status_dokument : $req->input('status_dokument'),
            "status_verif" => $get_ef->status_verif
        );

        if ($get_ef->status_verif === "2" && !array_filter($get_empty_verif)) {
            $efilling_updt = array(
                "user_id" => empty($req->input('user_id')) ? $get_ef->user_id : $req->input('user_id'),
                "user_verif" => empty($req->input('user_verif')) ? $get_ef->user_verif : $req->input('user_verif'),
                "tgl_buat" => empty($req->input('tgl_buat')) ? $get_ef->tgl_buat : $req->input('tgl_buat'),
                "tgl_verif" => empty($req->input('tgl_verif')) ? $get_ef->tgl_verif : $req->input('tgl_verif'),
                "tgl_update" => Carbon::now(),
                "status_dokument" => empty($req->input('status_dokument')) ? $get_ef->status_dokument : $req->input('status_dokument'),
                "status_verif" => "3"
            );
        } elseif (!array_filter($get_empty_verif)) {
            $efilling_updt = array(
                "user_id" => empty($req->input('user_id')) ? $get_ef->user_id : $req->input('user_id'),
                "user_verif" => empty($req->input('user_verif')) ? $get_ef->user_verif : $req->input('user_verif'),
                "tgl_buat" => empty($req->input('tgl_buat')) ? $get_ef->tgl_buat : $req->input('tgl_buat'),
                "tgl_verif" => empty($req->input('tgl_verif')) ? $get_ef->tgl_verif : $req->input('tgl_verif'),
                "tgl_update" => Carbon::now(),
                "status_dokument" => empty($req->input('status_dokument')) ? $get_ef->status_dokument : $req->input('status_dokument'),
                "status_verif" => $get_ef->status_verif
            );
        } elseif ($get_status_verif) {
            $efilling_updt = array(
                "user_id" => empty($req->input('user_id')) ? $get_ef->user_id : $req->input('user_id'),
                "user_verif" => empty($req->input('user_verif')) ? $get_ef->user_verif : $req->input('user_verif'),
                "tgl_buat" => empty($req->input('tgl_buat')) ? $get_ef->tgl_buat : $req->input('tgl_buat'),
                "tgl_verif" => empty($req->input('tgl_verif')) ? $get_ef->tgl_verif : $req->input('tgl_verif'),
                "tgl_update" => Carbon::now(),
                "status_dokument" => empty($req->input('status_dokument')) ? $get_ef->status_dokument : $req->input('status_dokument'),
                "status_verif" => "2"
            );
        } elseif (empty($get_status_verif)) {
            $efilling_updt = array(
                "user_id" => empty($req->input('user_id')) ? $get_ef->user_id : $req->input('user_id'),
                "user_verif" => empty($req->input('user_verif')) ? $get_ef->user_verif : $req->input('user_verif'),
                "tgl_buat" => empty($req->input('tgl_buat')) ? $get_ef->tgl_buat : $req->input('tgl_buat'),
                "tgl_verif" => empty($req->input('tgl_verif')) ? $get_ef->tgl_verif : $req->input('tgl_verif'),
                "tgl_update" => Carbon::now(),
                "status_dokument" => empty($req->input('status_dokument')) ? $get_ef->status_dokument : $req->input('status_dokument'),
                "status_verif" => "1"
            );
        } else {
            $efilling_updt = array(
                "user_id" => empty($req->input('user_id')) ? $get_ef->user_id : $req->input('user_id'),
                "user_verif" => empty($req->input('user_verif')) ? $get_ef->user_verif : $req->input('user_verif'),
                "tgl_buat" => empty($req->input('tgl_buat')) ? $get_ef->tgl_buat : $req->input('tgl_buat'),
                "tgl_verif" => empty($req->input('tgl_verif')) ? $get_ef->tgl_verif : $req->input('tgl_verif'),
                "tgl_update" => Carbon::now(),
                "status_dokument" => empty($req->input('status_dokument')) ? $get_ef->status_dokument : $req->input('status_dokument'),
                "status_verif" => $get_ef->status_verif
            );
        }

        $data_efilling =  Efilling::where('no_rekening', $no_kontrak)->update($efilling_updt);
        try {
            return response()->json([
                "code" => 200,
                "message" => "success",
                "data" => array($efilling_updt, $nas, $permohonan, $pengajuan_bi, $creditanalist, $foto, $jaminan, $legal, $asset, $spkndk)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code'    => 501,
                'status'  => 'error',
                'message' => $e
            ], 501);
        }
    }

    public function showViewHeaderEfilling(Request $req)
    {

        $kode_kantor = $req->input('kode_kantor');
        $baki_debet = $req->input('baki_debet');
        $status_verifikasi = $req->input('status_verifikasi');
        $no_rekening = $req->input('no_rekening');



        $db = DB::connection('centro')->table('view_efiling_header')->orWhere('kode_kantor', $kode_kantor)->orWhere('no_rekening', $no_rekening)->orWhere('baki_debet', $baki_debet)->orWhere('status_verifikasi', $status_verifikasi)->orWhere('no_rekening', 'LIKE', '%{$no_rekening%}')->get();
        // $spkndk = Efilling_spkndk::where('no_rekening', $no_kontrak)->first();
        //  $get = DB::connection('centro')->table('efiling')->where('no_rekening', $no_kontrak);
        // dd($get);
        //   dd($db);
        return response()->json([
            'data'   => $db
        ]);
    }

    public function show($no_kontrak)
    {

        $efilling = Efilling::where('no_rekening', $no_kontrak)->first();
        $bichecking = Bi_checking::where('no_rekening', $no_kontrak)->first();
        $effiling_bichecking = Efilling_bi::where('no_rekening', $no_kontrak)->first();
        $effiling_ca = Efilling_ca::where('no_rekening', $no_kontrak)->first();
        $efilling_legal = Efilling_legal::where('no_rekening', $no_kontrak)->first();
        $efillingnasabah = EfillingNasabah::where('no_rekening', $no_kontrak)->first();
        $efillingjaminan = EfillingJaminan::where('no_rekening', $no_kontrak)->first();
        $efillingpermohonan = EfillingPermohonan::where('no_rekening', $no_kontrak)->first();
        $efillingfoto = Efilling_foto::where('no_rekening', $no_kontrak)->first();
        $efilling_aset = Efilling_asset::where('no_rekening', $no_kontrak)->first();
        $spkndk = Efilling_spkndk::where('no_rekening', $no_kontrak)->first();
        $db = DB::connection('centro')->table('view_efiling_header')->where('no_rekening', $no_kontrak)->first();
        //  $get = DB::connection('centro')->table('efiling')->where('no_rekening', $no_kontrak);
        // dd($get);
        return response()->json([
            'data'   => array("header_efiling" => $db, "efilling" => $efilling, "bichecking" => $bichecking, "efilling_bichecking" => $effiling_bichecking, "efilling_ca" => $effiling_ca, "efilling_legal" => $efilling_legal, "efilling_nasabah" => $efillingnasabah, "efilling_jaminan" => $efillingjaminan, "efilling_foto" => $efillingfoto, "efilling_permohonan" => $efillingpermohonan, "efilling_aset" => $efilling_aset, "efilling_spkndk" => $spkndk)
        ], 200);
    }


    public function delete($no_rekening)
    {
    }
}
