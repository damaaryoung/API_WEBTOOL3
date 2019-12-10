<?php

namespace App\Models\CC;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class PemeriksaanAgunKen extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'web';

    protected $table = 'periksa_agunan_kendaraan';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_calon_debitur', 'id_agunan_kendaraan', 'nama_pengguna', 'status_pengguna', 'jml_roda_kendaraan', 'kondisi_kendaraan', 'keberadaan_kendaraan', 'body', 'interior', 'km', 'modifikasi', 'aksesoris', 'flg_aktif'
    ];

    public function debt(){
        return $this->belongsTo('App\Models\CC\Debitur', 'id_calon_debitur');
    }

    public function kendaraan(){
        return $this->belongsTo('App\Models\CC\AgunanKendaraan', 'id_agunan_kendaraan');
    }
}
