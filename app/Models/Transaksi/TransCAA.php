<?php

namespace App\Models\Transaksi;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class TransCAA extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'web';

    protected $table = 'trans_caa';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nomor_caa', 'user_id', 'id_trans_so', 'id_pic', 'id_cabang', 'peyimpangan', 'team_caa', 'rincian', 'file_mao_mca', 'file_lain', 'catatan_caa', 'status_caa', 'flg_aktif'
    ];

    public function so(){
        return $this->belongsTo('App\Models\Transaksi\TransaksiSO', 'id_trans_so');
    }

    public function pic(){
        return $this->belongsTo('App\Models\AreaKantor\PIC', 'id_pic');
    }

    public function cabang(){
        return $this->belongsTo('App\Models\AreaKantor\Cabang', 'id_cabang');
    }
}