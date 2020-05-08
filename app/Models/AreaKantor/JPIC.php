<?php

namespace App\Models\AreaKantor;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\SoftDeletes;

class JPIC extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, SoftDeletes;

    protected $connection = 'web';

    protected $table = 'mj_pic';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nama_jenis', 'cakupan', 'urutan_jabatan', 'keterangan', 'bagian'
    ];

    protected $casts = [
        'created_at' => 'date:m-d-Y H:i:s',
        'updated_at' => 'date:m-d-Y H:i:s'
    ];

    protected $dates = ['deleted_at'];
}
