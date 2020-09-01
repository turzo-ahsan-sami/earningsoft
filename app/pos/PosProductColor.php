<?php

namespace App\pos;

use Illuminate\Database\Eloquent\Model;

class PosProductColor extends Model
{
    public $timestamps = false;
    protected $table ='pos_product_color';
    protected $fillable = ['name', 'productGroupId', 'productCategoryId', 'productSubCategoryId', 'productBrandId', 'productModelId', 'productSizeId', 'createdDate'];
}
