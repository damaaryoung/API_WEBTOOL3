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
       'segmentasi_bpr', 'penyimpangan_struktur', 'penyimpangan_dokumen', 'recom_nilai_pinjaman', 'recom_tenor', 'recom_angsuran', 'recom_produk_kredit', 'note_recom', 'notaris'
    ];

    public $timestamps = false;
}
