<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

use App\Models\User;
use App\Models\AreaKantor\PIC;
use App\Models\AreaKantor\Area;
use App\Models\AreaKantor\Cabang;


class TeleAssign extends Model
{
    // use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'web';

    protected $table = 'telesales_assign';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'no_rekening',
        'source',
        'nama_debitur',
        'no_hp',
        'no_hp2',
        'produk',
        'new_plafond',
        'new_angsuran',
        'new_tenor',
        'baki_debet',
        'notes',
        'kode_cabang',
        'user_id',
        'id_pic',
        'id_area',
        'id_cabang',
        'created_at',
        'updated_at'
    ];

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
