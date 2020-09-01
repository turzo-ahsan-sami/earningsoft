<?php

namespace App\pos;

use Illuminate\Database\Eloquent\Model;

class PosProductBrand extends Model
{
    public $timestamps = false;
    protected $table ='pos_product_brand';
    protected $fillable = ['name', 'productGroupId', 'productCategoryId', 'productSubCategoryId', 'createdDate'];
}
