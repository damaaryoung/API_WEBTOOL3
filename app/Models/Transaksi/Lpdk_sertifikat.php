<?php

namespace App\Models\Transaksi;

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

class Lpdk_sertifikat extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'web';

    protected $table = 'lpdk_sertifikat';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'trans_so',
        'nama_sertifikat',
        'status_sertifikat',
        'nama_pas_sertifikat',
        'status_pas_sertifikat',
        'no_sertifikat',
        'jenis_sertifikat',
        'tgl_berlaku_shgb',
        'lampiran_sertifikat',
        'lampiran_imb',
        'lampiran_pbb',
        'created_at',
        'updated_at'
    ];

    // protected $casts = [
    //     'flg_aktif'  => 'boolean',
    //     'created_at' => 'date:m-d-Y H:i:s',
    //     'updated_at' => 'date:m-d-Y H:i:s'
    // ];
}
