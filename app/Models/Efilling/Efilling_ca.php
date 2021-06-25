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

class Efilling_ca extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'centro';

    protected $table = 'efiling_credit_analist';
    protected $primaryKey = 'no_rekening';

    protected $fillable = [
        'no_rekening',
        'memo_ao',
        'memo_ca',
        'offering_letter',
        'penilaian_jaminan',
        'cheklist_survey',
        'persetujuan_kredit',
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
