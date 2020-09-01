<?php

namespace App\inventory;

use Illuminate\Database\Eloquent\Model;

class InvProductSize extends Model
{
    public $timestamps = false;
    protected $table ='inv_product_size';
    protected $fillable = ['name', 'productGroupId', 'productCategoryId', 'productSubCategoryId', 'productBrandId', 'productModelId', 'createdDate'];
}
