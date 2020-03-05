<?php

namespace App\Models\Wilayah;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

use App\Models\Wilayah\Kabupaten;
use App\Models\Pengajuan\SO\Debitur;
use App\Models\Pengajuan\SO\Pasangan;
use App\Models\Pengajuan\SO\Penjamin;
use App\Models\Pengajuan\AO\AgunanTanah;

class Provinsi extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    protected $connection = 'web';

    protected $table = 'master_provinsi';
    protected $primaryKey = 'id';

    protected $fillable = [
       'nama', 'flg_aktif'
    ];

    protected $casts = [
        'flg_aktif' => 'boolean'
    ];

    public $timestamps = false;

    public function kab(){
        return $this->hasMany(Kabupaten::class)
            ->withDefault(function () {
                return new Kabupaten();
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
