<?php

namespace App\Models\VerifikasiTelp;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

use App\Models\Transaksi\TransSO;

use App\Models\User;

use App\Models\AreaKantor\PIC;
use App\Models\AreaKantor\Area;
use App\Models\AreaKantor\Cabang;

class VerifTelp extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'web';

    protected $table = 'verif_telp';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_trans_so',
        'user_id_ca',
        'id_param_verif',
        'hasil', 'keterangan'
    ];

    public $timestamps = false;

    // protected $casts = [
    //     'flg_aktif'  => 'boolean',
    //     'created_at' => 'date:m-d-Y H:i:s',
    //     'updated_at' => 'date:m-d-Y H:i:s'
    // ];


}
