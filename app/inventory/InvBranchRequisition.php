<?php

namespace App\inventory;

use Illuminate\Database\Eloquent\Model;

class InvBranchRequisition extends Model
{
    public $timestamps = false;
    protected $table ='inv_branch_requisition';
    protected $fillable = ['requisitionNo', 'branchId', 'requisitionTo', 'totalQuantity', 'totalAmount', 'description','createdDate'];

    function getcreatedDateAttribute()
	{
    	return date('m-d-Y', strtotime($this->attributes['createdDate']));
	}
}
