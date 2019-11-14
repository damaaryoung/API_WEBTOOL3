<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class FlgOto extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    protected $connection = 'dpm';

    protected $table = 'flg_otorisasi';
    protected $primaryKey = 'id';

    protected $fillable = [
       'user_id', 'modul', 'id_modul', 'ip', 'email', 'no_hp', 'pesan', 'tgl', 'jam', 'approval', 'otorisasi', 'subject', 'keterangan', 'waktu_otorisasi', 'sent_android'
    ];

    public $timestamps = false;
}
