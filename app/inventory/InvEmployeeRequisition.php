<?php

namespace App\inventory;

use Illuminate\Database\Eloquent\Model;

class InvEmployeeRequisition extends Model
{
    public $timestamps = false;
    protected $table ='inv_employee_requisition';
    protected $fillable = ['requisitionNo', 'branchId', 'employeeId', 'totalQuantity', 'totalAmount','description' ,'createdDate'];

    function getcreatedDateAttribute()
	{
    	return date('m-d-Y', strtotime($this->attributes['createdDate']));
	}
}
