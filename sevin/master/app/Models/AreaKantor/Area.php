<?php

namespace App\Models\AreaKantor;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class Area extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'web';

    protected $table = 'mk_area';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nama', 'id_provinsi', 'id_kabupaten', 'flg_aktif'
    ];

    public function prov(){
        return $this->belongsTo('App\Models\Wilayah\Provinsi', 'id_provinsi')->select(['id', 'nama']);
    }

    public function kab(){
        return $this->belongsTo('App\Models\Wilayah\Kabupaten', 'id_kabupaten')->select(['id', 'nama']);
    }

    public function kec(){
        return $this->belongsTo('App\Models\Wilayah\Kecamatan', 'id_kecamatan')->select(['id', 'nama']);
    }

    public function kel(){
        return $this->belongsTo('App\Models\Wilayah\Kelurahan', 'id_kelurahan')->select(['id', 'nama', 'kode_pos']);
    }
}
