<?php

namespace App\Models\Wilayah;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class Provinsi extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    protected $connection = 'web';

    protected $table = 'master_provinsi';
    protected $primaryKey = 'id';

    protected $fillable = [
       'nama', 'flg_aktif'
    ];

    public $timestamps = false;
}
