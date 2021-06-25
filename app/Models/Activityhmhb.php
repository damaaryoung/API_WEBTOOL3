<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

use App\Models\User;


class Activityhmhb extends Model
{
    // use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'web';

    protected $table = 'activity_hmhb';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'jenis_pic',
        'activity',
        'tgl_assign',
        'no_kontrak',
	'kode_mb',
        'nama_mb',
        'nama_debitur',
        'alamat_mb',
        'alamat_debitur',
        'nama_pic',
'produk',
  'new_plafond',
  'new_angsuran',
  'new_tenor',
  'baki_debet'
    ];

    // protected $casts = [
    //     'flg_aktif'  => 'boolean',
    //     'created_at' => 'date:m-d-Y H:i:s',
    //     'updated_at' => 'date:m-d-Y H:i:s'
    // ];
}
