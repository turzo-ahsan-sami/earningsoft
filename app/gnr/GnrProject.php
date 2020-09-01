<?php

namespace App\gnr;

use Illuminate\Database\Eloquent\Model;

class GnrProject extends Model
{
    public $timestamps = false;
    protected $table ='gnr_project';
    protected $fillable = ['customerId', 'companyId','name', 'projectCode', 'createdDate'];

    public function getprojectCodeAttribute( $value ){
        return sprintf('%03d', $value);
    }

    public function company()
    {
        return $this->belongsTo('App\gnr\GnrCompany','companyId');
    }

    public function projectType()
    {
        return $this->hasMany('App\gnr\GnrProjectType','projectId','id');
    }
}
