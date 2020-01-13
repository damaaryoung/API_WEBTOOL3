<?php

namespace App\Models\Transaksi;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class TransCA extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'web';

    protected $table = 'trans_ca';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nomor_ca', 'user_id', 'id_trans_so', 'id_pic', 'id_cabang', 'id_mutasi_bank', 'id_log_tabungan', 'id_info_analisa_cc', 'id_ringkasan_analisa', 'id_recom_ca', 'id_rekomendasi_pinjaman', 'id_asuransi_jiwa', 'id_asuransi_jaminan', 'catatan_ca', 'status_ca'
    ];

    public function pic(){
        return $this->belongsTo('App\Models\AreaKantor\PIC', 'id_pic');
    }

    public function cabang(){
        return $this->belongsTo('App\Models\AreaKantor\Cabang', 'id_cabang');
    }

    public function mutasi(){
        return $this->belongsTo('App\Models\Pengajuan\CA\MutasiBank', 'id_mutasi_bank');
    }

    public function log_tab(){
        return $this->belongsTo('App\Models\Pengajuan\CA\TabDebt', 'id_log_tabungan');
    }

    public function Info_acc(){
        return $this->belongsTo('App\Models\Pengajuan\CA\InfoACC', 'id_info_analisa_cc');
    }

    public function ringkasan(){
        return $this->belongsTo('App\Models\Pengajuan\CA\RingkasanAnalisa', 'id_ringkasan_analisa');
    }

    public function recom_ca(){
        return $this->belongsTo('App\Models\Pengajuan\CA\Rekomendasi_CA', 'id_recom_ca');
    }

    public function recom_pin(){
        return $this->belongsTo('App\Models\Pengajuan\CA\RekomendasiPinjaman', 'id_rekomendasi_pinjaman');
    }

    public function as_jiwa(){
        return $this->belongsTo('App\Models\Pengajuan\CA\AsuransiJiwa', 'id_asuransi_jiwa');
    }

    public function as_jaminan(){
        return $this->belongsTo('App\Models\Pengajuan\CA\AsuransiJaminan', 'id_asuransi_jaminan');
    }
}
