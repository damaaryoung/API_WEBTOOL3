<?php

namespace App\Models\Transaksi;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

use App\Models\Transaksi\TransSO;

use App\Models\User;

use App\Models\AreaKantor\PIC;
use App\Models\AreaKantor\Area;
use App\Models\AreaKantor\Cabang;

use App\Models\Pengajuan\AO\ValidModel;
use App\Models\Pengajuan\AO\VerifModel;
use App\Models\Pengajuan\AO\AgunanTanah;
use App\Models\Pengajuan\AO\AgunanKendaraan;
use App\Models\Pengajuan\AO\PemeriksaanAgunTan;
use App\Models\Pengajuan\AO\PemeriksaanAgunKen;
use App\Models\Pengajuan\AO\KapBulanan;
use App\Models\Pengajuan\AO\PendapatanUsaha;
use App\Models\Pengajuan\AO\RekomendasiAO;

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
        'nomor_ao', 'id_trans_so', 'user_id', 'id_pic', 'id_area', 'id_cabang', 'id_validasi', 'id_verifikasi', 'id_agunan_tanah', 'id_agunan_kendaraan', 'id_periksa_agunan_tanah', 'id_periksa_agunan_kendaraan', 'id_kapasitas_bulanan', 'id_pendapatan_usaha', 'id_recom_ao', 'catatan_ao', 'status_ao', 'form_persetujuan_ideb', 'flg_aktif','verifikasi_hm','status_return','note_return','tgl_pending'
    ];

    protected $casts = [
        'flg_aktif'  => 'boolean',
        'created_at' => 'date:m-d-Y H:i:s',
        'updated_at' => 'date:m-d-Y H:i:s'
    ];

    public function so(){
        return $this->belongsTo(TransSO::class, 'id_trans_so')
            ->withDefault(function () {
                return new TransSO();
            });
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id')
            ->withDefault(function () {
                return new User();
            });
    }

    public function pic(){
        return $this->belongsTo(PIC::class, 'id_pic')
            ->withDefault(function () {
                return new PIC();
            });
    }

    public function area(){
        return $this->belongsTo(Area::class, 'id_area')
            ->withDefault(function () {
                return new Area();
            });
    }

    public function cabang(){
        return $this->belongsTo(Cabang::class, 'id_cabang')
            ->withDefault(function () {
                return new Cabang();
            });
    }

    public function valid(){
        return $this->belongsTo(ValidModel::class, 'id_validasi')
            ->withDefault(function () {
                return new ValidModel();
            });
    }

    public function verif(){
        return $this->belongsTo(VerifModel::class, 'id_verifikasi')
            ->withDefault(function () {
                return new VerifModel();
            });
    }

    public function tan(){
        return $this->belongsTo(AgunanTanah::class, 'id_agunan_tanah')
            ->withDefault(function () {
                return new AgunanTanah();
            });
    }

    public function ken(){
        return $this->belongsTo(AgunanKendaraan::class, 'id_agunan_kendaraan')
            ->withDefault(function () {
                return new AgunanKendaraan();
            });
    }

    public function pe_tan(){
        return $this->belongsTo(PemeriksaanAgunanTanah::class, 'id_periksa_agunan_tanah')
            ->withDefault(function () {
                return new PemeriksaanAgunanTanah();
            });
    }

    public function pe_ken(){
        return $this->belongsTo(PemeriksaanAgunanKendaraan::class, 'id_periksa_kendaraan')
            ->withDefault(function () {
                return new PemeriksaanAgunanKendaraan();
            });
    }

    public function kapbul(){
        return $this->belongsTo(KapBulanan::class, 'id_kapasitas_bulanan')
            ->withDefault(function () {
                return new KapBulanan();
            });
    }

    public function usaha(){
        return $this->belongsTo(PendapatanUsaha::class, 'id_pendapatan_usaha')
            ->withDefault(function () {
                return new PendapatanUsaha();
            });
    }

    public function recom_ao(){
        return $this->belongsTo(RekomendasiAO::class, 'id_recom_ao')
            ->withDefault(function () {
                return new RekomendasiAO();
            });
    }
}
