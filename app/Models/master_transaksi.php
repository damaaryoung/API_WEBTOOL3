<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

use App\Models\Transaksi\TransSO;

use App\Models\User;


class master_transaksi extends Model
{
    // use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'simar';

    protected $table = 'cs_master_transaksi';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id',
  'tgl_transaksi',
  'id_aplikasi',
  'nomor_aplikasi',
  'nama_debitur',
  'id_area',
  'id_cabang',
  'nama_so',
  'nama_ao',
  'risiko',
  'rekomendasi'
    ];

    // protected $casts = [
    //     'flg_aktif'  => 'boolean',
    //     'created_at' => 'date:m-d-Y H:i:s',
    //     'updated_at' => 'date:m-d-Y H:i:s'
    // ];
}
