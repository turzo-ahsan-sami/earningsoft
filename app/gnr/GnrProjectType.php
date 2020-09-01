<?php

namespace App\gnr;

use Illuminate\Database\Eloquent\Model;

class GnrProjectType extends Model
{
    public $timestamps = false;
    protected $table ='gnr_project_type';
    protected $fillable = ['name', 'customerId','companyId','projectId', 'projectTypeCode', 'createdDate'];

    public function getprojectTypeCodeAttribute( $value ){
        return sprintf('%05d', $value);
    }

    public function project()
    {
        return $this->belongsTo('App\gnr\GnrProject','projectId');
    }

    public function branch()
    {
        return $this->hasMany('App\gnr\GnrBranch','projectTypeId','id');
    }
}
