<?php

namespace App\Models\Pengajuan;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class KeuanganUsaha extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'web';

    protected $table = 'keuangan_usaha';
    protected $primaryKey = 'id';

    protected $fillable = [
       'id_calon_debitur', 'pemasukan_tunai', 'pemasukan_kredit', 'biaya_sewa', 'biaya_gaji_pegawai', 'biaya_belanja_brg', 'biaya_telp_listr_air', 'biaya_sampah_kemanan', 'biaya_kirim_barang', 'biaya_hutang_dagang', 'biaya_angsuran', 'biaya_lain_lain', 'total_pemasukan', 'total_pengeluaran', 'laba_usaha', 'flg_aktif'
    ];

    public function debt(){
        return $this->belongsTo('App\Models\Pengajuan\Debitur', 'id_calon_debitur');
    }
}
