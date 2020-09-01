<?php

namespace App\pos;

use Illuminate\Database\Eloquent\Model;

class PosSale extends Model
{
    public $timestamps = false;
    protected $table ='pos_sales';
    protected $fillable = ['saleBillNo', 'salesDate', 'productId', 'branchId', 'amount', 'productTotalCost', 'createdDate'];


    public function getAmountAttribute($value)
    {
        return number_format($value, 2, '.', ',');
    }

    public function getProductTotalCostAttribute($value)
    {
        return number_format($value, 2, '.', ',');
    }
}
