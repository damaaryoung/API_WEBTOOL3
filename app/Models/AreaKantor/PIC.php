<?php

namespace App\Models\AreaKantor;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class PIC extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'web';

    protected $table = 'm_pic';
    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id', 'id_mk_area', 'id_mk_cabang', 'id_mj_pic', 'nama', 'flg_aktif'
    ];

    public function user(){
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function area(){
        return $this->belongsTo('App\Models\AreaKantor\Area', 'id_mk_area');
    }

    public function cabang(){
        return $this->belongsTo('App\Models\AreaKantor\Cabang', 'id_mk_cabang');
    }

    public function jpic(){
        return $this->belongsTo('App\Models\AreaKantor\JPIC', 'id_mj_pic');
    }

    // public function jpic_caa(){
    //     return $this->belongsTo('App\Models\AreaKantor\JPIC', 'id_mj_pic')->where('keterangan', 'Team CAA');
    // }
}
