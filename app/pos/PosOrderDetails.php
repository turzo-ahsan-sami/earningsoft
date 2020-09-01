<?php

namespace App\pos;

use Illuminate\Database\Eloquent\Model;

class PosOrderDetails extends Model
{
    public $timestamps = false;
    protected $table ='pos_order_details';
    protected $fillable = [
        'productId',
        'orderId',
        'quantity',
        'price',
        'total', 
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
