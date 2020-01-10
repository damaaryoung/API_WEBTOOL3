<?php

namespace App\Models\Pengajuan;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

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
        'id_calon_debitur', 'id_agunan_tanah', 'nama_penghuni', 'status_penghuni', 'bentuk_bangunan', 'kondisi_bangunan', 'fasilitas', 'listrik', 'nilai_taksasi_agunan', 'nilai_taksasi_bangunan', 'tgl_taksasi', 'nilai_likuidasi', 'flg_aktif'
    ];

    public function debt(){
        return $this->belongsTo('App\Models\Pengajuan\Debitur', 'id_calon_debitur');
    }

    public function tanah(){
        return $this->belongsTo('App\Models\Pengajuan\AgunanTanah', 'id_agunan_tanah');
    }
}
