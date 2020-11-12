<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

use App\Models\User;


class ActivitySo extends Model
{
    // use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'web';

    protected $table = 'activity_so';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
       'id',
  'activity',
  'nomor_so',
  'nama_mb',
  'hasil_maintain',
  'nama_debitur',
  'alamat_domisili',
  'hasil_visit',
  'swafoto',
  'longitude',
  'latitude',
  'tanggal'
    ];

    // protected $casts = [
    //     'flg_aktif'  => 'boolean',
    //     'created_at' => 'date:m-d-Y H:i:s',
    //     'updated_at' => 'date:m-d-Y H:i:s'
    // ];
}
