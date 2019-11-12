<?php

namespace App\Models\CC;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class Debitur extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'web';

    protected $table = 'calon_debitur';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nama_lengkap', 'jenis_kelamin', 'ibu_kandung', 'status_nikah', 'no_ktp', 'no_ktp_kk', 'no_kk', 'no_npwp', 'tempat_lahir', 'tgl_lahir', 'agama', 'alamat_ktp', 'rt_ktp', 'rw_ktp', 'id_provinsi_ktp', 'id_kabupaten_ktp', 'id_kecamatan_ktp', 'id_kelurahan_ktp', 'alamat_domisili', 'rt_domisili', 'rw_domisili', 'id_provinsi_domisili', 'id_kabupaten_domisili', 'id_kecamatan_domisili', 'id_kelurahan_domisili', 'pendidikan_terakhir', 'jumlah_tanggungan', 'no_telp', 'alamat_surat', 'nama_anak1', 'tgl_lahir_anak1', 'nama_anak2', 'tgl_lahir_anak2', 'lamp_surat_cerai', 'pekerjaan', 'lamp_buku_tabungan', 'lamp_kk'
    ];


    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    // protected $hidden = [

    // ];
}
