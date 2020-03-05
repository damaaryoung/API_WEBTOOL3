<?php

namespace App\Models\Wilayah;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

use App\Models\Wilayah\Provinsi;
use App\Models\Wilayah\Kecamatan;
use App\Models\Pengajuan\SO\Debitur;
use App\Models\Pengajuan\SO\Pasangan;
use App\Models\Pengajuan\SO\Penjamin;
use App\Models\Pengajuan\AO\AgunanTanah;

class Kabupaten extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    protected $connection = 'web';

    protected $table = 'master_kabupaten';
    protected $primaryKey = 'id';

    protected $fillable = [
       'nama', 'id_provinsi', 'flg_aktif'
    ];

    protected $casts = [
        'flg_aktif' => 'boolean'
    ];

    public $timestamps = false;

    public function prov(){
        return $this->belongsTo(Provinsi::class, 'id_provinsi')->select(['id', 'nama'])
            ->withDefault(function () {
                return new Provinsi();
            });
    }

    public function kec(){
        return $this->hasMany(Kecamatan::class)
            ->withDefault(function () {
                return new Kecamatan();
            });
    }

    public function debt(){
        return $this->hasMany(Debitur::class)
            ->withDefault(function () {
                return new Debitur();
            });
    }

    public function pas(){
        return $this->hasMany(Pasangan::class)
            ->withDefault(function () {
                return new Pasangan();
            });
    }

    public function penj(){
        return $this->hasMany(Penjamin::class)
            ->withDefault(function () {
                return new Penjamin();
            });
    }

    public function tanah(){
        return $this->hasMany(AgunanTanah::class)
            ->withDefault(function () {
                return new AgunanTanah();
            });
    }
}
