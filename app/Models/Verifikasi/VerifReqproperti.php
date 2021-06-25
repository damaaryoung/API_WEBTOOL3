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

class VerifReqProperti extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'web';

    protected $table = 'verif_req_properti';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_trans_so',
        'id_agunan_tanah',
        'no_ktp',
        'property_address',
        'property_name',
        'property_building_area',
        'property_surface_area',
        'property_estimation',
        'certificate_address',
        'certificate_id',
        'certificate_name',
        'certificate_type',
        'certificate_date',
        'trx_id',
        'ref_id',
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
