<?php

namespace App\inventory\transaction\issue;

use Illuminate\Database\Eloquent\Model;

class InvTraIssueReturn extends Model
{
    public $timestamps = false;
    protected $table ='inv_tra_issue_return';
    	
    	protected $fillable = [
    						 'issueReturnBillNo',
    						 'branchId',
    						 'totalQuantity',
    						 'totalAmount',
    						 'createdDate'
    						];
}
