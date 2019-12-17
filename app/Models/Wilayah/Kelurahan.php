<?php

namespace App\Models\Wilayah;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class Kelurahan extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    protected $connection = 'web';

    protected $table = 'master_kelurahan';
    protected $primaryKey = 'id';

    protected $fillable = [
       'nama', 'kode_pos', 'id_kecamatan', 'flg_aktif'
    ];

    public $timestamps = false;

    public function kec(){
        return $this->belongsTo('App\Models\Wilayah\Kecamatan', 'id_kecamatan')->select(['id', 'nama']);
    }

    public function debt(){
        return $this->hasMany('App\Models\CC\Debitur');
    }

    public function pas(){
        return $this->hasMany('App\Models\CC\Pasangan');
    }

    public function penj(){
        return $this->hasMany('App\Models\CC\Penjamin');
    }

    public function tanah(){
        return $this->hasMany('App\Models\CC\AgunanTanah');
    }
}
