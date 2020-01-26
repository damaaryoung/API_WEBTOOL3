<?php

namespace App\Models\Karyawan;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

use App\Models\User;
use App\Models\AreaKantor\JPIC;
use App\Models\AreaKantor\Area;
use App\Models\AreaKantor\Cabang;

class TeamCAA extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    protected $connection = 'web';

    protected $table = 'team_caa';
    protected $primaryKey = 'id';

    protected $fillable = [
       'id','user_id','id_mj_pic','id_mk_area','id_mk_cabang','nama','email'
    ];

    public $timestamps = false;

    public function user(){
        return $this->belongsTo(User::class, 'id_provinsi')->select(['id', 'nama'])
            ->withDefault(function () {
                return new User();
            });
    }

    public function jpic(){
        return $this->belongsTo(JPIC::class, 'id_mj_pic')
            ->withDefault(function () {
                return new JPIC();
            });
    }

    public function area(){
        return $this->belongsTo(Area::class, 'id_mk_area')
            ->withDefault(function () {
                return new Area();
            });
    }

    public function cabang(){
        return $this->belongsTo(Cabang::class, 'id_mk_cabang')
            ->withDefault(function () {
                return new Cabang();
            });
    }
}
