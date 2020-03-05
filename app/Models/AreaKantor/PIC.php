<?php

namespace App\Models\AreaKantor;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

use App\Models\User;
use App\Models\AreaKantor\Area;
use App\Models\AreaKantor\Cabang;
use App\Models\AreaKantor\JPIC;

class PIC extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'web';

    protected $table = 'm_pic';
    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id', 'id_area', 'id_cabang', 'id_mj_pic', 'nama', 'email', 'plafon_caa', 'flg_aktif'
    ];

    protected $casts = [
        'flg_aktif' => 'boolean'
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id')
            ->withDefault(function () {
                return new User();
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

    public function jpic(){
        return $this->belongsTo(JPIC::class, 'id_mj_pic')
            ->withDefault(function () {
                return new JPIC();
            });
    }
}
