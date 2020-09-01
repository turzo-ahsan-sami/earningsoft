<?php

namespace App\fams;

use Illuminate\Database\Eloquent\Model;

class FamsEmployeeRequisition extends Model
{
    public $timestamps = false;
    protected $table ='fams_employee_requisition';
    protected $fillable = ['requisitionNo', 'branchId', 'employeeId', 'totalQuantity', 'totalAmount'];

    function getcreatedDateAttribute()
	{
    	return date('m-d-Y', strtotime($this->attributes['createdDate']));
	}
}
