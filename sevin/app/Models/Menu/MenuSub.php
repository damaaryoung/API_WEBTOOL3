<?php

namespace App\Models\Menu;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class MenuSub extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    protected $connection = 'web';

    protected $table = 'menu_sub';
    protected $primaryKey = 'id';

    protected $fillable = [
       'id_menu_master', 'nama', 'url', 'flg_aktif'
    ];

    public function menu_master()
    {
        return $this->belongsTo('App\Models\Menu\MenuMaster', 'id_menu_master')->select(['id', 'nama']);
    }

    public $timestamps = false;
}
