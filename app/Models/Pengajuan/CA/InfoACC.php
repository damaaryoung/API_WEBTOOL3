<?php

namespace App\Models\Pengajuan\CA;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class InfoACC extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'web';

    protected $table = 'informasi_analisa_cc';
    protected $primaryKey = 'id';

    protected $fillable = [
       'nama_bank', 'plafon', 'baki_debet', 'angsuran', 'collectabilitas', 'jenis_kredit'
    ];

    protected $casts = [
        'plafon'     => 'integer',
        'baki_debet' => 'integer',
        'angsuran'   => 'integer'
    ];

    public $timestamps = false;
}
