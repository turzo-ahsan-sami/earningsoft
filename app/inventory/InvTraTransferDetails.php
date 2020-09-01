<?php

namespace App\inventory;

use Illuminate\Database\Eloquent\Model;

class InvTraTransferDetails extends Model
{
    public $timestamps = false;
    protected $table ='inv_tra_transfer_details';
    protected $fillable = [
    						'transferId', 
    						'transferBillNo', 
    						'transferProductId', 
    						'transferQuantity', 
                            'price', 
    						'totalPrice', 
    						'createdDate'
    				
    					];
}
