<?php

namespace App\Models\Pengajuan\CA;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class TabDebt extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'web';

    protected $table = 'log_tabungan_debt';
    protected $primaryKey = 'id';

    protected $fillable = [
        'no_rekening', 'nama_bank', 'tujuan_pembukaan_rek', 'penghasilan_per_tahun', 'sumber_penghasilan', 'pemasukan_per_bulan', 'frek_trans_pemasukan', 'pengeluaran_per_bulan', 'frek_trans_pengeluaran', 'sumber_dana_setoran', 'tujuan_pengeluaran_dana'
    ];

    protected $casts = [
        'penghasilan_per_tahun' => 'integer',
        'pemasukan_per_bulan'   => 'integer',
        'frek_trans_pemasukan'  => 'integer',
        'pengeluaran_per_bulan' => 'integer',
        'frek_trans_pengeluaran'=> 'integer'
    ];

    public $timestamps = false;
}
