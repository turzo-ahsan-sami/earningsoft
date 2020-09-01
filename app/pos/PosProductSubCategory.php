<?php

namespace App\pos;

use Illuminate\Database\Eloquent\Model;

class PosProductSubCategory extends Model
{
    public $timestamps = false;
    protected $table ='pos_product_sub_category';
    protected $fillable = [
    						'name',
    						'productGroupId',
    						'productCategoryId',
    						'createdDate'
    						];
}
