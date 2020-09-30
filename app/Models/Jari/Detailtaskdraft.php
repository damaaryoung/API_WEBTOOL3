<?php

namespace App\Models\Jari;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class Detailtaskdraft extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    protected $connection = 'jari';

    protected $table = 'detail_taskbulkdraft';
    protected $primaryKey = 'id';
    //protected $guarded = [];
    protected $fillable = ['taskcode', 'duedate', 'period', 'dpd', 'angstung', 'denda'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    // protected $hidden = [
    // ];
    public $timestamps = false;

    function taskdraft()
    {
        return $this->belongsTo('App\Models\TaskDraft', 'taskcode')->select(['taskcode']);
    }
}
