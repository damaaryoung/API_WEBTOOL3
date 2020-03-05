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

class TransCAA extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'web';

    protected $table = 'trans_caa';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nomor_caa', 'user_id', 'id_trans_so', 'id_pic', 'id_area', 'id_cabang', 'pic_team_caa', 'penyimpangan', 'file_report_mao', 'file_report_mca', 'status_file_agunan', 'file_agunan', 'status_file_usaha', 'file_usaha', 'file_tempat_tinggal', 'file_lain', 'rincian', 'status_caa', 'status_team_caa', 'flg_aktif'
    ];

    protected $casts = [
        'flg_aktif' => 'boolean'
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
}
