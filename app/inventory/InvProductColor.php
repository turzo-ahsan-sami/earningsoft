<?php

namespace App\inventory;

use Illuminate\Database\Eloquent\Model;

class InvProductColor extends Model
{
    public $timestamps = false;
    protected $table ='inv_product_color';
    protected $fillable = ['name', 'productGroupId', 'productCategoryId', 'productSubCategoryId', 'productBrandId', 'productModelId', 'productSizeId', 'createdDate'];
}
