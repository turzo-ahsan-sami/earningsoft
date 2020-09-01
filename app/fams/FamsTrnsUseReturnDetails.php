<?php

namespace App\fams;

use Illuminate\Database\Eloquent\Model;

class FamsTrnsUseReturnDetails extends Model
{
    public $timestamps = false;
    protected $table ='fams_tra_use_return_details';
    protected $fillable = ['useReturnId', 'useReturnBillNo', 'useBillNo', 'productId', 'productQuantity', 'price', 'totalPrice', 'createdDate'];
}
