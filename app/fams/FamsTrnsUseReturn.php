<?php

namespace App\fams;

use Illuminate\Database\Eloquent\Model;

class FamsTrnsUseReturn extends Model
{
    public $timestamps = false;
    protected $table ='fams_tra_use_return';
    protected $fillable = ['useReturnBillNo', 'useId', 'useBillNo', 'branchId', 'employeeId','roomId', 'totalQuantity', 'totalAmount', 'createdDate'];

    /*function getcreatedDateAttribute()
	{
    	return date('m-d-Y', strtotime($this->attributes['createdDate']));
	}*/
}
