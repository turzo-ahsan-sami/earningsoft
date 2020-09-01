<?php

namespace App\inventory;

use Illuminate\Database\Eloquent\Model;

class InvTrnsUseReturn extends Model
{
    public $timestamps = false;
    protected $table ='inv_tra_use_return';
    protected $fillable = ['useReturnBillNo', 'useId', 'useBillNo', 'branchId', 'employeeId','roomId', 'totalQuantity', 'totalAmount', 'createdDate'];

    /*function getcreatedDateAttribute()
	{
    	return date('m-d-Y', strtotime($this->attributes['createdDate']));
	}*/
}
