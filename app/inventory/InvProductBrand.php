<?php

namespace App\inventory;

use Illuminate\Database\Eloquent\Model;

class InvProductBrand extends Model
{
    public $timestamps = false;
    protected $table ='inv_product_brand';
    protected $fillable = ['name', 'productGroupId', 'productCategoryId', 'productSubCategoryId', 'createdDate'];
}
