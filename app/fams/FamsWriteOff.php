<?php

namespace App\fams;

use Illuminate\Database\Eloquent\Model;

class FamsWriteOff extends Model
{
    public $timestamps = false;
    protected $table ='fams_write_off';
    protected $fillable = ['writeOffId','productId','branchId','branchWriteOffNo','amount', 'productTotalCost','productAdditionalCharge','depGenerated','createdDate','writeOffByUserId'];

    
    public function getAmountAttribute($value)
    {
        return number_format($value, 2, '.', ',');   
    }

    public function getProductTotalCostAttribute($value)
    {
        return number_format($value, 2, '.', ',');   
    }

    public function getProductAdditionalChargeAttribute($value)
    {
        return number_format($value, 2, '.', ',');   
    }

    public function getDepGeneratedAttribute($value)
    {
        return number_format($value, 2, '.', ',');   
    }
}
