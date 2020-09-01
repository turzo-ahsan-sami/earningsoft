<?php

namespace App\inventory;

use Illuminate\Database\Eloquent\Model;

class InvProductSubCategory extends Model
{
    public $timestamps = false;
    protected $table ='inv_product_sub_category';
    protected $fillable = [
    						'name',
    						'productGroupId',
    						'productCategoryId',
    						'createdDate'
    						];
}
