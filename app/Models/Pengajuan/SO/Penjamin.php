<?php

namespace App\Models\Pengajuan\SO;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

use App\Models\Wilayah\Provinsi;
use App\Models\Wilayah\Kabupaten;
use App\Models\Wilayah\Kecamatan;
use App\Models\Wilayah\Kelurahan;

class Penjamin extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'web';

    protected $table = 'penjamin_calon_debitur';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_trans_so','nama_ktp', 'nama_ibu_kandung', 'no_ktp', 'no_npwp', 'tempat_lahir', 'tgl_lahir', 'jenis_kelamin', 'alamat_ktp', 'no_telp', 'hubungan_debitur', 'pekerjaan', 'posisi_pekerjaan', 'nama_tempat_kerja', 'jenis_pekerjaan', 'alamat_tempat_kerja', 'id_prov_tempat_kerja', 'id_kab_tempat_kerja', 'id_kec_tempat_kerja', 'id_kel_tempat_kerja', 'rt_tempat_kerja', 'rw_tempat_kerja', 'tgl_mulai_kerja', 'no_telp_tempat_kerja', 'lamp_ktp', 'lamp_ktp_pasangan', 'lamp_kk', 'lamp_buku_nikah', 'foto_selfie_penjamin','lampiran_npwp','pemasukan_penjamin','flg_aktif'
    ];

    protected $casts = [
        'flg_aktif' => 'boolean'
    ];

    public $timestamps = false;

    // Tempat Kerja
    public function prov_kerja(){
        return $this->belongsTo(Provinsi::class, 'id_prov_tempat_kerja')->select(['id', 'nama'])
            ->withDefault(function () {
                return new Provinsi();
            });
    }

    public function kab_kerja(){
        return $this->belongsTo(Kabupaten::class, 'id_kab_tempat_kerja')->select(['id', 'nama'])
            ->withDefault(function () {
                return new Kabupaten();
            });
    }

    public function kec_kerja(){
        return $this->belongsTo(Kecamatan::class, 'id_kec_tempat_kerja')->select(['id', 'nama'])
            ->withDefault(function () {
                return new Kecamatan();
            });
    }

    public function kel_kerja(){
        return $this->belongsTo(Kelurahan::class, 'id_kel_tempat_kerja')->select(['id', 'nama', 'kode_pos'])
            ->withDefault(function () {
                return new Kelurahan();
            });
    }
}
