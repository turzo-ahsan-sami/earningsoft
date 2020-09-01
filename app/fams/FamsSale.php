<?php

namespace App\fams;

use Illuminate\Database\Eloquent\Model;

class FamsSale extends Model
{
    public $timestamps = false;
    protected $table ='fams_sale';
    protected $fillable = ['saleId','productId','branchId','amount', 'productTotalCost','productAdditionalCharge','productResaleValue','depGenerated','createdDate','writeOffByUserId'];

    
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
