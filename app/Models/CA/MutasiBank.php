<?php

namespace App\Models\CA;

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

    protected $table = 'trans_ca';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_trans_so', 'urutan_mutasi', 'nama_bank', 'no_rekening', 'nama_pemilik', 'periode', 'frek_debet', 'nominal_debet', 'frek_kredit', 'nominal_kredit', 'saldo'
    ];

    public function so(){
        return $this->belongsTo('App\Models\Bisnis\TransSo', 'id_trans_so');
    }
}
