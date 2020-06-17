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

class Lpdk_lampiran extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'web';

    protected $table = 'lpdk_lampiran';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'trans_so',
        'lampiran_ktp_deb',
        'lampiran_ktp_pasangan',
        'lampiran_npwp',
        'lampiran_surat_kematian',
        'lampiran_sk_desa',
        'lampiran_ktp_penjamin',
        'lampiran_pbb',
        'lampiran_imb',
        'lampiran_ajb',
        'lampiran_ahliwaris',
        'lampiran_aktahibah',
        'lampiran_skk',
        'lampiran_sku',
        'lampiran_slipgaji',
        'lampiran_kk',
        'lampiran_surat_lahir',
        'lampiran_surat_cerai',
        'lampiran_ktp_pemilik_sertifikat',
        'lampiran_ktp_pasangan_sertifikat',
        'lampiran_surat_nikah',
        'created_at',
        'updated_at'
    ];

    // protected $casts = [
    //     'flg_aktif'  => 'boolean',
    //     'created_at' => 'date:m-d-Y H:i:s',
    //     'updated_at' => 'date:m-d-Y H:i:s'
    // ];
}
