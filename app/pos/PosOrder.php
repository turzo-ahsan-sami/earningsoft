<?php

namespace App\pos;

use Illuminate\Database\Eloquent\Model;

class PosOrder extends Model
{
    public $timestamps = false;
    protected $table ='pos_order';
    protected $fillable = [
        'companyId',
        'billNo',
        'conPerson',
        'customerId',
        'productId', 
        'projectId', 
        'projectTypeId', 
        'branchId', 
        'cashBankLedgerId', 
        'paymentType', 
        'remark', 
        'qty',
        'totalAmount',
        'discountAmount',
        'totalAmaountAfterDis',
        'vatAmount',
        'grossTotal',
        'payAmount',
        'dueAmount',
        'orderDate',
        'createdDate'
    ];


    // public function getAmountAttribute($value)
    // {
    //     return number_format($value, 2, '.', ',');
    // }

    // public function getProductTotalCostAttribute($value)
    // {
    //     return number_format($value, 2, '.', ',');
    // }
}
