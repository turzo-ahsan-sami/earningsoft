<?php

namespace App\inventory;

use Illuminate\Database\Eloquent\Model;

class InvPurchaseDetails extends Model
{
    public $timestamps = false;
    protected $table ='inv_purchase_details';
    protected $fillable = ['purchaseId', 'billNo', 'productId', 'quantity', 'price', 'totalPrice'];
}
