<?php

namespace App\inventory;

use Illuminate\Database\Eloquent\Model;

class InvTrnsUse extends Model
{
    public $timestamps = false;
    protected $table ='inv_tra_use';
    protected $fillable = ['useBillNo', 'requisitionNo', 'requisition', 'branchId', 'employeeId', 'roomId','departmentId', 'totlalUseQuantity', 'totalUseAmount','useDate','useNumber'];

    function getuseDateAttribute()
	{
    	return date('m-d-Y', strtotime($this->attributes['useDate']));
	}
}
