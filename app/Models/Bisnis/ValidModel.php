<?php

namespace App\Models\Bisnis;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class ValidModel extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'web';

    protected $table = 'tb_validasi';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_trans_so', 'id_calon_debitur', 'val_data_debt', 'val_lingkungan_debt', 'val_domisili_debt', 'val_pekerjaan_debt', 'val_data_pasangan', 'val_data_penjamin', 'val_agunan_tanah', 'val_agunan_kendaraan', 'val_usaha_debt', 'catatan'
    ];
}
