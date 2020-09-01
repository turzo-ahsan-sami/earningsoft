<?php

namespace App\inventory\transaction\issue;

use Illuminate\Database\Eloquent\Model;

class InvTraIssue extends Model
{
    public $timestamps = false;
    protected $table ='inv_tra_issue';
    
    protected $fillable = [
    						 'issueBillNo',
    						 'orderNo',
                             'projectId',
                             'projectTypeId',
    						 'issueOrderNo',
    						 'branchId',
    						 'issueDate',
                             'createdBy',
    						 'totalQuantity',
    						 'totalAmount'
    						];

    function getIssueDateAttribute()
	{
    	return date('d-m-Y', strtotime($this->attributes['issueDate']));
	}

	// public function issueDetails()
	// {
	//     return $this->hasOne('App\InvTraIssueDetails','id', 'id');
	// }
}
