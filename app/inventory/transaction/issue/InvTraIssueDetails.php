<?php

namespace App\inventory\transaction\issue;

use Illuminate\Database\Eloquent\Model;

class InvTraIssueDetails extends Model
{
    public $timestamps = false;
    protected $table ='inv_tra_issue_details';

    protected $fillable = [
    						 'issueId',
    						 'issueBillNo',
    						 'issueProductId',
    						 'issueQuantity',
    						 'price',
    						 'totalPrice',
    						 'createdDate'
    						];
    
    // public function InvTraIssue()
    // {
    //     return $this->belongsTo('App\InvTraIssue','id','id');
    // }
}
