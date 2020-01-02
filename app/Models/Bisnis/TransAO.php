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

    protected $table = 'trans_ao';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nomor_ao', 'id_trans_so', 'user_id', 'id_pic', 'nama_ao', 'produk', 'plafon_kredit', 'jangka_waktu', 'suku_bunga', 'pembayaran_bunga', 'akad_kredit', 'ikatan_agunan', 'analisa_ao', 'biaya_provisi', 'biaya_administrasi', 'biaya_credit_checking', 'biaya_tabungan', 'catatan_ao', 'status_ao', 'flg_aktif'
    ];

    public function so(){
        return $this->belongsTo('App\Models\Bisnis\TransSo', 'id_trans_so');
    }
}
