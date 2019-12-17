<?php

namespace App\Models\Wilayah;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class Provinsi extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    protected $connection = 'web';

    protected $table = 'master_provinsi';
    protected $primaryKey = 'id';

    protected $fillable = [
       'nama', 'flg_aktif'
    ];

    public $timestamps = false;

    public function kab(){
        return $this->hasMany('App\Models\Wilayah\Kabupaten');
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
