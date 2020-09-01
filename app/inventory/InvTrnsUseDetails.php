<?php

namespace App\inventory;

use Illuminate\Database\Eloquent\Model;

class InvTrnsUseDetails extends Model
{
    public $timestamps = false;
    protected $table ='inv_tra_use_details';
    protected $fillable = ['useId', 'useBillNo', 'productId', 'productName', 'productQuantity', 'costPrice', 'totalCostPrice', 'createdDate'];
}
