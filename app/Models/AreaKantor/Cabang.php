<?php

namespace App\Models\AreaKantor;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class Cabang extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'web';

    protected $table = 'mk_cabang';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_mk_area', 'nama', 'id_provinsi', 'id_kabupaten', 'id_kecamatan', 'id_kelurahan', 'jenis_kantor', 'flg_aktif'
    ];

    public function area(){
        return $this->belongsTo('App\Models\AreaKantor\Area', 'id_mk_area')->select('id', 'nama');
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
