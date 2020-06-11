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

class Lpdk_Cek extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'web';

    protected $table = 'tb_lpdk_cek';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'trans_so',
        'nomor_so',
        'nama_so',
        'asal_data',
        'nama_marketing',
        'area_kerja',
        'plafon',
        'tenor',
        'nama_debitur',
        'nama_pasangan',
        'nama_penjamin',
        'ibu_kandung_penjamin',
        'status_nikah',
        'produk',
        'no_sertifikat',
        'nama_sertifikat',
        'status_sertifikat',
        'nama_pas_sertifikat',
        'status_pas_sertifikat',
        'hub_cadeb',
        'lampiran_ktp_deb',
        'lampiran_ktp_pasangan',
        'lampiran_npwp',
        'lampiran_surat_kematian',
        'lampiran_sk_desa',
        'lampiran_ktp_penjamin',
        'lampiran_sertifikat',
        'lampiran_pbb',
        'lampiran_imb',
        'lampiran_ajb',
        'lampiran_ahliwaris',
        'lampiran_aktahibah',
        'lampiran_skk',
        'lampiran_sku',
        'lampiran_slipgaji',
        'status_kredit',
        'notes',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'flg_aktif'  => 'boolean',
        'created_at' => 'date:m-d-Y H:i:s',
        'updated_at' => 'date:m-d-Y H:i:s'
    ];
}
