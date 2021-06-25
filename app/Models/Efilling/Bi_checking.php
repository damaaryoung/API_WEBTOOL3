<?php

namespace App\Models\Efilling;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

use App\Models\Transaksi\TransSO;

use App\Models\User;

use App\Models\AreaKantor\PIC;
use App\Models\AreaKantor\Area;
use App\Models\AreaKantor\Cabang;

class Bi_checking extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'centro';

    protected $table = 'bi_checking';
    protected $primaryKey = 'no_rekening';

    protected $fillable = [
        'id',
        'nasabah_id',
        'id_order',
        'nomor',
        'kd_kantor',
        'nama_ao',
        'sumber_informasi',
        'nama_asal_data',
        'nama_mb_agency',
        'plafon',
        'tenor',
        'tujuan_kredit',
        'nama_deb',
        'nama_deb_identitas',
        'jk_deb',
        'jenis_debitur',
        'status_nikah_deb',
        'nama_ibu_deb',
        'npwp_deb',
        'ktp_deb',
        'ktp_kk_deb',
        'tempat_lahir_deb',
        'tgl_lahir_deb',
        'alamat_ktp_deb',
        'rt_deb',
        'rw_deb',
        'kelurahan_deb',
        'kecamatan_deb',
        'kabupaten_deb',
        'nama_pas',
        'nama_ibu_pas',
        'npwp_pas',
        'ktp_pas',
        'ktp_kk_pas',
        'tempat_lahir_pas',
        'tgl_lahir_pas',
        'alamat_ktp_pas',
        'rt_pas',
        'rw_pas',
        'kelurahan_pas',
        'kecamatan_pas',
        'kabupaten_pas',
        'user_id_verifikasi',
        'verifikasi',
        'tgl_verifikasi',
        'user_id_bi',
        'user_id_bi_ca',
        'tgl_bi_checking',
        'tgl_bi_checking_ca',
        'sla',
        'sla_ca',
        'ket_sla',
        'ket_sla_ca',
        'user_id',
        'user_ca',
        'tgl_buat',
        'tgl_buat_ca',
        'tgl_update',
        'dok_pendukung',
        'root_server',
        'folder_master',
        'lampiran',
        'lampiran_idi',
        'lampiran_idi_ca',
        'lampiran_no_din',
        'lampiran_no_din_ca',
        'lampiran_ideb',
        'lampiran_ideb_ca',
        'counter_notes',
        'counter_notes_ca',
        'notes',
        'notes_ca',
        'tgl_notes',
        'collection',
        'is_use',
        'is_exception',
        'no_rekening'
    ];

    public $timestamps = false;

    // protected $casts = [
    //     'flg_aktif'  => 'boolean',
    //     'created_at' => 'date:m-d-Y H:i:s',
    //     'updated_at' => 'date:m-d-Y H:i:s'
    // ];

    public function pic()
    {
        return $this->belongsTo(PIC::class, 'id_pic')
            ->withDefault(function () {
                return new PIC();
            });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')
            ->withDefault(function () {
                return new User();
            });
    }

    public function area()
    {
        return $this->belongsTo(Area::class, 'id_area')
            ->withDefault(function () {
                return new Area();
            });
    }

    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'id_cabang')
            ->withDefault(function () {
                return new Cabang();
            });
    }
}
