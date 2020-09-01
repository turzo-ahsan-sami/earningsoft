<?php

namespace App\pos;

use Illuminate\Database\Eloquent\Model;

class PosPurchase extends Model
{
    public $timestamps = false;
    protected $table ='pos_purchase';
    protected $fillable = [
        'companyId',
        'billNo',
        'conPerson',
        'supplierId',
        'productId', 
        'remark', 
        'qty',
        'totalAmount',
        'discountAmount',
        'totalAmaountAfterDis',
        'vatAmount',
        'grossTotal',
        'payAmount',
        'dueAmount',
        'purchaseDate',
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
