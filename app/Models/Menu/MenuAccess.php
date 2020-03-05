<?php

namespace App\Models\Menu;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

use App\Models\Menu\MenuMaster;
use App\Models\Menu\MenuSub;

class MenuAccess extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    protected $connection = 'web';

    protected $table = 'menu_akses';
    protected $primaryKey = 'id';

    protected $fillable = [
       'id_user', 'id_menu_master', 'id_menu_sub', 'print_access', 'add_access', 'edit_access', 'delete_access', 'flg_aktif'
    ];

    protected $casts = [
        'flg_aktif' => 'boolean'
    ];

    public function menu_master()
    {
        return $this->belongsTo(MenuMaster::class, 'id_menu_master')
            ->withDefault(function () {
                return new MenuMaster();
            });
    }

    public function menu_sub()
    {
        return $this->belongsTo(MenuSub::class, 'id_menu_sub')
            ->withDefault(function () {
                return new MenuSub();
            });
    }

    public $timestamps = false;
}
