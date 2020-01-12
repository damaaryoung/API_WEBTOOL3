<?php

namespace App\Models\Pengajuan\SO;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class FasilitasPinjaman extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'web';

    protected $table = 'fasilitas_pinjaman';
    protected $primaryKey = 'id';

    protected $fillable = [
        'jenis_pinjaman', 'tujuan_pinjaman', 'plafon', 'tenor'
    ];

    public $timestamps = false;
}
