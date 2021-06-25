<?php

namespace App\Models\Pengajuan\SO;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Support\Carbon;

use App\Models\Wilayah\Provinsi;
use App\Models\Wilayah\Kabupaten;
use App\Models\Wilayah\Kecamatan;
use App\Models\Wilayah\Kelurahan;

class Anak extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'web';

    protected $table = 'nasabah_anak';
    protected $primaryKey = 'anak_id';

    protected $fillable = [
        'nasabah_id', 'nama_anak', 'tgl_lahir_anak'
    ];
    protected $dateFormat = 'd/m/Y';


    protected $casts = [
         'tgl_lahir_anak' => 'date:d-m-Y',
          'updated_at' => 'date:m-d-Y H:i:s'

     ];



    public $timestamps = false;

    // // KTP
    // public function prov_ktp()
    // {
    //     return $this->belongsTo(Provinsi::class, 'id_prov_ktp')->select(['id', 'nama'])
    //         ->withDefault(function () {
    //             return new Provinsi();
    //         });
    // }

    // public function kab_ktp()
    // {
    //     return $this->belongsTo(Kabupaten::class, 'id_kab_ktp')->select(['id', 'nama'])
    //         ->withDefault(function () {
    //             return new Kabupaten();
    //         });
    // }

    // public function kec_ktp()
    // {
    //     return $this->belongsTo(Kecamatan::class, 'id_kec_ktp')->select(['id', 'nama'])
    //         ->withDefault(function () {
    //             return new Kecamatan();
    //         });
    // }

    // public function kel_ktp()
    // {
    //     return $this->belongsTo(Kelurahan::class, 'id_kel_ktp')->select(['id', 'nama', 'kode_pos'])
    //         ->withDefault(function () {
    //             return new Kelurahan();
    //         });
    // }

    // // Domisili
    // public function prov_dom()
    // {
    //     return $this->belongsTo(Provinsi::class, 'id_prov_domisili')->select(['id', 'nama'])
    //         ->withDefault(function () {
    //             return new Provinsi();
    //         });
    // }

    // public function kab_dom()
    // {
    //     return $this->belongsTo(Kabupaten::class, 'id_kab_domisili')->select(['id', 'nama'])
    //         ->withDefault(function () {
    //             return new Kabupaten();
    //         });
    // }

    // public function kec_dom()
    // {
    //     return $this->belongsTo(Kecamatan::class, 'id_kec_domisili')->select(['id', 'nama'])
    //         ->withDefault(function () {
    //             return new Kecamatan();
    //         });
    // }

    // public function kel_dom()
    // {
    //     return $this->belongsTo(Kelurahan::class, 'id_kel_domisili')->select(['id', 'nama', 'kode_pos'])
    //         ->withDefault(function () {
    //             return new Kelurahan();
    //         });
    // }

    // // Tempat Kerja
    // public function prov_kerja()
    // {
    //     return $this->belongsTo(Provinsi::class, 'id_prov_tempat_kerja')->select(['id', 'nama'])
    //         ->withDefault(function () {
    //             return new Provinsi();
    //         });
    // }

    // public function kab_kerja()
    // {
    //     return $this->belongsTo(Kabupaten::class, 'id_kab_tempat_kerja')->select(['id', 'nama'])
    //         ->withDefault(function () {
    //             return new Kabupaten();
    //         });
    // }

    // public function kec_kerja()
    // {
    //     return $this->belongsTo(Kecamatan::class, 'id_kec_tempat_kerja')->select(['id', 'nama'])
    //         ->withDefault(function () {
    //             return new Kecamatan();
    //         });
    // }

    // public function kel_kerja()
    // {
    //     return $this->belongsTo(Kelurahan::class, 'id_kel_tempat_kerja')->select(['id', 'nama', 'kode_pos'])
    //         ->withDefault(function () {
    //             return new Kelurahan();
    //         });
    // }
}
