<?php

namespace App\fams;

use Illuminate\Database\Eloquent\Model;

class FamsBranchRequisition extends Model
{
    public $timestamps = false;
    protected $table ='fams_branch_requisition';
    protected $fillable = ['requisitionNo', 'branchId', 'requisitionTo', 'totalQuantity', 'totalAmount'];

    function getcreatedDateAttribute()
	{
    	return date('m-d-Y', strtotime($this->attributes['createdDate']));
	}
}
