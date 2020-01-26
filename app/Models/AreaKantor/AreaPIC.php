<?php

namespace App\Models\AreaKantor;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

use App\Models\AreaKantor\Area;
use App\Models\AreaKantor\Cabang;

use App\Models\Wilayah\Provinsi;
use App\Models\Wilayah\Kabupaten;
use App\Models\Wilayah\Kecamatan;
use App\Models\Wilayah\Kelurahan;

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
        return $this->belongsTo(Area::class, 'id_mk_area')->select(['id', 'nama'])
            ->withDefault(function () {
                return new Area();
            });
    }

    public function cabang(){
        return $this->belongsTo(Cabang::class, 'id_mk_cabang')->select(['id','nama'])
            ->withDefault(function () {
                return new Cabang();
            });
    }

    public function prov(){
        return $this->belongsTo(Provinsi::class, 'id_provinsi')->select(['id', 'nama'])
            ->withDefault(function () {
                return new Provinsi();
            });
    }

    public function kab(){
        return $this->belongsTo(Kabupaten::class, 'id_kabupaten')->select(['id', 'nama'])
        ->withDefault(function () {
                return new Kabupaten();
            });
    }

    public function kec(){
        return $this->belongsTo(Kecamatan::class, 'id_kecamatan')->select(['id', 'nama'])
            ->withDefault(function () {
                return new Kecamatan();
            });
    }

    public function kel(){
        return $this->belongsTo(Kelurahan::class, 'id_kelurahan')->select(['id', 'nama', 'kode_pos'])
            ->withDefault(function () {
                return new Kelurahan();
            });
    }
}
