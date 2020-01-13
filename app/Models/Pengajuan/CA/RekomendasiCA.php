<?php

namespace App\Models\Pengajuan\CA;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class RekomendasiCA extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'web';

    protected $table = 'recom_ca';
    protected $primaryKey = 'id';

    protected $fillable = [
       'produk', 'plafon_kredit', 'jangka_waktu', 'suku_bunga', 'pembayaran_bunga', 'akad_kredit', 'ikatan_agunan', 'biaya_provisi', 'biaya_administrasi', 'biaya_credit_checking', 'id_asuransi_jiwa', 'id_asuransi_jaminan', 'notaris', 'biaya_tabungan'
    ];

    public $timestamps = false;

    public function as_jiwa(){
        return $this->belongsTo('App\Models\Pengajuan\CA\AsuransiJiwa', 'id_asuransi_jiwa');
    }

    public function as_jaminan(){
        return $this->belongsTo('App\Models\Pengajuan\CA\AsuransiJaminan', 'id_asuransi_jaminan');
    }
}
