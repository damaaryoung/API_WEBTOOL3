<?php

namespace App\Models\Pengajuan\AO;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class KapBulanan extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'web';

    protected $table = 'kapasitas_bulanan';
    protected $primaryKey = 'id';

    protected $fillable = [
       'pemasukan_cadebt', 'pemasukan_pasangan', 'pemasukan_penjamin', 'biaya_rumah_tangga', 'biaya_transport', 'biaya_pendidikan', 'biaya_telp_listr_air', 'angsuran', 'biaya_lain', 'total_pemasukan', 'total_pengeluaran', 'penghasilan_bersih'
    ];

    public $timestamps = false;
}
