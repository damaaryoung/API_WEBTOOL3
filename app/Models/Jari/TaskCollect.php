<?php

namespace App\Models\Jari;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class TaskCollect extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    protected $connection = 'jari';

    protected $table = 'task_collect';
    protected $primaryKey = 'id';
    //protected $guarded = [];
    protected $fillable = ['id', 'company', 'taskcode', 'collectorid', 'dpd', 'taskid', 'accno', 'accountname', 'tasktype', 'ospokok', 'assigndate', 'odinstallment', 'penalty',  'collectfee', 'tenor', 'installment', 'collectorcode', 'validuntil', 'custno', 'custtype', 'gender', 'maritalsts', 'birthdate', 'birthplace', 'custaddress', 'custphoneno', 'custemail', 'mobileno', 'mobileno2', 'negativests', 'negativedesc', 'relativesname', 'relativestype', 'relativesaddress', 'relativesphone', 'spousename', 'spousebirthdate', 'spousebirthplace', 'spouseaddress', 'spousemobileno', 'spouseoffice', 'spouseofficephoneno', 'spousejobname', 'companyname', 'companyaddress', 'companyphone', 'companyfax', 'jobname', 'merkname', 'modelname', 'typename', 'categoryname', 'caryear', 'color', 'chassisno', 'engineno', 'policeno', 'route', 'collbusname', 'minperiod', 'latitude', 'longitude', 'access_token'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    // protected $hidden = [
    // ];
    public $timestamps = false;

    public function collect()
    {
        return $this->belongsTo('App\Models\Jari\CollectResult', 'taskid');
    }
}
