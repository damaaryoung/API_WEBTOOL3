<?php

namespace App\Models\Transaksi;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class TransTCAA extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'web';

    protected $table = 'trans_tcaa';
    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id', 'id_trans_so', 'id_trans_caa', 'id_pic', 'id_cabang', 'plafon', 'tenor', 'rincian', 'status', 'tanggal'
    ];

    public $timestamps = false;

    public function so(){
        return $this->belongsTo('App\Models\Transaksi\TransSO', 'id_trans_so');
    }

    public function caa(){
        return $this->belongsTo('App\Models\Transaksi\TransCAA', 'id_trans_caa');
    }

    public function pic(){
        return $this->belongsTo('App\Models\AreaKantor\PIC', 'id_pic');
    }

    public function cabang(){
        return $this->belongsTo('App\Models\AreaKantor\Cabang', 'id_cabang');
    }
}
