<?php

namespace App\Models\CC;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class TransPenjamin extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'web';

    protected $table = 'transaksi_pinjaman';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_calon_debitur', 'jenis_pinjaman', 'produk', 'plafon', 'tenor', 'suku_bunga', 'pby_bunga_tiap_bulan', 'akad_kredit', 'akad_agunan', 'ikatan_agunan', 'analisa_ao', 'biaya_prov', 'biaya_admin', 'biaya_cek_kredit', 'biaya_tabungan', 'lamp_ijin_ideb'
    ];

    public function debt(){
        return $this->belongsTo('App\Models\CC\Debitur', 'id_calon_debitur');
    }
}
