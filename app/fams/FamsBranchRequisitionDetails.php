<?php

namespace App\fams;

use Illuminate\Database\Eloquent\Model;

class FamsBranchRequisitionDetails extends Model
{
    public $timestamps = false;
    protected $table ='fams_branch_requisition_details';
    protected $fillable = ['branchReqId', 'requisitionNo', 'productId', 'productQuantity', 'price', 'totalPrice'];
}
