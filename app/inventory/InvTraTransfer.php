<?php

namespace App\inventory;

use Illuminate\Database\Eloquent\Model;

class InvTraTransfer extends Model
{
    public $timestamps = false;
    protected $table ='inv_tra_transfer';
    protected $fillable = [
    						'transferBillNo', 
    						'orderNo', 
    						'transferOrderNo', 
    						'brancIdFrom', 
    						'branchIdTo', 
    						'transferDate', 
    						'totalTransferQuantity', 
    						'totalTransferAmount'
    						];

    public function gettransferDateAttribute(){
        return date('d/m/Y');
    }                        
}
