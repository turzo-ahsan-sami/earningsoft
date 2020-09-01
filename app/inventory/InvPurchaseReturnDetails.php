<?php

namespace App\inventory;

use Illuminate\Database\Eloquent\Model;

class InvPurchaseReturnDetails extends Model
{
    public $timestamps = false;
    protected $table ='inv_purchase_return_details';
    protected $fillable = ['purchaseReturnId', 'purchaseReturnBillNo', 'purchaseBillNo', 'productId', 'quantity', 'price', 'totalPrice'];
}
