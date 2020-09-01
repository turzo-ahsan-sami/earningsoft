<?php

namespace App\inventory;

use Illuminate\Database\Eloquent\Model;

class InvTrnsUseReturnDetails extends Model
{
    public $timestamps = false;
    protected $table ='inv_tra_use_return_details';
    protected $fillable = ['useReturnId', 'useReturnBillNo', 'useBillNo', 'productId', 'productQuantity', 'price', 'totalPrice', 'createdDate'];
}
