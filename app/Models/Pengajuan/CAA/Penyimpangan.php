<?php

namespace App\Models\Pengajuan\CAA;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class Penyimpangan extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'web';

    protected $table = 'penyimpangan_caa';
    protected $primaryKey = 'id';

    protected $fillable = [
       'id_trans_so','id_trans_caa','biaya_provisi','biaya_admin','biaya_kredit','past_due_ro','struktur_kredit','ltv','tenor','kartu_pinjaman','sertifikat_diatas_50','sertifikat_diatas_150', 'profesi_beresiko', 'jaminan_kp_tenor_48'
    ];

    public $timestamps = false;
}
