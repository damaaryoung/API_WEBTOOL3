<?php

namespace App\Models\CC;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class Pasangan extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    protected $connection = 'web';
    protected $table = 'pasangan_calon_debitur';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_calon_debitur', 'nama_lengkap', 'nama_ibu_kandung', 'gelar_keagamaan', 'gelar_pendidikan', 'jenis_kelamin', 'no_ktp', 'no_ktp_kk', 'no_npwp', 'tempat_lahir', 'tgl_lahir', 'alamat_ktp', 'no_telp', 'pekerjaan', 'posisi_pekerjaan', 'lamp_ktp', 'lamp_buku_nikah', 'ver_ktp', 'ver_akta_nikah', 'validasi', 'flg_aktif'
    ];
}
