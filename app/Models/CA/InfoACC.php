<?php

namespace App\Models\CA;

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
        'id_trans_so', 'nama_bank', 'plafon', 'baki_debet', 'angsuran', 'collectabilitas', 'jenis_kredit'
    ];

    public function so(){
        return $this->belongsTo('App\Models\Bisnis\TransSo', 'id_trans_so');
    }
}
