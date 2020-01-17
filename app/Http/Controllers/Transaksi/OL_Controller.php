<?php

namespace App\Http\Controllers\Transaksi;

use Laravel\Lumen\Routing\Controller as BaseController;
// use App\Models\Pengajuan\CA\RekomendasiPinjaman;
// use App\Models\Pengajuan\CA\AsuransiJaminan;
use App\Http\Controllers\Controller as Helper;
// use App\Http\Requests\Transaksi\BlankRequest;
// use App\Models\Pengajuan\CA\RingkasanAnalisa;
// use App\Models\Pengajuan\CA\RekomendasiCA;
// use App\Models\Pengajuan\CA\AsuransiJiwa;
// use App\Models\Pengajuan\AO\KapBulanan;
// use App\Models\Pengajuan\CA\MutasiBank;
use Illuminate\Support\Facades\File;
// use App\Models\Pengajuan\CA\TabDebt;
// use App\Models\Pengajuan\CA\InfoACC;
use App\Models\Transaksi\TransCA;
// use App\Models\Transaksi\TransAO;
// use App\Models\Transaksi\TransSO;
use App\Models\AreaKantor\JPIC;
use App\Models\AreaKantor\PIC;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\User;
use Carbon\Carbon;
use DB;

class OL_Controller extends BaseController
{
    public function show($id){}
}
