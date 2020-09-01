<?php

namespace App\inventory;

use Illuminate\Database\Eloquent\Model;

class InvEmployeeRequisitionDetails extends Model
{
    public $timestamps = false;
    protected $table ='inv_employee_requisition_details';
    protected $fillable = ['empRequisitionId', 'requisitionNo', 'productId', 'productQuantity', 'price', 'totalPrice', 'createdDate'];
}
