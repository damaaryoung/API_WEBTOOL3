<?php

namespace App\Models\AreaKantor;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\SoftDeletes;

class ParameterAcc extends Model
{
    //use Authenticatable, Authorizable, SoftDeletes;

    protected $connection = 'web';

    protected $table = 'parameter_access';
    protected $primaryKey = 'id';

    protected $fillable = [
        'value', 'keterangan'
    ];

    protected $casts = [
        'id' => 'varchar',
        'updated_at' => 'date:m-d-Y H:i:s'
    ];

    // protected $dates = ['deleted_at'];
}
