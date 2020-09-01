<?php

namespace App\fams;

use Illuminate\Database\Eloquent\Model;

class FamsEmployeeRequisitionDetails extends Model
{
    public $timestamps = false;
    protected $table ='fams_employee_requisition_details';
    protected $fillable = ['empRequisitionId', 'requisitionNo', 'productId', 'productQuantity', 'price', 'totalPrice'];
}
