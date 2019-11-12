<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class MenuMaster extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'web';

    protected $table = 'menu_master';
    protected $primaryKey = 'id';

    protected $fillable = [
       'nama', 'url', 'icon'
    ];

    public function mAccess()
    {
        return $this->hasMany('App\MenuAccess', 'id');
    }

    public function mSub()
    {
        return $this->hasMany('App\MenuSub', 'id');
    }

    public $timestamps = false;
}
