<?php

namespace App\pos;

use Illuminate\Database\Eloquent\Model;
use App\pos\PosProduct;

class PosCostSheet extends Model
{
    public $timestamps = false;
    protected $table ='pos_cost_sheet';
    protected $fillable = ['companyId', 'productId', 'productInfo', 'otherCost', 'effectDate', 'totalAmount'];

    function product()
    {
        return $this->hasOne(PosProduct::class, 'id', 'productId');
    }
}
