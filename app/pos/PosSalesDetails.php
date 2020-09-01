<?php

namespace App\pos;

use Illuminate\Database\Eloquent\Model;

class PosSalesDetails extends Model
{
    public $timestamps = false;
    protected $table ='pos_sales_details';
    protected $fillable = [
        'productId',
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
