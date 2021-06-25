<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

use App\Models\User;


class TeleSales extends Model
{
    // use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'web';

    protected $table = 'activity_telesales';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
       'no_kontrak',
  'tgl_telp',
  'nama_debitur',
  'usia_debitur',
  'no_telp_1',
  'no_telp_2',
  'no_telp_3',
  'tanggal_lahir',
  'alamat_domisili',
  'update_pekerjaan',
  'update_penghasilan',
  'plafon_awal',
  'angsuran_ke',
  'sisa_angsuran',
  'max_pastdue',
  'total_denda',
  'nominal_angsuran',
  'taksasi_agunan',
  'baki_debet',
  'total_pelunasan',
  'jenis_agunan',
  'shgb_expired',
  'pengajuan_ro',
  'tenor',
  'produk_kredit',
  'rate_bulan',
  'angsuran',
  'biaya_provisi',
  'biaya_adm',
  'biaya_cc',
  'dsr',
  'idir',
  'ltv',
  'total_pencairan',
  'result_contacted',
  'result_uncontacted',
  'result_unconnected',
  'note_tele_sales',
  'id_pic',
  'kode_kantor'
    ];

    // protected $casts = [
    //     'flg_aktif'  => 'boolean',
    //     'created_at' => 'date:m-d-Y H:i:s',
    //     'updated_at' => 'date:m-d-Y H:i:s'
    // ];
}
