<?php

namespace App\inventory\transaction\issue;

use Illuminate\Database\Eloquent\Model;

class InvTraIssueReturnDetails extends Model
{
    public $timestamps = false;
    protected $table ='inv_tra_issue_return_details';
    	
    	protected $fillable = [
    						 'issueReturnId',
    						 'issueReturnBillNo',
    						 'productId',
                             'quantity',
                             'price',
                             'totalAmount',
    						 'createdDate'
    						];
}
