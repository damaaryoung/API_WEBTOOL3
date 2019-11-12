<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use DB;

class PinjamanController extends BaseController
{
    public function index() {
        $query = DB::connection('web')->table('fasilitas_pinjaman')->get();

        dd($query);
    }

    public function store(Request $req) {
        $query = DB::connection('web')->table('fasilitas_pinjaman')->insert([
            'nomor_so'        => $req->input('nomor_so'),
            'jenis_pinjaman'  => $req->input('jenis_pinjaman'),
            'tujuan_pinjaman' => $req->input('tujuan_pinjaman'),
            'plafon'          => $req->input('plafon'),
            'tenor'           => $req->input('tenor')
        ]);

        dd($query);
    }

    public function plus() {
        $query = DB::connection('web')->table('fasilitas_pinjaman')->get();

        dd($query);
    }
}
