<?php

namespace App\Models\Jari;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class TaskDraft extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    protected $connection = 'jari';

    protected $table = 'taskdraft';
    protected $primaryKey = 'id';
    //protected $guarded = [];
    protected $fillable = ['taskcode', 'accno', 'accountname', 'tasktype', 'ospokok', 'assigndate', 'validuntil',  'collectfee', 'tenor', 'installment', 'collectorcode', 'cabangcode', 'custno', 'custtype', 'gender', 'maritalsts', 'birthdate', 'birthplace', 'custaddress', 'custphoneno', 'custemail', 'mobileno', 'mobileno2', 'negativests', 'negativedesc', 'relativesname', 'relativestype', 'relativesaddress', 'relativesphone', 'spousename', 'spousebirthdate', 'spousebirthplace', 'spouseaddress', 'spousemobileno', 'spouseoffice', 'spouseofficephoneno', 'spousejobname', 'companyname', 'companyaddress', 'companyphone', 'companyfax', 'jobname', 'merkname', 'modelname', 'typename', 'categoryname', 'caryear', 'color', 'chassisno', 'engineno', 'policeno', 'route', 'collbussname', 'minperiod', 'latitude', 'longitude', 'flag_kirim', 'access_token'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    // protected $hidden = [
    // ];
    public $timestamps = false;

    function detail()
    {
        return $this->hasMany('App\Models\Detailtaskdraft', 'taskcode')->select(['duedate', 'period', 'dpd', 'angstung', 'denda']);
    }
}
