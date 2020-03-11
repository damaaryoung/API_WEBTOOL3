<?php

namespace App\Models\Pengajuan\CA;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class RekomendasiCA extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'web';

    protected $table = 'recom_ca';
    protected $primaryKey = 'id';

    protected $fillable = [
       'produk', 'plafon_kredit', 'jangka_waktu', 'suku_bunga', 'pembayaran_bunga', 'akad_kredit', 'ikatan_agunan', 'biaya_provisi', 'biaya_administrasi', 'biaya_credit_checking', 'biaya_asuransi_jiwa', 'biaya_asuransi_jaminan', 'notaris', 'biaya_tabungan', 'rekom_angsuran', 'angs_pertama_bunga_berjalan', 'pelunasan_nasabah_ro', 'blokir_dana', 'pelunasan_tempat_lain', 'blokir_angs_kredit'
    ];

    protected $casts = [
        'plafon_kredit'               => 'integer',
        'jangka_waktu'                => 'integer',
        'suku_bunga'                  => 'float',
        'pembayaran_bunga'            => 'integer',
        'biaya_provisi'               => 'integer',
        'biaya_administrasi'          => 'integer',
        'biaya_credit_checking'       => 'integer',
        'biaya_asuransi_jiwa'         => 'integer',
        'biaya_asuransi_jaminan'      => 'integer',
        'notaris'                     => 'integer',
        'biaya_tabungan'              => 'integer',
        'rekom_angsuran'              => 'integer',
        'angs_pertama_bunga_berjalan' => 'integer',
        'pelunasan_nasabah_ro'        => 'integer',
        'blokir_dana'                 => 'integer',
        'pelunasan_tempat_lain'       => 'integer',
        'blokir_angs_kredit'          => 'integer'
    ];

    public $timestamps = false;
}
