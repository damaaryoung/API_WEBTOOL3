<?php

namespace App\Models\CC;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class Usaha extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'web';

    protected $table = 'usaha_calon_debt';
    protected $primaryKey = 'id';

    protected $fillable = [
       'id_calon_debitur', 'nama_tempat_usaha', 'jenis_usaha', 'alamat', 'id_provinsi', 'id_kabupaten', 'id_kecamatan', 'id_kelurahan', 'rt', 'rw', 'tunai', 'kredit', 'biaya_sewa', 'gaji_pegawai', 'belanja_brg', 'telp-listr-air', 'sampah-kemanan', 'biaya_ongkir', 'hutang_dagang', 'lain_lain', 'laba', 'lamp_surat_ket_usaha', 'lamp_pembukuan_usaha', 'lamp_rek_tabungan', 'lamp_persetujuan_ideb', 'lamp_tempat_usaha', 'lama_usaha', 'telp_tempat_usaha', 'ver_sku', 'ver_pembukuan_usaha', 'validasi', 'flg_aktif'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    // protected $hidden = [

    // ];

    // public $timestamps = false;
}
