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
       'id_calon_debitur', 'nama_tempat_usaha', 'jenis_usaha', 'alamat', 'id_provinsi', 'id_kabupaten', 'id_kecamatan', 'id_kelurahan', 'rt', 'rw', 'pemasukan_tunai', 'pemasukan_kredit', 'biaya_sewa', 'biaya_gaji_pegawai', 'biaya_belanja_brg', 'biaya_telp_listr_air', 'biaya_sampah_kemanan', 'biaya_kirim_barang', 'biaya_hutang_dagang', 'biaya_angsuran', 'biaya_lain_lain', 'laba_usaha', 'lamp_surat_ket_usaha', 'lamp_pembukuan_usaha', 'lamp_rek_tabungan', 'lamp_persetujuan_ideb', 'lamp_tempat_usaha', 'tgl_mulai_usaha', 'telp_tempat_usaha', 'ver_sku', 'ver_pembukuan_usaha', 'validasi', 'flg_aktif'
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
