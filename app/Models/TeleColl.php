<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

use App\Models\User;


class TeleColl extends Model
{
    // use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'web';

    protected $table = 'activity_tele';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'total_call',
        'tanggal_telpon',
        'nomor_kontrak',
        'nama_debitur',
        'usia_debitur',
        'no_telp_1',
        'no_telp_2',
        'no_telp_3',
        'tanggal_lahir',
        'sisa_angsuran',
        'tgl_kredit_tabungan',
        'total_denda',
        'angsuran_ke',
        'tgl_jatuh_tempo',
        'pastdue',
        'nominal_angsuran',
        'baki_debet',
        'total_pelunasan',
        'karakter_debitur',
        'kondisi_kerja',
        'update_pekerjaan',
        'update_penghasilan',
        'contacted',
        'uncontacted',
        'unconnected',
        'tgl_janji_bayar',
        'metode_pembayaran',
        'note_tele',
	'id_pic',
	'kode_kantor'
    ];

    // protected $casts = [
    //     'flg_aktif'  => 'boolean',
    //     'created_at' => 'date:m-d-Y H:i:s',
    //     'updated_at' => 'date:m-d-Y H:i:s'
    // ];
}
