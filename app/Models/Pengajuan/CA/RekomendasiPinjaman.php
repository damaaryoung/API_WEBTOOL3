<?php

namespace App\Models\Pengajuan\CA;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class RekomendasiPinjaman extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'web';

    protected $table = 'rekomendasi_pinjaman';
    protected $primaryKey = 'id';

    protected $fillable = [
       'penyimpangan_struktur', 'penyimpangan_dokumen', 'recom_nilai_pinjaman', 'recom_tenor', 'recom_angsuran', 'recom_produk_kredit', 'note_recom'
    ];

    protected $casts = [
        'recom_nilai_pinjaman' => 'integer',
        'recom_tenor'          => 'integer',
        'recom_angsuran'       => 'integer'
    ];

    public $timestamps = false;
}
