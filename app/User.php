<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'dpm';

    protected $table = 'user';
    protected $primaryKey = 'user_id';

    protected $fillable = [
        'user', 'nik', 'level', 'kd_cabang', 'nama', 'divisi_id', 'kode_jabatan', 'jabatan', 'tgl_expired', 'flg_block', 'session', 'session_date', 'user_id', 'user_id_induk', 'user_code', 'ip_address', 'flag', 'kode_perk_kas', 'kode_perk_kas_utama', 'penerimaan', 'pengeluaran', 'penerimaan_ob', 'pengeluaran_ob', 'plafon_caa', 'group_menu', 'group_menu_webtool', 'email', 'no_hp', 'imei', 'reg_id_gcm', 'fcm_token', 'flg_busy', 'sound', 'kode_group3', 'kode_area', 'ip_public', 'flg_survey', 'min_survey', 'last_update', 'access_menu_asuransi'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password'
    ];

    public $timestamps = false;
}
