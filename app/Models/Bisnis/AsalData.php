<?php

namespace App\Models\Bisnis;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class AsalData extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'web';

    protected $table = 'master_asal_data';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nama', 'info', 'flg_aktif'
    ];

    //relasi one to many (Saya memiliki banyak anggota di model .....)
//     public function transo(){
//         return $this->hasMany('App\Models\Bisnis\TansSo', 'id_asal_data');
//     }
}
