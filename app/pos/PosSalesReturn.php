<?php

namespace App\pos;

use Illuminate\Database\Eloquent\Model;

class PosSalesReturn extends Model
{
    public $timestamps = false;
    protected $table ='pos_sales_return';
    protected $fillable = [
        'companyId',
        'billNo',
        'customerId',
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
