<?php

namespace App\Models\Pengajuan\AO;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class RekomendasiAO extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'web';

    protected $table = 'recom_ao';
    protected $primaryKey = 'id';

    protected $fillable = [
        'produk', 'plafon_kredit', 'jangka_waktu', 'suku_bunga', 'pembayaran_bunga', 'akad_kredit', 'ikatan_agunan', 'analisa_ao', 'biaya_provisi', 'biaya_administrasi', 'biaya_credit_checking', 'biaya_tabungan', 'tujuan_pinjaman', 'jenis_pinjaman'
    ];

    protected $casts = [
        'plafon_kredit'         => 'integer',
        'jangka_waktu'          => 'integer',
        'suku_bunga'            => 'float',
        'biaya_provisi'         => 'integer',
        'biaya_administrasi'    => 'integer',
        'biaya_credit_checking' => 'integer',
        'biaya_tabungan'        => 'integer'
    ];

    public $timestamps = false;
}
