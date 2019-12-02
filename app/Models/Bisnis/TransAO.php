<?php

namespace App\Models\Bisnis;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class TransAO extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'web';

    protected $table = 'recomendasi_ao';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nomor_ao', 'id_trans_so', 'produk', 'plafon_kredit', 'jangka_waktu', 'suku_bunga', 'pembayaran_bunga', 'akad_kredit', 'ikatan_agunan', 'analisa_ao', 'biaya_provinsi', 'biaya_administrasi', 'biaya_credit_checking', 'biaya_tabungan', 'flg_aktif'
    ];

    //relasi one to many (Saya memiliki banyak anggota di model .....)
//     public function transo(){
//         return $this->hasMany('App\Models\Bisnis\TansSo', 'id_asal_data');
//     }
}
