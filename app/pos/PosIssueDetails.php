<?php

namespace App\pos;

use Illuminate\Database\Eloquent\Model;

class PosIssueDetails extends Model
{
    public $timestamps = false;
    protected $table ='pos_issue_details';
    protected $fillable = [
        'productId',
        'orderId',
        'orderQty',
        'rowMaterialId',
        'rowMaterialIdQty',  
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
