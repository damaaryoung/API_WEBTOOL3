<?php

namespace App\Models\Verifikasi;

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

class VerifReqCadebt extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'web';

    protected $table = 'verif_req_cadebt';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_trans_so',
        'no_ktp',
        'nama',
        'tempat_lahir',
        'tgl_lahir',
        'alamat',
        'trx_id',
        'ref_id',
        'limit_call',
        'created_at',
        'updated_at',
        'user_id',
        'id_pic',
        'id_area',
        'id_cabang',
        'nominal'
    ];

    // protected $casts = [
    //     'flg_aktif'  => 'boolean',
    //     'created_at' => 'date:m-d-Y H:i:s',
    //     'updated_at' => 'date:m-d-Y H:i:s'
    // ];


}
