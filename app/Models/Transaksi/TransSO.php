<?php

namespace App\Models\Transaksi;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

use App\Models\Transaksi\TransAO;
use App\Models\Transaksi\TransCA;
use App\Models\Transaksi\TransCAA;

use App\Models\User;

use App\Models\AreaKantor\PIC;
use App\Models\AreaKantor\Area;
use App\Models\AreaKantor\Cabang;
use App\Models\AreaKantor\AsalData;
use App\Models\Pengajuan\SO\Debitur;
use App\Models\Pengajuan\SO\Pasangan;
use App\Models\Pengajuan\SO\Penjamin;
use App\Models\Pengajuan\SO\FasilitasPinjaman;

class TransSO extends Model implements AuthenticatableContract, AuthorizableContract
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
        'nomor_so', 'norev_so', 'user_id', 'id_pic', 'id_area', 'id_cabang', 'id_asal_data', 'nama_marketing', 'nama_so', 'id_fasilitas_pinjaman', 'id_calon_debitur', 'id_pasangan', 'id_penjamin', 'id_trans_ao', 'id_trans_ca', 'id_trans_caa', 'catatan_das', 'catatan_hm', 'status_das', 'status_hm', 'lamp_ideb', 'lamp_pefindo', 'form_persetujuan_ideb', 'flg_aktif'
    ];

    protected $casts = [
        'flg_aktif'  => 'boolean',
        'created_at' => 'date:d-m-Y H:i:s',
        'updated_at' => 'date:d-m-Y H:i:s'

    ];

    public function pic()
    {
        return $this->belongsTo(PIC::class, 'id_pic')
            ->withDefault(function () {
                return new PIC();
            });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')
            ->withDefault(function () {
                return new User();
            });
    }

    public function area()
    {
        return $this->belongsTo(Area::class, 'id_area')
            ->withDefault(function () {
                return new Area();
            });
    }

    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'id_cabang')
            ->withDefault(function () {
                return new Cabang();
            });
    }

    public function asaldata()
    {
        return $this->belongsTo(AsalData::class, 'id_asal_data')
            ->withDefault(function () {
                return new AsalData();
            });
    }

    public function debt()
    {
        return $this->belongsTo(Debitur::class, 'id_calon_debitur')
            ->withDefault(function () {
                return new Debitur();
            });
    }

    public function pas()
    {
        return $this->belongsTo(Pasangan::class, 'id_pasangan')
            ->withDefault(function () {
                return new Pasangan();
            });
    }

    public function penjamin()
    {
        return $this->belongsTo(Penjamin::class, 'id_penjamin')
            ->withDefault(function () {
                return new Penjamin();
            });
    }

    public function faspin()
    {
        return $this->belongsTo(FasilitasPinjaman::class, 'id_fasilitas_pinjaman')
            ->withDefault(function () {
                return new FasilitasPinjaman();
            });
    }

    public function ao()
    {
        return $this->belongsTo(TransAO::class, 'id_trans_ao')
            ->withDefault(function () {
                return new TransAO();
            });
    }

    public function ca()
    {
        return $this->belongsTo(TransCA::class, 'id_trans_ca')
            ->withDefault(function () {
                return new TransCA();
            });
    }

    public function caa()
    {
        return $this->belongsTo(TransCAA::class, 'id_trans_caa')
            ->withDefault(function () {
                return new TransCAA();
            });
    }
}
