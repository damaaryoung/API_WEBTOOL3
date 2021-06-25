<?php

namespace App\Models\Efilling;

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

class Efilling_spkndk extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'centro';

    protected $table = 'efiling_spk_ndk';
    protected $primaryKey = 'no_rekening';

    protected $fillable = [
        'no_rekening',
        'spk_ndk',
        'asuransi',
        'sp_no_imb',
        'jadwal_angsuran',
        'personal_guarantee',
        'hold_dana',
        'surat_transfer',
        'keabsahan_data',
        'sp_beda_jt_tempo',
        'sp_authentic',
        'sp_penyerahan_jaminan',
        'surat_aksep',
        'tt_uang',
        'sp_pendebetan_rekening',
        'sp_plang',
        'hal_penting',
        'restruktur_bunga_denda',
	'spajk_spa_fpk',
        'verifikasi',
        'notes'
    ];

    public $timestamps = false;

    // protected $casts = [
    //     'flg_aktif'  => 'boolean',
    //     'created_at' => 'date:m-d-Y H:i:s',
    //     'updated_at' => 'date:m-d-Y H:i:s'
    // ];

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
}
