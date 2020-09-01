<?php

namespace App\inventory;

use Illuminate\Database\Eloquent\Model;

class InvBranchRequisitionDetails extends Model
{
    public $timestamps = false;
    protected $table ='inv_branch_requisition_details';
    protected $fillable = ['branchReqId', 'requisitionNo', 'productId', 'productQuantity', 'price', 'totalPrice', 'createdDate'];
}
