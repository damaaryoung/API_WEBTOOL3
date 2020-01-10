<?php

namespace App\Models\Pengajuan;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class AgunanTanah extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'web';

    protected $table = 'agunan_tanah';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_calon_debitur', 'tipe_lokasi', 'alamat', 'id_provinsi', 'id_kabupaten', 'id_kecamatan', 'id_kelurahan', 'id_kabupaten', 'id_kecamatan', 'id_kelurahan', 'rt', 'rw', 'luas_tanah', 'luas_bangunan', 'nama_pemilik_sertifikat', 'jenis_sertifikat', 'no_sertifikat', 'tgl_ukur_sertifikat', 'tgl_berlaku_shgb', 'no_imb', 'njop', 'nop', 'lamp_agunan_depan', 'lamp_agunan_kanan', 'lamp_agunan_kiri', 'lamp_agunan_belakang', 'lamp_agunan_dalam', 'lamp_sertifikat', 'lamp_imb', 'lamp_pbb', 'flg_aktif'
    ];

    public function debt(){
        return $this->belongsTo('App\Models\Pengajuan\Debitur', 'id_calon_debitur');
    }

    public function prov(){
        return $this->belongsTo('App\Models\Wilayah\Provinsi', 'id_provinsi')->select(['id', 'nama']);
    }

    public function kab(){
        return $this->belongsTo('App\Models\Wilayah\Kabupaten', 'id_kabupaten')->select(['id', 'nama']);
    }

    public function kec(){
        return $this->belongsTo('App\Models\Wilayah\Kecamatan', 'id_kecamatan')->select(['id', 'nama']);
    }

    public function kel(){
        return $this->belongsTo('App\Models\Wilayah\Kelurahan', 'id_kelurahan')->select(['id', 'nama', 'kode_pos']);
    }
}
