<?php

namespace App\inventory;

use Illuminate\Database\Eloquent\Model;

class InvPurchaseReturn extends Model
{
    public $timestamps = false;
    protected $table ='inv_purchase_return';
    protected $fillable = ['purchaseReturnBillNo', 'purchaseBillNo','supplierId','remark','purchaseDate','purchaseReturnDate','totalQuantity', 'totalAmount','discountPercent', 'discount', 'amountAfterDiscount','vatPercent','vat','grossTotal','createdDate', 'branchId', 'createdBy'];

    function getpurchaseReturnDateAttribute()
	{
    	return date('Y-m-d', strtotime($this->attributes['purchaseReturnDate']));
	}
}


