<?php

namespace App\Models\Pengajuan\AO;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class AgunanKendaraan extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'web';

    protected $table = 'agunan_kendaraan';
    protected $primaryKey = 'id';

    protected $fillable = [
        'no_bpkb', 'nama_pemilik', 'alamat_pemilik', 'merk', 'jenis', 'no_rangka', 'no_mesin', 'warna', 'tahun', 'no_polisi', 'no_stnk', 'tgl_kadaluarsa_pajak', 'tgl_kadaluarsa_stnk', 'no_faktur', 'lamp_agunan_depan', 'lamp_agunan_kanan', 'lamp_agunan_kiri', 'lamp_agunan_belakang', 'lamp_agunan_dalam'
    ];

    protected $casts = [
        'tahun' => 'integer'
    ];

    public $timestamps = false;
}
