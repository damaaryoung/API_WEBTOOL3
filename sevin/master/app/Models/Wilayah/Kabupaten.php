<?php

namespace App\Models\Wilayah;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class Kabupaten extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    protected $connection = 'web';

    protected $table = 'master_kabupaten';
    protected $primaryKey = 'id';

    protected $fillable = [
       'nama', 'id_provinsi', 'flg_aktif'
    ];

    public $timestamps = false;

    public function prov(){
        return $this->belongsTo('App\Models\Wilayah\Provinsi', 'id_provinsi')->select(['id', 'nama']);
    }

    public function kec(){
        return $this->hasMany('App\Models\Wilayah\Kecamatan');
    }

    public function debt(){
        return $this->hasMany('App\Models\Pengajuan\Debitur');
    }

    public function pas(){
        return $this->hasMany('App\Models\Pengajuan\Pasangan');
    }

    public function penj(){
        return $this->hasMany('App\Models\Pengajuan\Penjamin');
    }

    public function tanah(){
        return $this->hasMany('App\Models\Pengajuan\AgunanTanah');
    }
}
