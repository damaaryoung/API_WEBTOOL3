<?php

namespace App\Models\Pengajuan\CA;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class MutasiBank extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'web';

    protected $table = 'mutasi_bank';
    protected $primaryKey = 'id';

    protected $fillable = [
        'urutan_mutasi', 'nama_bank', 'no_rekening', 'nama_pemilik', 'periode', 'frek_debet', 'nominal_debet', 'frek_kredit', 'nominal_kredit', 'saldo'
    ];

    public $timestamps = false;
}
