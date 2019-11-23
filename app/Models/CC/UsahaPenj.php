<?php

namespace App\Models\CC;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class UsahaPenj extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'web';

    protected $table = 'usaha_penjamin';
    protected $primaryKey = 'id';

    protected $fillable = [
       'id_calon_debitur', 'id_penjamin', 'nama_tempat_usaha', 'jenis_usaha', 'alamat_usaha', 'id_provinsi', 'id_kabupaten', 'id_kecamatan', 'id_kelurahan', 'rt', 'rw', 'tgl_mulai_usaha', 'telp_tempat_usaha'
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
