<?php

namespace App\Models\Pengajuan\CA;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class LogAsuransiJiwa extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'web';

    protected $table = 'log_asuransi_jiwa';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'nama_asuransi', 'jangka_waktu', 'nilai_pertanggungan', 'jatuh_tempo', 'berat_badan', 'tinggi_badan', 'umur_nasabah'
    ];

    protected $casts = [
        'jangka_waktu' => 'integer',
        'berat_badan'  => 'integer',
        'tinggi_badan' => 'integer',
        'jatuh_tempo' => 'date:Y-m-d'
    ];

    public $timestamps = false;
}
