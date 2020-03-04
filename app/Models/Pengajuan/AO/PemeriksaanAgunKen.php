<?php

namespace App\Models\Pengajuan\AO;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

use App\Models\Pengajuan\AgunanKendaraan;

class PemeriksaanAgunKen extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'web';

    protected $table = 'periksa_agunan_kendaraan';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_agunan_kendaraan', 'nama_pengguna', 'status_pengguna', 'jml_roda_kendaraan', 'kondisi_kendaraan', 'keberadaan_kendaraan', 'body', 'interior', 'km', 'modifikasi', 'aksesoris'
    ];

    protected $casts = [
        'jml_roda_kendaraan' => 'integer',
        'km'                 => 'integer'
    ];

    public $timestamps = false;

    public function kendaraan(){
        return $this->belongsTo(AgunanKendaraan::class, 'id_agunan_kendaraan')
            ->withDefault(function () {
                return new AgunanKendaraan();
            });
    }
}
