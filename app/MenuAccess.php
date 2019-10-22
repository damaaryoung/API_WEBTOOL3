<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class MenuAccess extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    protected $connection = 'web';

    protected $table = 'menu_akses';
    protected $primaryKey = 'id';

    protected $fillable = [
       'id_user', 'id_menu_master', 'id_menu_sub', 'print_access', 'add_access', 'edit_access', 'delete_access'
    ];

    public function mMaster()
    {
        return $this->belongsTo('App\MenuMaster', 'id');
    }

    public $timestamps = false;
}
