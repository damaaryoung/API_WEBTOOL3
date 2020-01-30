<?php

namespace App\Models\Transaksi;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

use App\Models\Transaksi\TransSO;
use App\Models\Transaksi\TransCAA;

use App\Models\User;

use App\Models\AreaKantor\PIC;
use App\Models\AreaKantor\Area;
use App\Models\AreaKantor\Cabang;

class Approval extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'web';

    protected $table = 'tb_approval';
    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id', 'id_trans_so', 'id_trans_caa', 'id_pic', 'id_area', 'id_cabang', 'plafon', 'tenor', 'rincian', 'status', 'tujuan_forward', 'flg_aktif'
    ];

    // public $timestamps = false;

    public function user(){
        return $this->belongsTo(User::class, 'user_id')
            ->withDefault(function () {
                return new User();
            });
    }

    public function so(){
        return $this->belongsTo(TransSO::class, 'id_trans_so')
            ->withDefault(function () {
                return new TransSO();
            });
    }

    public function caa(){
        return $this->belongsTo(TransCAA::class, 'id_trans_caa')
            ->withDefault(function () {
                return new TransCAA();
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
