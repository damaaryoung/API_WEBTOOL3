<?php

namespace App\Models\Pengajuan\SO;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Support\Carbon;

use App\Models\Wilayah\Provinsi;
use App\Models\Wilayah\Kabupaten;
use App\Models\Wilayah\Kecamatan;
use App\Models\Wilayah\Kelurahan;

class Debitur extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'web';

    protected $table = 'calon_debitur';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nama_lengkap', 'gelar_keagamaan', 'gelar_pendidikan', 'jenis_kelamin', 'status_nikah', 'ibu_kandung', 'no_ktp', 'no_ktp_kk', 'no_kk', 'no_npwp', 'tempat_lahir', 'tgl_lahir', 'agama', 'alamat_ktp', 'rt_ktp', 'rw_ktp', 'id_prov_ktp', 'id_kab_ktp', 'id_kec_ktp', 'id_kel_ktp', 'alamat_domisili', 'rt_domisili', 'rw_domisili', 'id_prov_domisili', 'id_kab_domisili', 'id_kec_domisili', 'id_kel_domisili', 'pendidikan_terakhir', 'jumlah_tanggungan', 'no_telp', 'no_hp', 'alamat_surat', 'nama_anak', 'tgl_lahir_anak', 'tinggi_badan', 'berat_badan', 'pekerjaan', 'posisi', 'nama_tempat_kerja', 'jenis_pekerjaan', 'alamat_tempat_kerja', 'id_prov_tempat_kerja', 'id_kab_tempat_kerja', 'id_kec_tempat_kerja', 'id_kel_tempat_kerja', 'rt_tempat_kerja', 'rw_tempat_kerja', 'tgl_mulai_kerja', 'no_telp_tempat_kerja', 'lamp_surat_cerai', 'lamp_ktp', 'lamp_kk', 'lamp_buku_tabungan', 'lamp_sttp_pbb', 'lamp_sertifikat', 'lamp_imb', 'lamp_skk', 'lamp_sku', 'lamp_slip_gaji', 'lamp_foto_usaha', 'lamp_tempat_tinggal', 'foto_agunan_rumah', 'foto_pembukuan_usaha', 'NASABAH_ID'
    ];
    protected $dateFormat = 'd/m/Y';


    // protected $casts = [
    //     'tgl_mulai_kerja' => 'date:d-m-Y',
    //     //  'updated_at' => 'date:m-d-Y H:i:s'

    // ];



    public $timestamps = false;

    // KTP
    public function prov_ktp()
    {
        return $this->belongsTo(Provinsi::class, 'id_prov_ktp')->select(['id', 'nama'])
            ->withDefault(function () {
                return new Provinsi();
            });
    }

    public function kab_ktp()
    {
        return $this->belongsTo(Kabupaten::class, 'id_kab_ktp')->select(['id', 'nama'])
            ->withDefault(function () {
                return new Kabupaten();
            });
    }

    public function kec_ktp()
    {
        return $this->belongsTo(Kecamatan::class, 'id_kec_ktp')->select(['id', 'nama'])
            ->withDefault(function () {
                return new Kecamatan();
            });
    }

    public function kel_ktp()
    {
        return $this->belongsTo(Kelurahan::class, 'id_kel_ktp')->select(['id', 'nama', 'kode_pos'])
            ->withDefault(function () {
                return new Kelurahan();
            });
    }

    // Domisili
    public function prov_dom()
    {
        return $this->belongsTo(Provinsi::class, 'id_prov_domisili')->select(['id', 'nama'])
            ->withDefault(function () {
                return new Provinsi();
            });
    }

    public function kab_dom()
    {
        return $this->belongsTo(Kabupaten::class, 'id_kab_domisili')->select(['id', 'nama'])
            ->withDefault(function () {
                return new Kabupaten();
            });
    }

    public function kec_dom()
    {
        return $this->belongsTo(Kecamatan::class, 'id_kec_domisili')->select(['id', 'nama'])
            ->withDefault(function () {
                return new Kecamatan();
            });
    }

    public function kel_dom()
    {
        return $this->belongsTo(Kelurahan::class, 'id_kel_domisili')->select(['id', 'nama', 'kode_pos'])
            ->withDefault(function () {
                return new Kelurahan();
            });
    }

    // Tempat Kerja
    public function prov_kerja()
    {
        return $this->belongsTo(Provinsi::class, 'id_prov_tempat_kerja')->select(['id', 'nama'])
            ->withDefault(function () {
                return new Provinsi();
            });
    }

    public function kab_kerja()
    {
        return $this->belongsTo(Kabupaten::class, 'id_kab_tempat_kerja')->select(['id', 'nama'])
            ->withDefault(function () {
                return new Kabupaten();
            });
    }

    public function kec_kerja()
    {
        return $this->belongsTo(Kecamatan::class, 'id_kec_tempat_kerja')->select(['id', 'nama'])
            ->withDefault(function () {
                return new Kecamatan();
            });
    }

    public function kel_kerja()
    {
        return $this->belongsTo(Kelurahan::class, 'id_kel_tempat_kerja')->select(['id', 'nama', 'kode_pos'])
            ->withDefault(function () {
                return new Kelurahan();
            });
    }
}
