<?php

namespace App\Models\CC;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class AgunanTanah extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'web';

    protected $table = 'agunan_tanah';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_calon_debitur', 'tipe_lokasi', 'alamat', 'id_kelurahan', 'rt', 'rw', 'luas_tanah', 'luas_bangunan', 'nama_pemilik_sertifikat', 'jenis_sertifikat', 'no_sertifikat', 'tgl_ukur_sertifikat', 'masa_berlaku_shgb', 'lam_imb', 'no_imb', 'njop', 'nop', 'lamp_agunan_depan', 'lamp_agunan_kanan', 'lamp_agunan_kiri', 'lamp_agunan_belakang', 'lamp_agunan_dalam', 'lamp_sertifikat', 'lamp_imb', 'lamp_pbb', 'ver_imb', 'ver_pbb', 'validation'
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
