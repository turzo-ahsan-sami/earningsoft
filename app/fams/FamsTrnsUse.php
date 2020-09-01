<?php

namespace App\fams;

use Illuminate\Database\Eloquent\Model;

class FamsTrnsUse extends Model
{
    public $timestamps = false;
    protected $table ='fams_tra_use';
    protected $fillable = ['useBillNo', 'requisitionNo', 'requisition', 'branchId', 'employeeId','roomId', 'totlalUseQuantity', 'totalUseAmount', 'useDate'];

    /*function getuseDateAttribute()
	{
    	return date('m-d-Y', strtotime($this->attributes['useDate']));
	}*/
}
