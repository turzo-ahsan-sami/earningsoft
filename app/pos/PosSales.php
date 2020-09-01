<?php

namespace App\pos;

use Illuminate\Database\Eloquent\Model;

class PosSales extends Model
{
    public $timestamps = false;
    protected $table ='pos_sales';
    protected $fillable = [
        'companyId',
        'saleBillNo',
        'conPerson',
        'customerId',
        'productId', 
        'remark', 
        'stock', 
        'quantity',
        'totalAmount',
        'discountAmount',
        'totalAmaountAfterDis',
        'vatAmount',
        'grossTotal',
        'payAmount',
        'dueAmount',
        'salesDate',
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
