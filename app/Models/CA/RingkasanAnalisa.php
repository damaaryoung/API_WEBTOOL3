<?php

namespace App\Models\CA;

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
        'id_trans_so', 'kuantitatif_ttl_pendapatan', 'kuantitatif_ttl_pengeluaran', 'kuantitatif_pendapatan', 'kuantitatif_angsuran', 'kuantitatif_ltv', 'kuantitatif_dsr', 'kuantitatif_idir', 'kuantitatif_hasil', 'kualitatif_analisa', 'kualitatif_swot', 'kualitatif_strenght', 'kualitatif_weakness', 'kualitatif_opportunity', 'kualitatif_threatness'
    ];

    public function so(){
        return $this->belongsTo('App\Models\Bisnis\TransSo', 'id_trans_so');
    }
}
