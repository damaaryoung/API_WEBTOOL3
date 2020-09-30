<?php

namespace App\Models\Jari;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class CollectResult extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    protected $connection = 'jari';

    protected $table = 'collectresult';
    //  protected $primaryKey = 'id';
    //protected $guarded = [];
    protected $fillable = [
        "id",
        "company",
        "code",
        "trackingid",
        "trxid",
        "taskid",
        "datetime",
        "amount",
        "collectfee",
        "denda",
        "titipan",
        "txntype",
        "contactperson",
        "nextfollowup",
        "address",
        "addressvalid",
        "notes",
        "customerexist",
        "unitexist",
        "tipeproblem",
        "nextaction",
        "meetwith",
        "exportfilename",
        "status",
        "statusby_id",
        "statusby_company",
        "statusby_username",
        "statusby_displayname",
        "statusby_akses",
        "statusby_importfilename",
        "created_at",
        "updated_at",
        "statusby_alldataaccess",
        "statusby_phone",
        "statusby_email",
        "statusby_address",
        "statusby_about",
        "statusby_image",
        "statusby_isActive",
        "statusdate",
        "created_at",
        "updated_at",
        "tracking_id",
        "tracking_company",
        "tracking_collectorid",
        "tracking_latitude",
        "tracking_longitude",
        "tracking_datetime",
        "tracking_type",
        "tracking_imei",
        "tracking_taskid",
        "tracking_created_at",
        "tracking_updated_at",
        "tracking_appversion",
        "collector_id",
        "collector_company",
        "collector_collectorcode",
        "collector_collectorname",
        "collector_pin",
        "collector_imei",
        "collector_password",
        "collector_wrongpasscounter",
        "collector_changepass",
        "collector_cashlimit",
        "collector_cabang",
        "collector_area",
        "collector_importfilename",
        "collector_created_at",
        "collector_updated_at",
        "collector_abslatitude",
        "collector_abslongitude",
        "collector_presisiabsensi",
        "collector_isActive",
        "collector_last_apk_version", 'preference'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    // protected $hidden = [
    // ];
    // public $timestamps = false;

    // function DataCredit()
    // {
    //     return $this->belongsTo('App\User', 'user_id');
    // }
    public function taskid()
    {
        return $this->hasOne('App\Models\Jari\TaskCollect');
    }
}
