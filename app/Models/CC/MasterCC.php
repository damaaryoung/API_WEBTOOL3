<?php

namespace App\Models\CC;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class MasterCC extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'web';

    protected $table = 'master_cc';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_fasilitas_pinjaman', 'id_calon_debt', 'id_pasangan', 'id_penjamin', 'id_verifikasi', 'id_validasi', 'id_agunan_tanah', 'id_agunan_kendarran', 'id_periksa_agunan_tanah', 'id_periksa_agunan_kendarran', 'id_usaha', 'id_recomendasi_ao'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    // protected $hidden = [

    // ];

    // public $timestamps = false;
}
