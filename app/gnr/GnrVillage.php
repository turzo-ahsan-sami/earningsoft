<?php

namespace App\gnr;

use Illuminate\Database\Eloquent\Model;

class GnrVillage extends Model
{
    public $timestamps = false;
    protected $table ='gnr_village';
    protected $fillable = ['name', 'divisionId','districtId','upzillaId','unionId','branchId','createdDate'];
}
