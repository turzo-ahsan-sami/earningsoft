<?php

namespace App\fams;

use Illuminate\Database\Eloquent\Model;

class FamsTrnsUseDetails extends Model
{
    public $timestamps = false;
    protected $table ='fams_tra_use_details';
    protected $fillable = ['useId', 'useBillNo', 'productId', 'productName', 'productQuantity', 'costPrice', 'totalCostPrice', 'createdDate'];
}
