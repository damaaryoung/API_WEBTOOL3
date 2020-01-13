<?php

namespace App\Models\Transaksi;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class TransAO extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'web';

    protected $table = 'trans_ao';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nomor_ao', 'id_trans_so', 'user_id', 'id_pic', 'id_cabang', 'id_validasi', 'id_verifikasi', 'id_agunan_tanah', 'id_agunan_kendaraan', 'id_periksa_agunan_tanah', 'id_periksa_agunan_kendaraan', 'id_kapasitas_bulanan', 'id_pendapatan_usaha', 'id_recom_ao', 'catatan_ao', 'status_ao'
    ];

    public function so(){
        return $this->belongsTo('App\Models\Transaksi\TransSO', 'id_trans_so');
    }

    public function pic(){
        return $this->belongsTo('App\Models\AreaKantor\PIC', 'id_pic');
    }

    public function cabang(){
        return $this->belongsTo('App\Models\AreaKantor\Cabang', 'id_cabang');
    }

    public function valid(){
        return $this->belongsTo('App\Models\Pengajuan\AO\ValidModel', 'id_validasi');
    }

    public function verif(){
        return $this->belongsTo('App\Models\Pengajuan\AO\VerifModel', 'id_verifikasi');
    }

    public function tan(){
        return $this->belongsTo('App\Models\Pengajuan\AO\AgunanTanah', 'id_agunan_tanah');
    }

    public function ken(){
        return $this->belongsTo('App\Models\Pengajuan\AO\AgunanKendaraan', 'id_agunan_kendaraan');
    }

    public function pe_tan(){
        return $this->belongsTo('App\Models\Pengajuan\AO\PemeriksaanAgunanTanah', 'id_periksa_agunan_tanah');
    }

    public function pe_ken(){
        return $this->belongsTo('App\Models\Pengajuan\AO\PemeriksaanAgunanKendaraan', 'id_periksa_kendaraan');
    }

    public function kapbul(){
        return $this->belongsTo('App\Models\Pengajuan\AO\KapasitasBulanan', 'id_kapasitas_bulanan');
    }

    public function usaha(){
        return $this->belongsTo('App\Models\Pengajuan\AO\PendapatanUsaha', 'id_pendapatan_usaha');
    }

    public function recom_ao(){
        return $this->belongsTo('App\Models\Pengajuan\AO\RekomendasiAO', 'id_recom_ao');
    }
}
