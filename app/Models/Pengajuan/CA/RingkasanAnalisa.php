<?php

namespace App\Models\Pengajuan\CA;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class RingkasanAnalisa extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'web';

    protected $table = 'ringkasan_analisa_ca';
    protected $primaryKey = 'id';

    protected $fillable = [
        'kuantitatif_ttl_pendapatan', 'kuantitatif_ttl_pengeluaran', 'kuantitatif_pendapatan_bersih', 'kuantitatif_angsuran', 'kuantitatif_ltv', 'kuantitatif_dsr', 'kuantitatif_idir', 'kuantitatif_hasil', 'kualitatif_analisa', 'kualitatif_strenght', 'kualitatif_weakness', 'kualitatif_opportunity', 'kualitatif_threatness'
    ];

    protected $casts = [
        'kuantitatif_ttl_pendapatan'    => 'integer',
        'kuantitatif_ttl_pengeluaran'   => 'integer',
        'kuantitatif_pendapatan_bersih' => 'integer',
        'kuantitatif_angsuran'          => 'integer'
    ];

    public $timestamps = false;
}
