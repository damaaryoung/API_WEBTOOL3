<?php

namespace App\Models\Pengajuan;

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
        'id_calon_debitur', 'jenis_pinjaman', 'tujuan_pinjaman', 'plafon', 'tenor', 'flg_aktif'
    ];

    public function debt(){
        return $this->belongsTo('App\Models\Pengajuan\Debitur', 'id_calon_debitur');
    }
}
