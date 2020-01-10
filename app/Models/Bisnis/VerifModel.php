<?php

namespace App\Models\Bisnis;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class VerifModel extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'web';

    protected $table = 'tb_verifikasi';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_trans_so', 'id_trans_ao', 'id_calon_debitur', 'ver_ktp_debt', 'ver_kk_debt', 'ver_akta_cerai_debt', 'ver_akta_kematian_debt', 'ver_rek_tabungan_debt', 'ver_sertifikat_debt', 'ver_sttp_pbb_debt', 'ver_imb_debt', 'ver_ktp_pasangan', 'ver_akta_nikah_pasangan', 'ver_data_penjamin', 'ver_sku_debt', 'ver_pembukuan_usaha_debt', 'catatan'
    ];

    public function debt(){
        return $this->belongsTo('App\Models\Pengajuan\Debitur', 'id_calon_debitur');
    }

    public function so(){
        return $this->belongsTo('App\Models\Bisnis\TransSo', 'id_trans_so');
    }

    public function ao(){
        return $this->belongsTo('App\Models\Bisnis\TransAo', 'id_trans_ao');
    }
}
