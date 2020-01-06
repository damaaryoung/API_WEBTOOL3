<?php

namespace App\Models\Bisnis;

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
        'nomor_ca', 'user_id', 'id_trans_ao', 'id_pic', 'id_cabang', 'segmentasi_bpr', 'id_mutasi_bank', 'id_log_tabungan', 'id_info_analisa_cc', 'id_ringkasan_analisa', 'penyimpangan_struktur', 'penyimpanan_dokumen', 'recom_nilai_pinjaman', 'recom_tenor', 'recom_angsuran', 'recom_produk_kredit', 'note_recom', 'id_asuransi_jiwa', 'id_asuransi_jaminan', 'notaris', 'biaya_tabungan'
    ];

    public function ao(){
        return $this->belongsTo('App\Models\Bisnis\TransAO', 'id_trans_ao');
    }

    public function pic(){
        return $this->belongsTo('App\Models\AreaKantor\PIC', 'id_pic');
    }

    public function cabang(){
        return $this->belongsTo('App\Models\AreaKantor\Cabang', 'id_cabang');
    }
}
