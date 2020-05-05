<?php

namespace App\Models\Pengajuan\AO;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

use App\Models\Pengajuan\AgunanTanah;

class PemeriksaanAgunTan extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'web';

    protected $table = 'periksa_agunan_tanah';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_agunan_tanah', 'nama_penghuni', 'status_penghuni', 'bentuk_bangunan', 'kondisi_bangunan', 'fasilitas', 'listrik', 'nilai_taksasi_agunan', 'nilai_taksasi_bangunan', 'tgl_taksasi', 'nilai_likuidasi', 'nilai_agunan_independen', 'perusahaan_penilai_independen'
    ];

    protected $casts = [
        'nilai_taksasi_agunan'   => 'integer',
        'nilai_taksasi_bangunan' => 'integer',
        'tgl_taksasi'   => 'date:d-m-Y'

    ];

    public $timestamps = false;

    public function tanah()
    {
        return $this->belongsTo(AgunanTanah::class, 'id_agunan_tanah')
            ->withDefault(function () {
                return new AgunanTanah();
            });
    }
}
