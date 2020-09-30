<?php

namespace App\Models\Transaksi;

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

class Lpdk_hasil extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'web';

    protected $table = 'lpdk_hasil';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'trans_so',
	'id_pic',
	'id_area',
	'id_cabang',
        'ktp_deb',
        'ktp_deb_ket',
        'ktp_pas',
        'ktp_pas_ket',
        'kk',
        'kk_ket',
        'akta_nikah',
        'akta_nikah_ket',
        'akta_cerai',
        'akta_cerai_ket',
        'akta_lahir',
        'akta_lahir_ket',
        'surat_kematian',
        'surat_kematian_ket',
        'npwp',
        'npwp_ket',
        'skd_pmi',
        'skd_pmi_ket',
        'shm_shgb',
        'shm_shgb_ket',
        'imb',
        'imb_ket',
        'pbb',
        'pbb_ket',
        'sttpbb',
        'sttpbb_ket',
        'fotocopy_ktp_ortu',
        'fotocopy_ktp_ortu_ket',
        'fotocopy_kk_ortu',
        'fotocopy_kk_ortu_ket',
        'pg_ortu',
        'pg_ortu_ket',
        'akta_nikah_ortu',
        'akta_nikah_ortu_ket',
        'sk_waris',
        'sk_waris_ket',
        'akta_lahir_waris',
        'akta_lahir_waris_ket',
        'sk_anak',
        'sk_anak_ket',
        'ktp_penjamin',
        'ktp_penjamin_ket',
        'ktp_pasangan_pen',
        'ktp_pasangan_pen_ket',
        'kk_penjamin',
        'kk_penjamin_ket',
        'aktanikah_penj',
        'aktanikah_penj_ket',
        'aktacerai_penj',
        'aktacerai_penj_ket',
        'akta_lahir_penj',
        'akta_lahir_penj_ket',
        'skematian_penjamin',
        'skematian_penjamin_ket',
        'npwp_penjamin',
        'npwp_penjamin_ket',
        'skd_penjamin',
        'skd_penjamin_ket',
        'ktp_penjual',
        'ktp_penjual_ket',
        'ktp_pas_penjual',
        'ktp_pas_penjual_ket',
        'kk_penjual',
        'kk_penjual_ket',
        'aktanikah_penjual',
        'aktanikah_penjual_ket',
        'aktacerai_penjual',
        'aktacerai_penjual_ket',
        'aktalahir_penjual',
        'aktalahir_penjual_ket',
        'skematian_penjual',
        'skematian_penjual_ket',
        'npwp_penjual',
        'npwp_penjual_ket',
        'skd_penjual',
        'skd_penjual_ket',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'flg_aktif'  => 'boolean',
        'created_at' => 'date:d-m-Y H:i:s',
        'updated_at' => 'date:d-m-Y H:i:s'
    ];
}
