<?php

namespace App\Models\Transaksi;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class TransSO extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'web';

    protected $table = 'trans_so';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nomor_so', 'user_id', 'id_pic', 'id_cabang', 'id_asal_data', 'nama_marketing', 'nama_so', 'id_fasilitas_pinjaman', 'id_calon_debitur', 'id_pasangan', 'id_penjamin', 'id_trans_ao', 'id_trans_ca', 'catatan_das', 'catatan_hm', 'status_das', 'status_hm', 'lamp_ideb', 'lamp_pefindo', 'flg_aktif'
    ];

    public function pic(){
        return $this->belongsTo('App\Models\AreaKantor\PIC', 'id_pic');
    }

    public function cabang(){
        return $this->belongsTo('App\Models\AreaKantor\Cabang', 'id_cabang');
    }

    public function asaldata(){
        return $this->belongsTo('App\Models\AreaKantor\AsalData', 'id_asal_data');
    }

    public function debt(){
        return $this->belongsTo('App\Models\Pengajuan\SO\Debitur', 'id_calon_debitur');
    }

    public function pas(){
        return $this->belongsTo('App\Models\Pengajuan\SO\Pasangan', 'id_pasangan');
    }

    // public function penjamin(){
    //     return $this->belongsTo('App\Models\Pengajuan\SO\Penjamin', 'id_penjamin');
    // }

    public function faspin(){
        return $this->belongsTo('App\Models\Pengajuan\SO\FasilitasPinjaman', 'id_fasilitas_pinjaman');
    }

    public function ao(){
        return $this->belongsTo('App\Models\Transaksi\TransAO', 'id_trans_ao');
    }

    public function ca(){
        return $this->belongsTo('App\Models\Transaksi\TransCA', 'id_trans_ca');
    }
}
