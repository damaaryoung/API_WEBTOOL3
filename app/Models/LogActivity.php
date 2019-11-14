<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;
// use App\Models\User;

class LogActivity extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'web';

    protected $table = 'history';

    protected $fillable = [
       'subject', 'url', 'method', 'ip', 'agent', 'user_id'
    ];

    // public function user()
    // {
    //     return $this->belongsTo(User::class, 'user_id');
    // }
}
