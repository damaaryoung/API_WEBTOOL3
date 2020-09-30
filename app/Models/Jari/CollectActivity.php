<?php

namespace App\Models\Jari;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class CollectActivity extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    protected $connection = 'jari';

    protected $table = 'collect_activity';
    protected $primaryKey = 'id';
    //protected $guarded = [];
    protected $fillable = [
        'id',
        'tgl_collect',
        'activity',
        'task',
        'kontrak',
        'jml_tunggakan',
        'total_ospokok',
        'current',
        'collection_rasio'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    // protected $hidden = [
    // ];
    public $timestamps = false;

    // function detail()
    // {
    //     return $this->hasMany('App\Models\Detailtaskdraft', 'taskcode')->select(['duedate', 'period', 'dpd', 'angstung', 'denda']);
    // }
}
