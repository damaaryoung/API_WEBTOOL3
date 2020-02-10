<?php

namespace App\Models\AreaKantor;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class KodeKantor extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'dpm';

    protected $table = 'app_kode_kantor';
    protected $primaryKey = 'kode_kantor';

    protected $fillable = [
        'kode_cabang', 'nama_kantor', 'nama_area_kerja', 'initial', 'alamat_kantor', 'kota_kantor', 'kode_internal', 'nama_internal', 'userid', 'password', 'ip_address', 'KODE_PERK_RAK', 'keterangan', 'nama_database', 'nama_password_database', 'nama_user_database', 'nama_pimpinan', 'jabatan_pimpinan', 'PORT', 'USER_SYNC', 'PASSWORD_SYNC', 'DIRECTORY_SYNC', 'USER_ID_TRANS', 'KODE_PERK_RAK_RAB', 'SHADOW', 'STATUS_ONLINE', 'tgl_tutup_transaksi', 'user_ftp_report', 'password_ftp_report', 'folder_ftp_report', 'nama_pimpinan1', 'nama_pimpinan2', 'IP_ADDRESS_REPORT', 'sandi_bank', 'sandi_cabang', 'sandi_kota_kab', 'kasi_pelayanan', 'kode_area', 'ip_address_fp', 'port_fp', 'kode_litigasi', 'tlp', 'fax', 'latitude', 'longitude', 'path_img', 'tgl_mulai', 'flg_eom', 'flg_aktif'
    ];

    public $timestamps = false;
}
