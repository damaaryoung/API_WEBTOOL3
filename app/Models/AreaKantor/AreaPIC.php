<?php

namespace App\Models\AreaKantor;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class AreaPIC extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'web';

    protected $table = 'm_area_pic';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_mk_area', 'id_mk_cabang', 'nama_area_pic', 'id_provinsi', 'id_kabupaten', 'id_kecamatan', 'id_kelurahan', 'flg_aktif'
    ];

    public function area(){
        return $this->belongsTo('App\Models\AreaKantor\Area', 'id_mk_area')->select(['id', 'nama']);
    }

    public function cabang(){
        return $this->belongsTo('App\Models\AreaKantor\Cabang', 'id_mk_cabang')->select(['id','nama']);
    }

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
