<?php

namespace App\pos;

use Illuminate\Database\Eloquent\Model;

class PosPurchaseReturn extends Model
{
    public $timestamps = false;
    protected $table ='pos_purchase_return';
    protected $fillable = [
        'companyId',
        'billNo',
        'supplierId',
        'productId', 
        'qty',
        'totalAmount',
        'returnDate',
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
