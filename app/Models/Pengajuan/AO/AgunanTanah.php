<?php

namespace App\Models\Pengajuan\AO;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

use App\Models\Wilayah\Provinsi;
use App\Models\Wilayah\Kabupaten;
use App\Models\Wilayah\Kecamatan;
use App\Models\Wilayah\Kelurahan;

class AgunanTanah extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'web';

    protected $table = 'agunan_tanah';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_trans_so','tipe_lokasi','collateral', 'alamat', 'id_provinsi', 'id_kabupaten', 'id_kecamatan', 'id_kelurahan', 'rt', 'rw', 'luas_tanah', 'luas_bangunan', 'nama_pemilik_sertifikat', 'jenis_sertifikat', 'no_sertifikat','tgl_sertifikat', 'tgl_ukur_sertifikat', 'tgl_berlaku_shgb', 'no_imb', 'njop', 'nop', 'agunan_bag_depan', 'agunan_bag_jalan', 'agunan_bag_ruangtamu', 'agunan_bag_kamarmandi', 'agunan_bag_dapur', 'lamp_sertifikat', 'lamp_imb', 'lamp_pbb',  'asli_ajb',
'status',
  'asli_imb',
  'asli_sppt',
  'asli_skmht',
  'asli_gambar_denah',
  'asli_surat_roya',
  'asli_sht',
  'asli_stts',
  'asli_ssb',
  'imb',
  'sppt',
  'no_sppt',
  'sppt_tahun',
  'skmht',
  'gambar_denah',
  'surat_roya',
  'sht',
  'no_sht',
  'sht_propinsi',
  'sht_kota',
  'stts',
  'stts_tahun',
  'ssb',
  'ssb_atas_nama',
  'lain_lain',
  'plan_akad',
  'created_at',
  'updated_at'
    ];

    protected $casts = [
        'luas_tanah'    => 'integer',
        'luas_bangunan' => 'integer',
        'tgl_berlaku_shgb'  => 'date:d-m-Y'

    ];

    public $timestamps = false;

    public function prov()
    {
        return $this->belongsTo(Provinsi::class, 'id_provinsi')->select(['id', 'nama'])
            ->withDefault(function () {
                return new Provinsi();
            });
    }

    public function kab()
    {
        return $this->belongsTo(Kabupaten::class, 'id_kabupaten')->select(['id', 'nama'])
            ->withDefault(function () {
                return new Kabupaten();
            });
    }

    public function kec()
    {
        return $this->belongsTo(Kecamatan::class, 'id_kecamatan')->select(['id', 'nama'])
            ->withDefault(function () {
                return new Kecamatan();
            });
    }

    public function kel()
    {
        return $this->belongsTo(Kelurahan::class, 'id_kelurahan')->select(['id', 'nama', 'kode_pos'])
            ->withDefault(function () {
                return new Kelurahan();
            });
    }
}
