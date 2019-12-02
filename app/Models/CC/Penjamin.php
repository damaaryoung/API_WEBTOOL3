<?php

namespace App\Models\CC;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class Penjamin extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'web';

    protected $table = 'penjamin_calon_debitur';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_calon_debitur', 'nama_ktp', 'nama_ibu_kandung', 'no_ktp', 'no_npwp', 'tempat_lahir', 'tgl_lahir', 'jenis_kelamin', 'alamat_ktp', 'no_telp', 'hubungan_debitur', 'pekerjaan', 'posisi_pekerjaan', 'nama_tempat_kerja', 'jenis_pekerjaan', 'alamat_tempat_kerja', 'id_prov_tempat_kerja', 'id_kab_tempat_kerja', 'id_kec_tempat_kerja', 'id_kelurahan', 'rt_tempat_kerja', 'rw_tempat_kerja', 'tgl_mulai_kerja', 'no_telp_tempat_kerja', 'lamp_ktp', 'lamp_ktp_pasangan', 'lamp_kk', 'lamp_buku_nikah', 'flg_aktif'
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
