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

use App\Models\Pengajuan\CA\MutasiBank;
use App\Models\Pengajuan\CA\TabDebt;
use App\Models\Pengajuan\CA\InfoACC;
use App\Models\Pengajuan\CA\RingkasanAnalisa;
use App\Models\Pengajuan\CA\RekomendasiCA;
use App\Models\Pengajuan\CA\RekomendasiPinjaman;
use App\Models\Pengajuan\CA\AsuransiJiwa;
use App\Models\Pengajuan\CA\AsuransiJaminan;

class TransCA extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'web';

    protected $table = 'trans_ca';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nomor_ca', 'user_id', 'id_trans_so', 'id_pic', 'id_area', 'id_cabang', 'id_mutasi_bank', 'id_log_tabungan', 'id_info_analisa_cc', 'id_ringkasan_analisa', 'id_recom_ca', 'id_rekomendasi_pinjaman', 'id_asuransi_jiwa', 'id_asuransi_jaminan', 'catatan_ca', 'status_ca', 'revisi', 'flg_aktif'
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

    public function mutasi(){
        return $this->belongsTo(MutasiBank::class, 'id_mutasi_bank')
            ->withDefault(function () {
                return new MutasiBank();
            });
    }

    public function log_tab(){
        return $this->belongsTo(TabDebt::class, 'id_log_tabungan')
            ->withDefault(function () {
                return new TabDebt();
            });
    }

    public function info_acc(){
        return $this->belongsTo(InfoACC::class, 'id_info_analisa_cc')
            ->withDefault(function () {
                return new InfoACC();
            });
    }

    public function ringkasan(){
        return $this->belongsTo(RingkasanAnalisa::class, 'id_ringkasan_analisa')
            ->withDefault(function () {
                return new RingkasanAnalisa();
            });
    }

    public function recom_ca(){
        return $this->belongsTo(RekomendasiCA::class, 'id_recom_ca')
            ->withDefault(function () {
                return new RekomendasiCA();
            });
    }

    public function recom_pin(){
        return $this->belongsTo(RekomendasiPinjaman::class, 'id_rekomendasi_pinjaman')
            ->withDefault(function () {
                return new RekomendasiPinjaman();
            });
    }

    public function as_jiwa(){
        return $this->belongsTo(AsuransiJiwa::class, 'id_asuransi_jiwa')
            ->withDefault(function () {
                return new AsuransiJiwa();
            });
    }

    public function as_jaminan(){
        return $this->belongsTo(AsuransiJaminan::class, 'id_asuransi_jaminan')
            ->withDefault(function () {
                return new AsuransiJaminan();
            });
    }
}
