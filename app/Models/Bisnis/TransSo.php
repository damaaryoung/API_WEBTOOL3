<?php

namespace App\Models\Bisnis;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class TransSo extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'web';

    protected $table = 'trans_so';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nomor_so', 'user_id', 'kode_kantor', 'nama_so', 'id_asal_data', 'nama_marketing',
        'id_fasilitas_pinjaman', 'id_calon_debt', 'id_pasangan', 'id_penjamin', 'id_agunan_tanah', 'id_agunan_kendaraan', 'id_periksa_agunan_tanah', 'id_periksa_agunan_kendaraan', 'id_usaha', 'recomendasi_ao', 'catatan_hasil_cek', 'plafon', 'tenor', 'flg_aktif'
    ];

    public function asaldata(){
        return $this->belongsTo('App\Models\Bisnis\AsalData', 'id_asal_data');
    }

    public function debt(){
        return $this->belongsTo('App\Models\CC\Debitur', 'id_calon_debt');
    }

    public function faspin(){
        return $this->belongsTo('App\Models\CC\FasilitasPinjaman', 'id_fasilitas_pinjaman');
    }

    public function pas(){
        return $this->belongsTo('App\Models\CC\Pasangan', 'id_pasangan');
    }

    public function penj(){
        return $this->belongsTo('App\Models\CC\Penjamin', 'id_penjamin');
    }
}
