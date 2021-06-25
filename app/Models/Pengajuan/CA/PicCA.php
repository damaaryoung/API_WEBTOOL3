<?php

namespace App\Models\Pengajuan\CA;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class PicCA extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'web';

    protected $table = 'pic_ca';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'user_id',
        'id_ca',
        'region',
        'nama',
        'jenis_pic',
        'flg_aktif'
    ];

    // protected $casts = [
    //     'plafon_kredit'               => 'integer',
    //     'jangka_waktu'                => 'integer',
    //     'suku_bunga'                  => 'float',
    //     'pembayaran_bunga'            => 'integer',
    //     'biaya_provisi'               => 'integer',
    //     'biaya_administrasi'          => 'integer',
    //     'biaya_credit_checking'       => 'integer',
    //     'biaya_asuransi_jiwa'         => 'integer',
    //     'biaya_asuransi_jaminan_kebakaran'      => 'integer',
    //     'biaya_asuransi_jaminan_kendaraan'      => 'integer',
    //     'notaris'                     => 'integer',
    //     'biaya_tabungan'              => 'integer',
    //     'rekom_angsuran'              => 'integer',
    //     'angs_pertama_bunga_berjalan' => 'integer',
    //     'pelunasan_nasabah_ro'        => 'integer',
    //     'blokir_dana'                 => 'integer',
    //     'pelunasan_tempat_lain'       => 'integer',
    //     'blokir_angs_kredit'          => 'integer'
    // ];

    public $timestamps = false;
}
