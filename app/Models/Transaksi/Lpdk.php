<?php

namespace App\Models\Transaksi;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

use App\Models\Transaksi\TransSO;

use App\Models\User;

use App\Models\AreaKantor\PIC;
use App\Models\AreaKantor\Area;
use App\Models\AreaKantor\Cabang;

class Lpdk extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'web';

    protected $table = 'lpdk';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'trans_so',
        'nomor_so',
        'nama_so',
        'asal_data',
        'nama_marketing',
        'request_by',
        'area_kerja',
        'plafon',
        'tenor',
        'nama_debitur',
        'nama_pasangan',
        'status_nikah',
        'produk',
        'alamat_ktp_vs_jaminan',
        'hub_cadeb',
        'akta_notaris',
        'status_kredit',
        'notes_progress',
        'notes_counter',
        'id_sertifikat',
        'id_penjamin',
        'id_lampiran',
        'created_at',
        'updated_at'
    ];

    // protected $casts = [
    //     'flg_aktif'  => 'boolean',
    //     'created_at' => 'date:m-d-Y H:i:s',
    //     'updated_at' => 'date:m-d-Y H:i:s'
    // ];
}
