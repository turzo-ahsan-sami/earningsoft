<?php

namespace App\inventory;

use Illuminate\Database\Eloquent\Model;

class InvPurchase extends Model
{
	public $timestamps = false;
	protected $table ='inv_purchase';
	protected $fillable = ['billNo','orderNo','orderDate','chalanNo','chalanDate','projectId','projectTypeId','supplierId','contactPerson','purchaseDate','totalQuantity','totalAmount', 'discountPercent','discount', 'amountAfterDiscount', 'vatPercent','vat','grossTotal','payAmount','due','paymentStatus','branchId','createdBy','remark','createdDate'];

    /*function getpurchaseDateAttribute()
	{
    	return date('m-d-Y', strtotime($this->attributes['purchaseDate']));
    }*/
}


