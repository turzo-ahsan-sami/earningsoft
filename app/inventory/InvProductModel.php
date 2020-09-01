<?php

namespace App\inventory;

use Illuminate\Database\Eloquent\Model;

class InvProductModel extends Model
{
    public $timestamps = false;
    protected $table ='inv_product_model';
    protected $fillable = ['name', 'productGroupId', 'productCategoryId', 'productSubCategoryId', 'productBrandId', 'createdDate'];
}
