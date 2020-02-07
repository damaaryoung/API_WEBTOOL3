<?php

namespace App\Models\Pengajuan\AO;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class PendapatanUsaha extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'web';

    protected $table = 'pendapatan_usaha_cadebt';
    protected $primaryKey = 'id';

    protected $fillable = [
       'pemasukan_tunai', 'pemasukan_kredit', 'biaya_sewa', 'biaya_gaji_pegawai', 'biaya_belanja_brg', 'biaya_telp_listr_air', 'biaya_sampah_kemanan', 'biaya_kirim_barang', 'biaya_hutang_dagang', 'biaya_angsuran', 'biaya_lain_lain', 'total_pemasukan', 'total_pengeluaran', 'laba_usaha'
    ];

    protected $casts = [
        'pemasukan_tunai'      => 'integer',
        'pemasukan_kredit'     => 'integer',
        'biaya_sewa'           => 'integer',
        'biaya_gaji_pegawai'   => 'integer',
        'biaya_belanja_brg'    => 'integer',
        'biaya_telp_listr_air' => 'integer',
        'biaya_sampah_kemanan' => 'integer',
        'biaya_kirim_barang'   => 'integer',
        'biaya_hutang_dagang'  => 'integer',
        'biaya_angsuran'       => 'integer',
        'biaya_lain_lain'      => 'integer',
        'total_pemasukan'      => 'integer',
        'total_pengeluaran'    => 'integer',
        'laba_usaha'           => 'integer'
    ];

    public $timestamps = false;
}
